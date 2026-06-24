# Magenettic_IpWhitelist

IP whitelist for admin login in Magento 2.4.x

## Overview

This module restricts admin panel access to a configurable list of allowed IP addresses, adding an extra security layer for your Magento store.

## Features

### 1. Enable/Disable IP Whitelist

Toggle the IP filter on or off from the admin configuration panel.

### 2. Allowed IP Addresses (Whitelist)

Define a list of IP addresses that are permitted to access the admin panel. One IP per line.

## Configuration

Navigate to: **Stores > Settings > Configuration > Advanced > Admin > Security**

| Setting | Description |
|---------|-------------|
| **Enable IP Whitelist** | Enable/disable the IP whitelist filter |
| **Allowed IP Addresses (Whitelist)** | Enter IP addresses one per line (e.g., `192.168.1.1`) |

## Requirements

- Magento 2.4.x
- PHP 8.2+

## Installation

### Composer Installation

```bash
composer require magenettic/module-ip-whitelist
```

### Manual Installation

```bash
bin/magento module:enable Magenettic_IpWhitelist
bin/magento setup:upgrade
bin/magento cache:flush
```

## Validation Flow

```
Admin login attempt
    |
    v
Is IP Whitelist enabled?
    |-- No  --> Allow login (proceed normally)
    |
    +-- Yes --> Check if client IP is in allowed list
                  |-- Yes --> Allow login
                  +-- No  --> Reject with "Access denied" message
```

## Module Structure

```
Magenettic/IpWhitelist/
├── Helper/Data.php                    # Configuration helper
├── Plugin/AdminLoginPlugin.php        # Admin login validation
├── Test/Unit/
│   ├── Helper/DataTest.php            # Helper unit tests
│   └── Plugin/AdminLoginPluginTest.php # Plugin unit tests
├── etc/
│   ├── adminhtml/system.xml           # Configuration fields
│   ├── adminhtml/di.xml               # Plugin registration
│   ├── config.xml                     # Default configuration
│   └── module.xml                     # Module declaration
├── i18n/
│   └── pt_BR.csv                      # Portuguese (Brazil) translation
├── composer.json
├── README.md
└── registration.php
```

## Testing

### Unit Tests

```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Magenettic/IpWhitelist/Test/Unit/
```

### Test Coverage

| Component | Tests | Coverage |
|-----------|-------|----------|
| Helper\Data | 9 tests | isEnabled, getAllowedIps, isIpAllowed |
| Plugin\AdminLoginPlugin | 4 tests | beforeLogin (enabled/disabled, IP allowed/blocked) |

## License

MIT
