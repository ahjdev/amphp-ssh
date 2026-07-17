<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

use Amp\ForbidCloning;
use Amp\ForbidSerialization;

final class Rfc4253CompilerFactory implements SshCompilerFactory
{
    use ForbidCloning;
    use ForbidSerialization;

    #[\Override]
    public function create(): Rfc4253Compiler
    {
        return new Rfc4253Compiler;
    }
}
