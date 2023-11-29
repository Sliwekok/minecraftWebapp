<?php

declare(strict_types=1);

namespace App\Service\Config;

use App\Entity\Config;
use App\Exception\Server\CouldNotOpenServerPropertyFile;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ConfigInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;

class UpdateConfigService
{

    public function __construct (
        private EntityManagerInterface $entityManager
    )
    {}

    public function updateConfig (
        Config|array  $config
    ): bool {
        if (!is_array($config)) {

            return $this->updatePropertyFile($config);
        }

        $config
            ->setSeed($config[ConfigInterface::PROPERTY_SEED])
            ->setPort($config[ConfigInterface::PROPERTY_PORT])
            ->setWhitelist($config[ConfigInterface::PROPERTY_WHITELIST])
            ->setPvp($config[ConfigInterface::PROPERTY_PVP])
            ->setMaxRam($config[ConfigInterface::PROPERTY_MAXRAM])
            ->setMaxPlayers($config[ConfigInterface::PROPERTY_MAXPLAYERS])
            ->setHardcore($config[ConfigInterface::PROPERTY_HARDCORE])
            ->setDifficulty($config[ConfigInterface::PROPERTY_DIFFICULTY])
            ->setAllowFlight($config[ConfigInterface::PROPERTY_ALLOWFLIGHT])
        ;

        $this->entityManager->persist($config);
        $this->entityManager->flush();

        return $this->updatePropertyFile($config);
    }

    public function updatePropertyFile (
        Config  $config
    ): bool {
        try {
            $server = $config->getServer();
            $path = (new FilesystemService($server->getDirectoryPath()))->getAbsoluteMinecraftPath();
            $file = fopen($path . '/' .ServerDirectoryInterface::MINECRAFT_SERVERPROPERTIES, 'r+');
            $reflection = new ReflectionClass(ConfigInterface::class);
            $consts = $reflection->getConstants();

            while (($line = fgets($file)) !== false) {
                foreach ($consts as $const => $value) {
                    if (str_starts_with($line, $value)) {
                        $newLine = $value . '=' . $reflection->getConstant($const);

                        fwrite($file, $newLine);
                    }
                }
            }
            fclose($file);
        } catch (Exception $exception) {
            throw new  CouldNotOpenServerPropertyFile($exception->getMessage());
        }

        return true;
    }

}