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
    const PREFIX = 'commerce_';
    private $mapper;
    private $transient = [];

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function makeKey(string $source): string
    {
        return sanitize_key(md5($source));
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

        return set_site_transient(
            self::PREFIX . $key,
            ['expire' => gmdate('U') + $expire, 'contents' => $contents],
            $expire
        );
    }

    public function has(string $key): bool
    {
        return (bool)$this->getTransient($key) && ! $this->expired($key);
    }

    private function getTransient(string $key): ?array
    {
        if (! array_key_exists($key, $this->transient)) {
            $transient = get_site_transient(self::PREFIX . $key);
            if (! $transient) {
                return null;
            }
            $this->transient[$key] = $transient;
        }

        return $this->transient[$key];
    }

    public function expired(string $key): bool
    {
        if (is_null($this->getTransient($key))) {
            return true;
        }

        return (int)$this->transient[$key]['expire'] <= gmdate('U');
    }

    public function get(string $key)
    {
        if (! is_null($this->getTransient($key))) {
            return unserialize($this->transient[$key]['contents']);
        }

        return null;
    }
}
