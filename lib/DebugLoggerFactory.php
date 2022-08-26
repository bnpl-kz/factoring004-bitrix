<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Diag\LogFormatter;
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
        $path = $this->getLogPath();
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;

        if (!is_writable(dirname($fullPath))) {
            return new NullLogger();
        }

        $level = $this->isDebug() ? LogLevel::DEBUG : LogLevel::INFO;

        if (class_exists('\Bitrix\Main\Diag\Logger')) {
            return $this->createPsrLogger($fullPath, $level);
        }

        return new SimpleDebugLogger($path, $level);
    }

    private function isDebug(): bool
    {
        return (bool) Configuration::getValue('exception_handling')['debug'] ?? false;
    }

    private function createPsrLogger(string $path, string $level): LoggerInterface
    {
        $logger = new \Bitrix\Main\Diag\FileLogger($path);
        $logger->setLevel($level);
        $logger->setFormatter(new class() extends LogFormatter {
            public function format($message, array $context = []): string
            {
                return parent::format($message, $context) . PHP_EOL;
            }
        });

        return $logger;
    }

    private function getLogPath(): string
    {
        $config = Configuration::getValue('exception_handling')['log'] ?? [];
        $path = $config['settings']['file'] ?? '';

        if (empty($path)) {
            return 'bitrix/tmp/factoring004/' . $this->getLogFilename();
        }

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
