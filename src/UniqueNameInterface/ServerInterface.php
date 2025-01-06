<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerInterface
{
    public const ENTITY_ID = 'id';
    public const ENTITY_CREATEDAT = 'created_at';
    public const ENTITY_MODIFIEDAT = 'modified_at';
    public const ENTITY_PID = 'pid';
    public const ENTITY_STATUS = 'status';

    public const ENTITY_PASCAL_CREATEDTA = 'createAt';
    public const ENTITY_PASCAL_MODIFIEDAT = 'modifiedAt';

    public const FORM_STEP1 = 'step_1';
    public const FORM_STEP1_NAME = 'name';
    public const FORM_STEP1_VERSION = 'version';
    public const FORM_STEP1_SEED = 'seed';
    public const FORM_STEP2 = 'step_2';
    public const FORM_STEP2_GAMETYPE = 'gameType';
    public const FORM_STEP2_GAMETYPE_VANILLA = 'vanilla';
    public const FORM_STEP2_GAMETYPE_FABRIC = 'fabric';
    public const FORM_STEP2_GAMETYPE_FORGE = 'forge';
    public const FORM_STEP3 = 'step_3';
    public const FORM_STEP3_WHITELIST = 'whitelist';
    public const FORM_STEP4 = 'step_4';

    public const STATUS = 'status';
    public const STATUS_OFFLINE = 'offline';
    public const STATUS_ONLINE = 'online';
    public const STATUS_SUSPENDED = 'suspended';

    public const OS_WINDOWS = 'WIN';

    public const REPLACE_MINECRAFT_VERSION = '{minecraft}';
    public const FABRIC_URL = 'https://meta.fabricmc.net/v2/versions/loader/'. self::REPLACE_MINECRAFT_VERSION . '/0.16.5/1.0.1/server/jar';
    public const FORGE_URL = 'https://files.minecraftforge.net/net/minecraftforge/forge/index_' . self::REPLACE_MINECRAFT_VERSION . '.html';

}
