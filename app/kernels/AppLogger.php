<?php

namespace App\Kernels;

use App\Config\LoggerConfig;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

/**
 * ログ出力処理を行うクラス
 * NOTE: シングルトンクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class AppLogger
{
    private $logger;
    private static AppLogger $singleton;

    /**
     * ログ出力のための設定を定義する
     */
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

    /**
     * インスタンスを取得する。
     * ただし、アプリケーション内で本クラスのインスタンスを1つ以上作成しない。
     *
     * @return self
     */
    public static function getInstance(): self
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
