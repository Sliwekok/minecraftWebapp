<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Exception\Server\CouldNotExecuteServerStopException;
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
     * first we stop all processes that are running in the screen
     * next we delete screen session itself
     * we can't just use screen -X quit due to java holding space (never ending process)
     */
    public function stopServer (
        Server $server
    ): void {
        $getPids = $this->getPids($server);
        $this->commandHelper->runCommand($getPids);
        $pids = explode("\n", $this->commandHelper->getReturnedValue());
        foreach ($pids as $pid) {
            if (posix_getpgid((int)$pid)) {
                if (!posix_kill((int) $pid, 0)) {
                    throw new CouldNotExecuteServerStopException();
                }
            }
        }

        $closeScreen = $this->getStopCommand($server);
        $this->commandHelper->runCommand($closeScreen);
    }

    /**
     * create server booting command to CLI
     * append to already existing screen by bypassing command
     */
    private function getStartupCommand (
        Server  $server,
        string  $path
    ): string {
        $screen = ServerUnixCommandsInterface::SCREEN_SWITCH;
        $screen = str_replace(ServerUnixCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), $screen);

        $java = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_RAM,
            (string)$server->getConfig()->getMaxRam(),
            ServerUnixCommandsInterface::RUN_SERVER
        );
        $java = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_PATH,
            $path,
            $java
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            $java,
            $screen
        );

        return $command;
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


    private function getPids (
        Server  $server
    ): string {
        $command = str_replace(ServerUnixCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), ServerUnixCommandsInterface::GET_RELATED_SCREEN_PID);

        return $command;
    }
}
