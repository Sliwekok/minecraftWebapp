<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerCommandsInterface
{
    public const REPLACEMENT_RAM = '{ram}';
    public const REPLACEMENT_NAME = '{name}';

    public const RUN_JAVA = 'start cmd /k "title server_'. self::REPLACEMENT_NAME.' & java -Xmx'. self::REPLACEMENT_RAM. 'G -jar server.jar --nogui"';
    public const STOP_JAVA = 'taskkill /fi "windowtitle eq server_'. self::REPLACEMENT_NAME. '*"';

    public const PROCESS_RUNNING = 'running';
    public const PROCESS_EXITCODE = 'exitcode';

    // this path is relative - minecraft directory is next to backups, so we give right path instantly
    public const ARCHIVE_NAME = '{archiveName}';
    public const ARCHIVE_COMMAND = 'tar.exe -a -c -f "../backups/'. self::ARCHIVE_NAME .'.zip" *';

}
