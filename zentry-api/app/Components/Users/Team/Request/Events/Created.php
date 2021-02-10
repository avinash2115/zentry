<?php

namespace App\Components\Users\Team\Request\Events;

use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

/**
 * Class Created
 */
class Created
{
    use SerializesModels;

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
    public function __construct(TeamReadonlyContract $team, RequestReadonlyContract $request, string $link = '#')
    {
        $this->team = $team;
        $this->request = $request;
        $this->link = $link;
    }

    /**
     * @return TeamReadonlyContract
     * @throws PropertyNotInit
     */
    public function team(): TeamReadonlyContract
    {
        if (!$this->team instanceof TeamReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->team;
    }

    /**
     * @return RequestReadonlyContract
     * @throws PropertyNotInit
     */
    public function request(): RequestReadonlyContract
    {
        if (!$this->request instanceof RequestReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->request;
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function link(): string
    {
        if (!filter_var($this->link, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Link is not valid');
        }

        return $this->link;
    }
}
