<?php

namespace App\Assistants\Files\Http\Controllers\Traits;

use Kladislav\LaravelChunkUpload\Exceptions\UploadMissingFileException;
use Kladislav\LaravelChunkUpload\Receiver\FileReceiver;
use Kladislav\LaravelChunkUpload\Save\AbstractSave;

/**
 * Trait FileReceiverTrait
 *
 * @package App\Assistants\Files\Http\Controllers\Traits
 */
trait FileReceiverTrait
{
    /**
     * @param FileReceiver $receiver
     *
     * @return AbstractSave
     * @throws UploadMissingFileException
     */
    private function receive(FileReceiver $receiver): AbstractSave
    {
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if (!$save instanceof AbstractSave) {
            throw new UploadMissingFileException();
        }

        return $save;
    }
}
