<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerUnixCommandsInterface
{
    public const REPLACEMENT_RAM = '{ram}';
    public const REPLACEMENT_NAME = '{name}';
    public const REPLACEMENT_COMMAND = '{command}';
    public const REPLACEMENT_PATH = '{path}';
    public const REPLACEMENT_BACKUPPATH = '{back_path}';
    public const REPLACEMENT_LOG_PATH = '{log_path}';
    public const REPLACEMENT_LOG_FILENAME = '{log_file}';
    public const LOG_SUFFIX = '_console.log';

    public const SCREEN_CREATE = 'sudo screen -dmS '. self::REPLACEMENT_NAME . ' -L';
    public const SCREEN_ADDLOGGING = 'logfile '. self::REPLACEMENT_LOG_PATH. DIRECTORY_SEPARATOR. self::REPLACEMENT_LOG_FILENAME;
    public const SCREEN_SWITCH = 'sudo screen -S '. self::REPLACEMENT_NAME. " -X stuff '". self::REPLACEMENT_COMMAND. "\n'";
    public const SCREEN_SWTCH_WITHOUTSTUFF = 'sudo screen -S '. self::REPLACEMENT_NAME. " -X ". self::REPLACEMENT_COMMAND;
    public const SCREEN_GETSPECIFICPID = "sudo screen -ls | grep -w '". self::REPLACEMENT_NAME ."' | awk '{print $1}' | cut -d. -f1";
    public const SCREEN_CHANGEDIRECTORY = 'cd '. self::REPLACEMENT_PATH;
    public const RUN_SERVER = 'sudo java -Xmx'. self::REPLACEMENT_RAM. 'G -jar '. self::REPLACEMENT_PATH. '/server.jar --nogui';

    public const KILL_SERVER = 'sudo screen -S '. self::REPLACEMENT_NAME. ' -X quit';

    public const GET_RELATED_SCREEN_PID = "pgrep -f '". self::REPLACEMENT_NAME. "'";

    public const ARCHIVE_NAME = '{archiveName}';
    public const ARCHIVE_COMMAND = 'sudo zip '. self::ARCHIVE_NAME. ' '. self::REPLACEMENT_PATH . ' -r -x ' . self::REPLACEMENT_PATH. DIRECTORY_SEPARATOR. self::REPLACEMENT_NAME. self::LOG_SUFFIX. ' '. self::REPLACEMENT_PATH. DIRECTORY_SEPARATOR. 'screenlog.0; sudo mv '. self::ARCHIVE_NAME. ' '. self::REPLACEMENT_BACKUPPATH. DIRECTORY_SEPARATOR. self::ARCHIVE_NAME;
}
