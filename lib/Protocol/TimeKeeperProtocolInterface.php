<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Protocol;

interface TimeKeeperProtocolInterface
{
    public function getStartTime(): ?int;

    public function connect();

    public function disconnect();
}
