<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerCommandsInterface
{
    public const REPLACEMENT_RAM = '{ram}';
    public const REPLACEMENT_PID = '{pid}';

    public const CMD_START = 'start';
    public const CMD_SERVER = 'server_';

    public const RUN_JAVA = 'java';
    public const RUN_JAVA_RAM = '-Xmx' . self::REPLACEMENT_RAM . 'G';
    public const RUN_JAVA_FILE = '-jar server.jar';
    public const RUN_JAVA_NOGUI = '--nogui';

    public const STOP_JAVA = 'taskkill';
    public const STOP_JAVA_PID = '/PID '. self::REPLACEMENT_PID;
    public const STOP_JAVA_FORCE = '/F';

    public const PROCESS_PID = 'pid';
    public const PROCESS_RUNNING = 'running';
    public const PROCESS_EXITCODE = 'exitcode';

}
