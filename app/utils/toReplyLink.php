<?php

/**
 * コメント返信文字をリンクに変換するビューヘルパー関数
 *
 * @param string $str
 * @return string
 */
function toReplyLink(string $str): string
{
    // エスケープ後の文字列で判定する
    $pattern = '/&gt;&gt;(\d+)/';
    $replacement = '<a href="#comment-$1" class="c-link">$0</a>';
    return preg_replace($pattern, $replacement, $str);
}
