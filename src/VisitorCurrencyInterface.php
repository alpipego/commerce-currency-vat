<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 11:21
 */

namespace Alpipego\Commerce;

interface VisitorCurrencyInterface
{
    public function setDefaultCurrency(string $currency);

    public function getCurrencyCode(): string;

    public function getExchangeRate(): float;

    public function getConvertedPrice(float $price): float;

    public function getCurrencyName(): string;

    public function getCurrencySymbol(): string;
}
