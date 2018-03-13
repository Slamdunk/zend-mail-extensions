<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Tests\Transport;

use PHPUnit\Framework\TestCase;
use Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxy;
use Slam\Zend\Mail\Transport\Smtp;
use Zend\Mail\Message;

/**
 * @covers \Slam\Zend\Mail\Transport\Smtp
 * @covers \Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxyDelegatorFactory
 */
final class SmtpTest extends TestCase
{
    public function testProtocolloPersonalizzato()
    {
        $transport = new Smtp();

        $pluginManager = $transport->getPluginManager();

        $this->assertInstanceOf(TimeKeeperSmtpProxy::class, $pluginManager->get('smtp'));
    }

    public function testDisconnetteERiconnetteDaSoloTraDueInviiSuccessiviSeEPassatoTroppoTempoPerEvitareIlReuseTimeLimitDiPostfix()
    {
        $message = new Message();
        $message
            ->addTo('email@example.com')
            ->setSender('email@example.com', \uniqid())
        ;

        $protocol = new TestAsset\ProtocolSmtp();

        $transport = new Smtp();
        $transport->setConnection($protocol);

        $transport->reuseTimeLimit = 0;

        $this->assertSame(0, $protocol->getDisconnectCount());

        $transport->send($message);

        $this->assertSame(0, $protocol->getDisconnectCount());

        $transport->send($message);

        $this->assertSame(1, $protocol->getDisconnectCount());

        $transport->send($message);

        $this->assertSame(2, $protocol->getDisconnectCount());

        $transport->reuseTimeLimit = 999;

        $transport->send($message);

        $this->assertSame(2, $protocol->getDisconnectCount());
    }
}
