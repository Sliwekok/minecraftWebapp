<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Config;
use App\Entity\Server;
use App\Exception\Server\CouldNotExecuteServerStart;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ServerCommandsInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Symfony\Component\Process\Process;

class ServerCommanderService
{

    private const MAX_TIMEOUT = 30;

    public function __construct (
    )
    {}

    public function startServer (
        Server $server
    ): void {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsolutePath(). '/'. ServerDirectoryInterface::DIRECTORY_MINECRAFT. '/';
        $command = $this->getStartupCommand($server->getConfig(), $path);
        chdir($path);
        ob_start();
        $exec = exec($command . " 2>&1");
        ob_end_clean();

        if (false === $exec) {
            throw new CouldNotExecuteServerStart();
        }
    }

    public function getStartupCommand (
        Config $config,
        string $path,
    ): string {
        $ram = $config->getMaxRam();
        $port = $config->getPort();

        $command = ServerCommandsInterface::RUN_JAVA_SERVER;
        $command = str_replace(ServerCommandsInterface::RUN_REPLACEMENT_RAM, (string)$ram, $command);
        $command = str_replace(ServerCommandsInterface::RUN_REPLACEMENT_PORT, (string)$port, $command);
        $command = str_replace(ServerCommandsInterface::RUN_REPLACEMENT_PATH, $path, $command);

        return $command;
    }

}