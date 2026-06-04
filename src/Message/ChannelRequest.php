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
    final public static function decode(SshBinary $data): self
    {
        $recipientChannel = $data->readInt();
        $type = ChannelRequestType::from($data->readString());
        $wantReply = $data->readBoolean();

        if ($type === ChannelRequestType::PTY) {
            $term    = $data->readString();
            $columns = $data->readInt();
            $rows    = $data->readInt();
            $width   = $data->readInt();
            $height  = $data->readInt();
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
                command  : $data->readString()
            ),
            ChannelRequestType::EXIT_STATUS => new ChannelRequestExitStatus(
                $recipientChannel,
                code: $data->readInt()
            ),
            ChannelRequestType::SIGNAL => new ChannelRequestSignal(
                $recipientChannel,
                signal: Signal::from($data->readString())
            ),
            ChannelRequestType::EXIT_SIGNAL => new ChannelRequestExitSignal(
                $recipientChannel,
                wantReply   : $wantReply,
                coreDumped  : $data->readBoolean(),
                errorMessage: $data->readString(),
                languageTag : $data->readString(),
                signal      : Signal::from($data->readString()),
            ),
            ChannelRequestType::ENV => new ChannelRequestEnv(
                $recipientChannel,
                wantReply: $wantReply,
                name     : $data->readString(), value: $data->readString()
            ),
            ChannelRequestType::WINDOW_CHANGE => new ChannelRequestWindowChange(
                $recipientChannel,
                columns: $data->readInt(), rows  : $data->readInt(),
                width  : $data->readInt(), height: $data->readInt(),
            ),
        };
    }

    #[\Override]
    final public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_REQUEST;
    }
}
