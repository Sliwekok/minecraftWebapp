<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\Exception\Server\CouldNotDownloadAndSaveServerFileException;
use App\Service\Mojang\MinecraftVersions;
use App\UniqueNameInterface\ServerInterface;

class ServerFileHelper
{
    public function __construct(
        private MinecraftVersions       $minecraftVersions,
    ) {}

    public function getServerFile (
        string $version,
        string $type,
        string $saveTo
    ): mixed {
        try {
            switch ($type) {
                case ServerInterface::FORM_STEP2_GAMETYPE_VANILLA:

                    return $this->minecraftVersions->getMinecraftVersion($version);
                case ServerInterface::FORM_STEP2_GAMETYPE_FORGE:

                    return $this->minecraftVersions->getForgeVersion($version, $saveTo);
                case ServerInterface::FORM_STEP2_GAMETYPE_FABRIC:

                    return $this->minecraftVersions->getFabricVersion($version);
                default:

                    throw new CouldNotDownloadAndSaveServerFileException();
            }
        } catch (\Exception $exception) {

            throw new CouldNotDownloadAndSaveServerFileException($exception->getMessage());
        }
    }
}
