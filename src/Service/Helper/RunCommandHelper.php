<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\Exception\Command\CouldNotExecuteCommandException;
use App\UniqueNameInterface\ServerWindowsCommandsInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\Filesystem\FilesystemService;

class RunCommandHelper
{

    private string $returned = '';

    public function __construct (
        private LoggerInterface $commandLogger,
        private Security        $security
    ) {}

    /**
     * run command line command
     * @var string|array $commands executable command
     * @var string $path path where tu run command. If not provided - default public directory
     * @return bool
     */
    public function runCommand (
        string|array    $commands,
        string          $path = '',
        array           $args = [],
    ): bool {
        try {
            if ($path === '') {
                $path = FilesystemService::getAbsolutePublicPath();
            }
            $descriptorspec = [
                0 => ["pipe", "r"],  // stdin is a pipe that the child will read from
                1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
                2 => ['pipe', 'w']   // stderr is a pipe that the child will write to
            ];
            if (is_array($commands)) {
                $commands = implode("/n", $commands);
            }

            if (!empty($args)) {
                foreach ($args as $arg) {
                    $commands .= ' '. $arg;
                }
            }

            $process = proc_open($commands, $descriptorspec, $pipes, $path);

            $count = 0;
            $procData = proc_get_status($process);

            while ($count < 3) {
                if (!$procData[ServerWindowsCommandsInterface::PROCESS_RUNNING] && $procData[ServerWindowsCommandsInterface::PROCESS_EXITCODE] === 0) {
                    if (is_resource($pipes[1])) {
                        $this->returned = @stream_get_contents($pipes[1]);
                    }
                }
                sleep(1);
                $procData = proc_get_status($process);
                $count++;
            }

            /**
            stream_set_blocking($pipes[2], false);
            $errorMsg = @stream_get_contents($pipes[2]);
             */
            if (!empty($errorMsg)) {
                $this->commandLogger->info('Error occurred', [
                    'command'   => $commands,
                    'returned'  => $this->getReturnedValue(),
                    'error'     => $errorMsg,
                    'path'      => $path,
                    'userId'    => $this->security->getUser()->getId(),
                ]);
                
                throw new \Exception($errorMsg);
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            $this->commandLogger->info('Exec command', [
                'command'   => $commands,
                'returned'  => $this->getReturnedValue(),
                'path'      => $path,
                'userId'    => $this->security->getUser()->getId(),
            ]);

        } catch (\Exception $exception) {
            $commands = is_array($commands) ? implode(',', $commands) : $commands;

            throw new CouldNotExecuteCommandException($exception->getMessage(). ' when executing command: '. $commands);
        }
        return true;
    }

    public function getReturnedValue(): mixed {
        return $this->returned;
    }
}
