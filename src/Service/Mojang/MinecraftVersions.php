<?php

declare(strict_types=1);

namespace App\Service\Mojang;

use App\Exception\Server\CouldNotDownloadAndSaveServerFileException;
use App\Exception\Server\VersionNotFoundException;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\MojangInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;
use App\UniqueNameInterface\ServerInterface;
use App\UniqueNameInterface\ServerUnixCommandsInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MinecraftVersions
{

    public function __construct(
        private RunCommandHelper    $commandHelper,
        #[Autowire('%kernel.project_dir%')] private string $rootDirectory,
    )
    {}

    /**
     * get all releases with full detailed information from API. Snapshots are skipped.
     */
    public function getAllVersionsData(): array {
        $url = MojangInterface::VERSIONS_URL;
        $allVersions = json_decode(file_get_contents($url, true), true)[MojangInterface::VERSIONS_MANIFEST_VERSIONS];
        $officialVersions = [];

        // along versions there are snapshots that are not compatible
        foreach ($allVersions as $version) {
            if (MojangInterface::VERSIONS_MANIFEST_TYPE_RELEASE === $version[MojangInterface::VERSIONS_MANIFEST_TYPE]) {
                $officialVersions[] = $version;
            }
        }

        return $officialVersions;
    }

    /**
     * get only version numbers from API (without any more data - just version number e.g. 1.12.1, 1.12.2 etc
     */
    public function getAllVersions(): array {
        $onlyVersions = [];
        $allData = $this->getAllVersionsData();
        foreach ($allData as $version) {
            $onlyVersions[] = $version[MojangInterface::VERSIONS_MANIFEST_ID];
        }

        return $onlyVersions;
    }

    /**
     * return all data from API about specific version (e.g. only 1.12.1)
     */
    public function getSpecificVersion(string $version): array {
        $versions = $this->getAllVersionsData();
        $key = array_search($version, array_column($versions, MojangInterface::VERSIONS_MANIFEST_ID));

        if (null === $versions[$key]) {
            throw new VersionNotFoundException();
        }

        return $versions[$key];
    }

    public function getMinecraftVersion(string $version): mixed {
        try {
            // double request because server file is in second url
            $fileUrl = $this->getSpecificVersion($version)[MojangInterface::VERSIONS_MANIFEST_URL];
            $specificData = json_decode(file_get_contents($fileUrl), true);

            $serverFileUrl = $specificData[MojangInterface::PACKAGES_DOWNLOADS]
            [MojangInterface::PACKAGES_DOWNLOADS_SERVER][MojangInterface::PACKAGES_DOWNLOADS_SERVER_URL];

            $file = file_get_contents($serverFileUrl);
        } catch (\Exception $e) {

            throw new CouldNotDownloadAndSaveServerFileException($e->getMessage());
        }

        return $file;
    }

    public function getFabricVersion(string $version): mixed {
        try {
            $fileUrl = str_replace(
                ServerInterface::REPLACE_MINECRAFT_VERSION,
                $version,
                ServerInterface::FABRIC_URL
            );

        $file = file_get_contents($fileUrl);

        } catch (\Exception $e) {

            throw new CouldNotDownloadAndSaveServerFileException($e->getMessage());
        }

        return $file;

    }

    // we got double redirects, need to do multiple calls
    public function getForgeVersion(
        string $version,
        string $saveTo
    ): mixed {
        try {
            $url = str_replace(
                ServerInterface::REPLACE_MINECRAFT_VERSION,
                $version,
                ServerInterface::FORGE_URL
            );

            // run this for environment preparation
            $pythonPath = $this->rootDirectory. DIRECTORY_SEPARATOR. ServerDirectoryInterface::BIN. DIRECTORY_SEPARATOR .ServerDirectoryInterface::PYTHON_FORGE_DOWNLOADER;
            $command = ServerUnixCommandsInterface::DISPLAY_SET_PORT. ';python3 '. $pythonPath;
            $this->commandHelper->runCommand($command, args: [$url, $saveTo]);

            if (str_starts_with($this->commandHelper->getReturnedValue(), 'Error:')) {
                // Remove quotes and newline characters using regex
                $errorMsg = preg_replace("/['\"\n\r]/", '', $this->commandHelper->getReturnedValue());

                throw new CouldNotDownloadAndSaveServerFileException($errorMsg);
            }

            return file_get_contents($saveTo. DIRECTORY_SEPARATOR. ServerDirectoryInterface::MINECRAFT_SERVER_FILE);
        } catch (\Exception $e) {

            throw new CouldNotDownloadAndSaveServerFileException($e->getMessage());
        }
    }
}
