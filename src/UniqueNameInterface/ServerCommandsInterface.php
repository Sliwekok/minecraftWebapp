<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class ServerCommandsInterface
{
    public const RUN_REPLACEMENT_RAM = '{ram}';
    public const RUN_REPLACEMENT_PORT = '{port}';
    public const RUN_REPLACEMENT_PATH = '{path}';
    public const RUN_JAVA_SERVER = 'java -Xmx'.self::RUN_REPLACEMENT_RAM.'G -jar "'.self::RUN_REPLACEMENT_PATH.'server.jar" --port '.self::RUN_REPLACEMENT_PORT.' --nogui';
}