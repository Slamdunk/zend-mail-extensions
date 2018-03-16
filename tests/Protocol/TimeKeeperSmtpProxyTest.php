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
     * @dataProvider provideKnownSmtpTypes
     */
    public function testProxyPublicMethodsExceptQuitOne(string $smtpClass): void
    {
        $zendSmtp = $this->createMock($smtpClass);

        $methods = [
            'setMaximumLog'         => [[999], null],
            'getMaximumLog'         => [[], 999],
            'getRequest'            => [[], null],
            'getResponse'           => [[], null],
            'getLog'                => [[], ''],
            'resetLog'              => [[], null],
            'connect'               => [[], true],
            'helo'                  => [[\uniqid()], null],
            'hasSession'            => [[], false],
            'mail'                  => [[\uniqid()], null],
            'rcpt'                  => [[\uniqid()], null],
            'data'                  => [[\uniqid()], null],
            'rset'                  => [[], null],
            'noop'                  => [[], null],
            'vrfy'                  => [[\uniqid()], null],
            'quit'                  => [[], null],
            'auth'                  => [[], null],
            'disconnect'            => [[], null],
        ];

        if (ZendProtocolSmtp::class !== $smtpClass) {
            $methods['setUsername'] = [['foo'], $zendSmtp];
            $methods['getUsername'] = [[], 'foo'];
            $methods['setPassword'] = [['bar'], $zendSmtp];
            $methods['getPassword'] = [[], 'bar'];
        }

        foreach ($methods as $methodName => $blob) {
            list($arguments, $return) = $blob;
            $invocationMocker         = $zendSmtp->expects($this->once());
            $invocationMocker->method($methodName);

            if (\count($arguments)) {
                $invocationMocker->with(...$arguments);
            }

            $invocationMocker->willReturn($return);
        }

        $zendSmtp
            ->expects($this->once())
            ->method('setUseCompleteQuit')
            ->with($this->identicalTo(false))
        ;

        $protocol = new TimeKeeperSmtpProxy($zendSmtp);
        foreach ($methods as $methodName => $blob) {
            list($arguments, $return) = $blob;
            $this->assertSame($return, $protocol->{$methodName}(...$arguments));
        }
    }

    public function provideKnownSmtpTypes()
    {
        return [
            [ZendProtocolSmtp\Auth\Crammd5::class],
            [ZendProtocolSmtp\Auth\Login::class],
            [ZendProtocolSmtp\Auth\Plain::class],
            [ZendProtocolSmtp::class],
        ];
    }

    public function testAccountTheStartTime()
    {
        $zendProtocolSmtpMock = $this->createMock(ZendProtocolSmtp::class);
        $zendProtocolSmtpMock->expects($this->once())->method('connect')->willReturn(true);
        $zendProtocolSmtpMock->expects($this->once())->method('disconnect');
        $protocol = new TimeKeeperSmtpProxy($zendProtocolSmtpMock);

        $this->assertNull($protocol->getStartTime());

        $protocol->connect();
        $protocol->helo();

        $this->assertGreaterThan(0, $protocol->getStartTime());

        $protocol->disconnect();

        $this->assertNull($protocol->getStartTime());
    }
}
