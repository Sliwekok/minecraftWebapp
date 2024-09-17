<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Exception\Screen\CouldNotCreateNewScreenSessionException;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ServerUnixCommandsInterface;

class UnixSessionService
{
    /**
     * check if screen with given PID exists
     */
    public static function checkScreenExists (
        Server  $server
    ): bool {
        // find screen id in screen sessions
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_GETSPECIFICPID
        );

        $commandHelper = new RunCommandHelper;
        $commandHelper->runCommand($command);
        $pid = $commandHelper->getReturnedValue();
        // we need to get PID of new screen, it's like this: 1234.{name}
        $pid = substr($pid, 0, strpos($pid, "."));

        if (false !== $pid && is_int( (int)$pid )) {

            return true;
        }
        else {

            return false;
        }

    }

    /**
     * create new named screen session. Returns PID of screen session
     */
    public static function createNewSession (
        Server  $server
    ): int {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

        $commandHelper = new RunCommandHelper;
        $commandHelper->runCommand($command);
        $pid = $commandHelper->getReturnedValue();
        // we need to get PID of new screen, it's like this: 1234.{name}
        $pid = substr($pid, 0, strpos($pid, "."));

        if (false !== $pid && is_int( (int)$pid)) {

            throw new CouldNotCreateNewScreenSessionException();
        }

        return (int)$pid;
    }

    public static function attachToSession (
        Server  $server
    ): void {
        $commandHelper1 = new RunCommandHelper();
        $currentPid = $commandHelper1->runCommand(ServerUnixCommandsInterface::SCREEN_GETCURRENTPID);

        $specificPid = self::getSpecificPid($server);

        if ($currentPid !== $specificPid) {

            $command = str_replace(
                ServerUnixCommandsInterface::REPLACEMENT_NAME,
                $server->getName(),
                ServerUnixCommandsInterface::SCREEN_SWITCH
            );
            $commandHelper2 = new RunCommandHelper();
            $commandHelper2->runCommand($command);
        }
    }

    public static function getSpecificPid (
        Server  $server
    ): string {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_CREATE
        );

        $commandHelper = new RunCommandHelper;
        $commandHelper->runCommand($command);

        return $commandHelper->getReturnedValue();
    }

}
