<?php

declare(strict_types=1);

namespace App\Service\Console;

use App\Entity\Server;
use App\Exception\Command\IncorrectCommandException;
use App\Service\Filesystem\FilesystemService;
use App\UniqueNameInterface\ConsoleInterface;
use App\Service\Helper\RunCommandHelper;
use App\UniqueNameInterface\ServerUnixCommandsInterface;

class ConsoleService
{
    public function __construct (
        private RunCommandHelper    $commandHelper,
    ) {}
    public function getConsoleHistory (
        Server  $server
    ): string {
        $fs = new FilesystemService($server->getDirectoryPath());
        $history = $fs->getLogFileContent($server->getName());
        $history = $this->refactorConsoleHistory($history);

        return $history;
    }

    public function executeConsoleCommand (
        Server  $server,
        string  $command
    ): bool {
        /**
         * trim from whitespaces,
         * delete special chars,
         */
        $command = htmlspecialchars(trim($command));

        if (!$this->checkWhitelistedCommands($command)) {
            throw new IncorrectCommandException();

            return false;
        }

        $screen = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_NAME,
            (string)$server->getName(),
            ServerUnixCommandsInterface::SCREEN_SWITCH
        );
        $command = str_replace(
            ServerUnixCommandsInterface::REPLACEMENT_COMMAND,
            $command,
            $screen
        );

        $this->commandHelper->runCommand($command);

        return true;
    }

    /**
     * method refactors all output from secure data
     * we're trimming pathing, local ip to global
     */
    private function refactorConsoleHistory (
        string $history
    ): string {
        $exploded = explode("\n", $history);
        /** @var  $pattern
         *
         *  \b: Word boundary to ensure we match a full IP address, not part of a longer string.
            (25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?): Matches numbers from 0 to 255:
            25[0-5]: Matches numbers from 250 to 255.
            2[0-4][0-9]: Matches numbers from 200 to 249.
            [01]?[0-9][0-9]?: Matches numbers from 0 to 199.
            {3}: Ensures the above pattern is repeated 3 times for the first three octets of the IP address.
            \.: Escapes the dot between octets.
            (25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?): Matches the last octet of the IP address.
         *
         */
        $pattern = '/\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';
        /** iterate through lines and check if line is empty*/

        foreach ($exploded as $key => $line) {
            if (0 === strlen($line)) {

                continue;
            }
            if (str_contains($line, ConsoleInterface::SECUREDATA_CHECKKEYWORD_SUDO)) {
                $exploded[$key] = str_repeat("?", strlen($line));
            }
            if (preg_match($pattern, $line)) {
                $exploded[$key] = preg_replace($pattern, gethostbyname(gethostname()), $line);
            }
        }

        $history = implode("\n", $exploded);

        return $history;
    }

    private function checkWhitelistedCommands(
        string $command
    ): bool {
        $whitelistedCommands = ['/advancement', '/attribute', '/execute', '/bossbar', '/clear', '/clone', '/damage', '/data', '/datapack', '/debug', '/defaultgamemode', '/difficulty', '/effect', '/me', '/enchant', '/experience', '/xp', '/fill', '/fillbiome', '/forceload', '/function', '/gamemode', '/gamerule', '/give', '/help', '/item', '/kick', '/kill', '/list', '/locate', '/loot', '/msg', '/tell', '/w', '/particle', '/place', '/playsound', '/random', '/reload', '/recipe', '/return', '/ride', '/say', '/schedule', '/scoreboard', '/seed', '/setblock', '/spawnpoint', '/setworldspawn', '/spectate', '/spreadplayers', '/stopsound', '/summon', '/tag', '/team', '/teammsg', '/tm', '/teleport', '/tp', '/tellraw', '/tick', '/time', '/title', '/trigger', '/weather', '/worldborder', '/jfr', '/ban-ip', '/banlist', '/ban', '/deop', '/op', '/pardon', '/pardon-ip', '/perf', '/setidletimeout', '/transfer', '/whitelist'];

        return in_array($command, $whitelistedCommands);
    }
}
