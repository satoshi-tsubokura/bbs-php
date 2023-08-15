<?php

namespace App\Utils;

function trimSpaceStr(string $target): string
{
    return trim($target, "　 \n\r\t\v\x00");
}
