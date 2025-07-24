<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\ByteStream\WritableStream;
use Amp\ByteStream\ReadableStream;

interface SshShell extends SshSession, WritableStream, ReadableStream
{
    public function windowsSize(int $columns = 80, int $rows = 24, int $width = 800, int $height = 600);
}
