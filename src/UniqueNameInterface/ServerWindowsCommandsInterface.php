<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerWindowsCommandsInterface
{
    public const REPLACEMENT_RAM = '{ram}';
    public const REPLACEMENT_NAME = '{name}';

    public const RUN_JAVA = 'start cmd /B /k "title server_'. self::REPLACEMENT_NAME.' & java -Xmx'. self::REPLACEMENT_RAM. 'G -jar server.jar --nogui"';
    public const STOP_JAVA = 'start cmd /B /k taskkill /fi "windowtitle eq server_'. self::REPLACEMENT_NAME. '*"';
//    public const STOP_JAVA = <<<kill
//for /f "tokens=2 delims=," %A in ('tasklist /v /fo csv ^| findstr /i "server_sliweks server"') do taskkill /PID %A /F
//kill
//    ;

    public const PROCESS_RUNNING = 'running';
    public const PROCESS_EXITCODE = 'exitcode';

    // this path is relative - minecraft directory is next to backups, so we give right path instantly
    public const ARCHIVE_NAME = '{archiveName}';
    public const ARCHIVE_COMMAND = 'tar.exe -a -c -f "../backups/'. self::ARCHIVE_NAME .'" *';

}
