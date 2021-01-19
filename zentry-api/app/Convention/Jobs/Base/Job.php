<?php

namespace App\Convention\Jobs\Base;

use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use InvalidArgumentException;
use Log;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Class JobBase
 *
 * @package App\Convention\Jobs\Base
 */
abstract class Job implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;

    /**
     * @throws BindingResolutionException
     * @throws DeleteException
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws ReadException
     * @throws UnexpectedValueException
     * @throws UploadException
     * @throws NoResultException
     * @throws RuntimeException
     */
    abstract protected function _handle(): void;

    /**
     * @throws BindingResolutionException
     * @throws DeleteException
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws ReadException
     * @throws UnexpectedValueException
     * @throws UploadException
     * @throws NoResultException
     * @throws RuntimeException
     */
    public function handle(): void
    {
        $this->_handle();
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        Log::error(
            "[Job Processing Error]. Message: {$exception->getMessage()} Trace: {$exception->getTraceAsString()}"
        );
    }
}
