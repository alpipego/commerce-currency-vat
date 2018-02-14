<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 09:15
 */

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;
use Alpipego\Commerce\Models\VatCountry;
use Alpipego\Commerce\Models\VatRates;

final class Vat implements VatInterface
{
    const VAT_API = 'https://jsonvat.com/';
    private $request;
    private $country;
    private $fallbackRates = [
        'standard'      => 0,
        'reduced1'      => 0,
        'reduced2'      => 0,
        'super_reduced' => 0,
    ];
    private $rates;

    public function __construct(RequestInterface $request, LocateVisitorInterface $location)
    {
        $this->request = $request;
        $this->country = $location->locate();
    }

    public function run()
    {
//        add_action('wp_footer', function () {
//            echo '<code><pre>';
//            var_dump($this->getRates());
//            echo '</pre></code>';
//        });
    }

    private function getRates() : array
    {
        $overrides = apply_filters('ccv/vat/overrides', []);
        $overrides = is_array($overrides) ? $overrides : [];

        return array_merge($this->getAPIRates()->rates, $overrides);
    }

    private function getAPIRates(): VatRates
    {
        return $this->request->get(self::VAT_API, 'vatRates', 3600 * 24 * 30);
    }

    public function getStandardRate(): float
    {
        return $this->getRatesByCountry()['standard'];
    }

    private function getRatesByCountry(): array
    {
        if (! empty($this->rates)) {
            return $this->rates;
        }
        /** @var VatCountry $country */
        foreach ($this->getRates() as $country) {
            if (in_array($this->country, [$country->country_code, $country->code], true)) {
                return $this->rates = array_merge($this->fallbackRates, $country->getEffectiveRates());
            }
        }

        return $this->fallbackRates;
    }

    public function getReducedRate(): ?float
    {
        return $this->getRatesByCountry()['reduced1'];
    }

    public function getSuperReducedRate(): ?float
    {
        return $this->getRatesByCountry()['super_reduced'];
    }

    public function getReducedRateAlt(): ?float
    {
        return $this->getRatesByCountry()['reduced2'];
    }
}
