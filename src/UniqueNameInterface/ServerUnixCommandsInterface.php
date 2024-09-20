<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerUnixCommandsInterface
{
    public const REPLACEMENT_RAM = '{ram}';
    public const REPLACEMENT_NAME = '{name}';
    public const REPLACEMENT_PID = '{pid}';

    public const SCREEN_CREATE = 'screen -S '. self::REPLACEMENT_NAME. ';';
    public const SCREEN_SWITCH = 'screen -dr '. self::REPLACEMENT_NAME. ';';
//    public const SCREEN_GETSPECIFICPID = 'screen -ls | awk "/\.'. self::REPLACEMENT_NAME .'\t/ {print strtonum($1)}";';
    public const SCREEN_GETSPECIFICPID = "screen -ls | grep -w '". self::REPLACEMENT_NAME ."' | awk '{print $1}' | cut -d. -f1";
    public const SCREEN_GETCURRENTPID = 'echo $STY;';

    public const RUN_SERVER = 'java -Xmx'. self::REPLACEMENT_RAM. 'G -jar server.jar --nogui"';

    public const PROCESS_RUNNING = 'running';
    public const PROCESS_EXITCODE = 'exitcode';

    // move created archive to backups directory
    public const MOVE_ARCHIVE_COMMAND = 'mv '. self::ARCHIVE_NAME. '.zip ../backups/'. self::ARCHIVE_NAME. '.zip';

    // this path is relative - minecraft directory is next to backups, so we give right path instantly
    public const ARCHIVE_NAME = '{archiveName}';
    public const ARCHIVE_COMMAND = 'zip '. self::ARCHIVE_NAME. '.zip ./ -r;'. self::MOVE_ARCHIVE_COMMAND;

}
