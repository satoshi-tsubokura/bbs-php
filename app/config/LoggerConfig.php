<?php

namespace App\Config;

/**
 * ログに関する定数クラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
final class LoggerConfig
{
    // ログレベル定数
    public const DEBUG = 0;
    public const NOTICE = 1;
    public const INFO = 2;
    public const WARNING = 3;
    public const ERROR = 4;

    public const LOG_LEVEL = self::INFO;
    // ログ出力ディレクトリパス
    public const LOG_DIRECTORY = DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'app';
    // ログ出力ファイルベース名
    public const LOG_FILE = 'bbs.log';

    // 最大ファイル数
    public const FILE_MAX_NUM = 30;
}
