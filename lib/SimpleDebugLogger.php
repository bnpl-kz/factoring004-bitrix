<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\Diag\Debug;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class SimpleDebugLogger extends AbstractLogger
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function log($level, $message, array $context = [])
    {
        if ($level !== LogLevel::DEBUG) {
            return;
        }

        Debug::writeToFile($message, '', $this->path);
    }
}
