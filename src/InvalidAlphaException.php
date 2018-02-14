<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 04.10.2017
 * Time: 10:13
 */

namespace Alpipego\Commerce;

class InvalidAlphaException extends \Exception
{
    protected $message = 'Please provide a valid ISO 3166-1 alpha-2 or ISO 3166-1 alpha-3 country code';
}
