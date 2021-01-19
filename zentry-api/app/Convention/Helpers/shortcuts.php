<?php

use App\Convention\Exceptions\Auth\UnauthorizedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Exceptions\Handler;
use Illuminate\Contracts\Container\BindingResolutionException;

if (!function_exists('denied')) {
    /**
     * @param string $message
     *
     * @return void
     * @throws PermissionDeniedException
     */
    function denied($message = 'Forbidden – you don’t have permission to access this page.'): void
    {
        throw new PermissionDeniedException($message, 403);
    }
}

if (!function_exists('unauthorized')) {
    /**
     * @param string $message
     *
     * @return void
     * @throws UnauthorizedException
     */
    function unauthorized($message = 'Unauthorized.'): void
    {
        throw new UnauthorizedException($message, 401);
    }
}

if (!function_exists('dateTimeFormatted')) {
    /**
     * @param DateTime|null $date
     *
     * @return string|null
     */
    function dateTimeFormatted(DateTime $date = null): ?string
    {
        if ($date instanceof DateTime) {
            return $date->format(config('app.date_time_format'));
        }

        return null;
    }
}

if (!function_exists('strEmpty')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function strEmpty(string $string): bool
    {
        return trim($string) === '';
    }
}

if (!function_exists('toUTC')) {
    /**
     * @param DateTime $date
     *
     * @return DateTime
     */
    function toUTC(DateTime $date): DateTime
    {
        if ($date->getTimezone()->getName() !== 'Z') {
            $date->setTimezone(new DateTimeZone('UTC'));
        }

        return $date;
    }
}

if (!function_exists('toISO8601')) {
    /**
     * @param DateTime $date
     *
     * @return string
     */
    function dateToISO8601(DateTime $date): string
    {
        return $date->format(\DateTimeInterface::ISO8601);
    }
}

if (!function_exists('report')) {
    /**
     * @param Throwable $exception
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    function report(Throwable $exception): void
    {
        app()->make(Handler::class)->reportSilent($exception);
    }
}

if (!function_exists('readableBytes')) {
    /**
     * @param int $bytes
     *
     * @return string
     */
    function readableBytes(int $bytes): string
    {
        switch (true) {
            case $bytes < 1024:
                return "{$bytes}B";
            case $bytes < 1048576:
                $rounded = round($bytes / 1024, 2);

                return "{$rounded}KB";
            default:
                $rounded = round($bytes / 1048576, 2);

                return "{$rounded}MB";
        }
    }
}
