<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\Config\Configuration;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class DebugLoggerFactory
{
    public static function create(): DebugLoggerFactory
    {
        return new static();
    }

    public function createLogger(): LoggerInterface
    {
        if (!$this->isEnabled()) {
            return new NullLogger();
        }

        if (class_exists('\Bitrix\Main\Diag\Logger')) {
            return $this->createPsrLogger();
        }

        return new SimpleDebugLogger($this->getLogPath());
    }

    private function isEnabled(): bool
    {
        return (bool) Configuration::getValue('exception_handling')['debug'] ?? false;
    }

    private function createPsrLogger(): LoggerInterface
    {
        $logger = new \Bitrix\Main\Diag\FileLogger($this->getLogPath());
        $logger->setLevel(LogLevel::DEBUG);

        return $logger;
    }

    private function getLogPath(): string
    {
        $config = Configuration::getValue('exception_handling')['log'] ?? [];
        $path = $config['settings']['file'] ?? '';

        $path = pathinfo($path, PATHINFO_DIRNAME);
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        $path = trim($path, '/');

        if ($path) {
            $path = $path . '/';
        }

        return $path . $this->getLogFilename();
    }

    private function getLogFilename(): string
    {
        return 'factoring004-' . date('Y-m-d') . '.log';
    }
}
