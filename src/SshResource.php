<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\Closable;

interface SshResource extends Closable
{
    public function getSession(): SshSession;
}
