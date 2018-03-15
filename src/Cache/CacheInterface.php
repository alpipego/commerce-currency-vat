<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 12:09
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Cache;

interface CacheInterface
{
    public function makeKey(string $source): string;

    public function set(string $key, string $value, string $type, int $expire = 60 * 60 * 24): bool;

    public function expired(string $key): bool;

    public function has(string $key): bool;

    public function get(string $key);
}
