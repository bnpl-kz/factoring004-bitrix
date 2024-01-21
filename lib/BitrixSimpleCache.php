<?php

namespace Bnpl\Payment;

use Psr\SimpleCache\CacheInterface;
use Bitrix\Main\Data\Cache as BitrixCache;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use DateInterval;

/**
 * @see https://gist.github.com/SerginhoLD/285240cd0f8b979c4e60855dec9acab2
 */
class BitrixSimpleCache implements CacheInterface
{
    /** @var int */
    private $maxTtl = 2592000; // 30 days

    /** @var BitrixCache */
    private $bitrixCache;

    /** @var string */
    private $initDir = '/';

    /** @var string */
    private $baseDir = 'cache/__simple';

    /**
     * @param BitrixCache $bitrixCache
     * @param string $initDir todo: в битриксе по умолчанию false, тогда за папку отвечает request, но это полный пиздец, по этому в жопу false, всегда string
     * @param string $baseDir
     */
    public function __construct(BitrixCache $bitrixCache, $initDir = null, $baseDir = null)
    {
        $this->bitrixCache = $bitrixCache;

        if ($initDir !== null) {
            $this->initDir = $initDir;
        }

        if ($baseDir !== null) {
            $this->baseDir = $baseDir;
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $result = $this->getResult($key);
        return $result->isSuccess() ? $result->getData()['value'] : $default;
    }

    /**
     * todo: возможно следует в этом методе вызывать delete() если срок действия вышел, а файл есть
     *
     * @param string $key
     *
     * @return Result
     */
    private function getResult($key)
    {
        $result = new Result();

        // todo: в исходниках проверка, если $dateCreate < (time() - $ttl), то false; надо обязательно передавать большое значение $ttl
        if ($this->bitrixCache->initCache($crutch = $this->maxTtl, $key, $this->initDir, $this->baseDir)) {
            $data = $this->bitrixCache->getVars();

            if (!isset($data['expire'])) {
                $result->addError(new Error('Expire not found'));
                return $result;
            }

            $data['expire'] = (int) $data['expire'];

            if ($data['expire'] < time()) {
                $result->addError(new Error('Expired'));
                return $result;
            }

            if (!array_key_exists('value', $data)) {
                $result->addError(new Error('Value not found'));
                return $result;
            }

            $result->setData(['value' => $data['value']]);
        } else {
            $result->addError(new Error('Key not found'));
        }

        return $result;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->getResult($key)->isSuccess();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null|int|\DateInterval $ttl
     *
     * @return bool
     * @throws \Exception
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->delete($key); // todo: т.к. битрикс в исходниках положил хуй на пересохранение, то всегда сначала delete

        if ($ttl instanceof \DateInterval) {
            $ttl = $this->dateIntervalToSeconds($ttl);
        }

        try {
            $ttl = $ttl === null ? $this->maxTtl : $ttl;

            if ($this->bitrixCache->startDataCache($ttl, $key, $this->initDir, [], $this->baseDir)) {
                // todo: т.к. битрикс в исходниках положил хуй на $dateexpire, то храним еще и срок действия
                $this->bitrixCache->endDataCache([
                    'expire' => time() + $ttl,
                    'value' => $value,
                ]);
            }
        } catch (\Exception $e) {
            $this->bitrixCache->abortDataCache();
            throw $e;
        }

        return true;
    }

    /**
     * @param \DateInterval $interval
     *
     * @return int
     * @throws \Exception
     */
    private function dateIntervalToSeconds(\DateInterval $interval)
    {
        $now = new \DateTimeImmutable();
        $endTime = $now->add($interval);
        return $endTime->getTimestamp() - $now->getTimestamp();
    }

    /**
     * @param string $key
     * @param callable|\Closure $callable
     * @param null|int|\DateInterval $ttl
     *
     * @return mixed
     * @throws \Exception
     */
    public function getOrSet($key, callable $callable, $ttl = null)
    {
        $result = $this->getResult($key);

        if ($result->isSuccess()) {
            $value = $result->getData()['value'];
        } else {
            $value = call_user_func($callable, $this);
            $this->set($key, $value, $ttl);
        }

        return $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        $this->bitrixCache->clean($key, $this->initDir, $this->baseDir);
        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->bitrixCache->cleanDir($this->initDir, $this->baseDir);
        return true;
    }

    /**
     * @param iterable $keys
     * @param mixed $default
     *
     * @return iterable|\Generator
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    /**
     * @param iterable $values
     * @param null|int|\DateInterval $ttl
     *
     * @return bool
     * @throws \Exception
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param iterable $keys
     *
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }
}