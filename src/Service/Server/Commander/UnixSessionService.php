<?php

declare(strict_types=1);

namespace App\Service\Server\Commander;

use App\Entity\Server;
use App\Exception\Screen\CouldNotCreateNewScreenSessionException;
use App\UniqueNameInterface\ServerUnixCommandsInterface;

class UnixSessionService
{
    /**
     * check if screen with given PID exists
     */
    public static function checkScreenExists (
        Server  $server
    ): bool {
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            $server->getName(),
            ServerUnixCommandsInterface::SCREEN_GETSPECIFICPID
        );

        $descriptorSpec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout -> we use this
            2 => array("pipe", "w")   // stderr
        );

        $process = proc_open($command, $descriptorSpec[1], $pipes);
        // check if process run successfully
        while (0 !== proc_get_status($process)[ServerUnixCommandsInterface::PROCESS_EXITCODE]) {
            usleep(250);
        }
        $pid = fgets($pipes[1], 1024);
        proc_close($process);
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

        $descriptorSpec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout -> we use this
            2 => array("pipe", "w")   // stderr
        );

        $process = proc_open($command, $descriptorSpec[1], $pipes);
        // check if process run successfully
        while (0 !== proc_get_status($process)[ServerUnixCommandsInterface::PROCESS_EXITCODE]) {
            usleep(250);
        }
        $pid = fgets($pipes[1], 1024);
        proc_close($process);
        $pid = substr($pid, 0, strpos($pid, "."));

        if (false === $pid) {

            throw new CouldNotCreateNewScreenSessionException();
        }

        return (int)$pid;
    }

}
