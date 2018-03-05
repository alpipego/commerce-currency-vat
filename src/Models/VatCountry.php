<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 11:06
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Models;

class VatCountry
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $country_code;

    /**
     * @var VatPeriod[]
     */
    public $periods;

    public function getEffectiveRates(): array
    {
        if (count($this->periods) > 1) {
            usort($this->periods, [$this, 'sortRates']);
        }

        foreach ($this->periods as $period) {
            if (new \DateTime() > $period->effective_from) {
                return $period->rates;
            }
        }

        return [];
    }

    private function sortRates(VatPeriod $period1, VatPeriod $period2)
    {
        return $period2->effective_from <=> $period1->effective_from;
    }
}
