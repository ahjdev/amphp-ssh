<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshMac
{
    public function getLength(): int;

    public function getName(): string;

    public function hash(string $payload): string;

    public function setKey(string $key): self;
}
