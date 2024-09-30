<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ConfigInterface
{

    public const ENTITY_SERVER = 'server';
    public const ENTITY_ID = 'id';
    public const ENTITY_PORT = 'port';
    public const ENTITY_DIFFICULTY = 'difficulty';
    public const ENTITY_ALLOWFLIGHT = 'allow_flight';
    public const ENTITY_PVP = 'pvp';
    public const ENTITY_HARDCORE = 'hardcore';
    public const ENTITY_MAXPLAYERS = 'max_players';
    public const ENTITY_WHITELIST = 'whitelist';
    public const ENTITY_SEED = 'seed';
    public const ENTITY_LEVELNAME = 'level_name';
    public const ENTITY_MAXRAM = 'max_ram';
    public const ENTITY_MOTD = 'motd';

    public const DIFFICULTY_PEACEFUL = 'peaceful';
    public const DIFFICULTY_EASY = 'easy';
    public const DIFFICULTY_NORMAL = 'normal';
    public const DIFFICULTY_HARD = 'hard';

    // this is to match entity (db) with property (config file)
    public const PROPERTY = 'PROPERTY_';
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
    public const PROPERTY_STATIC_IP = 'server-ip';
    public const PROPERTY_STATIC_SERVERPORT = 'server-port';
    public const PROPERTY_STATIC_QUERYPORT = 'query.port';

    public const EULA_AGREED = 'eula=true';
}
