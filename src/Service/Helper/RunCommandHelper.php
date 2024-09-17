<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\Exception\Server\CouldNotExecuteServerStartException;
use App\UniqueNameInterface\ServerWindowsCommandsInterface;

class RunCommandHelper
{

    private string $returned = '';

    /**
     * run command line command
     * @var string|array $commands executable command
     * @var string $path path where tu run command. If not provided - default directory
     * @return bool
     */
    public function runCommand (
        string|array    $commands,
        string          $path = ''
    ): bool {
        if (is_array($commands)) {
            $process = proc_open($commands[0], [], $pipes, $path);
        } else {
            $process = proc_open($commands, [], $pipes, $path);
        }

        if (is_resource($process)) {
            if (is_array($commands)) {
                $first_array_skipped = false;
                foreach ($commands as $command) {
                    // we need to skip first array key since it was used in creating proc open
                    if (!$first_array_skipped) {
                        continue;
                    }
                    fwrite($pipes[0], $command . "\n");
                }
                fclose($pipes[0]);
            }
        }

        $procData = proc_get_status($process);

        // check if process run successfully
        while (0 !== proc_get_status($process)[ServerWindowsCommandsInterface::PROCESS_EXITCODE]) {
            usleep(250);
        }
        /**
         * wait until finished
         */
        $count = 0;
        while ($procData[ServerWindowsCommandsInterface::PROCESS_RUNNING]) {
            usleep(250);
            $procData = proc_get_status($process);
            $count++;
            if ($count > 2) {
                throw new CouldNotExecuteServerStartException();
            }
        }
        $this->returned = fgets($pipes[1], 1024);
        proc_close($process);

        return true;
    }

    public function getReturnedValue(): mixed {
        return $this->returned;
    }
}
