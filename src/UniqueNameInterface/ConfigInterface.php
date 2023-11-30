<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ConfigInterface
{
    public const ENTITY_PORT = 'port';

    public const DIFFICULTY = 'difficulty';
    public const DIFFICULTY_PEACEFUL = 'peaceful';
    public const DIFFICULTY_EASY = 'easy';
    public const DIFFICULTY_NORMAL = 'normal';
    public const DIFFICULTY_HARD = 'hard';

    public const PROPERTY_DIFFICULTY = 'difficulty';
    public const PROPERTY_ALLOWFLIGHT = 'allow-flight';
    public const PROPERTY_PVP = 'pvp';
    public const PROPERTY_HARDCORE = 'hardcore';
    public const PROPERTY_MAXPLAYERS = 'max-players';
    public const PROPERTY_WHITELIST = 'white-list';
    public const PROPERTY_ENFORCEWHITELIST = 'enforce-whitelist';
    public const PROPERTY_SEED = 'level-seed';
    public const PROPERTY_LEVELNAME = 'level-name';
    public const PROPERTY_MOTD = 'motd';
    public const PROPERTY_PORT = 'server-port';
    public const PROPERTY_MAXRAM = 'max-ram';

    public const EULA_AGREED = 'eula=true';
}