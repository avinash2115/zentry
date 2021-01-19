<?php

namespace App\Components\Users\Team\Request\Mails;

/**
 * Class Rejected
 *
 * @package App\Components\Users\Team\Request\Mails
 * @see \App\Components\Users\Team\Request\Mails\Applied
 */
class Rejected extends Applied
{
    /**
     * @var string
     */
    public $subject = '{name} has rejected the invitation to the {team}.';

    /**
     * @var string
     */
    protected string $template = 'emails.user.team.requests.rejected';
}
