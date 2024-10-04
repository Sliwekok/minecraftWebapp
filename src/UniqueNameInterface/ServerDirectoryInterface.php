<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerDirectoryInterface
{
    /**
     *  here is directory structure for each server
     *
     * Public
     * |-> user directory
     *     |->minecraft
     *     |->backups
     * |-> user directory ...
     *
     * Cache (that holds temporary backups)
     * |->dev|prod
     *      |->minecraft
     *          |->temp name file
     */

    public const DIRECTORY = 'servers';

    public const DIRECTORY_MINECRAFT = 'minecraft';
    public const MINECRAFT_SERVER_FILE = 'server.jar';
    public const MINECRAFT_EULA = 'eula.txt';
    public const MINECRAFT_SERVERPROPERTIES = 'server.properties';
    public const DIRECTORY_BACKUPS = 'backups';
    public const FILE_USERCACHE = 'usercache.json';
    public const FILE_OPS = 'ops.json';
    public const FILE_WHITELIST = 'whitelist.json';
    public const FILE_BANNED_PLAYERS = 'banned-players.json';
    public const FILE_BANNED_IPS = 'banned-ips.json';
}
