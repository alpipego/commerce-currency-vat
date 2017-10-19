<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 12:11
 */

namespace Alpipego\Commerce\Cache;

use Alpipego\Commerce\Models\Mapper;

final class Cache implements CacheInterface
{
    const CACHE_PATH = __DIR__ . '/../../cache';
    private $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function makeKey(string $source): string
    {
        return sanitize_file_name(md5($source));
    }

    public function set(string $key, string $value, string $type, int $expire = 60 * 60 * 24): bool
    {
        if ($expire <= 0) {
            $expire = 60 * 60 * 24;
        }
        if ($this->has($key)) {
            return false;
        }

        $contents = serialize($this->mapper->map($value, $type));

        return file_put_contents(
            self::CACHE_PATH . '/' . $key . '.json',
            json_encode(['expire' => gmdate('U') + $expire, 'contents' => $contents])
        );
    }

    public function has(string $key): bool
    {
        return file_exists(self::CACHE_PATH . '/' . $key . '.json') && ! $this->expired($key);
    }

    public function expired(string $key): bool
    {
        $file = self::CACHE_PATH . '/' . $key . '.json';
        if (! file_exists($file)) {
            return true;
        }
        $contents = json_decode(file_get_contents($file));

        return (int)$contents->expire <= gmdate('U');
    }

    public function get(string $key)
    {
        $file = self::CACHE_PATH . '/' . $key . '.json';
        if (file_exists($file)) {
            $contents = json_decode(file_get_contents($file));

            return unserialize($contents->contents);
        }

        return null;
    }
}
