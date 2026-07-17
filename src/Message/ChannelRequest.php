<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\Message\ChannelRequestExitSignal;
use Amp\Ssh\Message\ChannelRequestExitStatus;
use Amp\Ssh\Message\ChannelRequestPty;
use Amp\Ssh\Message\ChannelRequestShell;
use Amp\Ssh\Message\ChannelRequestSignal;
use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\ChannelRequestWindowChange;
use Amp\Ssh\Message\Signal;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

/**
 * @internal
 */
abstract class ChannelRequest extends Channel
{
    protected ChannelRequestType $type;

    public function __construct(int $recipientChannel, public readonly bool $wantReply = true)
    {
        parent::__construct($recipientChannel);
    }

    #[\Override]
    public function encode(): string
    {
        $type = $this->type->value;

        return parent::encode() . \pack('Na*C', \strlen($type), $type, $this->wantReply);
    }

    #[\Override]
    final public static function decode(string $data): self
    {
        [$recipientChannel, $type, $wantReply] = Strings::unpackSSH2('Nsb', $data);
        $type = ChannelRequestType::from($type);

        if ($type === ChannelRequestType::PTY) {
            [$term, $columns, $rows, $width, $height] = Strings::unpackSSH2('sN4', $data);
            $modes = [];
            $mode = new SshBinary($data->readString());
            while ($mode->isReadable()) {
                $id = $mode->readByte();
                if ($id === ChannelRequestPtyMode::TTY_OP_END || $id >= ChannelRequestPtyMode::TTY_OP_NOT_DEFINED) {
                    break;
                }
                $modes[$id] = $mode->readInt();
            }
            return new ChannelRequestPty($recipientChannel, $columns, $rows, $width, $height, $modes, $term, $wantReply);
        }

        return match ($type) {
            ChannelRequestType::SHELL => new ChannelRequestShell($recipientChannel, $wantReply),
            ChannelRequestType::EXEC  => new ChannelRequestExec(
                $recipientChannel,
                wantReply: $wantReply,
                command  : Strings::unpackSSH2('s', $data)[0],
            ),
            ChannelRequestType::EXIT_STATUS => new ChannelRequestExitStatus(
                $recipientChannel,
                code: Strings::unpackSSH2('N', $data)[0],
            ),
            ChannelRequestType::SIGNAL => new ChannelRequestSignal(
                $recipientChannel,
                signal: Signal::from(Strings::unpackSSH2('s', $data)[0])
            ),
            ChannelRequestType::EXIT_SIGNAL => new ChannelRequestExitSignal(
                $recipientChannel,
                wantReply   : $wantReply,
                coreDumped  : Strings::unpackSSH2('b', $data)[0],
                errorMessage: Strings::unpackSSH2('s', $data)[0],
                languageTag : Strings::unpackSSH2('s', $data)[0],
                signal      : Signal::from(Strings::unpackSSH2('s', $data)[0]),
            ),
            ChannelRequestType::ENV => new ChannelRequestEnv(
                $recipientChannel,
                wantReply: $wantReply,
                name : Strings::unpackSSH2('s', $data)[0],
                value: Strings::unpackSSH2('s', $data)[0]
            ),
            ChannelRequestType::WINDOW_CHANGE => new ChannelRequestWindowChange(
                $recipientChannel,
                columns: Strings::unpackSSH2('N', $data)[0],
                rows   : Strings::unpackSSH2('N', $data)[0],
                width  : Strings::unpackSSH2('N', $data)[0],
                height : Strings::unpackSSH2('N', $data)[0],
            ),
        };
    }

    #[\Override]
    final public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_REQUEST;
    }
}
