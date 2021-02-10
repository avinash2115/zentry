<?php

namespace App\Components\CRM\Contracts;

use App\Convention\Entities\Contracts\IdentifiableContract;
use Illuminate\Support\Collection;

/**
 * Interface CRMImportableContract
 *
 * @package App\Components\CRM\Contracts
 */
interface CRMImportableContract extends IdentifiableContract
{
    public const CRM_ALIAS_PREFIX = 'crm_source_entity:';

    public const CRM_ENTITY_TYPE_TEAM = 'team';

    public const CRM_ENTITY_TYPE_SERVICE = 'service';

    public const CRM_ENTITY_TYPE_PROVIDER = 'provider';

    public const CRM_ENTITY_TYPE_PARTICIPANT = 'participant';

    public const CRM_ENTITY_TYPE_PARTICIPANT_GOAL = 'participant_goal';

    public const CRM_ENTITY_TYPE_PARTICIPANT_IEP = 'participant_iep';

    public const CRM_ENTITY_TYPE_SCHOOL = 'school';

    public const CRM_ENTITY_TYPE_SESSION = 'session';

    public const CRM_ENTITY_TYPES_AVAILABLE = [
        self::CRM_ENTITY_TYPE_TEAM,
        self::CRM_ENTITY_TYPE_PARTICIPANT,
        self::CRM_ENTITY_TYPE_PARTICIPANT_GOAL,
        self::CRM_ENTITY_TYPE_PARTICIPANT_IEP,
        self::CRM_ENTITY_TYPE_SCHOOL,
        self::CRM_ENTITY_TYPE_SESSION,
        self::CRM_ENTITY_TYPE_SERVICE,
        self::CRM_ENTITY_TYPE_PROVIDER,
    ];

    /**
     * @return string
     */
    public function sourceEntityClass(): string;

    /**
     * @return string
     */
    public static function crmEntityType(): string;

    /**
     * @return Collection
     */
    public function sources(): Collection;
}
