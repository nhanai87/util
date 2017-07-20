<?php

namespace go1\util\schema\tests;

use go1\util\DB;
use go1\util\tests\UtilTestCase;

class DBTest extends UtilTestCase
{
    public function testConnectionOptions()
    {
        putenv('_DOCKER_FOO_DB_NAME=foo_db');
        putenv('_DOCKER_FOO_DB_USERNAME=foo_username');
        putenv('_DOCKER_FOO_DB_PASSWORD=foo_password');
        putenv('_DOCKER_FOO_DB_SLAVE=slave.foo.com');
        putenv('_DOCKER_FOO_DB_HOST=foo.com');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $foo = DB::connectionOptions('foo');
        $this->assertEquals('pdo_mysql', $foo['driver']);
        $this->assertEquals('foo_db', $foo['dbname']);
        $this->assertNotEquals('slave.foo.com', $foo['host']);
        $this->assertEquals('foo_username', $foo['user']);
        $this->assertEquals('foo_password', $foo['password']);
        $this->assertEquals(3306, $foo['port']);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $foo = DB::connectionOptions('foo');
        $this->assertEquals('slave.foo.com', $foo['host']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $foo = DB::connectionOptions('foo', true);
        $this->assertEquals('slave.foo.com', $foo['host']);
    }

    public function testCacheSet()
    {
        $cache = &DB::cache(self::class, []);
        $cache['foo'] = 'bar';

        $this->assertEquals(['foo' => 'bar'], DB::cache(self::class));
    }

    public function testCacheRetrieval()
    {
        $this->assertEquals(['foo' => 'bar'], DB::cache(self::class));
    }

    public function testCacheRetrievalReset()
    {
        $this->assertEquals([], DB::cache(self::class, null, true));
    }
}