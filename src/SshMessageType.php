<?php declare(strict_types=1);

namespace Amp\Ssh;

enum SshMessageType
{
    /**
     * Transport Layer (RFC 4253 & 8308)
     */
    case DISCONNECT;
    case IGNORE;
    case UNIMPLEMENTED;
    case DEBUG;
    case SERVICE_REQUEST;
    case SERVICE_ACCEPT;
    case EXT_INFO;
    case NEWCOMPRESS;

    /**
     * Algorithm Negotiation (RFC 4253)
     */
    case KEXINIT;
    case NEWKEYS;

    /**
     * Key Exchange (RFC 4253, RFC 5656, RFC 4419)
     */
    case KEX_ECDH_INIT;
    case KEX_ECDH_REPLY;
    case KEXDH_GEX_INIT;
    case KEXDH_GEX_GROUP;
    case KEXDH_GEX_REPLY;
    case KEXDH_GEX_REQUEST;

    /**
     * User Authentication (RFC 4252 & 4256)
     */
    case USERAUTH_REQUEST;
    case USERAUTH_FAILURE;
    case USERAUTH_SUCCESS;
    case USERAUTH_BANNER;
    case USERAUTH_PK_OK;

    // case USERAUTH_PASSWD_CHANGEREQ;
    // case USERAUTH_INFO_REQUEST;
    // case USERAUTH_INFO_RESPONSE;

    /**
     * Connection Protocol (RFC 4254)
     */
    case GLOBAL_REQUEST; // @TODO
    case REQUEST_SUCCESS; // @TODO
    case REQUEST_FAILURE; // @TODO

    /**
     * Channel Messages (RFC 4254)
     */
    case CHANNEL_OPEN;
    case CHANNEL_OPEN_CONFIRMATION;
    case CHANNEL_OPEN_FAILURE;
    case CHANNEL_WINDOW_ADJUST;
    case CHANNEL_DATA;
    case CHANNEL_EXTENDED_DATA;
    case CHANNEL_EOF;
    case CHANNEL_CLOSE;
    case CHANNEL_REQUEST;
    case CHANNEL_SUCCESS;
    case CHANNEL_FAILURE;

    /**
     * Reserved / Private Use
     */
    // 101-127: Unassigned (Channel related messages)
    // 128-191: Reserved for client protocols
    // 192-255: Reserved for Private Use

    public function isChannelMessage(): bool
    {
        $value = $this->getNumber();
        return ($value >= 90) && ($value <= 100);
    }

    public function isErrorOrFailure(): bool
    {
        return match($this) {
            self::DISCONNECT,
            self::UNIMPLEMENTED,
            self::CHANNEL_FAILURE,
            self::REQUEST_FAILURE,
            self::USERAUTH_FAILURE,
            self::CHANNEL_OPEN_FAILURE => true,
            default => false,
        };
    }

    public function getNumber(): int
    {
        return match ($this) {
            self::DISCONNECT         => 1,
            self::IGNORE             => 2,
            self::UNIMPLEMENTED      => 3,
            self::DEBUG              => 4,
            self::SERVICE_REQUEST    => 5,
            self::SERVICE_ACCEPT     => 6,
            self::EXT_INFO           => 7,
            self::NEWCOMPRESS        => 8,
            self::KEXINIT            => 20,
            self::NEWKEYS            => 21,
            self::KEX_ECDH_INIT      => 30,
            self::KEX_ECDH_REPLY     => 31,
            self::KEXDH_GEX_INIT     => 32,
            self::KEXDH_GEX_GROUP    => 31,
            self::KEXDH_GEX_REPLY    => 33,
            self::KEXDH_GEX_REQUEST  => 34,
            self::USERAUTH_REQUEST   => 50,
            self::USERAUTH_FAILURE   => 51,
            self::USERAUTH_SUCCESS   => 52,
            self::USERAUTH_BANNER    => 53,
            self::USERAUTH_PK_OK     => 60,
            self::CHANNEL_OPEN       => 90,
            self::CHANNEL_OPEN_CONFIRMATION => 91,
            self::CHANNEL_OPEN_FAILURE      => 92,
            self::CHANNEL_WINDOW_ADJUST     => 93,
            self::CHANNEL_DATA              => 94,
            self::CHANNEL_EXTENDED_DATA     => 95,
            self::CHANNEL_EOF               => 96,
            self::CHANNEL_CLOSE             => 97,
            self::CHANNEL_REQUEST           => 98,
            self::CHANNEL_SUCCESS           => 99,
            self::CHANNEL_FAILURE           => 100,
        };
    }
}
