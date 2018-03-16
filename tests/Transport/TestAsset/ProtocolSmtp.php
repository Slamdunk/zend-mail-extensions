<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Tests\Transport\TestAsset;

use Slam\Zend\Mail\Protocol\TimeKeeperProtocolInterface;
use Zend\Mail\Protocol\Smtp as ZendProtocolSmtp;

final class ProtocolSmtp extends ZendProtocolSmtp implements TimeKeeperProtocolInterface
{
    private $startTime;

    private $disconnectCount = 0;

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function connect(): bool
    {
        $result = parent::connect();

        $this->startTime = \time();

        return $result;
    }

    public function disconnect(): void
    {
        parent::disconnect();

        $this->startTime = null;

        ++$this->disconnectCount;
    }

    public function getDisconnectCount(): int
    {
        return $this->disconnectCount;
    }

    protected function _connect($remote)
    {
        return true;
    }

    protected function _send($request)
    {
    }

    protected function _expect($code, $timeout = null)
    {
        return '';
    }
}
