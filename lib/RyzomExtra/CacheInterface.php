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
 * Interface CacheInterface
 */
interface CacheInterface
{
    /**
     * Stores a new value in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     *
     * @return void
     */
    public function set($key, $value, $ttl = null);

    /**
     * Fetches an object from the cache.
     * Return null if object was not found
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Deletes an item from the cache.
     *
     * @param string $key
     */
    public function delete($key);

    /**
     * Check if the key exists in the cache.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function exists($key);

    /**
     * Check if the object is expired
     *
     * @param string $key
     *
     * @return boolean
     */
    public function expired($key);

}
