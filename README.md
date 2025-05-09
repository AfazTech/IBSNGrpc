# IBSNGrpc

IBSNGrpc is a PHP library that allows you to interact with the IBSng subscriber management system via XML-RPC. This library provides a comprehensive set of methods for managing various aspects of the IBSng system.

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

$ip = 'IBSNG_SERVER_IP';
$port = 'IBSNG_SERVER_PORT'; // Typically 1235
$username = 'USERNAME'; // e.g., 'system'
$password = 'PASSWORD';

$ibsng = new Client($ip, $port, $username, $password);

// Example: Add a new user
$credit = 1;
$count = 1;
$group = "30d";
$owner = "system";
$response = $ibsng->addNewUser(
    $count = 1,
    $credit = 1, 
    $group_name = '30d',
    $owner_name = 'system'
);

if ($response['ok']) {
    $userId = $response['result'][0];
    echo "User created with ID: $userId";
} else {
    die("Error: " . $response['message']);
}

$response = $ibsng->changePassword(
    $userId,
    'newusername',
    'securepassword123'
);

if ($response['ok']) {
    echo "Username and password updated successfully!";
} else {
    die("Error: " . $response['message']);
}
```

## Available Methods

### IP Pool Management
| Method | Description |
|--------|-------------|
| `addNewIPpool($ippool_name, $comment = '')` | Add a new IP pool |
| `updateIPpool($ippool_id, $ippool_name, $comment = '')` | Update an existing IP pool |
| `getIPpoolNames()` | Get list of all IP pool names |
| `getIPpoolInfo($ippool_name)` | Get information about a specific IP pool |
| `deleteIPpool($ippool_name)` | Delete an IP pool |
| `delIPfromPool($ippool_name, $ip)` | Remove an IP from a pool |
| `addIPtoPool($ippool_name, $ip)` | Add an IP to a pool |

### User Management
| Method | Description |
|--------|-------------|
| `addNewUser($count, $credit, $group_name, $owner_name, $credit_comment = "")` | Add new user(s) |
| `getUserInfoByID($user_id)` | Get user information by ID |
| `updateUserAttrs($user_id, $attrs, $to_del_attrs = [])` | Update user attributes |
| `changeUserCredit($user_id, $credit, $credit_comment = '')` | Change user credit |
| `delUser($user_id, $delete_comment = '', $del_connection_logs = false, $del_audit_logs = false)` | Delete a user |
| `searchUser($conds = [], $from = 0, $to = 10, $order_by = '', $desc = false)` | Search for users |

### Messaging
| Method | Description |
|--------|-------------|
| `postMessageToUser($user_ids, $message)` | Send message to user(s) |
| `getUserMessages($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false)` | Get user messages |

### RAS Management
| Method | Description |
|--------|-------------|
| `addNewRas($ras_ip, $ras_description, $ras_type, $radius_secret, $comment = '')` | Add new RAS |
| `getRasInfo($ras_ip)` | Get RAS information |
| `updateRasInfo($ras_id, $ras_ip, $ras_description, $ras_type, $radius_secret, $comment = '')` | Update RAS information |

### VoIP Tariff Management
| Method | Description |
|--------|-------------|
| `addNewVoIPTariff($tariff_name, $comment = '')` | Add new VoIP tariff |
| `listVoIPTariffs()` | List all VoIP tariffs |
| `deleteVoIPTariff($tariff_name)` | Delete a VoIP tariff |

### Definitions Management
| Method | Description |
|--------|-------------|
| `getAllDefs()` | Get all definitions |
| `saveDefs($defs)` | Save definitions |

### Permission Management
| Method | Description |
|--------|-------------|
| `hasPerm($perm_name, $admin_username)` | Check if admin has permission |
| `getPermsOfAdmin($admin_username)` | Get permissions of an admin |

### Bandwidth Management
| Method | Description |
|--------|-------------|
| `addBwInterface($interface_name, $comment = '')` | Add bandwidth interface |
| `getBwInterfaces()` | Get all bandwidth interfaces |

### Admin Management
| Method | Description |
|--------|-------------|
| `addNewAdmin($username, $password, $name, $comment = '')` | Add new admin |
| `getAdminInfo($admin_username)` | Get admin information |

### Snapshot Management
| Method | Description |
|--------|-------------|
| `getRealTimeSnapShot($name)` | Get real-time snapshot |
| `getOnlinesSnapShot($conds = [], $type = '')` | Get online users snapshot |

### Report Management
| Method | Description |
|--------|-------------|
| `getConnections($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false)` | Get connection reports |
| `getCreditChanges($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false)` | Get credit change reports |

### Charge Management
| Method | Description |
|--------|-------------|
| `addNewCharge($name, $comment = '', $charge_type = '', $visible_to_all = true)` | Add new charge |
| `listCharges($charge_type = '')` | List all charges |

### Group Management
| Method | Description |
|--------|-------------|
| `addNewGroup($group_name, $comment = '')` | Add new group |
| `listGroups()` | List all groups |

### Miscellaneous
| Method | Description |
|--------|-------------|
| `getConsoleBuffer()` | Get console buffer |

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/AfazTech/IBSNGrpc/blob/main/LICENSE) file for details.