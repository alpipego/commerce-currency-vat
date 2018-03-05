<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 17.10.2017
 * Time: 10:51
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Models;

class VatNumber
{
    /**
     * @var string ISO 3166-1 alpha-2 Country Code
     */
    public $countryCode;
    /**
     * @var int
     */
    public $vatNumber;
    /**
     * @var bool
     */
    public $valid;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $address;
}
