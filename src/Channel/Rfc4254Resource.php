<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

use Amp\Ssh\Channel\SshResource;
use Amp\Ssh\Channel\SshChannel;

final class Rfc4254Resource implements SshResource
{
    public function getShell(): Rfc4254Shell
    {

    }

    public function getProcess(): Rfc4254Process
    {

    }

    public function close(): void
    {

    }

    public function isClosed(): bool
    {

    }

    public function onClose(\Closure $onClose): void
    {

    }
}
