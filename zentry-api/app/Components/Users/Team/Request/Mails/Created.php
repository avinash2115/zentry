<?php

namespace App\Components\Users\Team\Request\Mails;

use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class Created
 *
 * @package App\Components\Users\Team\Request\Mails
 */
class Created extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $subject = 'You\'ve been invited to the team!';

    /**
     * @var string
     */
    protected string $template = 'emails.user.team.requests.created';

    /**
     * @var TeamReadonlyContract
     */
    private TeamReadonlyContract $team;

    /**
     * @var RequestReadonlyContract
     */
    private RequestReadonlyContract $request;

    /**
     * @var string
     */
    private string $link;

    /**
     * @param TeamReadonlyContract    $team
     * @param RequestReadonlyContract $request
     * @param string                  $link
     */
    public function __construct(TeamReadonlyContract $team, RequestReadonlyContract $request, string $link)
    {
        $this->team = $team;
        $this->request = $request;
        $this->link = $link;
    }

    /**
     * @return $this
     * @throws Throwable
     */
    public function build()
    {
        return $this->view($this->template)->with(
            [
                'team' => $this->team,
                'request' => $this->request,
                'link' => $this->link
            ]
        );
    }
}
