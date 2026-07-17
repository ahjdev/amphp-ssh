<?php declare(strict_types=1);

namespace Amp\Ssh\Negotiator;

use Amp\ForbidCloning;
use Amp\ForbidSerialization;
use Amp\Ssh\Compression\SshCompression;
use Amp\Ssh\Crypto\SshCipher;
use Amp\Ssh\Crypto\SshCipherType;
use Amp\Ssh\Crypto\SshHostKey;
use Amp\Ssh\Crypto\SshKeyDeriver;
use Amp\Ssh\Crypto\SshKeyExchange;
use Amp\Ssh\Crypto\SshMac;
use Amp\Ssh\Crypto\SshMacType;
use Amp\Ssh\Message\KexExchangeDhGexRequest;
use Amp\Ssh\Message\KeyExchangeDhGexGroup;
use Amp\Ssh\Message\KeyExchangeInit;
use Amp\Ssh\Message\NewKeys;
use Amp\Ssh\SshException;
use Amp\Ssh\Transport\SshPacketHandler;
use phpseclib3\Common\Functions\Strings;

final class Rfc4253NegotiatorFactory implements SshNegotiatorFactory
{
    use ForbidCloning;
    use ForbidSerialization;

    #[\Override]
    public function create(SshPacketHandler $packetHandler, KeyExchangeInit $clientKex, KeyExchangeInit $serverKex): SshNegotiator
    {
        $kex = self::resolveKex($clientKex, $serverKex);
        $hostKey = self::resolveHostKey($clientKex, $serverKex);
        // Cryption
        $decrypt = self::resolveCryption($clientKex, $serverKex);
        $encrypt = self::resolveCryption($clientKex, $serverKex, true);
        // Compression
        $decompress = self::resolveCompression($clientKex, $serverKex);
        $compress = self::resolveCompression($clientKex, $serverKex, true);
        // Mac
        $inboundMac  = self::resolveMac($clientKex, $serverKex);
        $outboundMac = self::resolveMac($clientKex, $serverKex, true);
        // Exchange
        $keyLength  = \max($encrypt->getKeySize(), $decrypt->getKeySize());

        if ($kex->isGroupExchange()) {
            $exchangeRequest = new KexExchangeDhGexRequest();
            $exchangeGroup = $packetHandler->writeMessage($exchangeRequest);
            if (!$exchangeGroup instanceof KeyExchangeDhGexGroup) {
                // todo throw
            }
            $privateKey = $kex->createFromExchangeGrop($keyLength, $exchangeGroup);
        } else {
            $privateKey = $kex->createPrivateKey($keyLength);
        }

        $exchangeSend = $kex->createKeyExchange($privateKey);
        $exchangeRecv = $packetHandler->writeMessage($exchangeSend);
        $publicKeyFormat = Strings::unpackSSH2('s', $exchangeRecv->hostKey);
        $signatureFormat = Strings::unpackSSH2('s', $exchangeRecv->signature);
        $keyBytes = $kex->compute($privateKey, $exchangeRecv->fBytes);

        $clientKex = $clientKex->encode();
        $serverKex = $serverKex->encode();
        $identification = $packetHandler->getIdentification();
        $serverIdentification = $packetHandler->getServerIdentification();
        $exchangeHash = Strings::packSSH2('s5', $identification, $serverIdentification, $clientKex, $serverKex, $exchangeRecv->hostKey);

        if ($kex->isGroupExchange()) {
            $exchangeHash .= Strings::packSSH2('N3', $exchangeRequest->min, $exchangeRequest->ideal, $exchangeRequest->max);
            $exchangeHash .= Strings::packSSH2('s2', $exchangeGroup->prime, $exchangeGroup->generator);
        }
        $exchangeHash .= Strings::packSSH2('s3', $exchangeSend->exchange, $exchangeRecv->fBytes, $keyBytes);
        $sessionId = $kex->hash($exchangeHash);

        if ($hostKey->verify($publicKeyFormat, $signatureFormat) === false) {
            throw new SshException('Server Host Key Algorithm Mismatch (' . $signatureFormat . ' vs ' . $hostKey->value . ')');
        }

        if (!$packetHandler->writeMessage(new NewKeys) instanceof NewKeys) {
            // todo add exception
        }

        // todo: reset counter
        // if ($this->strict_kex_flag) {
            // $this->get_seq_no = $this->send_seq_no = 0;
        // }
        $derivation = new SshKeyDeriver($kex, $keyBytes, $exchangeHash, $sessionId);
        //
        [$inboundMac, $outboundMac, $decrypt, $encrypt] = array_pad([], 4, null);

        if (!$encrypt->isAead() || $inboundMac !== SshMacType::NONE) {
            $inboundMac = new SshMac($inboundMac, $derivation);
        }

        if (!$decrypt->isAead() || $inboundMac !== SshMacType::NONE) {
            $outboundMac = new SshMac($outboundMac, $derivation, true);
        }

        if ($decrypt !== SshCipherType::NONE) {
            $decrypt = new SshCipher($decrypt, $derivation);
        }

        if ($encrypt !== SshCipherType::NONE) {
            $encrypt = new SshCipher($encrypt, $derivation, true);
        }

        return new Rfc4253Negotiator($compress, $decompress, $encrypt, $decrypt, $inboundMac, $outboundMac);
    }

    #[\Override]
    public function createKeyExchange(): KeyExchangeInit
    {
        $macs = SshMacType::getSupported();
        $compressions = SshCompression::getSupported();
        $cryptions = SshCipherType::getSupported();

        return new KeyExchangeInit(
            hostKey: SshHostKey::getSupported(),
            kex: SshKeyExchange::getSupported(),
            macC2S: $macs,
            macS2C: $macs,
            encryptC2S: $cryptions,
            encryptS2C: $cryptions,
            compressC2S: $compressions,
            compressS2C: $compressions,
        );
    }

    private static function resolveMac(KeyExchangeInit $clientKex, KeyExchangeInit $serverKex, bool $outbound = false): SshMacType
    {
        [$array1, $array2] = $outbound
            ? [$clientKex->macC2S, $serverKex->macC2S]
            : [$clientKex->macS2C, $serverKex->macS2C];

        $compression = self::resolve($array1, $array2, 'mac');
        return SshMacType::from($compression);
    }

    private static function resolveCompression(KeyExchangeInit $clientKex, KeyExchangeInit $serverKex, bool $compress = false): SshCompression
    {
        [$array1, $array2] = $compress
            ? [$clientKex->compressC2S, $serverKex->compressC2S]
            : [$clientKex->compressS2C, $serverKex->compressS2C];

        $compression = self::resolve($array1, $array2, 'compression');
        return SshCompression::from($compression);
    }

    private static function resolveCryption(KeyExchangeInit $clientKex, KeyExchangeInit $serverKex, bool $encrypt = false): SshCipherType
    {
        [$array1, $array2] = $encrypt
            ? [$clientKex->encryptC2S, $serverKex->encryptC2S]
            : [$clientKex->encryptS2C, $serverKex->encryptS2C];

        $cryption = self::resolve($array1, $array2, 'cryption');
        return SshCipherType::from($cryption);
    }

    private static function resolveKex(KeyExchangeInit $clientKex, KeyExchangeInit $serverKex): SshKeyExchange
    {
        $kex = self::resolve($clientKex->kex, $serverKex->kex, 'kex');
        return SshKeyExchange::from($kex);
    }

    private static function resolveHostKey(KeyExchangeInit $clientKex, KeyExchangeInit $serverKex): SshHostKey
    {
        $hostKey = self::resolve($clientKex->hostKey, $serverKex->hostKey, 'host key');
        return SshHostKey::from($hostKey);
    }

    /**
     * @param list<string> $array1
     * @param list<string> $array2
     *
     * @throws SshException
     */
    private static function resolve(array $array1, array $array2, string $type): string
    {
        foreach ($array1 as $algorithm) {
            if (\in_array($algorithm, $array2, true)) {
                return $algorithm;
            }
        }
        throw new SshException("Key exchange failed: No matching algorithm for $type");;
    }
}
