<?php

/**
 * @copyright   Copyright (c) 2026 Magenettic
 * @author      Eliel de Paula <eliel@magenettic.com>
 * @license     MIT
 */

declare(strict_types=1);

namespace Magenettic\IpWhitelist\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public const XML_PATH_ENABLED = 'admin/security/ip_whitelist_enabled';
    public const XML_PATH_ALLOWED_IPS = 'admin/security/ip_whitelist_allowed_ips';

    /**
     * Check if IP whitelist is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED
        );
    }

    /**
     * Get list of allowed IPs.
     *
     * @return array
     */
    public function getAllowedIps(): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_IPS
        );

        return $this->parseList($value);
    }

    /**
     * Check if IP is allowed.
     *
     * @param string $ip
     * @return bool
     */
    public function isIpAllowed(string $ip): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $allowedIps = $this->getAllowedIps();

        if (empty($allowedIps)) {
            return false;
        }

        return in_array($ip, $allowedIps, true);
    }

    /**
     * Parse newline-separated list into array.
     *
     * @param string|null $value
     * @return array
     */
    private function parseList(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $lines = explode("\n", $value);
        $result = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $result[] = $trimmed;
            }
        }

        return $result;
    }
}
