<?php

declare(strict_types=1);

namespace App\Service\Players;

use App\Entity\Server;
use App\Exception\Players\CouldNotAddToWhitelistException;
use App\Exception\Screen\PlayerNameCannotBeEmptyException;
use App\Service\Filesystem\FilesystemService;
use App\Service\Server\Commander\ServerCommanderService;
use App\UniqueNameInterface\PlayerInterface;
use App\UniqueNameInterface\ServerDirectoryInterface;

class PlayersService
{

    public function __construct(
        private ServerCommanderService  $commanderService
    )
    {}


    /**
     * get all players that had connected to server.
     * Returns filtered users with OP permissions, activity and if they are banned
     */
    public function getAllPlayersArranged (
        Server  $server
    ): array|bool {
        $fs = new FilesystemService($server->getDirectoryPath());
        $path = $fs->getAbsoluteMinecraftPath();
        $allPlayers = $this->getAllPlayers($path);
        $allPlayers = $this->separateOnlinePlayers($allPlayers, $server, $fs);
        $allPlayers = $this->getOpsInPlayers($allPlayers, $path);
        $allPlayers = $this->getBannedInPlayers($allPlayers, $path);
        $allPlayers = $this->usortPlayers($allPlayers);
        if (!$allPlayers) {

            return false;
        }

        return $allPlayers;
    }

    /**
     * get all players from file (usercache.json)
     */
    public function getAllPlayers (
        string  $path
    ): array|bool {
        $path = $path. DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_USERCACHE;
        $file = file_get_contents($path);
        if (!$file) {

            return false;
        }
        $json = json_decode($file, true);

        return $json;
    }

    /**
     * check if provided users are offline or online
     * uses command to determinate it, comparing returned from command line with usercache
     */
    public function separateOnlinePlayers (
        array               $players,
        Server              $server,
        FilesystemService   $fs
    ): array {
        $this->commanderService->getPlayerList($server);
        // get timestamps to compare log changes, then fetch data
        $currentTime = new \DateTime('now');
        $modifiedDate = new \DateTime();
        $modifiedDate->setTimestamp(filemtime($fs->getLogFilePath()));

        while ($currentTime < $modifiedDate) {
            usleep(100);
        }

        $logValue = $fs->getLogFileContent(2);
        $playersOnline = substr($logValue, 0, strpos($logValue, 'online:'));
        $playersOnline = explode(',', $playersOnline);

        foreach ($players as &$player) {
            if (in_array($player[PlayerInterface::NAME], $playersOnline)) {
                $player[PlayerInterface::STATUS] = PlayerInterface::STATUS_ONLINE;
            } else {
                $player[PlayerInterface::STATUS] = PlayerInterface::STATUS_OFFLINE;
            }
        }
        // we don't want to show owner of server that we run some commands
        $fs->deleteRowsFromLogFile();

        return $players;
    }

    /**
     * get all privileged users from ops.json
     */
    public function getOps (
        string | Server | FilesystemService $path
    ): array {
        if ($path instanceof Server) {
            $path = (new FilesystemService($path->getDirectoryPath()))->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_OPS;
        }
        if ($path instanceof FilesystemService) {
            $path = $path->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_OPS;
        }
        if (is_string($path) && !str_ends_with($path, DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_OPS)) {
            $path = $path. DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_OPS;
        }
        $players = json_decode(file_get_contents($path), true);

        return $players;
    }

    /**
     * get all whitelisted players from whitelist.json
     */
    public function getWhitelistedPlayers (
        string | Server | FilesystemService $path
    ): array {
        if ($path instanceof Server) {
            $path = (new FilesystemService($path->getDirectoryPath()))->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_WHITELIST;
        }
        if ($path instanceof FilesystemService) {
            $path = $path->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_WHITELIST;
        }
        if (is_string($path) && !str_ends_with($path, DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_WHITELIST)) {
            $path = $path. DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_WHITELIST;
        }
        $players = json_decode(file_get_contents($path), true);

        return $players;
    }

    /**
     * get all blacklisted players from banned-players.json
     */
    public function getBannedPlayers (
        string | Server | FilesystemService $path
    ): array {
        if ($path instanceof Server) {
            $path = (new FilesystemService($path->getDirectoryPath()))->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_BANNED_PLAYERS;
        }
        if ($path instanceof FilesystemService) {
            $path = $path->getAbsoluteMinecraftPath(). DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_BANNED_PLAYERS;
        }
        if (is_string($path) && !str_ends_with($path, DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_BANNED_PLAYERS)) {
            $path = $path. DIRECTORY_SEPARATOR. ServerDirectoryInterface::FILE_BANNED_PLAYERS;
        }

        $players = json_decode(file_get_contents($path), true);

        return $players;
    }

    /**
     * check if provided users are in blacklist
     * uses file banned-players.json, comparing returned data with usercache
     */
    public function getBannedInPlayers (
        array   $players,
        string  $path
    ): array {
        $playersBanned = $this->getBannedPlayers($path);
        if (empty($playersBanned)) {
            foreach ($players as &$player) {
                $player[PlayerInterface::BLACKLISTED] = false;
            }
        } else {
            $playerBlacklistLookup = [];
            foreach ($playersBanned as $toBan) {
                $playerBlacklistLookup[trim($toBan[PlayerInterface::NAME])] = true;
            }

            foreach ($players as &$player) {
                $playerName = trim($player[PlayerInterface::NAME]);
                if (isset($playerBlacklistLookup[$playerName])) {
                    $player[PlayerInterface::BLACKLISTED] = true;
                } else {
                    $player[PlayerInterface::BLACKLISTED] = false;
                }
            }
        }

        return $players;
    }

    /**
     * check if provided users are privileged (OP - operative)
     * uses file ops.json, comparing returned from command line with usercache
     */
    public function getOpsInPlayers (
        array   $players,
        string  $path
    ): array {
        $playerOps = $this->getOps($path);
        if (empty($playerOps)) {
            foreach ($players as &$player) {
                $player[PlayerInterface::OP] = false;
            }
        } else {
            $playerOpsLookup = [];
            foreach ($playerOps as $playerOp) {
                $playerOpsLookup[trim($playerOp[PlayerInterface::NAME])] = true;
            }

            foreach ($players as &$player) {
                $playerName = trim($player[PlayerInterface::NAME]);
                if (isset($playerOpsLookup[$playerName])) {
                    $player[PlayerInterface::OP] = true;
                } else {
                    $player[PlayerInterface::OP] = false;
                }
            }

        }

        return $players;
    }

    /**
     * add player to whitelist via command
     */
    public function addToWhitelist (
        Server  $server,
        array   $toAdd,
    ): void {
        try {
            foreach ($toAdd as $newPlayer) {
                $nickname = trim($newPlayer);
                if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

                $this->commanderService->addToWhitelist($server, $nickname);
            }
        } catch (\Exception $e) {
            throw new CouldNotAddToWhitelistException($e->getMessage());
        }
    }

    /**
     * removes player from whitelist via command
     */
    public function removeFromWhitelist (
        Server  $server,
        string  $toRemove,
    ): void {
        $nickname = trim($toRemove);
        if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

        $this->commanderService->removeFromWhitelist($server, $nickname);
    }

    /**
     * add player to OP list via command
     */
    public function addToOpList (
        Server  $server,
        array  $toAdd
    ): void {
        try {
            foreach ($toAdd as $newPlayer) {
                $nickname = trim($newPlayer);
                if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

                $this->commanderService->addToOpList($server, $nickname);
            }
        } catch (\Exception $e) {
            throw new CouldNotAddToWhitelistException($e->getMessage());
        }
    }

    /**
     * removes player from OP list via command
     */
    public function removeFromOpList (
        Server  $server,
        string  $toRemove,
    ): void {
        $nickname = trim($toRemove);
        if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

        $this->commanderService->removeFromOpList($server, $nickname);
    }

    /**
     * add player to blacklist via command
     */
    public function addToBlacklist (
        Server  $server,
        array  $toAdd
    ): void {
        try {
            foreach ($toAdd as $newPlayer) {
                $nickname = trim($newPlayer);
                if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

                $this->commanderService->addToBlacklist($server, $nickname);
            }
        } catch (\Exception $e) {
            throw new CouldNotAddToWhitelistException($e->getMessage());
        }
    }

    /**
     * removes player from blacklist via command
     */
    public function removeFromBlacklist (
        Server  $server,
        string  $toRemove,
    ): void {
        $nickname = trim($toRemove);
        if (strlen($nickname) === 0) throw new PlayerNameCannotBeEmptyException();

        $this->commanderService->removeFromBlacklist($server, $nickname);
    }

    /**
     * sort first by blacklisted, next by status, next OP and at the end by name (alphabetical)
     */
    public function usortPlayers (
        array $players
    ): array {
        usort($players, function($a, $b) {
            // Compare blacklisted (1 for blacklisted and 0 for not)
            if ($a[PlayerInterface::BLACKLISTED] != $b[PlayerInterface::BLACKLISTED]) {

                return $a[PlayerInterface::BLACKLISTED] - $b[PlayerInterface::BLACKLISTED];
            }

            // Compare status (online comes before offline)
            if ($a[PlayerInterface::STATUS] != $b[PlayerInterface::STATUS]) {

                return ($a[PlayerInterface::STATUS] === PlayerInterface::STATUS) ? -1 : 1;
            }

            // Compare OP (true or false)
            if ($a[PlayerInterface::OP] != $b[PlayerInterface::OP]) {
                return $b[PlayerInterface::OP] - $a[PlayerInterface::OP];
            }

            // Compare name alphabetically (case-insensitive)
            return strcmp($a[PlayerInterface::NAME], $b[PlayerInterface::NAME]);
        });

        return $players;
    }

}
