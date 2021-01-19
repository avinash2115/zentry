<?php

namespace App\Components\Sessions\ValueObjects;

use App\Convention\Contracts\Arrayable;
use InvalidArgumentException;

/**
 * Class Geo
 *
 * @package App\Components\Sessions\ValueObjects
 */
final class Geo implements Arrayable
{
    /**
     * @var float
     */
    private float $lng;

    /**
     * @var float
     */
    private float $lat;

    /**
     * @var string
     */
    private string $place;

    /**
     * Geo constructor.
     *
     * @param float  $lng
     * @param float  $lat
     * @param string $place
     *
     * @throws InvalidArgumentException
     */
    public function __construct(float $lng, float $lat, string $place)
    {
        $this->lng = $lng;
        $this->lat = $lat;
        $this->setPlace($place);
    }

    /**
     * @param string $place
     *
     * @return Geo
     * @throws InvalidArgumentException
     */
    private function setPlace(string $place): Geo
    {
        if (strEmpty($place)) {
            throw new InvalidArgumentException('Place can\'t be empty');
        }

        $this->place = $place;

        return $this;
    }

    /**
     * @return float
     */
    public function lng(): float
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function lat(): float
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function place(): string
    {
        return $this->place;
    }

    /**
     * @param Geo $geo
     *
     * @return bool
     */
    public function equals(Geo $geo): bool
    {
        return $this->lng() === $geo->lng() && $this->lat() === $geo->lat() && $this->place() === $geo->place();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'lng' => $this->lng(),
            'lat' => $this->lat(),
            'place' => $this->place(),
        ];
    }
}