<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

interface SshCompilerFactory
{
    public function create(): SshCompiler;
}
