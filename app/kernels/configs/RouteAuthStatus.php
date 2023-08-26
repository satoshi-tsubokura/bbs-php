<?php

namespace App\Kernels\Configs;

/**
 * ルーティングでの認証に関する状態を羅列したEnum
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
enum RouteAuthStatus
{
    // 認証必須
    case Required;
    // 認証任意
    case Optional;
    // 非認証でなければならない
    case UnAuthenticated;
}
