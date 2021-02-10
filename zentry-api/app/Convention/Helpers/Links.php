<?php

namespace App\Convention\Helpers;

use InvalidArgumentException;
use Throwable;

/**
 * Class Links
 *
 * @package App\Convention\Helpers
 */
class Links
{
    /**
     * @var string
     */
    private string $schema;

    /**
     * @var string
     */
    private string $domain;

    /**
     * @throws InvalidArgumentException|Throwable
     */
    public function __construct()
    {
        $this->setSchema(env('DOMAIN_SCHEMA', 'http://'));
        $this->setDomain(env('DOMAIN_BASE'));
    }

    /**
     * @return string
     */
    public function schema()
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     *
     * @return Links
     */
    public function setSchema(string $schema): Links
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Links
     * @throws InvalidArgumentException|Throwable
     */
    private function setDomain(string $domain): Links
    {
        throw_if(empty($domain), new InvalidArgumentException("Doamin cannot be empty"));

        throw_if(
            filter_var(substr($domain, 1), FILTER_VALIDATE_DOMAIN) !== substr($domain, 1),
            new InvalidArgumentException("Not a valid Level A domain passed: {$domain}")
        );

        $this->domain = $domain;

        return $this;
    }

    /**
     * @param string $uri
     * @param array  $placeholders
     * @param array  $placeholdersValues
     *
     * @return string
     * @throws InvalidArgumentException|Throwable
     */
    public function url(string $uri, array $placeholders = [], array $placeholdersValues = []): string
    {
        return $this->schema() . $this->domain() . $this->path($uri, $placeholders, $placeholdersValues);
    }

    /**
     * @param string $uri
     * @param array  $placeholders
     * @param array  $placeholdersValues
     *
     * @return string
     */
    public function path(string $uri, array $placeholders = [], array $placeholdersValues = []): string
    {
        return str_replace($placeholders, $placeholdersValues, $uri);
    }
}
