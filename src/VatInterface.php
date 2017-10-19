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
    public function getStandardRate(): float;

    public function getReducedRate(): ?float;

    public function getSuperReducedRate(): ?float;

    public function getReducedRateAlt(): ?float;
}
