<?php

/**
 * @copyright   Copyright (c) 2026 Magenettic
 * @author      Eliel de Paula <elieldepaula@gmail.com>
 * @license     MIT
 */

declare(strict_types=1);

namespace Magenettic\IpWhitelist\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magenettic\IpWhitelist\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private Data $helper;
    private ScopeConfigInterface|MockObject $scopeConfig;
    private Context|MockObject $context;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfig);

        $this->helper = new Data($this->context);
    }

    public function testIsEnabledReturnsTrue(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(true);

        $this->assertTrue($this->helper->isEnabled());
    }

    public function testIsEnabledReturnsFalse(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(false);

        $this->assertFalse($this->helper->isEnabled());
    }

    public function testGetAllowedIpsReturnsParsedList(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn("192.168.1.1\n10.0.0.1\n");

        $result = $this->helper->getAllowedIps();

        $this->assertEquals(['192.168.1.1', '10.0.0.1'], $result);
    }

    public function testGetAllowedIpsReturnsEmptyArrayForNull(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(null);

        $result = $this->helper->getAllowedIps();

        $this->assertEquals([], $result);
    }

    public function testGetAllowedIpsIgnoresEmptyLines(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn("192.168.1.1\n\n10.0.0.1\n   \n");

        $result = $this->helper->getAllowedIps();

        $this->assertEquals(['192.168.1.1', '10.0.0.1'], $result);
    }

    public function testIsIpAllowedReturnsTrueWhenDisabled(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(false);

        $this->assertTrue($this->helper->isIpAllowed('192.168.1.1'));
    }

    public function testIsIpAllowedReturnsFalseWhenNoIpsConfigured(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(true);

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(null);

        $this->assertFalse($this->helper->isIpAllowed('192.168.1.1'));
    }

    public function testIsIpAllowedReturnsTrueForMatchingIp(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(true);

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn("192.168.1.1\n10.0.0.1\n");

        $this->assertTrue($this->helper->isIpAllowed('192.168.1.1'));
    }

    public function testIsIpAllowedReturnsFalseForNonMatchingIp(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Data::XML_PATH_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(true);

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(Data::XML_PATH_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn("192.168.1.1\n10.0.0.1\n");

        $this->assertFalse($this->helper->isIpAllowed('192.168.2.1'));
    }
}
