<?php

namespace RyzomExtra;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    protected $cachePath = '/tmp/ryex_cache';

    public function setUp() : void
    {
        // set mock time() to return '0'
        //time(0);
    }

    public function tearDown() : void
    {
        if (file_exists($this->cachePath)) {
            // first remove all cache files
            $files = glob($this->cachePath . '/*.cache');
            foreach ($files as $file) {
                unlink($file);
            }

            // test to make sure path is empty
            $files = glob($this->cachePath . '/*');
            if (!empty($files)) {
                $this->fail("Cache path is not empty ($this->cachePath)");
            }

            // remove directory only when it's empty
            rmdir($this->cachePath);
        }
    }

    public function testCreateCache()
    {
        $this->assertFileDoesNotExist($this->cachePath);

        $cache = new Cache($this->cachePath);
        $this->assertFileExists($this->cachePath, 'Cache directory was not created');
    }

    public function testCreateCacheException()
    {
        $this->expectException('\RuntimeException', 'Creating cache directory failed (/no-permission)');

        $cache = new Cache('/no-permission');
        $this->fail("No exception was thrown");
    }

    public function testSet()
    {
        $key = 'test-set';
        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, '1234', 1);
        $this->assertTrue($cache->exists($key), 'Cache file was not created');
    }

    public function testGet()
    {
        $key = 'test-get';
        $expected = uniqid('test');

        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, $expected, 0);
        $this->assertTrue($cache->exists($key), 'Cache file was not created');

        $got = $cache->get($key);
        $this->assertEquals($expected, $got);
    }

    public function testGetUnknownObject()
    {
        $key = 'test-get-unknown-object';
        $cache = new Cache($this->cachePath);

        $this->assertNull($cache->get($key));
        $this->assertTrue($cache->expired($key));
    }

    public function testDelete()
    {
        $key = 'test-delete';

        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, '1234', 0);
        $this->assertTrue($cache->exists($key), 'Cache file was not created');

        $cache->delete($key);
        $this->assertFalse($cache->exists($key), 'Cache file was not deleted');
    }

    public function testExpired()
    {
        $key = 'test-expired';

        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, '1234', 5);
        $this->assertTrue($cache->exists($key), 'Cache file was not created');

        // setup mock time() function to return '6'
        //time(6);
        //$this->assertTrue($cache->expired($key), 'Cache did not expire');
    }

    public function testNotExpired()
    {
        $key = 'test-not-expired';

        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, '1234', 5);
        $this->assertTrue($cache->exists($key), 'Cache file was not created');
        $this->assertFalse($cache->expired($key), 'TTL had no effect');
    }

    public function testDoesNotExpire()
    {
        $key = 'test-does-not-expire';

        $cache = new Cache($this->cachePath);
        $this->assertFalse($cache->exists($key), 'Cache file was found');

        $cache->set($key, '1234');
        $this->assertTrue($cache->exists($key), 'Cache file was not created');
        $this->assertFalse($cache->expired($key));
    }

}
