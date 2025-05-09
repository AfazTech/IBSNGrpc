<?php

declare(strict_types=1);

/**
 * @version 1.0.0
 * @author Abolfazl Majidi (MrAfaz)
 * @package IBSNGrpc
 * @license https://opensource.org/licenses/MIT
 * @link https://github.com/AfazTech/IBSNGrpc
 * 
 */

namespace IBSNGrpc;

/**
 * Client
 */
class Client {   
     
    /**
     * authName
     *
     * @var mixed
     */
    private $authName;

    /**
     * authPass
     *
     * @var mixed
     */
        
    /**
     * authPass
     *
     * @var mixed
     */
    private $authPass;
        
    /**
     * authType
     *
     * @var mixed
     */
    private $authType;
        
    /**
     * xmlrpcUrl
     *
     * @var mixed
     */
    private $xmlrpcUrl;
    
    /**
     * __construct
     *
     * @param  mixed $ip
     * @param  mixed $port
     * @param  mixed $username
     * @param  mixed $password
     * @param  mixed $authType
     * @return array
     */
    public function __construct($ip, $port, $username, $password, $authType = 'ADMIN') {
        $this->authName = $username;
        $this->authPass = $password;
        $this->authType = $authType;
        $this->xmlrpcUrl = "http://{$ip}:{$port}/xmlrpc";
    }
    
    /**
     * sendXmlRpcRequest
     *
     * @param  mixed $method
     * @param  mixed $params
     * @return array
     */
    private function sendXmlRpcRequest(string $method, array $params = []): array
    {
        $ch = null;
        try {
            if (empty($method)) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Method name cannot be empty'
                ];
            }
    
            $requestParams = array_merge([
                'auth_name' => $this->authName,
                'auth_pass' => $this->authPass,
                'auth_type' => $this->authType
            ], $params);
    
            $request = xmlrpc_encode_request($method, $requestParams);
            if ($request === false) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Failed to encode XML-RPC request'
                ];
            }
    
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->xmlrpcUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_HTTPHEADER => ['Content-Type: text/xml'],
                CURLOPT_TIMEOUT => 30
            ]);
    
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Connection error: ' . curl_error($ch)
                ];
            }
    
            $decoded = xmlrpc_decode($response);
            if (is_array($decoded) && xmlrpc_is_fault($decoded)) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => $decoded['faultString']
                ];
            }
    
            return [
                'success' => true,
                'data' => $decoded,
                'error' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        } finally {
            if ($ch !== null) {
                curl_close($ch);
            }
        }
    }

    // =============================================
    // IP Pool Management Methods
    // =============================================
    
    /**
     * addNewIPpool
     *
     * @param  mixed $ippool_name
     * @param  mixed $comment
     * @return array
     */
    public function addNewIPpool($ippool_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('ippool.addNewIPpool', [
            'ippool_name' => $ippool_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * updateIPpool
     *
     * @param  mixed $ippool_id
     * @param  mixed $ippool_name
     * @param  mixed $comment
     * @return array
     */
    public function updateIPpool($ippool_id, $ippool_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('ippool.updateIPpool', [
            'ippool_id' => $ippool_id,
            'ippool_name' => $ippool_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * getIPpoolNames
     *
     * @return array
     */
    public function getIPpoolNames(): array {
        return $this->sendXmlRpcRequest('ippool.getIPpoolNames');
    }
    
    /**
     * getIPpoolInfo
     *
     * @param  mixed $ippool_name
     * @return array
     */
    public function getIPpoolInfo($ippool_name): array  {
        return $this->sendXmlRpcRequest('ippool.getIPpoolInfo', [
            'ippool_name' => $ippool_name
        ]);
    }
    
    /**
     * deleteIPpool
     *
     * @param  mixed $ippool_name
     * @return array
     */
    public function deleteIPpool($ippool_name): array {
        return $this->sendXmlRpcRequest('ippool.deleteIPpool', [
            'ippool_name' => $ippool_name
        ]);
    }
    
    /**
     * delIPfromPool
     *
     * @param  mixed $ippool_name
     * @param  mixed $ip
     * @return array
     */
    public function delIPfromPool($ippool_name, $ip): array {
        return $this->sendXmlRpcRequest('ippool.delIPfromPool', [
            'ippool_name' => $ippool_name,
            'ip' => $ip
        ]);
    }
    
    /**
     * addIPtoPool
     *
     * @param  mixed $ippool_name
     * @param  mixed $ip
     * @return array
     */
    public function addIPtoPool($ippool_name, $ip): array {
        return $this->sendXmlRpcRequest('ippool.addIPtoPool', [
            'ippool_name' => $ippool_name,
            'ip' => $ip
        ]);
    }

    // =============================================
    // User Management Methods
    // =============================================
    
    /**
     * addNewUser
     *
     * @param  mixed $count
     * @param  mixed $credit
     * @param  mixed $group_name
     * @param  mixed $owner_name
     * @param  mixed $credit_comment
     * @return array
     */
    public function addNewUser($count, $credit, $group_name, $owner_name,$credit_comment = ""): array {
        return $this->sendXmlRpcRequest('user.addNewUsers', [
            'count' => $count,
            'credit' => $credit,
            'group_name' => $group_name,
            'owner_name' =>$owner_name,
            'credit_comment' => $credit_comment
        ]);
    }
    
    /**
     * setUserAttribute
     *
     * @param  mixed $user_id
     * @param  mixed $attrs
     * @return array
     */
    public function setUserAttribute($user_id, $attrs): array {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => $attrs
        ]);
    }

    
    /**
     * getUserInfoByID
     *
     * @param  mixed $user_id
     * @return array
     */
    public function getUserInfoByID($user_id): array {
        return $this->sendXmlRpcRequest('user.getUserInfo', [
            'user_id' => (string)$user_id
        ]);
    }
    
    /**
     * getUserInfoByUsername
     *
     * @param  mixed $username
     * @return array
     */
    public function getUserInfoByUsername($username): array {
        return $this->sendXmlRpcRequest('user.getUserInfo', [
            'normal_username' => $username
        ]);
    }
    
    /**
     * updateUserAttrs
     *
     * @param  mixed $user_id
     * @param  mixed $attrs
     * @param  mixed $to_del_attrs
     * @return array
     */
    public function updateUserAttrs($user_id, $attrs, $to_del_attrs = []): array {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => $attrs,
            'to_del_attrs' => $to_del_attrs
        ]);
    }
    
    /**
     * checkNormalUsernameForAdd
     *
     * @param  mixed $normal_username
     * @param  mixed $current_username
     * @return array
     */
    public function checkNormalUsernameForAdd($normal_username, $current_username = ''): array {
        return $this->sendXmlRpcRequest('normal_user.checkNormalUsernameForAdd', [
            'normal_username' => $normal_username,
            'current_username' => $current_username
        ]);
    }
    
    /**
     * checkVoIPUsernameForAdd
     *
     * @param  mixed $voip_username
     * @param  mixed $current_username
     * @return array
     */
    public function checkVoIPUsernameForAdd($voip_username, $current_username = ''): array {
        return $this->sendXmlRpcRequest('voip_user.checkVoIPUsernameForAdd', [
            'voip_username' => $voip_username,
            'current_username' => $current_username
        ]);
    }
    
    /**
     * changeUserCredit
     *
     * @param  mixed $user_id
     * @param  mixed $credit
     * @param  mixed $credit_comment
     * @return array
     */
    public function changeUserCredit($user_id, $credit, $credit_comment = ''): array {
        return $this->sendXmlRpcRequest('user.changeCredit', [
            'user_id' => (string)$user_id,
            'credit' => $credit,
            'credit_comment' => $credit_comment
        ]);
    }
    
    /**
     * delUser
     *
     * @param  mixed $user_id
     * @param  mixed $delete_comment
     * @param  mixed $del_connection_logs
     * @param  mixed $del_audit_logs
     * @return array
     */
    public function delUser($user_id, $delete_comment = '', $del_connection_logs = false, $del_audit_logs = false): array {
        return $this->sendXmlRpcRequest('user.delUser', [
            'user_id' => (string)$user_id,
            'delete_comment' => $delete_comment,
            'del_connection_logs' => $del_connection_logs,
            'del_audit_logs' => $del_audit_logs
        ]);
    }
    
    /**
     * killUser
     *
     * @param  mixed $user_id
     * @param  mixed $ras_ip
     * @param  mixed $unique_id_val
     * @param  mixed $kill
     * @return array
     */
    public function killUser($user_id, $ras_ip, $unique_id_val, $kill = true): array {
        return $this->sendXmlRpcRequest('user.killUser', [
            'user_id' => (string)$user_id,
            'ras_ip' => $ras_ip,
            'unique_id_val' => $unique_id_val,
            'kill' => $kill
        ]);
    }
    
    /**
     * searchAddUserSaves
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $order_by
     * @param  mixed $desc
     * @return array
     */
    public function searchAddUserSaves($conds = [], $from = 0, $to = 10, $order_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('addUserSave.searchAddUserSaves', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'order_by' => $order_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * deleteAddUserSaves
     *
     * @param  mixed $add_user_save_ids
     * @return array
     */
    public function deleteAddUserSaves($add_user_save_ids): array {
        return $this->sendXmlRpcRequest('addUserSave.deleteAddUserSaves', [
            'add_user_save_ids' => $add_user_save_ids
        ]);
    }
    
    /**
     * changeNormalUserPassword
     *
     * @param  mixed $normal_username
     * @param  mixed $password
     * @param  mixed $old_password
     * @return array
     */
    public function changeNormalUserPassword($normal_username, $password, $old_password = ''): array {
        return $this->sendXmlRpcRequest('normal_user.changePassword', [
            'normal_username' => $normal_username,
            'password' => $password,
            'old_password' => $old_password
        ]);
    }    
    /**
     * changePassword
     *
     * @param  mixed $user_id
     * @param  mixed $username
     * @param  mixed $password
     * @return array
     */
    public function changePassword($user_id, $username, $password): array {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => [
                   'normal_username' => $username,
                'normal_password' => $password,
                'normal_generate_password' => false,
                'normal_generate_password_len' => 4,
                'normal_save_usernames' => false
            ],
            'to_del_attrs' => []
        ]);
    }    
    /**
     * changeVoIPUserPassword
     *
     * @param  mixed $voip_username
     * @param  mixed $password
     * @param  mixed $old_password
     * @return array
     */
    public function changeVoIPUserPassword($voip_username, $password, $old_password = ''): array {
        return $this->sendXmlRpcRequest('voip_user.changePassword', [
            'voip_username' => $voip_username,
            'password' => $password,
            'old_password' => $old_password
        ]);
    }
    
    /**
     * calcApproxDuration
     *
     * @param  mixed $user_id
     * @return array
     */
    public function calcApproxDuration($user_id): array {
        return $this->sendXmlRpcRequest('user.calcApproxDuration', [
            'user_id' => (string)$user_id
        ]);
    }
    
    /**
     * searchUser
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $order_by
     * @param  mixed $desc
     * @return array
     */
    public function searchUser($conds = [], $from = 0, $to = 10, $order_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('user.searchUser', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'order_by' => $order_by,
            'desc' => $desc
        ]);
    }

    // Custom User Methods    
    /**
     * changeUsergroup
     *
     * @param  mixed $user_id
     * @param  mixed $new_group_name
     * @return array
     */
    public function changeUsergroup($user_id, $new_group_name): array {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => ['group_name' => $new_group_name],
            'to_del_attrs' => []
        ]);
    }
    
    /**
     * changeUserMultiLogin
     *
     * @param  mixed $user_id
     * @param  mixed $multi_login
     * @return array
     */
    public function changeUserMultiLogin($user_id, $multi_login): array {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => ['multi_login' => $multi_login],
            'to_del_attrs' => []
        ]);
    }
    
    /**
     * setUserCustomField
     *
     * @param  mixed $user_id
     * @param  mixed $field_name
     * @param  mixed $field_value
     * @return array
     */
    public function setUserCustomField($user_id, $field_name, $field_value): array {
        return $this->updateUserAttrs($user_id, [
            'custom_fields' => [$field_name => $field_value]
        ]);
    }
    
    /**
     * lockUser
     *
     * @param  mixed $user_id
     * @param  mixed $lock_reason
     * @return array
     */
    public function lockUser($user_id, $lock_reason = ''): array {
        return $this->updateUserAttrs($user_id, [
            'lock' => $lock_reason ?: 'Locked by system'
        ]);
    }
    
    /**
     * unlockUser
     *
     * @param  mixed $user_id
     * @return array
     */
    public function unlockUser($user_id): array {
        return $this->updateUserAttrs($user_id, [], ['lock']);
    }
    
    /**
     * resetFirstloginUser
     *
     * @param  mixed $user_id
     * @return array
     */
    public function resetFirstloginUser($user_id): array {
        return $this->updateUserAttrs($user_id, [
            'basic_info' => ['first_login' => '']
        ], ['first_login']);
    }

    // =============================================
    // Messaging Methods
    // =============================================
    
    /**
     * multiStrGetAll
     *
     * @param  mixed $str
     * @param  mixed $left_pad
     * @return array
     */
    public function multiStrGetAll($str, $left_pad = false): array {
        return $this->sendXmlRpcRequest('util.multiStrGetAll', [
            'str' => $str,
            'left_pad' => $left_pad
        ]);
    }
    
    /**
     * postMessageToUser
     *
     * @param  mixed $user_ids
     * @param  mixed $message
     * @return array
     */
    public function postMessageToUser($user_ids, $message): array {
        return $this->sendXmlRpcRequest('message.postMessageToUser', [
            'user_ids' => $user_ids,
            'message' => $message
        ]);
    }
    
    /**
     * postMessageToAdmin
     *
     * @param  mixed $message
     * @return array
     */
    public function postMessageToAdmin($message): array {
        return $this->sendXmlRpcRequest('message.postMessageToAdmin', [
            'message' => $message
        ]);
    }
    
    /**
     * getAdminMessages
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getAdminMessages($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('message.getAdminMessages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * getUserMessages
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getUserMessages($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('message.getUserMessages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * deleteUserMessages
     *
     * @param  mixed $message_ids
     * @return array
     */
    public function deleteUserMessages($message_ids): array {
        return $this->sendXmlRpcRequest('message.deleteUserMessages', [
            'message_ids' => $message_ids
        ]);
    }
    
    /**
     * deleteAdminMessages
     *
     * @param  mixed $message_ids
     * @param  mixed $table
     * @return array
     */
    public function deleteAdminMessages($message_ids, $table): array {
        return $this->sendXmlRpcRequest('message.deleteMessages', [
            'message_ids' => $message_ids,
            'table' => $table
        ]);
    }
    
    /**
     * getUserLastMessageID
     *
     * @return array
     */
    public function getUserLastMessageID(): array {
        return $this->sendXmlRpcRequest('message.getLastMessageID');
    }

    // =============================================
    // RAS Management Methods
    // =============================================
    
    /**
     * addNewRas
     *
     * @param  mixed $ras_ip
     * @param  mixed $ras_description
     * @param  mixed $ras_type
     * @param  mixed $radius_secret
     * @param  mixed $comment
     * @return array
     */
    public function addNewRas($ras_ip, $ras_description, $ras_type, $radius_secret, $comment = ''): array {
        return $this->sendXmlRpcRequest('ras.addNewRas', [
            'ras_ip' => $ras_ip,
            'ras_description' => $ras_description,
            'ras_type' => $ras_type,
            'radius_secret' => $radius_secret,
            'comment' => $comment
        ]);
    }
    
    /**
     * getRasInfo
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function getRasInfo($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.getRasInfo', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * getActiveRasIPs
     *
     * @return array
     */
    public function getActiveRasIPs(): array {
        return $this->sendXmlRpcRequest('ras.getActiveRasIPs');
    }
    
    /**
     * getRasDescriptions
     *
     * @return array
     */
    public function getRasDescriptions(): array {
        return $this->sendXmlRpcRequest('ras.getRasDescriptions');
    }
    
    /**
     * getInActiveRases
     *
     * @return array
     */
    public function getInActiveRases(): array {
        return $this->sendXmlRpcRequest('ras.getInActiveRases');
    }
    
    /**
     * getRasTypes
     *
     * @return array
     */
    public function getRasTypes(): array {
        return $this->sendXmlRpcRequest('ras.getRasTypes');
    }
    
    /**
     * getRasAttributes
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function getRasAttributes($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.getRasAttributes', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * getRasPorts
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function getRasPorts($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.getRasPorts', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * updateRasInfo
     *
     * @param  mixed $ras_id
     * @param  mixed $ras_ip
     * @param  mixed $ras_description
     * @param  mixed $ras_type
     * @param  mixed $radius_secret
     * @param  mixed $comment
     * @return array
     */
    public function updateRasInfo($ras_id, $ras_ip, $ras_description, $ras_type, $radius_secret, $comment = ''): array {
        return $this->sendXmlRpcRequest('ras.updateRasInfo', [
            'ras_id' => $ras_id,
            'ras_ip' => $ras_ip,
            'ras_description' => $ras_description,
            'ras_type' => $ras_type,
            'radius_secret' => $radius_secret,
            'comment' => $comment
        ]);
    }
    
    /**
     * updateRasAttributes
     *
     * @param  mixed $ras_ip
     * @param  mixed $attrs
     * @return array
     */
    public function updateRasAttributes($ras_ip, $attrs): array {
        return $this->sendXmlRpcRequest('ras.updateAttributes', [
            'ras_ip' => $ras_ip,
            'attrs' => $attrs
        ]);
    }
    
    /**
     * resetRasAttributes
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function resetRasAttributes($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.resetAttributes', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * addRasPort
     *
     * @param  mixed $ras_ip
     * @param  mixed $port_name
     * @param  mixed $phone
     * @param  mixed $type
     * @param  mixed $comment
     * @return array
     */
    public function addRasPort($ras_ip, $port_name, $phone, $type, $comment = ''): array {
        return $this->sendXmlRpcRequest('ras.addPort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name,
            'phone' => $phone,
            'type' => $type,
            'comment' => $comment
        ]);
    }
    
    /**
     * getPortTypes
     *
     * @return array
     */
    public function getPortTypes(): array {
        return $this->sendXmlRpcRequest('ras.getPortTypes');
    }
    
    /**
     * delRasPort
     *
     * @param  mixed $ras_ip
     * @param  mixed $port_name
     * @return array
     */
    public function delRasPort($ras_ip, $port_name): array {
        return $this->sendXmlRpcRequest('ras.delPort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name
        ]);
    }
    
    /**
     * getRasPortInfo
     *
     * @param  mixed $ras_ip
     * @param  mixed $port_name
     * @return array
     */
    public function getRasPortInfo($ras_ip, $port_name): array {
        return $this->sendXmlRpcRequest('ras.getRasPortInfo', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name
        ]);
    }
    
    /**
     * updateRasPort
     *
     * @param  mixed $ras_ip
     * @param  mixed $port_name
     * @param  mixed $phone
     * @param  mixed $type
     * @param  mixed $comment
     * @return array
     */
    public function updateRasPort($ras_ip, $port_name, $phone, $type, $comment = ''): array {
        return $this->sendXmlRpcRequest('ras.updatePort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name,
            'phone' => $phone,
            'type' => $type,
            'comment' => $comment
        ]);
    }
    
    /**
     * deActiveRas
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function deActiveRas($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.deActiveRas', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * reActiveRas
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function reActiveRas($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.reActiveRas', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * getRasIPpools
     *
     * @param  mixed $ras_ip
     * @return array
     */
    public function getRasIPpools($ras_ip): array {
        return $this->sendXmlRpcRequest('ras.getRasIPpools', [
            'ras_ip' => $ras_ip
        ]);
    }
    
    /**
     * addIPpoolToRas
     *
     * @param  mixed $ras_ip
     * @param  mixed $ippool_name
     * @return array
     */
    public function addIPpoolToRas($ras_ip, $ippool_name): array {
        return $this->sendXmlRpcRequest('ras.addIPpoolToRas', [
            'ras_ip' => $ras_ip,
            'ippool_name' => $ippool_name
        ]);
    }
    
    /**
     * delIPpoolFromRas
     *
     * @param  mixed $ras_ip
     * @param  mixed $ippool_name
     * @return array
     */
    public function delIPpoolFromRas($ras_ip, $ippool_name): array {
        return $this->sendXmlRpcRequest('ras.delIPpoolFromRas', [
            'ras_ip' => $ras_ip,
            'ippool_name' => $ippool_name
        ]);
    }

    // =============================================
    // VoIP Tariff Management Methods
    // =============================================
    
    /**
     * addNewVoIPTariff
     *
     * @param  mixed $tariff_name
     * @param  mixed $comment
     * @return array
     */
    public function addNewVoIPTariff($tariff_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('voip_tariff.addNewTariff', [
            'tariff_name' => $tariff_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * updateVoIPTariff
     *
     * @param  mixed $tariff_name
     * @param  mixed $tariff_id
     * @param  mixed $comment
     * @return array
     */
    public function updateVoIPTariff($tariff_name, $tariff_id, $comment = ''): array {
        return $this->sendXmlRpcRequest('voip_tariff.updateTariff', [
            'tariff_name' => $tariff_name,
            'tariff_id' => $tariff_id,
            'comment' => $comment
        ]);
    }
    
    /**
     * deleteVoIPTariff
     *
     * @param  mixed $tariff_name
     * @return array
     */
    public function deleteVoIPTariff($tariff_name): array {
        return $this->sendXmlRpcRequest('voip_tariff.deleteTariff', [
            'tariff_name' => $tariff_name
        ]);
    }
    
    /**
     * addVoIPTariffPrefix
     *
     * @param  mixed $tariff_name
     * @param  mixed $prefix_codes
     * @param  mixed $prefix_names
     * @param  mixed $cpms
     * @param  mixed $free_seconds
     * @param  mixed $min_durations
     * @param  mixed $round_tos
     * @param  mixed $min_chargable_durations
     * @return array
     */
    public function addVoIPTariffPrefix($tariff_name, $prefix_codes, $prefix_names, $cpms, $free_seconds, $min_durations, $round_tos, $min_chargable_durations): array {
        return $this->sendXmlRpcRequest('voip_tariff.addPrefix', [
            'tariff_name' => $tariff_name,
            'prefix_codes' => $prefix_codes,
            'prefix_names' => $prefix_names,
            'cpms' => $cpms,
            'free_seconds' => $free_seconds,
            'min_durations' => $min_durations,
            'round_tos' => $round_tos,
            'min_chargable_durations' => $min_chargable_durations
        ]);
    }
    
    /**
     * updateVoIPTariffPrefix
     *
     * @param  mixed $tariff_name
     * @param  mixed $prefix_id
     * @param  mixed $prefix_code
     * @param  mixed $prefix_name
     * @param  mixed $cpm
     * @param  mixed $free_seconds
     * @param  mixed $min_duration
     * @return array
     */
    public function updateVoIPTariffPrefix($tariff_name, $prefix_id, $prefix_code, $prefix_name, $cpm, $free_seconds, $min_duration): array {
        return $this->sendXmlRpcRequest('voip_tariff.updatePrefix', [
            'tariff_name' => $tariff_name,
            'prefix_id' => $prefix_id,
            'prefix_code' => $prefix_code,
            'prefix_name' => $prefix_name,
            'cpm' => $cpm,
            'free_seconds' => $free_seconds,
            'min_duration' => $min_duration
        ]);
    }
    
    /**
     * deleteVoIPTariffPrefix
     *
     * @param  mixed $tariff_name
     * @param  mixed $prefix_code
     * @return array
     */
    public function deleteVoIPTariffPrefix($tariff_name, $prefix_code): array {
        return $this->sendXmlRpcRequest('voip_tariff.deletePrefix', [
            'tariff_name' => $tariff_name,
            'prefix_code' => $prefix_code
        ]);
    }
    
    /**
     * getVoIPTariffInfo
     *
     * @param  mixed $tariff_name
     * @param  mixed $include_prefixes
     * @param  mixed $name_regex
     * @return array
     */
    public function getVoIPTariffInfo($tariff_name, $include_prefixes = false, $name_regex = ''): array {
        return $this->sendXmlRpcRequest('voip_tariff.getTariffInfo', [
            'tariff_name' => $tariff_name,
            'include_prefixes' => $include_prefixes,
            'name_regex' => $name_regex
        ]);
    }
    
    /**
     * listVoIPTariffs
     *
     * @return array
     */
    public function listVoIPTariffs(): array {
        return $this->sendXmlRpcRequest('voip_tariff.listTariffs');
    }

    // =============================================
    // Definitions Management Methods
    // =============================================
    
    /**
     * getAllDefs
     *
     * @return array
     */
    public function getAllDefs(): array {
        return $this->sendXmlRpcRequest('ibs_defs.getAllDefs');
    }
    
    /**
     * saveDefs
     *
     * @param  mixed $defs
     * @return array
     */
    public function saveDefs($defs): array {
        return $this->sendXmlRpcRequest('ibs_defs.saveDefs', [
            'defs' => $defs
        ]);
    }

    // =============================================
    // Permission Management Methods
    // =============================================
    
    /**
     * hasPerm
     *
     * @param  mixed $perm_name
     * @param  mixed $admin_username
     * @return array
     */
    public function hasPerm($perm_name, $admin_username): array {
        return $this->sendXmlRpcRequest('perm.hasPerm', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username
        ]);
    }
    
    /**
     * adminCanDo
     *
     * @param  mixed $perm_name
     * @param  mixed $admin_username
     * @param  mixed $params
     * @return array
     */
    public function adminCanDo($perm_name, $admin_username, $params = []): array {
        return $this->sendXmlRpcRequest('perm.canDo', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username,
            'params' => $params
        ]);
    }
    
    /**
     * getAdminPermVal
     *
     * @param  mixed $perm_name
     * @param  mixed $admin_username
     * @return array
     */
    public function getAdminPermVal($perm_name, $admin_username): array {
        return $this->sendXmlRpcRequest('perm.getAdminPermVal', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username
        ]);
    }
    
    /**
     * getPermsOfAdmin
     *
     * @param  mixed $admin_username
     * @return array
     */
    public function getPermsOfAdmin($admin_username): array {
        return $this->sendXmlRpcRequest('perm.getPermsOfAdmin', [
            'admin_username' => $admin_username
        ]);
    }
    
    /**
     * getAllPerms
     *
     * @param  mixed $category
     * @return array
     */
    public function getAllPerms($category = ''): array {
        return $this->sendXmlRpcRequest('perm.getAllPerms', [
            'category' => $category
        ]);
    }
    
    /**
     * changePermission
     *
     * @param  mixed $admin_username
     * @param  mixed $perm_name
     * @param  mixed $perm_value
     * @return array
     */
    public function changePermission($admin_username, $perm_name, $perm_value): array {
        return $this->sendXmlRpcRequest('perm.changePermission', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name,
            'perm_value' => $perm_value
        ]);
    }
    
    /**
     * delPermission
     *
     * @param  mixed $admin_username
     * @param  mixed $perm_name
     * @return array
     */
    public function delPermission($admin_username, $perm_name): array {
        return $this->sendXmlRpcRequest('perm.delPermission', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name
        ]);
    }
    
    /**
     * deletePermissionValue
     *
     * @param  mixed $admin_username
     * @param  mixed $perm_name
     * @param  mixed $perm_value
     * @return array
     */
    public function deletePermissionValue($admin_username, $perm_name, $perm_value): array {
        return $this->sendXmlRpcRequest('perm.delPermissionValue', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name,
            'perm_value' => $perm_value
        ]);
    }
    
    /**
     * savePermsOfAdminToTemplate
     *
     * @param  mixed $admin_username
     * @param  mixed $perm_template_name
     * @return array
     */
    public function savePermsOfAdminToTemplate($admin_username, $perm_template_name): array {
        return $this->sendXmlRpcRequest('perm.savePermsOfAdminToTemplate', [
            'admin_username' => $admin_username,
            'perm_template_name' => $perm_template_name
        ]);
    }
    
    /**
     * getListOfPermTemplates
     *
     * @return array
     */
    public function getListOfPermTemplates(): array {
        return $this->sendXmlRpcRequest('perm.getListOfPermTemplates');
    }
    
    /**
     * getPermsOfTemplate
     *
     * @param  mixed $perm_template_name
     * @return array
     */
    public function getPermsOfTemplate($perm_template_name): array {
        return $this->sendXmlRpcRequest('perm.getPermsOfTemplate', [
            'perm_template_name' => $perm_template_name
        ]);
    }
    
    /**
     * loadPermTemplateToAdmin
     *
     * @param  mixed $admin_username
     * @param  mixed $perm_template_name
     * @return array
     */
    public function loadPermTemplateToAdmin($admin_username, $perm_template_name): array {
        return $this->sendXmlRpcRequest('perm.loadPermTemplateToAdmin', [
            'admin_username' => $admin_username,
            'perm_template_name' => $perm_template_name
        ]);
    }
    
    /**
     * deletePermTemplate
     *
     * @param  mixed $perm_template_name
     * @return array
     */
    public function deletePermTemplate($perm_template_name): array {
        return $this->sendXmlRpcRequest('perm.deletePermTemplate', [
            'perm_template_name' => $perm_template_name
        ]);
    }

    // =============================================
    // Bandwidth Management Methods
    // =============================================
    
    /**
     * addBwInterface
     *
     * @param  mixed $interface_name
     * @param  mixed $comment
     * @return array
     */
    public function addBwInterface($interface_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('bw.addInterface', [
            'interface_name' => $interface_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * addBwNode
     *
     * @param  mixed $interface_name
     * @param  mixed $parent_id
     * @param  mixed $rate_kbits
     * @param  mixed $ceil_kbits
     * @return array
     */
    public function addBwNode($interface_name, $parent_id, $rate_kbits, $ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.addNode', [
            'interface_name' => $interface_name,
            'parent_id' => $parent_id,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }
    
    /**
     * addBwLeaf
     *
     * @param  mixed $leaf_name
     * @param  mixed $parent_id
     * @param  mixed $default_rate_kbits
     * @param  mixed $default_ceil_kbits
     * @param  mixed $total_rate_kbits
     * @param  mixed $total_ceil_kbits
     * @return array
     */
    public function addBwLeaf($leaf_name, $parent_id, $default_rate_kbits, $default_ceil_kbits, $total_rate_kbits, $total_ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.addLeaf', [
            'leaf_name' => $leaf_name,
            'parent_id' => $parent_id,
            'default_rate_kbits' => $default_rate_kbits,
            'default_ceil_kbits' => $default_ceil_kbits,
            'total_rate_kbits' => $total_rate_kbits,
            'total_ceil_kbits' => $total_ceil_kbits
        ]);
    }
    
    /**
     * addBwLeafService
     *
     * @param  mixed $leaf_name
     * @param  mixed $protocol
     * @param  mixed $filter
     * @param  mixed $rate_kbits
     * @param  mixed $ceil_kbits
     * @return array
     */
    public function addBwLeafService($leaf_name, $protocol, $filter, $rate_kbits, $ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.addLeafService', [
            'leaf_name' => $leaf_name,
            'protocol' => $protocol,
            'filter' => $filter,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }
    
    /**
     * getBwInterfaces
     *
     * @return array
     */
    public function getBwInterfaces(): array {
        return $this->sendXmlRpcRequest('bw.getInterfaces');
    }
    
    /**
     * getBwNodeInfo
     *
     * @param  mixed $node_id
     * @return array
     */
    public function getBwNodeInfo($node_id): array {
        return $this->sendXmlRpcRequest('bw.getNodeInfo', [
            'node_id' => $node_id
        ]);
    }
    
    /**
     * getBwLeafInfo
     *
     * @param  mixed $leaf_name
     * @return array
     */
    public function getBwLeafInfo($leaf_name): array {
        return $this->sendXmlRpcRequest('bw.getLeafInfo', [
            'leaf_name' => $leaf_name
        ]);
    }
    
    /**
     * getBwTree
     *
     * @param  mixed $interface_name
     * @return array
     */
    public function getBwTree($interface_name): array {
        return $this->sendXmlRpcRequest('bw.getTree', [
            'interface_name' => $interface_name
        ]);
    }
    
    /**
     * delBwLeafService
     *
     * @param  mixed $leaf_name
     * @param  mixed $leaf_service_id
     * @return array
     */
    public function delBwLeafService($leaf_name, $leaf_service_id): array {
        return $this->sendXmlRpcRequest('bw.delLeafService', [
            'leaf_name' => $leaf_name,
            'leaf_service_id' => $leaf_service_id
        ]);
    }
    
    /**
     * getAllBwLeafNames
     *
     * @return array
     */
    public function getAllBwLeafNames(): array {
        return $this->sendXmlRpcRequest('bw.getAllLeafNames');
    }
    
    /**
     * delBwNode
     *
     * @param  mixed $node_id
     * @return array
     */
    public function delBwNode($node_id): array {
        return $this->sendXmlRpcRequest('bw.delNode', [
            'node_id' => $node_id
        ]);
    }
    
    /**
     * delBwLeaf
     *
     * @param  mixed $leaf_name
     * @return array
     */
    public function delBwLeaf($leaf_name): array {
        return $this->sendXmlRpcRequest('bw.delLeaf', [
            'leaf_name' => $leaf_name
        ]);
    }
    
    /**
     * delBwInterface
     *
     * @param  mixed $interface_name
     * @return array
     */
    public function delBwInterface($interface_name): array {
        return $this->sendXmlRpcRequest('bw.delInterface', [
            'interface_name' => $interface_name
        ]);
    }
    
    /**
     * updateBwInterface
     *
     * @param  mixed $interface_id
     * @param  mixed $interface_name
     * @param  mixed $comment
     * @return array
     */
    public function updateBwInterface($interface_id, $interface_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('bw.updateInterface', [
            'interface_id' => $interface_id,
            'interface_name' => $interface_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * updateBwNode
     *
     * @param  mixed $node_id
     * @param  mixed $rate_kbits
     * @param  mixed $ceil_kbits
     * @return array
     */
    public function updateBwNode($node_id, $rate_kbits, $ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.updateNode', [
            'node_id' => $node_id,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }
    
    /**
     * updateBwLeaf
     *
     * @param  mixed $leaf_id
     * @param  mixed $leaf_name
     * @param  mixed $default_rate_kbits
     * @param  mixed $default_ceil_kbits
     * @param  mixed $total_rate_kbits
     * @param  mixed $total_ceil_kbits
     * @return array
     */
    public function updateBwLeaf($leaf_id, $leaf_name, $default_rate_kbits, $default_ceil_kbits, $total_rate_kbits, $total_ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.updateLeaf', [
            'leaf_id' => $leaf_id,
            'leaf_name' => $leaf_name,
            'default_rate_kbits' => $default_rate_kbits,
            'default_ceil_kbits' => $default_ceil_kbits,
            'total_rate_kbits' => $total_rate_kbits,
            'total_ceil_kbits' => $total_ceil_kbits
        ]);
    }
    
    /**
     * updateBwLeafService
     *
     * @param  mixed $leaf_name
     * @param  mixed $leaf_service_id
     * @param  mixed $protocol
     * @param  mixed $filter
     * @param  mixed $rate_kbits
     * @param  mixed $ceil_kbits
     * @return array
     */
    public function updateBwLeafService($leaf_name, $leaf_service_id, $protocol, $filter, $rate_kbits, $ceil_kbits): array {
        return $this->sendXmlRpcRequest('bw.updateLeafService', [
            'leaf_name' => $leaf_name,
            'leaf_service_id' => $leaf_service_id,
            'protocol' => $protocol,
            'filter' => $filter,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }
    
    /**
     * addBwStaticIP
     *
     * @param  mixed $ip_addr
     * @param  mixed $tx_leaf_name
     * @param  mixed $rx_leaf_name
     * @return array
     */
    public function addBwStaticIP($ip_addr, $tx_leaf_name, $rx_leaf_name): array {
        return $this->sendXmlRpcRequest('bw.addBwStaticIP', [
            'ip_addr' => $ip_addr,
            'tx_leaf_name' => $tx_leaf_name,
            'rx_leaf_name' => $rx_leaf_name
        ]);
    }
    
    /**
     * updateBwStaticIP
     *
     * @param  mixed $ip_addr
     * @param  mixed $tx_leaf_name
     * @param  mixed $rx_leaf_name
     * @param  mixed $static_ip_id
     * @return array
     */
    public function updateBwStaticIP($ip_addr, $tx_leaf_name, $rx_leaf_name, $static_ip_id): array {
        return $this->sendXmlRpcRequest('bw.updateBwStaticIP', [
            'ip_addr' => $ip_addr,
            'tx_leaf_name' => $tx_leaf_name,
            'rx_leaf_name' => $rx_leaf_name,
            'static_ip_id' => $static_ip_id
        ]);
    }
    
    /**
     * delBwStaticIP
     *
     * @param  mixed $ip_addr
     * @return array
     */
    public function delBwStaticIP($ip_addr): array {
        return $this->sendXmlRpcRequest('bw.delBwStaticIP', [
            'ip_addr' => $ip_addr
        ]);
    }
    
    /**
     * getAllBwStaticIPs
     *
     * @return array
     */
    public function getAllBwStaticIPs(): array {
        return $this->sendXmlRpcRequest('bw.getAllBwStaticIPs');
    }
    
    /**
     * getBwStaticIPInfo
     *
     * @param  mixed $ip_addr
     * @return array
     */
    public function getBwStaticIPInfo($ip_addr): array {
        return $this->sendXmlRpcRequest('bw.getBwStaticIPInfo', [
            'ip_addr' => $ip_addr
        ]);
    }
    
    /**
     * getAllActiveBwLeaves
     *
     * @return array
     */
    public function getAllActiveBwLeaves(): array {
        return $this->sendXmlRpcRequest('bw.getActiveLeaves');
    }
    
    /**
     * getBwLeafCharges
     *
     * @param  mixed $leaf_name
     * @return array
     */
    public function getBwLeafCharges($leaf_name): array {
        return $this->sendXmlRpcRequest('bw.getLeafCharges', [
            'leaf_name' => $leaf_name
        ]);
    }

    // =============================================
    // Admin Management Methods
    // =============================================
    
    /**
     * addNewAdmin
     *
     * @param  mixed $username
     * @param  mixed $password
     * @param  mixed $name
     * @param  mixed $comment
     * @return array
     */
    public function addNewAdmin($username, $password, $name, $comment = ''): array {
        return $this->sendXmlRpcRequest('admin.addNewAdmin', [
            'username' => $username,
            'password' => $password,
            'name' => $name,
            'comment' => $comment
        ]);
    }
    
    /**
     * getAdminInfo
     *
     * @param  mixed $admin_username
     * @return array
     */
    public function getAdminInfo($admin_username): array {
        return $this->sendXmlRpcRequest('admin.getAdminInfo', [
            'admin_username' => $admin_username
        ]);
    }
    
    /**
     * getAllAdminUsernames
     *
     * @return array
     */
    public function getAllAdminUsernames(): array {
        return $this->sendXmlRpcRequest('admin.getAllAdminUsernames');
    }
    
    /**
     * changeAdminPassword
     *
     * @param  mixed $admin_username
     * @param  mixed $new_password
     * @return array
     */
    public function changeAdminPassword($admin_username, $new_password): array {
        return $this->sendXmlRpcRequest('admin.changePassword', [
            'admin_username' => $admin_username,
            'new_password' => $new_password
        ]);
    }
    
    /**
     * updateAdminInfo
     *
     * @param  mixed $params
     * @return array
     */
    public function updateAdminInfo($params): array {
        return $this->sendXmlRpcRequest('admin.updateAdminInfo', $params);
    }
    
    /**
     * changeAdminDeposit
     *
     * @param  mixed $admin_username
     * @param  mixed $deposit_change
     * @param  mixed $comment
     * @return array
     */
    public function changeAdminDeposit($admin_username, $deposit_change, $comment = ''): array {
        return $this->sendXmlRpcRequest('admin.changeDeposit', [
            'admin_username' => $admin_username,
            'deposit_change' => $deposit_change,
            'comment' => $comment
        ]);
    }
    
    /**
     * deleteAdmin
     *
     * @param  mixed $admin_username
     * @return array
     */
    public function deleteAdmin($admin_username): array {
        return $this->sendXmlRpcRequest('admin.deleteAdmin', [
            'admin_username' => $admin_username
        ]);
    }
    
    /**
     * lockAdmin
     *
     * @param  mixed $admin_username
     * @param  mixed $reason
     * @return array
     */
    public function lockAdmin($admin_username, $reason = ''): array {
        return $this->sendXmlRpcRequest('admin.lockAdmin', [
            'admin_username' => $admin_username,
            'reason' => $reason
        ]);
    }
    
    /**
     * unlockAdmin
     *
     * @param  mixed $admin_username
     * @param  mixed $lock_id
     * @return array
     */
    public function unlockAdimin($admin_username, $lock_id): array {
        return $this->sendXmlRpcRequest('admin.unlockAdmin', [
            'admin_username' => $admin_username,
            'lock_id' => $lock_id
        ]);
    }

    // =============================================
    // Snapshot Management Methods
    // =============================================
    
    /**
     * getRealTimeSnapShot
     *
     * @param  mixed $name
     * @return array
     */
    public function getRealTimeSnapShot($name): array {
        return $this->sendXmlRpcRequest('snapshot.getRealTimeSnapShot', [
            'name' => $name
        ]);
    }
    
    /**
     * getBWSnapShotForUser
     *
     * @param  mixed $user_id
     * @param  mixed $ras_ip
     * @param  mixed $unique_id_val
     * @return array
     */
    public function getBWSnapShotForUser($user_id, $ras_ip, $unique_id_val): array {
        return $this->sendXmlRpcRequest('snapshot.getBWSnapShotForUser', [
            'user_id' => (string)$user_id,
            'ras_ip' => $ras_ip,
            'unique_id_val' => $unique_id_val
        ]);
    }
    
    /**
     * getOnlinesSnapShot
     *
     * @param  mixed $conds
     * @param  mixed $type
     * @return array
     */
    public function getOnlinesSnapShot($conds = [], $type = ''): array {
        return $this->sendXmlRpcRequest('snapshot.getOnlinesSnapShot', [
            'conds' => $conds,
            'type' => $type
        ]);
    }
    
    /**
     * getBWSnapShot
     *
     * @param  mixed $conds
     * @return array
     */
    public function getBWSnapShot($conds = []): array {
        return $this->sendXmlRpcRequest('snapshot.getBWSnapShot', [
            'conds' => $conds
        ]);
    }

    // =============================================
    // Report Management Methods
    // =============================================
    
    /**
     * getOnlineUsers
     *
     * @param  mixed $normal_sort_by
     * @param  mixed $normal_desc
     * @param  mixed $voip_sort_by
     * @param  mixed $voip_desc
     * @param  mixed $conds
     * @return array
     */
    public function getOnlineUsers($normal_sort_by = '', $normal_desc = false, $voip_sort_by = '', $voip_desc = false, $conds = []): array {
        return $this->sendXmlRpcRequest('report.getOnlineUsers', [
            'normal_sort_by' => $normal_sort_by,
            'normal_desc' => $normal_desc,
            'voip_sort_by' => $voip_sort_by,
            'voip_desc' => $voip_desc,
            'conds' => $conds
        ]);
    }
    
    /**
     * getConnections
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getConnections($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('report.getConnections', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * getDurations
     *
     * @param  mixed $conds
     * @return array
     */
    public function getDurations($conds = []): array {
        return $this->sendXmlRpcRequest('report.getDurations', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getGroupUsages
     *
     * @param  mixed $conds
     * @return array
     */
    public function getGroupUsages($conds = []): array {
        return $this->sendXmlRpcRequest('report.getGroupUsages', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getRasUsages
     *
     * @param  mixed $conds
     * @return array
     */
    public function getRasUsages($conds = []): array {
        return $this->sendXmlRpcRequest('report.getRasUsages', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getAdminUsages
     *
     * @param  mixed $conds
     * @return array
     */
    public function getAdminUsages($conds = []): array {
        return $this->sendXmlRpcRequest('report.getAdminUsages', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getVoIPDisconnectCausesCount
     *
     * @param  mixed $conds
     * @return array
     */
    public function getVoIPDisconnectCausesCount($conds = []): array {
        return $this->sendXmlRpcRequest('report.getVoIPDisconnectCauses', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getSuccessfulCounts
     *
     * @param  mixed $conds
     * @return array
     */
    public function getSuccessfulCounts($conds = []): array {
        return $this->sendXmlRpcRequest('report.getSuccessfulCounts', [
            'conds' => $conds
        ]);
    }
    
    /**
     * getCreditChanges
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getCreditChanges($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('report.getCreditChanges', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * getUserAuditLogs
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getUserAuditLogs($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('report.getUserAuditLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * getAdminDepositChangeLogs
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getAdminDepositChangeLogs($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('report.getAdminDepositChangeLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * delReports
     *
     * @param  mixed $table
     * @param  mixed $date
     * @param  mixed $date_unit
     * @return array
     */
    public function delReports($table, $date, $date_unit): array {
        return $this->sendXmlRpcRequest('report.delReports', [
            'table' => $table,
            'date' => $date,
            'date_unit' => $date_unit
        ]);
    }
    
    /**
     * autoCleanReports
     *
     * @param  mixed $connection_log_clean
     * @param  mixed $connection_log_unit
     * @param  mixed $credit_change_clean
     * @param  mixed $credit_change_unit
     * @param  mixed $user_audit_log_clean
     * @param  mixed $user_audit_log_unit
     * @param  mixed $snapshots_clean
     * @return array
     */
    public function autoCleanReports($connection_log_clean, $connection_log_unit, $credit_change_clean, $credit_change_unit, $user_audit_log_clean, $user_audit_log_unit, $snapshots_clean): array {
        return $this->sendXmlRpcRequest('report.autoCleanReports', [
            'connection_log_clean' => $connection_log_clean,
            'connection_log_unit' => $connection_log_unit,
            'credit_change_clean' => $credit_change_clean,
            'credit_change_unit' => $credit_change_unit,
            'user_audit_log_clean' => $user_audit_log_clean,
            'user_audit_log_unit' => $user_audit_log_unit,
            'snapshots_clean' => $snapshots_clean
        ]);
    }
    
    /**
     * getAutoCleanDates
     *
     * @return array
     */
    public function getAutoCleanDates(): array {
        return $this->sendXmlRpcRequest('report.getAutoCleanDates');
    }
    
    /**
     * getWebAnalyzerReport
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $sort_by
     * @param  mixed $desc
     * @return array
     */
    public function getWebAnalyzerReport($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false): array {
        return $this->sendXmlRpcRequest('web_analyzer.getWebAnalyzerLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }
    
    /**
     * getTopVisited
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @return array
     */
    public function getTopVisited($conds = [], $from = 0, $to = 10): array {
        return $this->sendXmlRpcRequest('web_analyzer.getTopVisited', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }
    
    /**
     * getInOutUsages
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @return array
     */
    public function getInOutUsages($conds = [], $from = 0, $to = 10): array {
        return $this->sendXmlRpcRequest('report.getInOutUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }
    
    /**
     * getCreditUsages
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @return array
     */
    public function getCreditUsages($conds = [], $from = 0, $to = 10): array {
        return $this->sendXmlRpcRequest('report.getCreditUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }
    
    /**
     * getDurationUsages
     *
     * @param  mixed $conds
     * @param  mixed $from
     * @param  mixed $to
     * @return array
     */
    public function getDurationUsages($conds = [], $from = 0, $to = 10): array {
        return $this->sendXmlRpcRequest('report.getDurationUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }

    // =============================================
    // Charge Management Methods
    // =============================================
    
    /**
     * addNewCharge
     *
     * @param  mixed $name
     * @param  mixed $comment
     * @param  mixed $charge_type
     * @param  mixed $visible_to_all
     * @return array
     */
    public function addNewCharge($name, $comment = '', $charge_type = '', $visible_to_all = true): array {
        return $this->sendXmlRpcRequest('charge.addNewCharge', [
            'name' => $name,
            'comment' => $comment,
            'charge_type' => $charge_type,
            'visible_to_all' => $visible_to_all
        ]);
    }
    
    /**
     * updateCharge
     *
     * @param  mixed $charge_id
     * @param  mixed $charge_name
     * @param  mixed $comment
     * @param  mixed $visible_to_all
     * @return array
     */
    public function updateCharge($charge_id, $charge_name, $comment = '', $visible_to_all = true): array {
        return $this->sendXmlRpcRequest('charge.updateCharge', [
            'charge_id' => $charge_id,
            'charge_name' => $charge_name,
            'comment' => $comment,
            'visible_to_all' => $visible_to_all
        ]);
    }
    
    /**
     * getChargeInfo
     *
     * @param  mixed $params
     * @return array
     */
    public function getChargeInfo($params = []): array {
        return $this->sendXmlRpcRequest('charge.getChargeInfo', $params);
    }
    
    /**
     * addInternetChargeRule
     *
     * @param  mixed $charge_name
     * @param  mixed $rule_start
     * @param  mixed $rule_end
     * @param  mixed $cpm
     * @param  mixed $cpk
     * @param  mixed $assumed_kps
     * @param  mixed $bandwidth_limit_kbytes
     * @return array
     */
    public function addInternetChargeRule($charge_name, $rule_start, $rule_end, $cpm, $cpk, $assumed_kps, $bandwidth_limit_kbytes): array {
        return $this->sendXmlRpcRequest('charge.addInternetChargeRule', [
            'charge_name' => $charge_name,
            'rule_start' => $rule_start,
            'rule_end' => $rule_end,
            'cpm' => $cpm,
            'cpk' => $cpk,
            'assumed_kps' => $assumed_kps,
            'bandwidth_limit_kbytes' => $bandwidth_limit_kbytes
        ]);
    }
    
    /**
     * listChargeRules
     *
     * @param  mixed $charge_name
     * @return array
     */
    public function listChargeRules($charge_name): array {
        return $this->sendXmlRpcRequest('charge.listChargeRules', [
            'charge_name' => $charge_name
        ]);
    }
    
    /**
     * listCharges
     *
     * @param  mixed $charge_type
     * @return array
     */
    public function listCharges($charge_type = ''): array {
        return $this->sendXmlRpcRequest('charge.listCharges', [
            'charge_type' => $charge_type
        ]);
    }
    
    /**
     * updateInternetChargeRule
     *
     * @param  mixed $charge_name
     * @param  mixed $charge_rule_id
     * @param  mixed $rule_start
     * @param  mixed $rule_end
     * @param  mixed $cpm
     * @param  mixed $cpk
     * @param  mixed $assumed_kps
     * @param  mixed $bandwidth_limit_kbytes
     * @return array
     */
    public function updateInternetChargeRule($charge_name, $charge_rule_id, $rule_start, $rule_end, $cpm, $cpk, $assumed_kps, $bandwidth_limit_kbytes): array {
        return $this->sendXmlRpcRequest('charge.updateInternetChargeRule', [
            'charge_name' => $charge_name,
            'charge_rule_id' => $charge_rule_id,
            'rule_start' => $rule_start,
            'rule_end' => $rule_end,
            'cpm' => $cpm,
            'cpk' => $cpk,
            'assumed_kps' => $assumed_kps,
            'bandwidth_limit_kbytes' => $bandwidth_limit_kbytes
        ]);
    }
    
    /**
     * delChargeRule
     *
     * @param  mixed $charge_rule_id
     * @param  mixed $charge_name
     * @return array
     */
    public function delChargeRule($charge_rule_id, $charge_name): array {
        return $this->sendXmlRpcRequest('charge.delChargeRule', [
            'charge_rule_id' => $charge_rule_id,
            'charge_name' => $charge_name
        ]);
    }
    
    /**
     * delCharge
     *
     * @param  mixed $charge_name
     * @return array
     */
    public function delCharge($charge_name): array {
        return $this->sendXmlRpcRequest('charge.delCharge', [
            'charge_name' => $charge_name
        ]);
    }
    
    /**
     * addVoIPChargeRule
     *
     * @param  mixed $charge_name
     * @param  mixed $rule_start
     * @param  mixed $rule_end
     * @param  mixed $tariff_name
     * @param  mixed $ras
     * @param  mixed $ports
     * @param  mixed $dows
     * @return array
     */
    public function addVoIPChargeRule($charge_name, $rule_start, $rule_end, $tariff_name, $ras = '', $ports = '', $dows = ''): array {
        return $this->sendXmlRpcRequest('charge.addVoIPChargeRule', [
            'charge_name' => $charge_name,
            'rule_start' => $rule_start,
            'rule_end' => $rule_end,
            'tariff_name' => $tariff_name,
            'ras' => $ras,
            'ports' => $ports,
            'dows' => $dows
        ]);
    }
    
    /**
     * updateVoIPChargeRule
     *
     * @param  mixed $charge_name
     * @param  mixed $charge_rule_id
     * @param  mixed $rule_start
     * @param  mixed $rule_end
     * @param  mixed $tariff_name
     * @param  mixed $ras
     * @param  mixed $ports
     * @return array
     */
    public function updateVoIPChargeRule($charge_name, $charge_rule_id, $rule_start, $rule_end, $tariff_name, $ras = '', $ports = ''): array {
        return $this->sendXmlRpcRequest('charge.updateVoIPChargeRule', [
            'charge_name' => $charge_name,
            'charge_rule_id' => $charge_rule_id,
            'rule_start' => $rule_start,
            'rule_end' => $rule_end,
            'tariff_name' => $tariff_name,
            'ras' => $ras,
            'ports' => $ports
        ]);
    }

    // =============================================
    // Group Management Methods
    // =============================================
    
    /**
     * addNewGroup
     *
     * @param  mixed $group_name
     * @param  mixed $comment
     * @return array
     */
    public function addNewGroup($group_name, $comment = ''): array {
        return $this->sendXmlRpcRequest('group.addNewGroup', [
            'group_name' => $group_name,
            'comment' => $comment
        ]);
    }
    
    /**
     * listGroups
     *
     * @return array
     */
    public function listGroups(): array {
        return $this->sendXmlRpcRequest('group.listGroups');
    }
    
    /**
     * getGroupInfo
     *
     * @param  mixed $group_name
     * @return array
     */
    public function getGroupInfo($group_name): array {
        return $this->sendXmlRpcRequest('group.getGroupInfo', [
            'group_name' => $group_name
        ]);
    }
    
    /**
     * updateGroup
     *
     * @param  mixed $group_id
     * @param  mixed $group_name
     * @param  mixed $comment
     * @param  mixed $owner_name
     * @return array
     */
    public function updateGroup($group_id, $group_name, $comment = '', $owner_name = ''): array {
        return $this->sendXmlRpcRequest('group.updateGroup', [
            'group_id' => $group_id,
            'group_name' => $group_name,
            'comment' => $comment,
            'owner_name' => $owner_name
        ]);
    }
    
    /**
     * updateGroupAttrs
     *
     * @param  mixed $group_name
     * @param  mixed $attrs
     * @param  mixed $to_del_attrs
     * @return array
     */
    public function updateGroupAttrs($group_name, $attrs, $to_del_attrs = []): array {
        return $this->sendXmlRpcRequest('group.updateGroupAttrs', [
            'group_name' => $group_name,
            'attrs' => $attrs,
            'to_del_attrs' => $to_del_attrs
        ]);
    }
    
    /**
     * delGroup
     *
     * @param  mixed $group_name
     * @return array
     */
    public function delGroup($group_name): array {
        return $this->sendXmlRpcRequest('group.delGroup', [
            'group_name' => $group_name
        ]);
    }

    // =============================================
    // Miscellaneous Methods
    // =============================================
    
    /**
     * getConsoleBuffer
     *
     * @return array
     */
    public function getConsoleBuffer(): array {
        return $this->sendXmlRpcRequest('log_console.getConsoleBuffer');
    }
}