<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 09:26
 */

namespace Alpipego\Commerce;

class LocateVisitor implements LocateVisitorInterface
{
    const COOKIE_NAME = 'visitor_country';
    private $services = [
        'https://ipinfo.io/{$ip}/json',
        'https://ipapi.co/{$ip}/json',
        'https://freegeoip.net/json/{$ip}',
    ];
    private $defaultCountry;
    private $country;

    public function setDefaultCountry(string $country): void
    {
        $this->defaultCountry = $country;
    }

    public function run()
    {
        add_action('shutdown', [$this, 'locate']);
    }

    public function locate(): string
    {
        return $this->country ?? $_COOKIE[self::COOKIE_NAME] ?? $this->getCountryCode();
    }

    public function getCountryCode(): string
    {
        $ip      = $this->getIpAddress();
        $country = null;

        array_walk($this->services, function (&$service) use ($ip) {
            $service = str_replace('{$ip}', $ip, $service);
        });

        while (count($this->services) && empty($country)) {
            try {
                $country = $this->makeRequest(array_shift($this->services));
            } catch (\Requests_Exception $e) {
            }
        }
        $country = empty($country) ? $this->defaultCountry : $country;
        $this->setCookie($country);

        return $country;
    }

    private function getIpAddress()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (empty($ip)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (empty($ip)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }

    private function makeRequest(string $url): ?string
    {
        $response = \Requests::get($url);

        if (is_a($response, 'Requests_Response')) {
            try {
                $r = json_decode($response->body);

                return $r->country ?? $r->country_code ?? null;
            } catch (\Exception $e) {
            }
        }

        return null;
    }

    private function setCookie(string $country)
    {
        $hostArr = explode(':', $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']);
        setcookie(self::COOKIE_NAME, $country, 0, '/', $hostArr[0], true);
    }

    public function setCountry(string $code)
    {
        $this->setCookie($code);

        return $this->country = $code;
    }
}
