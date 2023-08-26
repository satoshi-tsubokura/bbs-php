<?php

/**
 * html表示文字列をエスケープ処理するためのラッパー関数
 *
 * @param string $plainStr 返還前文字列
 * @param string $charset 文字コード
 * @return string 変換後文字列
 */
function h(string $plainStr, string $charset = 'UTF-8'): string
{
    return htmlspecialchars($plainStr, ENT_QUOTES | ENT_HTML5, $charset, false);
}
