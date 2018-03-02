<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 08:57
 */

namespace Alpipego\Commerce;

interface VatInterface
{
    public function getStandardRate(string $isoCode = ''): float;

    public function getReducedRate(string $isoCode = ''): ?float;

    public function getSuperReducedRate(string $isoCode = ''): ?float;

    public function getReducedRateAlt(string $isoCode = ''): ?float;
}
