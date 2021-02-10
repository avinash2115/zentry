<?php

namespace App\Components\Users\Jobs\Storage\Usage;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Flusher;

/**
 * Class Recalculate
 *
 * @package App\Components\Users\Jobs\Storage\Usage
 */
class Recalculate extends Job
{
    use LinkParametersTrait;
    use FileServiceTrait;
    use UserServiceTrait;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @param Identity $userIdentity
     */
    public function __construct(Identity $userIdentity)
    {
        $this->userIdentity = $userIdentity;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        Flusher::open();

        $enabledStorage = $this->userService__()->workWith($this->userIdentity)->readonly()->enabledStorage();

        if (!$enabledStorage->isDriver(
            StorageReadonlyContract::DRIVER_DEFAULT
        )) {
            $this->userService__()->storageService()->workWithDriver(StorageReadonlyContract::DRIVER_DEFAULT)->change(
                [
                    'used' => 0,
                ]
            );
        } else {
            $this->userService__()->storageService()->workWith($enabledStorage->identity())->sync(
                $this->userService__()->readonly()->fileNamespace()
            );
        }

        Flusher::flush();
        Flusher::commit();
    }

    /**
     * @param string $path
     *
     * @return int
     * @throws Exception
     */
    private function size(string $path): int
    {
        try {
            return $this->fileService__()->metadata($path)->size();
        } catch (Exception $exception) {
            report($exception);
        }

        return 0;
    }
}
