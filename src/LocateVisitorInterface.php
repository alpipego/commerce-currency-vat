<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 09:26
 */
declare(strict_types=1);

namespace Alpipego\Commerce;

interface LocateVisitorInterface
{
    /**
     * Get visitors country code (ISO 3166-1 alpha-2)
     * uses cache/cookie
     *
     * @return null|string
     */
    public function locate(): string;

    public function setDefaultCountry(string $country): void;

    public function setCountry(string $code);
}
