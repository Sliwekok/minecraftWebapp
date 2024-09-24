<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Exception\Server\ServerIsAlreadyRunningException;
use App\Service\Filesystem\FilesystemService;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
use App\Service\Server\Commander\UnixSessionService;

class LinuxCommanderService
{

    public function __construct (
        private RunCommandHelper    $commandHelper,
        private UnixSessionService  $unixSessionService,
    ) {}

    /**
     * creates separate screen that holds session
     */
    public function startServer (
        Server $server
    ): void {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
        $screenExists = $this->unixSessionService->checkScreenExists($server);
        if ($screenExists) {
            // show error to user about server that is already running
            throw new ServerIsAlreadyRunningException();
        }

        // create new session
        $this->unixSessionService->createNewSession($server, $path);
        // run server
        $command = $this->getStartupCommand($server, $path);
        $this->commandHelper->runCommand($command, $path);
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): void {
        $command = $this->getStopCommand($server);

        $this->commandHelper->runCommand($command);
    }

    /**
     * create server booting command to CLI
     * append to already existing screen by bypassing command
     */
    private function getStartupCommand (
        Server  $server,
        string  $path
    ): array {
        $screen = ServerUnixCommandsInterface::SCREEN_SWITCH;
        $screen = str_replace(ServerUnixCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), $screen);

        $changeDir = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            "cd ". $path,
            $screen
        );

        $java = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_RAM,
            (string)$server->getConfig()->getMaxRam(),
            ServerUnixCommandsInterface::RUN_SERVER
        );
        $java = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            $java,
            $screen
        );

        return [$changeDir, $java];
    }

    /**
     * create server killing command to CLI
     * creates new screen named after server name and there are all commands inserted
     */
    private function getStopCommand (
        Server $server
    ): string {
        $command = str_replace(ServerUnixCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), ServerUnixCommandsInterface::KILL_SERVER);

        return $command;
    }

}
