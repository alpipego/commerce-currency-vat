<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 09:24
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Models;

class Country
{
    /**
     * @var Currency[] $currencies
     */
    public $currencies;
    /**
     * @var string
     */
    public $alpha2code;
    /**
     * @var string
     */
    public $name;
}
