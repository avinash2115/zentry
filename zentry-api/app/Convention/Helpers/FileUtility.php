<?php

namespace App\Convention\Helpers;

/**
 * Class FileUtility
 *
 * @package App\Convention\Helpers
 */
class FileUtility
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function sanitizePath(string $path): string
    {
        return trim(
            collect(explode(DIRECTORY_SEPARATOR, $path))->map(
                function (string $part) {
                    return self::sanitizeName($part);
                }
            )->implode(DIRECTORY_SEPARATOR)
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function sanitizeName(string $name): string
    {
        $replacement = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", ' ', $name);

        return $replacement === false ? $name : trim($replacement);
    }
}
