<?php

namespace App\Components\Users\ValueObjects\CRM\Config;

use App\Convention\ValueObjects\Config\Config as ConventionConfig;

/**
 * Class Config
 *
 * @package App\Components\Users\ValueObjects\CRM\Config
 */
final class Config extends ConventionConfig
{
    /**
     * @var string
     */
    private string $driver;

    /**
     * @param string $driver
     * @param array $options
     * @param bool  $decryptBeforeEncrypt
     */
    public function __construct(array $options, string $driver = '', bool $decryptBeforeEncrypt = false)
    {
        parent::__construct($options, $decryptBeforeEncrypt);
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function driver(): string
    {
        return $this->driver;
    }

}
