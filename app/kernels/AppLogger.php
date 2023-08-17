<?php

namespace App\Kernels;

use App\Config\LoggerConfig;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

class AppLogger
{
    private $logger;
    private static AppLogger $singleton;

    private function __construct()
    {
        $logLevel = match (LoggerConfig::LOG_LEVEL) {
            LoggerConfig::DEBUG => Level::Debug,
            LoggerConfig::NOTICE => Level::Notice,
            LoggerConfig::INFO => Level::Info,
            LoggerConfig::WARNING => Level::Warning,
            LoggerConfig::ERROR => Level::Error,
            default => Level::DEBUG
        };
        $logFile = LoggerConfig::LOG_DIRECTORY . DIRECTORY_SEPARATOR . LoggerConfig::LOG_FILE;

        $this->logger = new Logger('Application');

        $this->logger->pushHandler(new StreamHandler($logFile, $logLevel));
        $this->logger->pushHandler(new RotatingFileHandler($logFile, LoggerConfig::FILE_MAX_NUM, $logLevel));
    }

    public static function getInstance()
    {
        if(! isset($singleton)) {
            self::$singleton = new AppLogger();
        }

        return self::$singleton;
    }

    /**
     * debugレベルのログを書き込む
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public function debug(string $message, array $data = []): void
    {
        $this->logger->debug($message, $data);
    }

    /**
     * infoレベルのログを書き込む
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public function info(string $message, array $data = []): void
    {
        $this->logger->info($message, $data);
    }

    /**
     * noticeレベルのログを書き込む
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public function notice(string $message, array $data = []): void
    {
        $this->logger->info($message, $data);
    }

    /**
     * warningレベルのログを書き込む
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public function warn(string $message, array $data = []): void
    {
        $this->logger->info($message, $data);
    }

    /**
     * errorレベルのログを書き込む
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public function error(string $message, array $data = []): void
    {
        $this->logger->info($message, $data);
    }
}
