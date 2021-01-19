<?php

namespace App\Components\Users\Participant\Therapy;

use InvalidArgumentException;

/**
 * Interface TherapyContract
 *
 * @package App\Components\Users\Participant\Therapy
 */
interface TherapyContract extends TherapyReadonlyContract
{
    /**
     * @param string $value
     *
     * @return TherapyContract
     * @throws InvalidArgumentException
     */
    public function changeDiagnosis(string $value): TherapyContract;

    /**
     * @param string $value
     *
     * @return TherapyContract
     * @throws InvalidArgumentException
     */
    public function changeFrequency(string $value): TherapyContract;

    /**
     * @param string $value
     *
     * @return TherapyContract
     * @throws InvalidArgumentException
     */
    public function changeEligibility(string $value): TherapyContract;

    /**
     * @param int $value
     *
     * @return TherapyContract
     */
    public function changeSessionsAmountPlanned(int $value): TherapyContract;

    /**
     * @param int $value
     *
     * @return TherapyContract
     */
    public function changeTreatmentAmountPlanned(int $value): TherapyContract;

    /**
     * @param string $value
     *
     * @return TherapyContract
     */
    public function changeNotes(string $value): TherapyContract;

    /**
     * @param string $value
     *
     * @return TherapyContract
     */
    public function changePrivateNotes(string $value): TherapyContract;
}
