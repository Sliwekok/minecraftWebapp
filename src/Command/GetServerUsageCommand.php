<?php

namespace App\Command;

use App\Repository\ServerRepository;
use App\Service\Filesystem\FilesystemService;
use App\Service\Server\ServerService;
use App\UniqueNameInterface\ServerDirectoryInterface;
use App\UniqueNameInterface\ServerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'minecraft:get-server-usage',
    description: 'Generates minecraft server usage for users',
    hidden: false
)]
class GetServerUsageCommand extends Command
{
    protected static $defaultDescription = 'Generates minecraft server usage for users';

    public function __construct(
        private ServerService       $serverService,
        private ServerRepository    $serverRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Started generating server usage");
        $servers = $this->serverRepository->findBy(['status' => ServerInterface::STATUS_ONLINE]);
        $output->writeln('Found '.count($servers).' servers');
        foreach ($servers as $server) {
            $usage = $this->serverService->getServerUsage($server);
            $fs = new FilesystemService($server->getDirectoryPath());
            $path = $fs->getAbsoluteMinecraftPath();
            $usageFile = json_decode($fs->getServerUsageFile(), true) ?? [];
            $usageFile[] = $usage;
            $timeLimit = new \DateTime();
            $timeLimit->modify('-5 minutes');
            // keep only 5 minutes of usage
            $usageFile = array_filter($usageFile, function ($entry) use ($timeLimit) {
                $entryTime = \DateTime::createFromFormat('Y-m-d H:i:s', $entry['time']);
                return $entryTime >= $timeLimit;
            });
            $fs->dumpFile($path . DIRECTORY_SEPARATOR . ServerDirectoryInterface::USAGE_FILE, json_encode($usageFile, JSON_PRESERVE_ZERO_FRACTION));
        }

        return OutputInterface::OUTPUT_NORMAL;
    }

    protected function configure(): void
    {
        $this
            ->setHelp(self::$defaultDescription)
        ;
    }
}
