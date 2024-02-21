<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Server;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ServerCommandsInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use App\UniqueNameInterface\ServerInterface;
use App\Exception\Server\CouldNotExecuteServerStartException;
use Doctrine\ORM\EntityManagerInterface;

class ServerCommanderService
{

    public function __construct (
        private EntityManagerInterface  $entityManager,
    )
    {}

    /**
     * start command via CommandLine for Minecraft Server. We create another CMD commandline with title based on Server name. The same way we kill process
     * TODO: upgrade server to linux to use [screen] command that allows to run multiple tasks with run-live access to process that allows us to send direct world commands and store more logs
     */
    public function startServer (
        Server $server
    ): Server {
        $path = (new FilesystemService($server->getDirectoryPath()))->getAbsolutePath(). '/'. ServerDirectoryInterface::DIRECTORY_MINECRAFT. '/';
        $command = $this->getStartupCommand($server);

        $process = proc_open($command, [], $pipes, $path);
        $processData = proc_get_status($process);
        // check if process run successfully
        if (!$processData[ServerCommandsInterface::PROCESS_RUNNING]) {
            throw new CouldNotExecuteServerStartException();
        }
        proc_close($process);

        return $this->saveStartServer($server);
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): Server {
        $command = $this->getStopCommand($server);

        $process = proc_open($command, [], $pipes);
        // check if process run successfully
        while (0 !== proc_get_status($process)[ServerCommandsInterface::PROCESS_EXITCODE]) {
            usleep(250);
        }
        proc_close($process);

        return $this->saveStopServer($server);
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

        $command = str_replace(ServerCommandsInterface::REPLACEMENT_RAM, (string)$ram, ServerCommandsInterface::RUN_JAVA);
        $command = str_replace(ServerCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), $command);

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
        $command = str_replace(ServerCommandsInterface::REPLACEMENT_NAME, (string)$server->getName(), ServerCommandsInterface::STOP_JAVA);

        return $command;
    }

    /**
     * save status to enitity
     */
    private function saveStartServer (
        Server  $server,
    ): Server {
        $server = $server
            ->setStatus(ServerInterface::STATUS_ONLINE)
        ;

        $this->entityManager->persist($server);
        $this->entityManager->flush();

        return $server;
    }

    /**
     * delete process PID from entity
     */
    private function saveStopServer (
        Server  $server,
    ): Server {
        $server = $server
            ->setStatus(ServerInterface::STATUS_OFFLINE)
        ;

        $this->entityManager->persist($server);
        $this->entityManager->flush();

        return $server;
    }

}
