<?php

namespace App\Components\Sessions\Session\Note;

use InvalidArgumentException;

/**
 * Interface NoteContract
 *
 * @package App\Components\Sessions\Session\Note
 */
interface NoteContract extends NoteReadonlyContract
{
    /**
     * @param string $text
     *
     * @return NoteContract
     */
    public function changeText(string $text): NoteContract;

    /**
     * @param string|null $url
     *
     * @return NoteContract
     * @throws InvalidArgumentException
     */
    public function changeUrl(?string $url = null): NoteContract;
}
