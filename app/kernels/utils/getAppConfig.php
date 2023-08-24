<?php

namespace App\Kernels\Utils;

/**
 * config/app_configの値を取得するためのユーティリティ関数
 *
 * @param string $key
 * @return string|integer|null
 */
function getAppConfig(string $key): string|int|null
{
    $config = require(__DIR__ . '/../../config/app_config.php');

    return $config[$key] ?? null;
}
