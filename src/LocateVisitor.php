<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 30.09.2017
 * Time: 09:26
 */
declare(strict_types=1);

namespace Alpipego\Commerce;

class LocateVisitor implements LocateVisitorInterface
{
    const COOKIE_NAME = 'visitor_country';
    private $services = [
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
        $this->locate();
    }

    public function locate(): string
    {
        return $this->country ?? $_COOKIE[self::COOKIE_NAME] ?? $this->getCountryCode();
    }

    private function getCountryCode(): string
    {
        $ip      = $this->getIpAddress();
        $country = null;

        array_walk($this->services, function (&$service) use ($ip) {
            $service = str_replace('{$ip}', $ip, $service);
        });

        while (count($this->services) && empty($country)) {
            $country = $this->makeRequest(array_shift($this->services));
        }
        $country = empty($country) ? $this->defaultCountry : $country;
        $this->setCookie($country);

        return $country;
    }

    private function getIpAddress()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = wp_unslash($_SERVER['HTTP_CLIENT_IP']);
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = wp_unslash($_SERVER['HTTP_X_FORWARDED']);
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = wp_unslash($_SERVER['HTTP_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = wp_unslash($_SERVER['HTTP_FORWARDED']);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = wp_unslash($_SERVER['REMOTE_ADDR']);
        } else {
            return '';
        }

        if (strpos($ip, ',') !== false) {
            $ips = explode(',', $ip);
            $ip  = trim($ips[0]);
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }

        $delimiter = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false ? '.' : ':';
        $ipArr     = explode($delimiter, $ip);
        array_pop($ipArr);

        return implode($delimiter, $ipArr) . '.0';
    }

    private function makeRequest(string $url): ?string
    {
        try {
            $response = \Requests::get($url);
            if (is_a($response, 'Requests_Response')) {
                try {
                    $r = json_decode($response->body);

                    return $r->country ?? $r->country_code ?? null;
                } catch (\Exception $e) {
                }
            }
        } catch (\Exception $e) {
            new \WP_Error($e->getCode(), $e->getMessage());
        }

        return null;
    }

    private function setCookie(string $country)
    {
        if ($host = ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? null)) {
            $hostArr = explode(':', $host);
            setcookie(self::COOKIE_NAME, $country, 0, '/', $hostArr[0], (bool)($_SERVER['HTTPS'] ?? true));
        }
    }

    public function setCountry(string $code)
    {
        $this->setCookie($code);

        return $this->country = $code;
    }
}
