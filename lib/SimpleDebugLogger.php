<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\Diag\Debug;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use ReflectionClass;

class SimpleDebugLogger extends AbstractLogger
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $levels;

    public function __construct(string $path, string $level = LogLevel::DEBUG)
    {
        $this->path = $path;
        $this->levels = $this->getWritableLevels($level);
    }

    public function log($level, $message, array $context = [])
    {
        if (in_array($level, $this->levels, true)) {
            return;
        }

        Debug::writeToFile(strtoupper($level) . ': ' . $message, '', $this->path);
    }

    /**
     * @return string[]
     */
    private function getWritableLevels(string $level): array
    {
        $class = new ReflectionClass(LogLevel::class);
        $levels = array_reverse(array_values($class->getConstants()));

        return array_slice($levels, array_search($level, $levels, true));
    }
}
