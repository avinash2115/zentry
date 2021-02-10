<?php

namespace App\Components\Sessions\Services\SOAP;

use App\Components\Sessions\Session\SOAP\SOAPDTO;
use App\Components\Sessions\Session\SOAP\SOAPReadonlyContract;
use App\Components\Sessions\ValueObjects\SOAP\Payload;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface SOAPServiceContract
 *
 * @package App\Components\Sessions\Services\SOAP
 */
interface SOAPServiceContract extends IdentifiableContract
{
    /**
     * @param string $id
     *
     * @return SOAPServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): SOAPServiceContract;

    /**
     * @return SOAPReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SOAPReadonlyContract;

    /**
     * @return SOAPDTO
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function dto(): SOAPDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return SOAPServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(array $data): SOAPServiceContract;

    /**
     * @param Payload $payload
     *
     * @return SOAPServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     * @throws PropertyNotInit|RuntimeException|InvalidArgumentException
     */
    public function create(Payload $payload): SOAPServiceContract;

    /**
     * @return SOAPServiceContract
     * @throws BindingResolutionException|PropertyNotInit|NotFoundException|DeleteException
     */
    public function remove(): SOAPServiceContract;
}
