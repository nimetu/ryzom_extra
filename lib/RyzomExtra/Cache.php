<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2013 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomExtra.
//
// RyzomExtra is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomExtra is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

namespace RyzomExtra;

/**
 * Simple file cache
 */
class Cache implements CacheInterface
{
    /**
     * Cache location
     *
     * @var string
     */
    private $path = '';

    /**
     * Sets up new cache
     * Create destination directory as needed with 0755 permissions
     *
     * @param string $path patch where to write cache, if it does not exists, will try to create it
     *
     * @throws \RuntimeException
     */
    function __construct($path)
    {
        $this->path = rtrim($path, '/');
        if (!file_exists($this->path) && @mkdir($this->path, 0755, true) !== true) {
            throw new \RuntimeException("Creating cache directory failed ($this->path)");
        }
    }

    /** {@inheritdoc} */
    public function set($key, $value, $ttl = 0)
    {
        if ($ttl > 0) {
            $expires = time() + $ttl;
        } else {
            $expires = false;
        }
        $meta = array(
            'ttl' => $ttl,
            'expires' => $expires,
            'data' => $value,
        );
        $this->write($key, $meta);
    }

    /** {@inheritdoc} */
    public function get($key)
    {
        $meta = $this->read($key);
        if ($meta) {
            return $meta['data'];
        }

        return null;
    }

    /** {@inheritdoc} */
    public function delete($key)
    {
        $fname = $this->getObjectPath($key);
        if (file_exists($fname)) {
            unlink($fname);
        }
    }

    /** {@inheritdoc} */
    public function exists($key)
    {
        $fname = $this->getObjectPath($key);
        return file_exists($fname);
    }

    /** {@inheritdoc} */
    public function expired($key)
    {
        $now = time();
        $meta = $this->read($key);
        if ($meta) {
            if ($meta['expires'] === false) {
                return false;
            }
            return $now >= $meta['expires'];
        }

        return true;
    }

    /**
     * Return full path for object
     *
     * @param string $key
     *
     * @return string
     */
    protected function getObjectPath($key)
    {
        return $this->path.'/'.$key.'.cache';
    }

    /**
     * Write cache object to a file
     *
     * @param string $key
     * @param array $data
     *
     * @throws \RuntimeException
     */
    private function write($key, array $data)
    {
        $fname = $this->getObjectPath($key);
        $content = serialize($data);
        $path = dirname($fname);
        if (!file_exists($path) && !mkdir($path, 0755, true)) {
            throw new \RuntimeException('Unable to create directory for cache');
        }
        file_put_contents($fname, $content);
    }

    /**
     * Read cache object from a file
     *
     * @param string $key
     *
     * @return array
     */
    private function read($key)
    {
        $fname = $this->getObjectPath($key);
        if (file_exists($fname)) {
            $content = file_get_contents($fname);
            return unserialize($content);
        }
        return null;
    }

}
