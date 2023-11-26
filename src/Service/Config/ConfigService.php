<?php

declare(strict_types=1);

namespace App\Service\Config;

use App\Entity\Config;
use App\Entity\Server;
use App\Repository\ConfigRepository;
use App\UniqueNameInterface\ConfigInterface;

class ConfigService
{

    public function __construct (
        private ConfigRepository    $configRepository
    )
    {}

    public function createConfig (
        Server $server,
        string $seed = null
    ): ?Config {
        $config = new Config();
        $port = $this->generatePort();
        $config
            ->setAllowFlight(true)
            ->setDifficulty(ConfigInterface::DIFFICULTY_EASY)
            ->setHardcore(false)
            ->setMaxPlayers(16)
            ->setMaxRam(4)
            ->setPvp(false)
            ->setWhitelist(false)
            ->setPort($port)
            ->setServer($server)
            ->setSeed($seed)
        ;

        return $config;
    }

    public function generatePort () :int {
        $configs = $this->configRepository->getPorts();
        $maxPort = max($configs[ConfigInterface::ENTITY_PORT]);
        if (null === $maxPort) {

            return 25565;
        } else {
            $maxPort++;

            return $maxPort;
        }
    }

}