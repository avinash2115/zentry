<?php

namespace App\Components\Sessions\Session\Stream;

/**
 * Interface StreamContract
 *
 * @package App\Components\Sessions\Session\Stream
 */
interface StreamContract extends StreamReadonlyContract
{
    /**
     * @param string $name
     *
     * @return StreamContract
     */
    public function changeName(string $name): StreamContract;

    /**
     * @param string $url
     *
     * @return StreamContract
     */
    public function changeUrl(string $url): StreamContract;

    /**
     * @param int $value
     *
     * @return StreamContract
     */
    public function convertProgressAdvance(int $value): StreamContract;
}
