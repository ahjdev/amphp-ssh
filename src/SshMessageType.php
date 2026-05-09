<?php declare(strict_types=1);

namespace Amp\Ssh;

enum SshMessageType: int
{
    /**
     * Transport Layer (RFC 4253 & 8308)
     */
    case SSH_MSG_DISCONNECT = 1;
    case SSH_MSG_IGNORE = 2;
    case SSH_MSG_UNIMPLEMENTED = 3;
    case SSH_MSG_DEBUG = 4;
    case SSH_MSG_SERVICE_REQUEST = 5;
    case SSH_MSG_SERVICE_ACCEPT = 6;
    case SSH_MSG_EXT_INFO = 7;
    case SSH_MSG_NEWCOMPRESS = 8;

    /**
     * Algorithm Negotiation (RFC 4253)
     */
    case SSH_MSG_KEXINIT = 20;
    case SSH_MSG_NEWKEYS = 21;

    /**
     * Key Exchange (RFC 4253, RFC 5656, RFC 4419)
     */
    case SSH_MSG_KEXDH_INIT = 30;
    case SSH_MSG_KEXDH_REPLY = 31;
    case SSH_MSG_KEX_DH_GEX_INIT = 32;
    case SSH_MSG_KEX_DH_GEX_REPLY = 33;
    case SSH_MSG_KEX_DH_GEX_REQUEST = 34;

    /**
     * User Authentication (RFC 4252 & 4256)
     */
    case SSH_MSG_USERAUTH_REQUEST = 50;
    case SSH_MSG_USERAUTH_FAILURE = 51;
    case SSH_MSG_USERAUTH_SUCCESS = 52;
    case SSH_MSG_USERAUTH_BANNER = 53;
    case SSH_MSG_USERAUTH_PK_OK = 60;

    // case SSH_MSG_USERAUTH_INFO_REQUEST = 60;
    // case SSH_MSG_USERAUTH_INFO_RESPONSE = 61;

    /**
     * Connection Protocol (RFC 4254)
     */
    case SSH_MSG_GLOBAL_REQUEST = 80;           // @TODO
    case SSH_MSG_REQUEST_SUCCESS = 81;          // @TODO
    case SSH_MSG_REQUEST_FAILURE = 82;          // @TODO

    /**
     * Channel Messages (RFC 4254)
     */
    case SSH_MSG_CHANNEL_OPEN = 90;
    case SSH_MSG_CHANNEL_OPEN_CONFIRMATION = 91;
    case SSH_MSG_CHANNEL_OPEN_FAILURE = 92;
    case SSH_MSG_CHANNEL_WINDOW_ADJUST = 93;
    case SSH_MSG_CHANNEL_DATA = 94;
    case SSH_MSG_CHANNEL_EXTENDED_DATA = 95;
    case SSH_MSG_CHANNEL_EOF = 96;
    case SSH_MSG_CHANNEL_CLOSE = 97;
    case SSH_MSG_CHANNEL_REQUEST = 98;
    case SSH_MSG_CHANNEL_SUCCESS = 99;
    case SSH_MSG_CHANNEL_FAILURE = 100;

    /**
     * Reserved / Private Use
     */
    // 101-127: Unassigned (Channel related messages)
    // 128-191: Reserved for client protocols
    // 192-255: Reserved for Private Use

    public function isChannelMessage(): bool
    {
        $value = $this->value;
        return ($value >= 90) && ($value <= 100);
    }
}
