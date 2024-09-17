<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Service\Filesystem\FilesystemService;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ServerWindowsCommandsInterface;

class WindowsCommanderService
{
    /**
     * start command via CommandLine for Minecraft Server.
     * We create another CMD commandline with title based on Server name.
     * The same way we kill process
     */
    public function startServer (
        Server $server
    ): void {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
        $command = $this->getStartupCommand($server);
        (new RunCommandHelper)->runCommand($command, $path);
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): void {
        $command = $this->getStopCommand($server);

        (new RunCommandHelper)->runCommand($command);
    }

    /**
     * create server booting command to CLI
     *
     * final command looks like: start cmd /k "title server_XYZ & java --Xmx4G -jar server.jar --nogui"
     */
    private function getStartupCommand (
        Server  $server,
    ): string {
        $ram = $server->getConfig()->getMaxRam();

        $command = str_replace(ServerWindowsCommandsInterface::REPLACEMENT_RAM, (string)$ram, ServerWindowsCommandsInterface::RUN_JAVA);
        $command = str_replace(ServerWindowsCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), $command);

        return $command;
    }

    /**
     * create server killing command to CLI
     *
     * final command looks like: taskkill /fi "windowtitle eq server_XYZ*"
     * that '*' is important due to some magical characters somewhere are put - I don't know where it is added (php, cmd title or some other magical place) but it is required
     */
    private function getStopCommand (
        Server $server
    ): string {
        $command = str_replace(ServerWindowsCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), ServerWindowsCommandsInterface::STOP_JAVA);

        return $command;
    }



}
