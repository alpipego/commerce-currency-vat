<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 01.10.2017
 * Time: 09:57
 */
declare(strict_types=1);

namespace Alpipego\Commerce\Models;

class Mapper
{
    private $mapper;

    public function __construct(\JsonMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function map($json, string $class)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($class);
        if (! class_exists($class)) {
            // TODO throw appropriate Exception
            throw new \Exception('Please provide an existing model');
        }
        if (! $json instanceof \stdClass) {
            $json = json_decode($json);
        }

        return $this->mapper->map($json, (new $class));
    }
}
