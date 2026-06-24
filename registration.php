<?php

/**
 * @copyright   Copyright (c) 2026 Magenettic
 * @author      Eliel de Paula <elieldepaula@gmail.com>
 * @license     MIT
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Magenettic_IpWhitelist',
    __DIR__
);
