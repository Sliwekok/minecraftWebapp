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
        string          $path = ''
    ): bool {
        try {
            if ($path === '') {
                $path = FilesystemService::getAbsolutePublicPath();
            }
            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            );
//            $options = ['bypass_shell' => true];
            $options = [];
            $process = proc_open($commands, $descriptorspec, $pipes, $path, options: $options);

            $count = 0;
            $procData = proc_get_status($process);

            while ($count < 3) {
                if (!$procData[ServerWindowsCommandsInterface::PROCESS_RUNNING] && $procData[ServerWindowsCommandsInterface::PROCESS_EXITCODE]) {
                    $this->returned = stream_get_contents($pipes[1]);
                    $this->commandLogger->info('Exec command', [
                        'command'   => $commands,
                        'returned'  => $this->getReturnedValue(),
                        'path'      => $path,
                        'userId'    => $this->security->getUser()->getId(),
                    ]);
                    fclose($pipes[1]);
                }
                sleep(1);
                $count++;
            }
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
