<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 04.10.2017
 * Time: 10:47
 */

namespace Alpipego\Commerce;

use Alpipego\Commerce\Cache\RequestInterface;

class VatNumber implements VatNumberInterface
{
    const VIES_API = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService';
    const SOAP_ENVELOPE = '<s11:Envelope xmlns:s11="http://schemas.xmlsoap.org/soap/envelope/">
          <s11:Body>
            <tns1:checkVat xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types">
              <tns1:countryCode>%s</tns1:countryCode>
              <tns1:vatNumber>%s</tns1:vatNumber>
            </tns1:checkVat>
          </s11:Body>
        </s11:Envelope>';
    private $location;
    private $request;

    public function __construct(LocateVisitorInterface $location, RequestInterface $request)
    {
        $this->location = $location;
        $this->request  = $request;
    }

    public function verify(string $vatNumber): bool
    {
        try {
            $data   = $this->parse($vatNumber);
            $result = $this->request($data['vat_number'], $data['country_code']);

            return $result->valid;
        } catch (VatNumberException $e) {
        }

        return false;
    }

    private function parse(string $vatNumber): array
    {
        $alpha2code = $this->location->locate();
        $vatNumber  = str_replace($alpha2code, '', $vatNumber);
        preg_replace('/\h/', '', $vatNumber);
        $regex = sprintf('/^%s$/', self::REGEXEN[$alpha2code]);
        if (! preg_match($regex, $vatNumber)) {
            throw new VatNumberException('Invalid VAT Number');
        }

        return ['vat_number' => $vatNumber, 'country_code' => $alpha2code];
    }

    private function request(string $vatNumber, string $alpha2code): Models\VatNumber
    {
        $keys = [
            'countryCode',
            'vatNumber',
            'valid',
            'name',
            'address',
        ];
        $data = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: text/xml; charset=utf-8; SOAPAction: checkVatService",
                'content' => sprintf(self::SOAP_ENVELOPE, $alpha2code, $vatNumber),
                'timeout' => 10,
            ],
        ];

        return $this->request->soap(self::VIES_API, $data, $keys, 'checkVatResponse', 'vatNumber');
    }
}
