<?php

namespace App\Config;

/**
 * ルーティングでの認証に関する状態を羅列したEnum
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
