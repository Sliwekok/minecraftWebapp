<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerInterface
{
    public const ENTITY_ID = 'id';

    public const FORM_STEP1 = 'step_1';
    public const FORM_STEP1_NAME = 'name';
    public const FORM_STEP1_VERSION = 'version';
    public const FORM_STEP1_SEED = 'seed';
    public const FORM_STEP2 = 'step_2';
    public const FORM_STEP2_GAMETYPE = 'gameType';
    public const FORM_STEP3 = 'step_3';
    public const FORM_STEP3_WHITELIST = 'whitelist';
    public const FORM_STEP4 = 'step_4';

    public const STATUS = 'status';
    public const STATUS_OFFLINE = 'offline';
    public const STATUS_ONLINE = 'online';
    public const STATUS_SUSPENDED = 'suspended';

}