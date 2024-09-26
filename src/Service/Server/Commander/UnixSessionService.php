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
        $command = $this->getScreenCreateCommand($server, $path);
        $this->commandHelper->runCommand($command, $path);
        $pid = $this->commandHelper->getReturnedValue();
        if (false !== $pid && is_int((int) $pid) && $pid !== '') {

            throw new CouldNotCreateNewScreenSessionException();
        }

        return (int)$pid;
    }

    private function getScreenCreateCommand (
        Server  $server,
        string  $path
    ): string {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

//        $command = str_replace(
//            ServerUnixCommandsInterface::REPLACEMENT_LOG_PATH,
//            $path,
//            $command
//        );
//
//        $command = str_replace(
//            ServerUnixCommandsInterface::REPLACEMENT_LOG_FILENAME,
//            $server->getName(). '_console.log',
//            $command
//        );

        return $command;
    }

}
