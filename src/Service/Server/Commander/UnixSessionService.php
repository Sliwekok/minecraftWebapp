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
        Server  $server
    ): int {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

        $this->commandHelper->runCommand($command);
        $pid = $this->commandHelper->getReturnedValue();
        if (false !== $pid && is_int((int) $pid) && $pid !== '') {

            throw new CouldNotCreateNewScreenSessionException();
        }

        return (int)$pid;
    }

    public function attachToSession (
        Server  $server
    ): void {
        $this->commandHelper->runCommand(ServerUnixCommandsInterface::SCREEN_GETCURRENTPID);
        $currentPid = $this->commandHelper->getReturnedValue();

        $specificPid = $this->getSpecificPid($server);

        if ($currentPid !== $specificPid) {

            $command = str_replace(
                ServerUnixCommandsInterface::REPLACEMENT_NAME,
                $server->getName(),
                ServerUnixCommandsInterface::SCREEN_SWITCH
            );
            $this->commandHelper->runCommand($command);
        }
    }

    public function getSpecificPid (
        Server  $server
    ): string {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

        $this->commandHelper->runCommand($command);

        return $this->commandHelper->getReturnedValue();
    }

}
