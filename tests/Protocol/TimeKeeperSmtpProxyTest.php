<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Tests\Protocol;

use PHPUnit\Framework\TestCase;
use Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxy;
use Zend\Mail\Protocol\Smtp as ZendProtocolSmtp;

/**
 * @covers \Slam\Zend\Mail\Protocol\TimeKeeperSmtpProxy
 */
final class TimeKeeperSmtpProxyTest extends TestCase
{
    /**
     * @dataProvider provideSmtpConosciuti
     */
    public function testProxyDeiMetodiPubbliciEccettoQuit(string $smtpClass)
    {
        $zendSmtp = $this->createMock($smtpClass);

        $methods = [
            'setMaximumLog'         => 999,
            'getMaximumLog'         => null,
            'getRequest'            => null,
            'getResponse'           => null,
            'getLog'                => null,
            'resetLog'              => null,
            'connect'               => null,
            'helo'                  => \uniqid(),
            'hasSession'            => null,
            'mail'                  => \uniqid(),
            'rcpt'                  => \uniqid(),
            'data'                  => \uniqid(),
            'rset'                  => null,
            'noop'                  => null,
            'vrfy'                  => \uniqid(),
            'quit'                  => null,
            'auth'                  => null,
            'disconnect'            => null,
        ];

        if (ZendProtocolSmtp::class !== $smtpClass) {
            $methods['setUsername'] = \uniqid();
            $methods['getUsername'] = null;
            $methods['setPassword'] = \uniqid();
            $methods['getPassword'] = null;
        }

        foreach ($methods as $methodName => $argument) {
            $invocationMocker = $zendSmtp->expects($this->once());
            $invocationMocker->method($methodName);

            if (null !== $argument) {
                $invocationMocker->with($this->identicalTo($argument));
                $invocationMocker->willReturn($argument);
            }
        }

        $zendSmtp
            ->expects($this->once())
            ->method('setUseCompleteQuit')
            ->with($this->identicalTo(false))
        ;

        $protocol = new TimeKeeperSmtpProxy($zendSmtp);
        foreach ($methods as $methodName => $argument) {
            $this->assertSame($argument, $protocol->{$methodName}($argument));
        }
    }

    public function provideSmtpConosciuti()
    {
        return [
            [ZendProtocolSmtp\Auth\Crammd5::class],
            [ZendProtocolSmtp\Auth\Login::class],
            [ZendProtocolSmtp\Auth\Plain::class],
            [ZendProtocolSmtp::class],
        ];
    }

    public function testConsideraLoStartTime()
    {
        $protocol = new TimeKeeperSmtpProxy($this->createMock(ZendProtocolSmtp::class));

        $this->assertNull($protocol->getStartTime());

        $protocol->connect();
        $protocol->helo();

        $this->assertGreaterThan(0, $protocol->getStartTime());

        $protocol->disconnect();

        $this->assertNull($protocol->getStartTime());
    }
}
