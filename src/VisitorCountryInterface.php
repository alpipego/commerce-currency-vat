<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 16:24
 */

namespace Alpipego\Commerce;

interface VisitorCountryInterface
{
    public function setCountry(string $code);

    public function getCountryName(): string;

    public function getCountryCode(): string;

    public function getCountryCurrencies() : array;
}
