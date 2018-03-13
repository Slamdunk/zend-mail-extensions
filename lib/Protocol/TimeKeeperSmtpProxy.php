<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Protocol;

use Zend\Mail\Protocol\Smtp as ZendProtocolSmtp;

final class TimeKeeperSmtpProxy extends ZendProtocolSmtp implements TimeKeeperProtocolInterface
{
    private $originalProtocol;

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

    public function setMaximumLog($maximumLog)
    {
        return $this->originalProtocol->setMaximumLog($maximumLog);
    }

    public function getMaximumLog()
    {
        return $this->originalProtocol->getMaximumLog();
    }

    public function connect()
    {
        $result = $this->originalProtocol->connect();

        $this->startTime = \time();

        return $result;
    }

    public function getRequest()
    {
        return $this->originalProtocol->getRequest();
    }

    public function getResponse()
    {
        return $this->originalProtocol->getResponse();
    }

    public function getLog()
    {
        return $this->originalProtocol->getLog();
    }

    public function resetLog()
    {
        return $this->originalProtocol->resetLog();
    }

    public function helo($host = '127.0.0.1')
    {
        return $this->originalProtocol->helo($host);
    }

    public function hasSession()
    {
        return $this->originalProtocol->hasSession();
    }

    public function mail($from)
    {
        return $this->originalProtocol->mail($from);
    }

    public function rcpt($to)
    {
        return $this->originalProtocol->rcpt($to);
    }

    public function data($data)
    {
        return $this->originalProtocol->data($data);
    }

    public function rset()
    {
        return $this->originalProtocol->rset();
    }

    public function noop()
    {
        return $this->originalProtocol->noop();
    }

    public function vrfy($user)
    {
        return $this->originalProtocol->vrfy($user);
    }

    public function quit()
    {
        return $this->originalProtocol->quit();
    }

    public function auth()
    {
        return $this->originalProtocol->auth();
    }

    public function disconnect()
    {
        $result = $this->originalProtocol->disconnect();

        $this->startTime = null;

        return $result;
    }

    public function setUsername($username)
    {
        return $this->originalProtocol->setUsername($username);
    }

    public function getUsername()
    {
        return $this->originalProtocol->getUsername();
    }

    public function setPassword($password)
    {
        return $this->originalProtocol->setPassword($password);
    }

    public function getPassword()
    {
        return $this->originalProtocol->getPassword();
    }
}
