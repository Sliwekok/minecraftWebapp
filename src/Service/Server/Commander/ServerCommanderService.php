<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Service\Helper\OperatingSystemHelper;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ConsoleInterface;
use App\UniqueNameInterface\ServerInterface;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
use Doctrine\ORM\EntityManagerInterface;

class ServerCommanderService
{

    /**
     * that class validates machine that PHP is run on - commands depends on OS
     */

    public function __construct (
        private EntityManagerInterface  $entityManager,
        private LinuxCommanderService   $linuxCommander,
        private WindowsCommanderService $windowsCommander,
        private RunCommandHelper        $commandHelper
    )
    {}

    public function startServer (
        Server $server
    ): Server {
        if (OperatingSystemHelper::isWindows()) {
            $this->windowsCommander->startServer($server);
        } else {
            $this->linuxCommander->startServer($server);
        }

        return $this->saveStartServer($server);
    }

    public function checkServerStatus (
        Server $server
    ): bool {
        if (OperatingSystemHelper::isWindows()) {
            return $this->windowsCommander->checkServerStatus($server);
        } else {
            return $this->linuxCommander->checkServerStatus($server);
        }
    }

    /**
     * end process server via CommandLine
     */
    public function stopServer (
        Server $server
    ): Server {
        if (OperatingSystemHelper::isWindows()) {
            $this->windowsCommander->stopServer($server);
        } else {
            $this->linuxCommander->stopServer($server);
        }

        return $this->saveStopServer($server);
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

    /**
     * This function doesn't return anything - it just proceeds to save to logs where data can be retrieved
     */
    public function getPlayerList (
        Server  $server
    ): void {
        // run /list command in minecraft server
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_LIST,
            $screen
        );

        $this->commandHelper->runCommand($command);
    }

    // run /whitelist add [nickname] command in minecraft server
    public function addToWhitelist (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_WHITELIST_ADD,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    // run /whitelist remove [nickname] command in minecraft server
    public function removeFromWhitelist (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_WHITELIST_REMOVE,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    public function addToOpList (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_OP_ADD,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    public function removeFromOpList (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_OP_REMOVE,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    public function addToBlacklist (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_BLACKLIST_ADD,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    public function removeFromBlacklist (
        Server  $server,
        string  $nickname
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            ConsoleInterface::COMMAND_PLAYER_BLACKLIST_REMOVE,
            $screen
        );
        $command = str_replace(
            ConsoleInterface::REPLACEMENT_NICKNAME,
            $nickname,
            $command
        );

        $this->commandHelper->runCommand($command);
    }

    public function installForgeClient (
        Server  $server,
    ): void {
        if (OperatingSystemHelper::isWindows()) {
//            $this->windowsCommander->stopServer($server);
            throw new \Exception("No Windows support - yet");
        } else {
            $this->linuxCommander->installForgeServer($server);
        }
    }

    public function getServerUsageFile (
        Server  $server
    ): mixed {
        if (OperatingSystemHelper::isWindows()) {
//            $this->windowsCommander->stopServer($server);
            throw new \Exception("No Windows support - yet");
        } else {
            return $this->linuxCommander->getServerUsageFile($server);
        }
    }

    public function getServerUsage (
        Server  $server
    ): array {
        if (OperatingSystemHelper::isWindows()) {
//            $this->windowsCommander->stopServer($server);
            throw new \Exception("No Windows support - yet");
        }
        else {
            return $this->linuxCommander->getServerUsage($server);
        }
    }
}
