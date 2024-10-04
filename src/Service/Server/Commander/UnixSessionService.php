<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Exception\Screen\CouldNotCreateNewScreenSessionException;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ServerUnixCommandsInterface;

class UnixSessionService
{

    public function __construct (
        private RunCommandHelper    $commandHelper
    ) {}

    /**
     * check if screen with given PID exists
     * example:
     *      sudo screen -ls | grep -w 'server_name' | awk '{print $1}' | cut -d. -f1
     */
    public function checkScreenExists (
        Server  $server
    ): bool {
        // find screen id in screen sessions
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_GETSPECIFICPID
        );

        $this->commandHelper->runCommand($command);
        $pid = $this->commandHelper->getReturnedValue();

        if (false !== $pid && is_int((int) $pid) && $pid !== '') {

            return true;
        }
        else {

            return false;
        }

    }

    /**
     * create new named screen session. Returns PID of screen session
     */
    public function createNewSession (
        Server  $server,
        string  $path
    ): int {
        $command = $this->getScreenCreateCommand($server);
        $this->commandHelper->runCommand($command, $path);
        $pid = $this->commandHelper->getReturnedValue();
        if (false !== $pid && is_int((int) $pid) && $pid !== '') {

            throw new CouldNotCreateNewScreenSessionException();
        }

        $this->changeDirectoryToMinecraft($server, $path);
        $this->addLoggingToScreen($server, $path);

        return (int)$pid;
    }

    private function getScreenCreateCommand (
        Server  $server,
    ): string {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

        return $command;
    }

    private function addLoggingToScreen (
        Server  $server,
        string  $path
    ): void {
        $logging = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_LOG_PATH,
            $path,
            ServerUnixCommandsInterface::SCREEN_ADDLOGGING
        );

        $logging = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_LOG_FILENAME,
            $server->getName(). ServerUnixCommandsInterface::LOG_SUFFIX,
            $logging
        );

        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            $logging,
            ServerUnixCommandsInterface::SCREEN_SWTCH_WITHOUTSTUFF
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            $command
        );

        $this->commandHelper->runCommand($command);

    }

    private function changeDirectoryToMinecraft (
        Server  $server,
        string  $path
    ): void {
        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $cd = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_PATH,
            $path,
            ServerUnixCommandsInterface::SCREEN_CHANGEDIRECTORY
        );

        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            $cd,
            $screen
        );

        $this->commandHelper->runCommand($command);
    }

}
