# IBSNGrpc

IBSNGrpc is a PHP library designed specifically for interacting with [AfazTech/IBSng](https://github.com/AfazTech/IBSng) subscriber management system via XML-RPC. Note that this package is built for this specific version of IBSng and may not be compatible with other versions.

This library provides a comprehensive set of methods for managing various aspects of the IBSng system. Contributions and improvements from the community are welcome!

<a href="http://www.coffeete.ir/afaz">
  <img src="http://www.coffeete.ir/images/buttons/lemonchiffon.png" width="260" />
</a>

## Table of Contents
- [Installation](#installation)
- [Requirements](#requirements)
- [Usage](#usage)
- [Available Methods](#available-methods)
  - [IP Pool Management](#ip-pool-management)
  - [User Management](#user-management)
  - [Messaging](#messaging)
  - [RAS Management](#ras-management)
  - [VoIP Tariff Management](#voip-tariff-management)
  - [Definitions Management](#definitions-management)
  - [Permission Management](#permission-management)
  - [Bandwidth Management](#bandwidth-management)
  - [Admin Management](#admin-management)
  - [Snapshot Management](#snapshot-management)
  - [Report Management](#report-management)
  - [Charge Management](#charge-management)
  - [Group Management](#group-management)
  - [Miscellaneous](#miscellaneous)
- [License](#license)
- [Error Handling](#error-handling)

## Installation

To install this library using Composer, run the following command:

```bash
composer require AfazTech/IBSNGrpc
```

## Requirements

- PHP 7.4 or higher
- PHP extensions:
  - curl
  - xmlrpc
- Composer (installed globally)

Verify required extensions with:
```bash
php -m | grep -E 'curl|xmlrpc'
```

## Usage

```php
require 'vendor/autoload.php';

use IBSNGrpc\Client;

// Initialize client
$ip = 'IBSNG_SERVER_IP';
$port = 'IBSNG_SERVER_PORT'; // Typically 1235
$username = 'USERNAME'; // e.g., 'system'
$password = 'PASSWORD';
$authType = 'ADMIN'; // Optional, default is 'ADMIN'

$ibsng = new Client($ip, $port, $username, $password, $authType);

// Example: Add a new user
$response = $ibsng->addNewUser(
    $count = 1,
    $credit = 1, 
    $group_name = '30d',
    $owner_name = 'system',
    $credit_comment = "Initial credit"
);

if ($response['success']) {
    $userId = $response['data'][0];
    echo "User created with ID: $userId";
} else {
    throw new Exception("Error: " . $response['error']);
}

// Example: Change user password
$response = $ibsng->changePassword(
    $userId,
    'newusername',
    'securepassword123'
);

if ($response['success']) {
    echo "Username and password updated successfully!";
} else {
    throw new Exception("Error: " . $response['error']);
}
```

## Available Methods

### IP Pool Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addNewIPpool` | Add a new IP pool | `$ippool_name`, `$comment` |
| `updateIPpool` | Update an existing IP pool | `$ippool_id`, `$ippool_name`, `$comment` |
| `getIPpoolNames` | Get list of all IP pool names | - |
| `getIPpoolInfo` | Get information about a specific IP pool | `$ippool_name` |
| `deleteIPpool` | Delete an IP pool | `$ippool_name` |
| `delIPfromPool` | Remove an IP from a pool | `$ippool_name`, `$ip` |
| `addIPtoPool` | Add an IP to a pool | `$ippool_name`, `$ip` |

### User Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addNewUser` | Add new user(s) | `$count`, `$credit`, `$group_name`, `$owner_name`, `$credit_comment` |
| `getUserInfoByID` | Get user info by ID | `$user_id` |
| `getUserInfoByUsername` | Get user info by username | `$username` |
| `updateUserAttrs` | Update user attributes | `$user_id`, `$attrs`, `$to_del_attrs` |
| `changeUserCredit` | Change user credit | `$user_id`, `$credit`, `$credit_comment` |
| `delUser` | Delete a user | `$user_id`, `$delete_comment`, `$del_connection_logs`, `$del_audit_logs` |
| `searchUser` | Search for users | `$conds`, `$from`, `$to`, `$order_by`, `$desc` |
| `changePassword` | Change user password | `$user_id`, `$username`, `$password` |
| `lockUser` | Lock a user | `$user_id`, `$lock_reason` |
| `unlockUser` | Unlock a user | `$user_id` |
| `changeUsergroup` | Change user's group | `$user_id`, `$new_group_name` |

### RAS Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addNewRas` | Add new RAS | `$ras_ip`, `$ras_description`, `$ras_type`, `$radius_secret`, `$comment` |
| `getRasInfo` | Get RAS information | `$ras_ip` |
| `updateRasInfo` | Update RAS information | `$ras_id`, `$ras_ip`, `$ras_description`, `$ras_type`, `$radius_secret`, `$comment` |
| `getActiveRasIPs` | Get active RAS IPs | - |
| `addIPpoolToRas` | Add IP pool to RAS | `$ras_ip`, `$ippool_name` |

### VoIP Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addNewVoIPTariff` | Add VoIP tariff | `$tariff_name`, `$comment` |
| `addVoIPTariffPrefix` | Add prefix to tariff | `$tariff_name`, `$prefix_codes`, `$prefix_names`, `$cpms`, `$free_seconds`, `$min_durations`, `$round_tos`, `$min_chargable_durations` |
| `getVoIPTariffInfo` | Get tariff info | `$tariff_name`, `$include_prefixes`, `$name_regex` |

### Bandwidth Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addBwInterface` | Add bandwidth interface | `$interface_name`, `$comment` |
| `addBwNode` | Add bandwidth node | `$interface_name`, `$parent_id`, `$rate_kbits`, `$ceil_kbits` |
| `updateBwLeaf` | Update bandwidth leaf | `$leaf_id`, `$leaf_name`, `$default_rate_kbits`, `$default_ceil_kbits`, `$total_rate_kbits`, `$total_ceil_kbits` |

### Admin Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `addNewAdmin` | Add new admin | `$username`, `$password`, `$name`, `$comment` |
| `changeAdminPassword` | Change admin password | `$admin_username`, `$new_password` |
| `lockAdmin` | Lock admin account | `$admin_username`, `$reason` |

### Report Management
| Method | Description | Parameters |
|--------|-------------|------------|
| `getOnlineUsers` | Get online users | `$normal_sort_by`, `$normal_desc`, `$voip_sort_by`, `$voip_desc`, `$conds` |
| `getConnections` | Get connection reports | `$conds`, `$from`, `$to`, `$sort_by`, `$desc` |
| `getCreditChanges` | Get credit changes | `$conds`, `$from`, `$to`, `$sort_by`, `$desc` |

## Error Handling

All methods return an array with the following structure:
```php
[
    'success' => bool,    // Whether the operation was successful
    'data' => mixed,      // Response data if successful
    'error' => string     // Error message if unsuccessful
]
```

Example error handling:
```php
$response = $ibsng->getUserInfoByID(123);

if (!$response['success']) {
    throw new Exception("Failed to get user info: " . $response['error']);
}

$userData = $response['data'];
```

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/AfazTech/IBSNGrpc/blob/main/LICENSE) file for details.