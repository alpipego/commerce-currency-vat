<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 16:25
 */
declare(strict_types=1);

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;
use Alpipego\Commerce\Models\Country;
use Alpipego\Commerce\Exception\InvalidAlphaException;

class VisitorCountry implements VisitorCountryInterface
{
    const COUNTRY_API = 'https://restcountries.eu/rest/v2/alpha/{$country}';
    private $location;
    private $request;

    public function __construct(LocateVisitorInterface $location, RequestInterface $request)
    {
        $this->location = $location;
        $this->request  = $request;
    }

    public function setCountry(string $code)
    {
        if ($this->location->locate() !== $code) {
            $country = $this->getCountry($code);
            if (is_null($country->alpha2code)) {
                throw new InvalidAlphaException();
            }
            $this->location->setCountry($country->alpha2code);
        }
    }

    private function getCountry(string $code = null): Country
    {
        $url = str_replace('{$country}', $code ?? $this->location->locate(), self::COUNTRY_API);

        return $this->request->get($url, 'country', 3600 * 24 * 30);
    }

    public function getCountryName(): string
    {
        return $this->getCountry()->name;
    }

    public function getCountryCode(): string
    {
        return $this->getCountry()->alpha2code;
    }

    public function getCountryCurrencies(): array
    {
        return $this->getCountry()->currencies;
    }
}
