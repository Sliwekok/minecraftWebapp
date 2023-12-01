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
        if (is_array($config)) {
            $config = (new Config)
                ->setSeed($config[ConfigInterface::PROPERTY_SEED])
                ->setPort($config[ConfigInterface::PROPERTY_PORT])
                ->setWhitelist($config[ConfigInterface::PROPERTY_WHITELIST])
                ->setPvp($config[ConfigInterface::PROPERTY_PVP])
                ->setMaxRam($config[ConfigInterface::PROPERTY_MAXRAM])
                ->setMaxPlayers($config[ConfigInterface::PROPERTY_MAXPLAYERS])
                ->setHardcore($config[ConfigInterface::PROPERTY_HARDCORE])
                ->setDifficulty($config[ConfigInterface::PROPERTY_DIFFICULTY])
                ->setAllowFlight($config[ConfigInterface::PROPERTY_ALLOWFLIGHT])
                ->setMotd($config[ConfigInterface::PROPERTY_MOTD])
                ->setLevelName($config[ConfigInterface::PROPERTY_LEVELNAME])
            ;
        }

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
            $filename = $path . '/' .ServerDirectoryInterface::MINECRAFT_SERVERPROPERTIES;
            $file = file_get_contents($filename);
            $fileContent = explode("\n", $file);
            $reflection = new ReflectionClass(ConfigInterface::class);
            $configArr = (array) $config;

            foreach ($fileContent as &$line) {
                if (str_starts_with('#', $line) || 0 === strlen($line)) {

                    continue;
                }
                foreach ($configArr as $entity => $value) {
                    $entityName = preg_replace('/[^\da-z]/i', '', strtoupper(str_replace(Config::class, '', $entity)));
                    $propertyName =  ConfigInterface::PROPERTY. $entityName;
                    $property = $reflection->getConstant($propertyName);
                    if (false === $property) {

                        continue;
                    }
                    if (str_starts_with($line, $property)) {
                        $line = $property. '='. (string)$value;
                    }
                }
            }

            file_put_contents($filename, implode("\n", $fileContent));
        } catch (Exception $exception) {

            throw new  CouldNotOpenServerPropertyFile($exception->getMessage());
        }

        return true;
    }
}
