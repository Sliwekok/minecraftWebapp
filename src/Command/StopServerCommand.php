<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:stop-server',
    description: 'Stops server and cron command',
    hidden: false
)]
class StopServerCommand extends Command
{
    protected static $defaultDescription = 'Stops server and cron command';

    public function __construct() {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Validate if the process is running
            $checkProcess = new Process(['pgrep', '-f', 'run_minecraft_usage.sh']);
            $checkProcess->run();

            if (!$checkProcess->isSuccessful()) {
                $output->writeln('<error>No running process found for run_minecraft_usage.sh</error>');
            }

            // Stop the cron command
            $command = "sudo pkill -SIGKILL -f run_minecraft_usage.sh";
            $process = Process::fromShellCommandline($command);
            $process->enableOutput();
            $process->setTty(true);
            $process->setTimeout(0);
            $process->start();

            if (!$process->isSuccessful()) {
                $output->writeln('<error>Failed to stop the cron command!</error>');
                $output->writeln('<comment>' . $process->getErrorOutput() . '</comment>');
                $output->writeln('<comment>' . $process->getOutput() . '</comment>');
            }

            // Stop the Symfony server
            $symfonyStopCommand = "sudo symfony server:stop";
            $symfonyProcess = Process::fromShellCommandline($symfonyStopCommand);
            $symfonyProcess->enableOutput();
            $symfonyProcess->setTimeout(0);
            $symfonyProcess->run();

            if (!$symfonyProcess->isSuccessful()) {
                $output->writeln('<error>Failed to stop the Symfony server!</error>');
                $output->writeln('<comment>' . $symfonyProcess->getErrorOutput() . '</comment>');
                return Command::FAILURE;
            }

            $output->writeln('<info>Successfully stopped the server and cron command.</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<comment>' . $process->getOutput() ?? '-' . '</comment>');

            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $output->writeln('<error>' . $e->getTraceAsString() . '</error>');

            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Stops server and cron command');
    }
}
