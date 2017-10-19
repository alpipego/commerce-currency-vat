<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 16:25
 */

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;
use Alpipego\Commerce\Models\Country;

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
                throw new InvalidAlphaException('Please provide a valid ISO 3166-1 alpha-2 or ISO 3166-1 alpha-3 country code');
            }
            $this->location->setCountry($country->alpha2code);
        }
    }

    public function getCountry(string $code = null): Country
    {
        $url = str_replace('{$country}', $code ?? $this->location->locate(), self::COUNTRY_API);

        return $this->request->get($url, 'country', 3600 * 24 * 30);
    }
}
