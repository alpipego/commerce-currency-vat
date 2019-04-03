<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 10:39
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Cache;

interface RequestInterface
{
    public function get(string $url, string $returnModel, int $expire = 3600 * 24);
}
