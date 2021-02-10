<?php

namespace App\Components\Users\Team\Request\Mails;

use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;
use Throwable;

/**
 * Class Applied
 *
 * @package App\Components\Users\Team\Request\Mails
 */
class Applied extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $subject = '{name} has accepted the invitation to the {team}.';

    /**
     * @var string
     */
    protected string $template = 'emails.user.team.requests.applied';

    /**
     * @var TeamReadonlyContract
     */
    private TeamReadonlyContract $team;

    /**
     * @var RequestReadonlyContract
     */
    private RequestReadonlyContract $request;

    /**
     * @param TeamReadonlyContract $team
     * @param RequestReadonlyContract $request
     *
     * @throws InvalidArgumentException
     */
    public function __construct(TeamReadonlyContract $team, RequestReadonlyContract $request)
    {
        $this->team = $team;
        $this->request = $request;
        $this->subject = str_replace(
            ['{name}', '{team}'],
            [$request->user()->profileReadonly()->displayName(), $team->name()],
            $this->subject
        );
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
            ]
        );
    }
}
