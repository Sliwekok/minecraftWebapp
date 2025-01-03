<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Alert;
use App\Entity\Server;
use App\Exception\Server\CouldNotExecuteServerStopException;
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

    public function checkServerStatus(Server $server): bool {
        return $this->unixSessionService->checkScreenExists($server);
    }

    /**
     * creates separate screen that holds session
     */
    public function startServer (
        Server $server
    ): void {
        $fs = new FilesystemService($server->getDirectoryPath());
        $path = $fs->getAbsoluteMinecraftPath();
        $screenExists = $this->unixSessionService->checkScreenExists($server);
        if ($screenExists) {
            // show error to user about server that is already running
            Alert::warning('Server is already running. Refresh page');

            return;
        }

        // clear log file each time the server is booting up
        $fs->dumpFile($server->getName(). ServerUnixCommandsInterface::LOG_SUFFIX, '');

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
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );

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

    public function installForgeServer (
        Server $server
    ): void {
        $command = $this->getInstallForgeInstallCommand($server);

        $this->commandHelper->runCommand($command);
    }

    private function getInstallForgeInstallCommand (
        Server  $server
    ): string {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );

        $java = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_RAM,
            (string)$server->getConfig()->getMaxRam(),
            ServerUnixCommandsInterface::SERVER_INSTALL_FORGE
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

    public function getServerUsage (
        Server  $server
    ): array {
        $pids = $this->getPids($server);
        $this->commandHelper->runCommand($pids);
        $pids = explode("\n", $this->commandHelper->getReturnedValue());
        $usage = [
            'cpu' => (string)0.0,
            'memory' => (string)0.0,
        ];
        foreach ($pids as $pid) {
            if (strlen($pid) == 0) continue;
            $command = str_replace(
                ServerUnixCommandsInterface::REPLACEMENT_PID,
                $pid,
                ServerUnixCommandsInterface::SERVER_USAGE
            );
            $this->commandHelper->runCommand($command);
            $output = $this->commandHelper->getReturnedValue();
            if ($output !== '') {
                $output = preg_split('/\s+/', trim($output));
                // check if process is java-type since we only care about server
                // table of content for top:
                // 75 - command type - we're looking for java
                // 84 - cpu usage
                // 85 - memory usage

                // check if keys exist
                if (array_key_exists(85, $output) && array_key_exists(84, $output)) {
                    if ((float)$usage['cpu'] <= (float)$output[84]) {
                        $usage['cpu'] = (string) $output[84];
                    }
                    if ((float)$usage['memory'] <= (float)$output[85]) {
                        $usage['memory'] = (string) $output[85];
                    }
                }
            }
        }

        $usage['time'] =  date('Y-m-d H:i:s');

        return $usage;
    }

    public function getServerUsageFile (
        Server  $server
    ): mixed {
        $fs = new FilesystemService($server->getDirectoryPath());

        return $fs->getServerUsageFile();
    }
}
