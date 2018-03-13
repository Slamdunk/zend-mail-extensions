<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Transport;

use Slam\Zend\Mail\Protocol as SlamProtocol;
use Zend\Mail\Message as ZendMessage;
use Zend\Mail\Protocol as ZendProtocol;
use Zend\Mail\Transport as ZendTransport;

final class Smtp extends ZendTransport\Smtp
{
    public $reuseTimeLimit = 270;

    public function setPluginManager(ZendProtocol\SmtpPluginManager $plugins)
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

    public function send(ZendMessage $message)
    {
        $connection = $this->getConnection();

        if (
                $connection instanceof SlamProtocol\TimeKeeperProtocolInterface
            and $this->reuseTimeLimit >= 0
            and $connection->getStartTime()
            and ((\time() - $connection->getStartTime()) >= $this->reuseTimeLimit)
        ) {
            $connection->disconnect();
        }

        return parent::send($message);
    }
}
