<?php

namespace App\Models\Databases;

interface IConnection
{
    public function connect(): void;
    public function close(): void;
}
