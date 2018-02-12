<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 11:23
 */

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;
use Alpipego\Commerce\Models\Currency;
use Alpipego\Commerce\Models\ExchangeRates;

class VisitorCurrency implements VisitorCurrencyInterface
{
    const EXCHANGE_RATE = 'https://api.fixer.io/latest';
    const CURRENCIES_API = 'https://gist.githubusercontent.com/Fluidbyte/2973986/raw/Common-Currency.json';
    private $country;
    private $defaultCurrency;
    private $currency;
    private $request;

    public function __construct(VisitorCountryInterface $country, RequestInterface $request)
    {
        $this->country = $country;
        $this->request = $request;
    }

    public function setDefaultCurrency(string $currency)
    {
        $currencies = $this->request->get(self::CURRENCIES_API, 'currencies', 3600 * 24 * 90);
        if (property_exists($currencies, $currency)) {
            $this->defaultCurrency = $currencies->$currency;
        }
    }

    public function getConvertedPrice(float $price): float
    {
        return $price * $this->getExchangeRate();
    }

    public function getExchangeRate(): float
    {
        $rates = $this->getAllExchangeRates();
        $code  = $this->getCurrencyCode();

        return $rates->rates[$code] ?? 1.00;
    }

    private function getAllExchangeRates(): ExchangeRates
    {
        return $this->request->get(
            self::EXCHANGE_RATE,
            'exchangeRates',
            strtotime("tomorrow 5:00 AM CET") - gmdate('U')
        );
    }

    public function getCurrencyCode(): string
    {
        return $this->getCurrency()->code;
    }

    private function getCurrency(): Currency
    {
        if (! empty($this->currency)) {
            return $this->currency;
        }

        $currencies = $this->country->getCountryCurrencies();
        /** @var Currency $currency */
        $currency = $currencies[0];
        if (count($currencies) > 1) {
            /** @var Currency $currency */
            foreach ($currencies as $currency) {
                if (is_string($currency->code) && strlen($currency->code) === 3) {
                    break;
                }
            }
        }

        return $this->currency = $currency;
    }

    public function getCurrencyName(): string
    {
        return $this->getCurrency()->name;
    }

    public function getCurrencySymbol(): string
    {
        return $this->getCurrency()->symbol;
    }
}
