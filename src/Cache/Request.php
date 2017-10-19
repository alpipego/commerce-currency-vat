<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 10:35
 */

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

        try {
            $res = \Requests::get($url);
            $this->cache->set($cacheKey, $res->body, $returnModel, $expire);

            return $this->cache->get($cacheKey);
        } catch (\Requests_Exception $e) {
        }

        return $this->cache->get($cacheKey);
    }

    public function soap(
        string $url,
        array $data,
        array $returnKeys,
        string $responseType,
        string $returnModel,
        int $expire = 3600 * 24
    ) {
        $cacheKey = $this->cache->makeKey($url . implode(';', $data));
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $response = [];
        $pattern  = '/<(%s).*?>([\s\S]*)<\/\1>/';
        $result   = file_get_contents($url, false, stream_context_create($data));
        if (preg_match(sprintf($pattern, $responseType), $result, $matches)) {
            foreach ($returnKeys as $key) {
                preg_match(sprintf($pattern, $key), $matches[2], $value);
                $response[$key] = $value[2];
            }
            $this->cache->set($cacheKey, json_encode($response), $returnModel, $expire);

            return $this->cache->get($cacheKey);
        }

        return $response;
    }
}
