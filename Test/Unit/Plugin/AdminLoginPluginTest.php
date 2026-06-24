<?php

/**
 * @copyright   Copyright (c) 2026 Magenettic
 * @author      Eliel de Paula <elieldepaula@gmail.com>
 * @license     MIT
 */

declare(strict_types=1);

namespace Magenettic\IpWhitelist\Test\Unit\Plugin;

use Magento\Backend\Model\Auth;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magenettic\IpWhitelist\Helper\Data;
use Magenettic\IpWhitelist\Plugin\AdminLoginPlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminLoginPluginTest extends TestCase
{
    private AdminLoginPlugin $plugin;
    private Data|MockObject $helperMock;
    private RemoteAddress|MockObject $remoteAddressMock;
    private Auth|MockObject $authMock;

    protected function setUp(): void
    {
        $this->helperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->remoteAddressMock = $this->getMockBuilder(RemoteAddress::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authMock = $this->getMockBuilder(Auth::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new AdminLoginPlugin($this->helperMock, $this->remoteAddressMock);
    }

    public function testBeforeLoginReturnsEarlyWhenDisabled(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->remoteAddressMock->expects($this->never())
            ->method('getRemoteAddress');

        $this->plugin->beforeLogin($this->authMock, 'admin', 'password');
    }

    public function testBeforeLoginThrowsExceptionWhenRemoteAddressFails(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->remoteAddressMock->expects($this->once())
            ->method('getRemoteAddress')
            ->willReturn(false);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Unable to determine your IP address. Access denied.');

        $this->plugin->beforeLogin($this->authMock, 'admin', 'password');
    }

    public function testBeforeLoginThrowsExceptionWhenIpNotAllowed(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->remoteAddressMock->expects($this->once())
            ->method('getRemoteAddress')
            ->willReturn('192.168.1.100');

        $this->helperMock->expects($this->once())
            ->method('isIpAllowed')
            ->with('192.168.1.100')
            ->willReturn(false);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Access denied. Your IP address is not allowed.');

        $this->plugin->beforeLogin($this->authMock, 'admin', 'password');
    }

    public function testBeforeLoginAllowsWhenIpIsAllowed(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->remoteAddressMock->expects($this->once())
            ->method('getRemoteAddress')
            ->willReturn('192.168.1.1');

        $this->helperMock->expects($this->once())
            ->method('isIpAllowed')
            ->with('192.168.1.1')
            ->willReturn(true);

        $this->plugin->beforeLogin($this->authMock, 'admin', 'password');
    }
}
