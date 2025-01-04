<?php

namespace App\Command;

use App\Repository\ServerRepository;
use App\Service\Server\ServerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'minecraft:turn-off-minecraft-servers',
    description: 'Turn off minecraft servers that are online for at least 7 days',
    hidden: false
)]
class TurnOffInactiveServersCommand extends Command
{
    protected static $defaultDescription = 'Turn off minecraft servers that are online for at least 7 days';

    public function __construct(
        private ServerService       $serverService,
        private ServerRepository    $serverRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Looking for servers to turn off");
        $servers = $this->serverRepository->findOnlineOlderThan7Days();
        $output->writeln('Found '.count($servers).' servers');

        foreach ($servers as $server) {
            $this->serverService->stopServer($server);
        }

        $output->writeln("Closed ". count($servers). ' servers');
        return OutputInterface::OUTPUT_NORMAL;
    }

    protected function configure(): void
    {
        $this
            ->setHelp(self::$defaultDescription)
        ;
    }
}
