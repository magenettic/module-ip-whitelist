<?php

/**
 * @copyright   Copyright (c) 2026 Magenettic
 * @author      Eliel de Paula <eliel@magenettic.com>
 * @license     MIT
 */

declare(strict_types=1);

namespace Magenettic\IpWhitelist\Plugin;

use Magento\Backend\Model\Auth;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magenettic\IpWhitelist\Helper\Data;

class AdminLoginPlugin
{

    /**
     * Class constructor.
     * @param Data $helper
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        private readonly Data $helper,
        private readonly RemoteAddress $remoteAddress
    ) {}

    /**
     * Check if the client IP is allowed.
     *
     * @param Auth $subject
     * @param string $username
     * @param string $password
     * @return void
     * @throws AuthenticationException
     */
    public function beforeLogin(Auth $subject, string $username, string $password): void
    {
        if (!$this->helper->isEnabled()) {
            return;
        }

        $clientIp = $this->remoteAddress->getRemoteAddress();

        if ($clientIp === false) {
            throw new AuthenticationException(
                __('Unable to determine your IP address. Access denied.')
            );
        }

        if (!$this->helper->isIpAllowed($clientIp)) {
            throw new AuthenticationException(
                __('Access denied. Your IP address is not allowed.')
            );
        }
    }
}
