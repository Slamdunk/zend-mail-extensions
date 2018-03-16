<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Protocol;

use Zend\Mail\Protocol\Smtp as ZendProtocolSmtp;
use Zend\Mail\Protocol\Smtp;

final class TimeKeeperSmtpProxy extends ZendProtocolSmtp implements TimeKeeperProtocolInterface
{
    /**
     * @var ZendProtocolSmtp|ZendProtocolSmtp\Auth\Login|ZendProtocolSmtp\Auth\Crammd5|ZendProtocolSmtp\Auth\Plain
     */
    private $originalProtocol;

    /**
     * @var null|int
     */
    private $startTime;

    public function __construct(ZendProtocolSmtp $originalProtocol)
    {
        $originalProtocol->setUseCompleteQuit(false);

        $this->originalProtocol = $originalProtocol;
    }

    public function __destruct()
    {
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setMaximumLog($maximumLog): void
    {
        $this->originalProtocol->setMaximumLog($maximumLog);
    }

    public function getMaximumLog(): int
    {
        return $this->originalProtocol->getMaximumLog();
    }

    public function connect(): bool
    {
        $result = $this->originalProtocol->connect();

        $this->startTime = \time();

        return $result;
    }

    public function getRequest(): ?string
    {
        return $this->originalProtocol->getRequest();
    }

    public function getResponse(): ?array
    {
        return $this->originalProtocol->getResponse();
    }

    public function getLog(): string
    {
        return $this->originalProtocol->getLog();
    }

    public function resetLog(): void
    {
        $this->originalProtocol->resetLog();
    }

    public function helo($host = '127.0.0.1'): void
    {
        $this->originalProtocol->helo($host);
    }

    public function hasSession(): bool
    {
        return $this->originalProtocol->hasSession();
    }

    public function mail($from): void
    {
        $this->originalProtocol->mail($from);
    }

    public function rcpt($to): void
    {
        $this->originalProtocol->rcpt($to);
    }

    public function data($data): void
    {
        $this->originalProtocol->data($data);
    }

    public function rset(): void
    {
        $this->originalProtocol->rset();
    }

    public function noop(): void
    {
        $this->originalProtocol->noop();
    }

    public function vrfy($user): void
    {
        $this->originalProtocol->vrfy($user);
    }

    public function quit(): void
    {
        $this->originalProtocol->quit();
    }

    public function auth(): void
    {
        $this->originalProtocol->auth();
    }

    public function disconnect(): void
    {
        $this->originalProtocol->disconnect();

        $this->startTime = null;
    }

    public function setUsername(string $username): Smtp
    {
        return $this->originalProtocol->setUsername($username);
    }

    public function getUsername(): string
    {
        return $this->originalProtocol->getUsername();
    }

    public function setPassword(string $password): Smtp
    {
        return $this->originalProtocol->setPassword($password);
    }

    public function getPassword(): string
    {
        return $this->originalProtocol->getPassword();
    }
}
