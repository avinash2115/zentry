<?php

namespace App\Assistants\Files\Http\Controllers\Traits\Resumable;

use App\Assistants\Files\Exceptions\Resumable\IsNotInstantiableException;
use App\Assistants\Files\Services\Resumable\Validator;
use App\Convention\Exceptions\Repository\NotFoundException;
use Illuminate\Http\Response;

/**
 * Trait PartialTrait
 *
 * @package App\Assistants\Files\Http\Controllers\Traits\Resumable
 */
trait PartialTrait
{
    /**
     * @param Validator $validator
     *
     * @return Response
     * @throws NotFoundException
     * @throws IsNotInstantiableException
     */
    public function isPartUploaded(Validator $validator): Response
    {
        if (!$validator->isPartUploaded()) {
            throw new NotFoundException();
        }

        return $this->acknowledgeResponse();
    }
}
