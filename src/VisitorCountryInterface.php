<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 16:24
 */

namespace Alpipego\Commerce;

use Alpipego\Commerce\Models\Country;

interface VisitorCountryInterface
{
    public function getCountry(string $code = null): Country;

    public function setCountry(string $code);

    public function getCountryName(): string;

    public function getCountryCode(): string;
}
