<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CustomTestCase extends TestCase
{
    /**
     * 非可視性のプロパティを取得するためのクラス
     *
     * @param object $instance
     * @param string $name プロパティ名
     * @return mixed プロパティの値
     */
    protected function getPrivateProperty(object $instance, string $name): mixed
    {
        $reflectionCls = new \ReflectionClass(get_class($instance));
        return $reflectionCls->getProperty($name)->getValue($instance);
    }
}
