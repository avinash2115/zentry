<?php

namespace App\Assistants\Transformers\Contracts;

use Illuminate\Http\Request;

/**
 * Interface TransformerContract
 *
 * @package App\Assistants\Transformers\Contracts
 */
interface TransformerContract
{
    /**
     * @param Request $request
     *
     * @return Request
     */
    public function from(Request $request);

    /**
     * @param array $data
     *
     * @return string
     */
    public function to(array $data): string;
}