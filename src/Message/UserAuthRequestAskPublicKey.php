<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

final class UserAuthRequestAskPublicKey extends UserAuthRequestPublicKey
{
    #[\Override]
    public function hasSignature(): bool
    {
        return false;
    }
}
