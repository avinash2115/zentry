<?php

namespace App\Components\Sessions\Session\SOAP;

use InvalidArgumentException;

/**
 * Interface SOAPContract
 *
 * @package App\Components\Sessions\Session\SOAP
 */
interface SOAPContract extends SOAPReadonlyContract
{
    /**
     * @return SOAPContract
     */
    public function present(): SOAPContract;

    /**
     * @return SOAPContract
     */
    public function absent(): SOAPContract;

    /**
     * @param string $value
     *
     * @return SOAPContract
     * @throws InvalidArgumentException
     */
    public function changeRate(string $value): SOAPContract;

    /**
     * @param string $value
     *
     * @return SOAPContract
     */
    public function changeActivity(string $value): SOAPContract;

    /**
     * @param string $value
     *
     * @return SOAPContract
     */
    public function changeNote(string $value): SOAPContract;

    /**
     * @param string $value
     *
     * @return SOAPContract
     */
    public function changePlan(string $value): SOAPContract;
}
