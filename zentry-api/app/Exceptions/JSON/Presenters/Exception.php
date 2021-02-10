<?php

namespace App\Exceptions\JSON\Presenters;

use App\Assistants\Files\Exceptions\Resumable\Part\NotFoundException as ResumableNotFoundException;
use App\Assistants\Files\Exceptions\Temporary\Url\NotFoundOrExpired;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Exceptions\Auth\UnauthorizedException;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use Doctrine\DBAL\DBALException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Class Exception
 *
 * @package App\Exceptions\JSON\Presenters
 */
class Exception
{
    use IdentifiableTrait;

    /**
     * @var int
     */
    private int $status = 500;

    /**
     * @var int
     */
    private int $code = 0;

    /**
     * @var string
     */
    private string $title = '';

    /**
     * @var string
     */
    private string $detail = '';

    /**
     * @var array
     */
    private array $source = [];

    /**
     * @var array
     */
    private array $meta = [];

    /**
     * @param Throwable   $exception
     * @param Request $request
     */
    public function __construct(Throwable $exception, Request $request)
    {
        $this->setIdentity(IdentityGenerator::next());

        $this->hydrateApplicationExceptions($exception);

        $this->setSource(
            [
                'parameters' => $request->query->all(),
                'body' => $request->request->all(),
            ]
        );
    }

    /**
     * @param Throwable $exception
     */
    private function hydrateApplicationExceptions(Throwable $exception): void
    {
        $this->setCode($exception->getCode());
        $this->setTitle($exception->getMessage());

        switch (true) {
            case $exception instanceof UnauthorizedException:
                $this->setStatus(401);
            break;
            case $exception instanceof HttpException:
                $this->setStatus($exception->getStatusCode());
                if (strEmpty($exception->getMessage())) {
                    $this->setTitle(
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                (str_replace('_http_exception', '', Str::snake(class_basename($exception))))
                            )
                        )
                    );
                }
            break;
            case $exception instanceof PermissionDeniedException:
                $this->setStatus(403);
            break;
            case $exception instanceof ResumableNotFoundException:
            case $exception instanceof NotFoundException:
            case $exception instanceof NotFoundOrExpired:
                $this->setStatus(404);
            break;
            case $exception instanceof FileNotFoundException:
                $this->setTitle('File not found.');
                $this->setStatus(404);
            break;
            case $exception instanceof DBALException:
                $this->setTitle('Database Error');
                $this->setDetail("Issue has been reported. Tracking number #{$this->identity()}");
            break;
            case $exception instanceof NotImplementedException:
                $this->setStatus(501);
                $this->setTitle('Server error.');
            break;
            case $exception instanceof PropertyNotInit:
                $this->setTitle('Server error.');
            break;
            default:
                $this->setDetail("Issue has been reported. Tracking number #{$this->identity()}");
            break;
        }

        $this->setMeta(
            [
                'trace' => explode("\n", $exception->getTraceAsString()),
            ]
        );
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'errors';
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Exception
     */
    public function setStatus(int $status): Exception
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return Exception
     */
    public function setCode(int $code): Exception
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Exception
     */
    public function setTitle(string $title): Exception
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function detail(): string
    {
        return $this->detail;
    }

    /**
     * @param string $detail
     *
     * @return Exception
     */
    public function setDetail($detail): Exception
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @return array
     */
    public function source(): array
    {
        return $this->source;
    }

    /**
     * @param array $source
     *
     * @return Exception
     */
    public function setSource(array $source): Exception
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return array
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     *
     * @return Exception
     */
    public function setMeta(array $meta): Exception
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return array|array[]
     */
    public function present(): array
    {
        return [
            'data' => [
                'id' => $this->identity()->toString(),
                'status' => $this->status(),
                'title' => $this->title(),
                'detail' => $this->detail(),
                'meta' => array_merge($this->meta(), ['source' => $this->source()]),
            ],
        ];
    }
}
