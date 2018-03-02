<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 09:15
 */
declare(strict_types=1);

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;
use Alpipego\Commerce\Models\VatCountry;
use Alpipego\Commerce\Models\VatRates;

final class Vat implements VatInterface
{
    const VAT_API = 'https://jsonvat.com/';
    private $request;
    private $location;
    private $fallbackRates = [
        'standard'      => 0,
        'reduced1'      => 0,
        'reduced2'      => 0,
        'super_reduced' => 0,
    ];
    private $rates;
    private $country;

    public function __construct(RequestInterface $request, LocateVisitorInterface $location)
    {
        $this->request  = $request;
        $this->location = $location;
        $this->country  = $location->locate();
    }

    public function run()
    {
//        add_action('wp_footer', function () {
//            echo '<code><pre>';
//            var_dump($this->getRates());
//            echo '</pre></code>';
//        });
    }

    public function getStandardRate(string $isoCode = ''): float
    {
        return $this->getRatesByCountry($isoCode)['standard'];
    }

    private function getRatesByCountry(string $isoCode = ''): array
    {
        if (! empty($this->rates) && $isoCode === $this->country) {
            return $this->rates;
        }

        if (! empty($isoCode) && $isoCode !== $this->country) {
            $this->country = $this->location->setCountry($isoCode);
        }

        /** @var VatCountry $country */
        foreach ($this->getRates() as $country) {
            if (in_array($this->country, [$country->country_code, $country->code], true)) {
                return $this->rates = array_merge($this->fallbackRates, $country->getEffectiveRates());
            }
        }

        return $this->fallbackRates;
    }

    private function getRates(): array
    {
        $overrides = apply_filters('ccv/vat/overrides', []);
        $overrides = is_array($overrides) ? $overrides : [];

        return array_merge($this->getAPIRates()->rates, $overrides);
    }

    private function getAPIRates(): VatRates
    {
        return $this->request->get(self::VAT_API, 'vatRates', 3600 * 24 * 30);
    }

    public function getReducedRate(string $isoCode = ''): ?float
    {
        return $this->getRatesByCountry($isoCode)['reduced1'];
    }

    public function getSuperReducedRate(string $isoCode = ''): ?float
    {
        return $this->getRatesByCountry($isoCode)['super_reduced'];
    }

    public function getReducedRateAlt(string $isoCode = ''): ?float
    {
        return $this->getRatesByCountry($isoCode)['reduced2'];
    }
}
