<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Config;
use App\Entity\Server;
use App\Exception\Server\CouldNotExecuteServerStopException;
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
     * start command via CommandLine for Minecraft Server. Once it's run we get PID of process - that's the way it's gonna be eventually killed.
     * TODO: upgrade server to linux to use [screen] command that allows to run multiple tasks with run-live access to process
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
        $pid = $processData[ServerCommandsInterface::PROCESS_PID];
        proc_close($process);

        return $this->saveStartServer($server, $pid);
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): Server {
        $pid = $server->getPid();
        $command = $this->getStopCommand($pid);

        $process = proc_open($command, [], $pipes);
        $processData = proc_get_status($process);
        // check if process run successfully
        if (0 !== $processData[ServerCommandsInterface::PROCESS_EXITCODE]) {
            throw new CouldNotExecuteServerStopException();
        }
        proc_close($process);

        return $this->saveStopServer($server);
    }

    /**
     * create server booting command to CLI
     */
    private function getStartupCommand (
        Server  $server,
    ): string {
        $ram = $server->getConfig()->getMaxRam();

        $cmd = ServerCommandsInterface::CMD_START. '"'. ServerCommandsInterface::CMD_SERVER. $server->getId() .'" ';

        $java = ServerCommandsInterface::RUN_JAVA;
        $java .= ' '. str_replace(ServerCommandsInterface::REPLACEMENT_RAM, (string)$ram, ServerCommandsInterface::RUN_JAVA_RAM);
        $java .= ' '. ServerCommandsInterface::RUN_JAVA_FILE;
        $java .= ' '. ServerCommandsInterface::RUN_JAVA_NOGUI;

        return $java. $cmd;
    }

    /**
     * create server killing command to CLI
     */
    private function getStopCommand (
        int $pid
    ): string {
        $commands = ServerCommandsInterface::STOP_JAVA;
        $commands .= ' '. str_replace(ServerCommandsInterface::REPLACEMENT_PID, (string)$pid, ServerCommandsInterface::STOP_JAVA_PID);
        $commands .= ' '. ServerCommandsInterface::STOP_JAVA_FORCE;

        return $commands;
    }

    /**
     * save process PID to enitity
     */
    private function saveStartServer (
        Server  $server,
        int     $pid
    ): Server {
        $server = $server
            ->setPid($pid)
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
            ->setPid(null)
            ->setStatus(ServerInterface::STATUS_OFFLINE)
        ;

        $this->entityManager->persist($server);
        $this->entityManager->flush();

        return $server;
    }

}
