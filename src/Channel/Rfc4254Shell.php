<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

use Amp\Ssh\Message\ChannelRequestExec;
use Amp\Ssh\Channel\SshShell;

final class Rfc4254Shell extends Rfc4254Channel implements SshShell
{
    public function start(int $columns = 80, int $rows = 24, int $width = 800, int $height = 600): void
    {

    }

    public function windowsSize(int $columns = 80, int $rows = 24, int $width = 800, int $height = 600): void
    {

    }

    public function exec(string $command)
    {
        $request = new ChannelRequestExec($this->channelId, command: $command);

        return $this->doRequest($request);
    }
}
