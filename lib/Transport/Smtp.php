<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Transport;

use Slam\Zend\Mail\Protocol as SlamProtocol;
use Zend\Mail\Message as ZendMessage;
use Zend\Mail\Protocol as ZendProtocol;
use Zend\Mail\Transport as ZendTransport;

final class Smtp extends ZendTransport\Smtp
{
    /**
     * @var int
     */
    private $reuseTimeLimit = 270;

    public function setPluginManager(ZendProtocol\SmtpPluginManager $plugins): ZendTransport\TransportInterface
    {
        $plugins->configure([
            'delegators' => [
                ZendProtocol\Smtp\Auth\Crammd5::class   => [SlamProtocol\TimeKeeperSmtpProxyDelegatorFactory::class],
                ZendProtocol\Smtp\Auth\Login::class     => [SlamProtocol\TimeKeeperSmtpProxyDelegatorFactory::class],
                ZendProtocol\Smtp\Auth\Plain::class     => [SlamProtocol\TimeKeeperSmtpProxyDelegatorFactory::class],
                ZendProtocol\Smtp::class                => [SlamProtocol\TimeKeeperSmtpProxyDelegatorFactory::class],
            ],
        ]);

        return parent::setPluginManager($plugins);
    }

    public function setReuseTimeLimit(int $reuseTimeLimit): void
    {
        $this->reuseTimeLimit = $reuseTimeLimit;
    }

    public function getReuseTimeLimit(): int
    {
        return $this->reuseTimeLimit;
    }

    public function send(ZendMessage $message): void
    {
        $connection = $this->getConnection();

        if ($connection instanceof SlamProtocol\TimeKeeperProtocolInterface
            && $this->reuseTimeLimit >= 0
            && $connection->getStartTime()
            && ((\time() - $connection->getStartTime()) >= $this->reuseTimeLimit)
        ) {
            $connection->disconnect();
        }

        parent::send($message);
    }
}
