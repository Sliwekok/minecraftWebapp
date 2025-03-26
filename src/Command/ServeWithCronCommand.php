<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Helper\OperatingSystemHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:serve-with-cron',
    description: 'Serve app with all cron commands',
    hidden: false
)]
class ServeWithCronCommand extends Command
{
    protected static $defaultDescription = 'Serve app with all cron commands';

    public function __construct() {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = $input->getOption('port');
        $daemon = $input->getOption('daemon');
        $extra = $input->getOption('extra');

        $command = "symfony serve --port=$port";

        if ($daemon) {
            $command .= '-d';
        }

        $process = Process::fromShellCommandline($command);
        $process->setTty(true);
        $process->enableOutput();
        $process->setTimeout(0);
        $process->start();

        sleep(10);

        if (!$process->isRunning()) {
            $output->writeln('<error>Failed to start Symfony server!</error>');
            $output->writeln('<comment>' . $process->getErrorOutput() . '</comment>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Symfony server started on port $port</info>");
        $output->writeln("<info>Running up cron</info>");

        if (OperatingSystemHelper::isUnix()) {
            $cron = 'sh bin/cron/run_minecraft_usage.sh';
        } else {
            $cron = <<<TEXT
echo "There's not support for cron tasks on windows yet
TEXT
            ;
        }
        $cron = Process::fromShellCommandline($cron);
        $cron->setTty(true);
        $cron->enableOutput();
        $cron->setTimeout(0);
        $cron->start();

        if (!$cron->isRunning()) {
            $output->writeln('<error>Failed to start cron commands!</error>');
            $output->writeln('<comment>' . $cron->getErrorOutput() . '</comment>');
            return Command::FAILURE;
        }

        if ($extra) {
            $output->writeln("<comment>Running extra command: $extra</comment>");
            $extraProcess = new Process(explode(' ', $extra));
            $extraProcess->run(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        }

        return Command::SUCCESS;
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Serve the app with all cron commands')
            ->setHelp('This command starts the Symfony server and runs additional commands.')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'Port number', 80)
            ->addOption('daemon', 'd', InputOption::VALUE_OPTIONAL, 'Run in the background')
            ->addOption('extra', null, InputOption::VALUE_OPTIONAL, 'Extra command to run alongside'); // Removed shortcut
    }
}
