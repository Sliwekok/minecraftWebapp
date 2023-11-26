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
     */

    public const DIRECTORY = 'servers';

    public const DIRECTORY_MINECRAFT = 'minecraft';
    public const MINECRAFT_SERVER_FILE = 'server.jar';

    public const DIRECTORY_BACKUPS = 'backups';
}