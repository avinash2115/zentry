<?php

namespace App\Components\Share\ValueObjects;

use App\Convention\Contracts\Arrayable;
use Str;

/**
 * Class Payload
 *
 * @package App\Components\Share\ValueObjects
 */
final class Payload implements Arrayable
{
    /**
     * @var array
     */
    private array $parameters;

    /**
     * @var string
     */
    private string $pattern;

    /**
     * @var array
     */
    private array $methods;

    /**
     * @param string $pattern
     * @param array  $parameters
     * @param array  $methods
     */
    public function __construct(string $pattern, array $parameters, array $methods = ['GET'])
    {
        $this->pattern = $pattern;
        $this->parameters = $parameters;
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function pattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function methods(): array
    {
        return $this->methods;
    }

    /**
     * @param Payload $payload
     *
     * @return bool
     */
    public function equals(Payload $payload): bool
    {
        return $this->pattern() === $payload->pattern() && count(array_diff_assoc($this->parameters(), $payload->parameters())) === 0;
    }

    /**
     * @param Payload $payload
     *
     * @return bool
     */
    public function contains(Payload $payload): bool
    {
        return Str::startsWith($payload->pattern(), $this->pattern()) && count(array_diff_assoc($this->parameters(), $payload->parameters())) === 0;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'pattern' => $this->pattern,
            'parameters' => $this->parameters,
            'methods' => $this->methods,
        ];
    }
}
