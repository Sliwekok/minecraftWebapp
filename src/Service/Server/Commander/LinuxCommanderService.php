<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Alert;
use App\Entity\Server;
use App\Exception\Server\CouldNotExecuteServerStartException;
use App\Exception\Server\ServerIsAlreadyRunningException;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ServerInterface;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
use App\UniqueNameInterface\ServerWindowsCommandsInterface;
use Doctrine\ORM\EntityManagerInterface;

class LinuxCommanderService
{
    /**
     * creates separate screen that holds session
     */
    public function startServer (
        Server $server
    ): void {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
        $screenExists = UnixSessionService::checkScreenExists($server);
        if ($screenExists) {
            // show error to user about server that is already running
            Alert::error(
                (new ServerIsAlreadyRunningException())->getMessage(),
            );
        }

        $pid = UnixSessionService::createNewSession($server);

    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): void {
        $command = $this->getStopCommand($server);

        $process = proc_open($command, [], $pipes);
        // check if process run successfully
        while (0 !== proc_get_status($process)[ServerWindowsCommandsInterface::PROCESS_EXITCODE]) {
            usleep(250);
        }
        proc_close($process);
    }

    /**
     * create server booting command to CLI
     * creates new screen with commands
     */
    private function getStartupCommand (
        Server  $server,
    ): string {
        $ram = $server->getConfig()->getMaxRam();
        $serverName = (string)$server->getName();
        $nameReplace = ServerUnixCommandsInterface::REPLACEMENT_NAME;

//        $screenCreate = str_replace($nameReplace, $serverName, ServerUnixCommandsInterface::SCREEN_CREATE);
//        $screenGetPid = str_replace($nameReplace, $serverName, ServerUnixCommandsInterface::SCREEN_GETPID);
//
//        return $command
        return '';
    }

    /**
     * create server killing command to CLI
     * creates new screen named after server name and there are all commands inserted
     */
    private function getStopCommand (
        Server $server
    ): string {
        $command = str_replace(ServerWindowsCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), ServerWindowsCommandsInterface::STOP_JAVA);

        return $command;
    }

}
