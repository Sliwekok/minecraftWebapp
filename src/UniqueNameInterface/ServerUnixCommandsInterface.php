<?php

    declare(strict_types=1);

    namespace App\UniqueNameInterface;

    class ServerUnixCommandsInterface
    {
        public const REPLACEMENT_RAM = '{ram}';
        public const REPLACEMENT_NAME = '{name}';

        public const SCREEN_CREATE = 'sudo screen -dmS '. self::REPLACEMENT_NAME;
        public const SCREEN_SWITCH = 'sudo screen -dr '. self::REPLACEMENT_NAME;
        public const SCREEN_GETSPECIFICPID = "sudo screen -ls | grep -w '". self::REPLACEMENT_NAME ."' | awk '{print $1}' | cut -d. -f1";
        public const SCREEN_GETCURRENTPID = 'echo $STY';

        public const RUN_SERVER = 'sudo java -Xmx'. self::REPLACEMENT_RAM. 'G -jar server.jar --nogui"';

        public const KILL_SERVER = 'sudo screen -S '. self::REPLACEMENT_NAME. ' -X quit';

        // move created archive to backups directory
        public const MOVE_ARCHIVE_COMMAND = 'mv '. self::ARCHIVE_NAME. '.zip ../backups/'. self::ARCHIVE_NAME. '.zip';

        // this path is relative - minecraft directory is next to backups, so we give right path instantly
        public const ARCHIVE_NAME = '{archiveName}';
        public const ARCHIVE_COMMAND = 'zip '. self::ARCHIVE_NAME. '.zip ./ -r;'. self::MOVE_ARCHIVE_COMMAND;

    }
