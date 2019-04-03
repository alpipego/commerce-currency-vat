<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 04.10.2017
 * Time: 10:47
 */
declare(strict_types=1);

namespace Alpipego\Commerce;

use Alpipego\Commerce\Exception\VatNumberException;
use DragonBe\Vies\CheckVatResponse;
use DragonBe\Vies\Vies;

class VatNumber implements VatNumberInterface
{
    private $location;
    private $vies;

    public function __construct(LocateVisitorInterface $location, Vies $vies)
    {
        $this->location = $location;
        $this->vies     = $vies;
    }

    public function verify(string $vatNumber, string $country = ''): bool
    {
        try {
            $data   = $this->parse($vatNumber, (! empty($country) ? $country : $this->location->locate()));
            $result = $this->request($data['vat_number'], $data['country_code']);

            return $result->isValid();
        } catch (VatNumberException $e) {
        }

        return false;
    }

    private function parse(string $vatNumber, string $country = ''): array
    {
        $vatNumber  = strtoupper($vatNumber);
        $alpha2code = strtoupper($country);
        $vatNumber  = str_replace($alpha2code, '', $vatNumber);
        // greece can use either GR or EL as country code (ISO is GR, EU uses EL)
        if (in_array($alpha2code, ['GR', 'EL'], true)) {
            $vatNumber = preg_replace('/^GR|EL/', '', $vatNumber);
        }

        if ( ! array_key_exists($alpha2code, self::REGEXEN)) {
            throw new VatNumberException(sprintf('%s is not a VAT enabled country', $alpha2code));
        }

        preg_replace('/\h/', '', $vatNumber);
        $regex = sprintf('/^%s$/', self::REGEXEN[$alpha2code]);
        if ( ! preg_match($regex, $vatNumber)) {
            throw new VatNumberException(sprintf('%s is not a valid VAT ID number for %s', $vatNumber, $alpha2code));
        }

        return ['vat_number' => $vatNumber, 'country_code' => $alpha2code];
    }

    private function request(string $vatNumber, string $alpha2code): CheckVatResponse
    {
        if ( ! $this->vies->getHeartBeat()->isAlive()) {
            throw new VatNumberException('Can\'t connect to VIES system.');
        }

        try {
            return $this->vies->validateVat($alpha2code, $vatNumber);
        } catch (\Exception $e) {
            throw new VatNumberException($e->getMessage());
        }
    }
}
