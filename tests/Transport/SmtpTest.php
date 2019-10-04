<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Tests\Transport;

use PHPUnit\Framework\TestCase;
use Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxy;
use Slam\Zend\Mail\Transport\Smtp;
use Zend\Mail\Message;

/**
 * @covers \Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxyDelegatorFactory
 * @covers \Slam\Zend\Mail\Transport\Smtp
 */
final class SmtpTest extends TestCase
{
    public function testProtocolsAreProxied(): void
    {
        $transport = new Smtp();

        $pluginManager = $transport->getPluginManager();

        static::assertInstanceOf(TimeKeeperSmtpProxy::class, $pluginManager->get('smtp'));
    }

    public function testAutomaticallyDisconnectAndRiconnectBetweenTwoConsecutivesSendsIfTooMuchTimeIsPassedInOrderToAvoidReuseTimeLimitBond(): void
    {
        $message = new Message();
        $message
            ->addTo('email@example.com')
            ->setSender('email@example.com', \uniqid())
        ;

        $protocol = new TestAsset\ProtocolSmtp();

        $transport = new Smtp();
        $transport->setConnection($protocol);

        static::assertGreaterThan(0, $transport->getReuseTimeLimit());

        $transport->setReuseTimeLimit(0);

        static::assertSame(0, $protocol->getDisconnectCount());

        $transport->send($message);

        static::assertSame(0, $protocol->getDisconnectCount());

        $transport->send($message);

        static::assertSame(1, $protocol->getDisconnectCount());

        $transport->send($message);

        static::assertSame(2, $protocol->getDisconnectCount());

        $transport->setReuseTimeLimit(999);

        $transport->send($message);

        static::assertSame(2, $protocol->getDisconnectCount());
    }
}
