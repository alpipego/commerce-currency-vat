<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 10:35
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Cache;

final class Request implements RequestInterface
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function get(string $url, string $returnModel, int $expire = 3600 * 24)
    {
        $cacheKey = $this->cache->makeKey($url);
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $res = \Requests::get($url);
        if ($res->success) {
            $this->cache->set($cacheKey, $res->body, $returnModel, $expire);
        }

        return $this->cache->get($cacheKey);
    }
}
