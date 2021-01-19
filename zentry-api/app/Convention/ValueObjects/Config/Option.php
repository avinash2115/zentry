<?php

namespace App\Convention\ValueObjects\Config;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;

/**
 * Class Option
 *
 * @package App\Convention\ValueObjects\Config
 */
final class Option implements Arrayable
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $value;

    /**
     * @var bool
     */
    private bool $encryption;

    /**
     * @var bool
     */
    private bool $hidden;

    /**
     * Option constructor.
     *
     * @param string $type
     * @param string $title
     * @param string $value
     * @param bool   $encryption
     * @param bool   $hidden
     *
     * @throws InvalidArgumentException
     * @throws EncryptException
     */
    public function __construct(string $type, string $title, string $value, bool $encryption = false, bool $hidden = false)
    {
        $this->setType($type)->setTitle($title)->setEncryption($encryption)->setValue($value)->setHidden($hidden);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type() === $type;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function encryption(): bool
    {
        return $this->encryption;
    }


    /**
     * @return bool
     */
    public function hidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'value' => $this->value,
            'encryption' => $this->encryption,
            'hidden' => $this->hidden,
        ];
    }

    /**
     * @return array
     * @throws DecryptException
     */
    public function toArrayDecrypted(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'value' => $this->encryption() ? Crypt::decryptString($this->value()) : $this->value(),
            'encryption' => $this->encryption,
            'hidden' => $this->hidden,
        ];
    }

    /**
     * @param Option $parameter
     *
     * @return bool
     * @throws DecryptException
     */
    public function equals(Option $parameter): bool
    {
        return $this->type() === $parameter->type() && $this->title() === $parameter->title() && $this->encryption() === $parameter->encryption() && $this->hidden() === $parameter->hidden() && $this->equalsValue($parameter->value());
    }

    /**
     * @param bool $encryption
     *
     * @return Option
     */
    private function setEncryption(bool $encryption): Option
    {
        $this->encryption = $encryption;

        return $this;
    }

    /**
     * @param bool $hidden
     *
     * @return Option
     */
    private function setHidden(bool $hidden): Option
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return Option
     * @throws EncryptException
     */
    private function setValue(string $value): Option
    {
        $this->value = $this->encryption() ? Crypt::encryptString($value) : $value;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return Option
     * @throws InvalidArgumentException
     */
    private function setTitle(string $title): Option
    {
        if (strEmpty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        $this->title = $title;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Option
     * @throws InvalidArgumentException
     */
    private function setType(string $type): Option
    {
        if (strEmpty($type)) {
            throw new InvalidArgumentException('Type cannot be empty');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return bool
     * @throws DecryptException
     */
    private function equalsValue(string $value): bool
    {
        return $this->encryption() ? Crypt::decryptString($this->value()) === Crypt::decryptString($value) : $this->value() === $value;
    }
}
