<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 11:06
 */

namespace Alpipego\Commerce\Models;

class VatPeriod
{
    /**
     * @var \DateTime
     */
    public $effective_from;

    /**
     * @var float[]
     */
    public $rates;
}
