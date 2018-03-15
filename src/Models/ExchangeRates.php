<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 10:22
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Models;

class ExchangeRates
{
    /**
     * @var string
     */
    public $base;
    /**
     * @var \DateTime
     */
    public $date;
    /**
     * @var float[]
     */
    public $rates;
}
