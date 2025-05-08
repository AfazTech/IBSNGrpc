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

class Client {
    private $authName;
    private $authPass;
    private $authType;
    private $xmlrpcUrl;

    public function __construct($ip, $port, $username, $password, $authType = 'ADMIN') {
        $this->authName = $username;
        $this->authPass = $password;
        $this->authType = $authType;
        $this->xmlrpcUrl = "http://{$ip}:{$port}/xmlrpc";
    }

    private function sendXmlRpcRequest($method, $params = []) {
        try {
            $requestParams = array_merge([
                'auth_name' => $this->authName,
                'auth_pass' => $this->authPass,
                'auth_type' => $this->authType
            ], $params);

            $request = xmlrpc_encode_request($method, $requestParams);

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
                return ['ok' => false, 'message' => 'Connection error: ' . curl_error($ch)];
            }
            curl_close($ch);

            $decoded = xmlrpc_decode($response);
            if (is_array($decoded) && xmlrpc_is_fault($decoded)) {
                return ['ok' => false, 'message' => $decoded['faultString']];
            }

            return ['ok' => true, 'result' => $decoded];
        } catch (Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    // =============================================
    // IP Pool Management Methods
    // =============================================

    public function addNewIPpool($ippool_name, $comment = '') {
        return $this->sendXmlRpcRequest('ippool.addNewIPpool', [
            'ippool_name' => $ippool_name,
            'comment' => $comment
        ]);
    }

    public function updateIPpool($ippool_id, $ippool_name, $comment = '') {
        return $this->sendXmlRpcRequest('ippool.updateIPpool', [
            'ippool_id' => $ippool_id,
            'ippool_name' => $ippool_name,
            'comment' => $comment
        ]);
    }

    public function getIPpoolNames() {
        return $this->sendXmlRpcRequest('ippool.getIPpoolNames');
    }

    public function getIPpoolInfo($ippool_name) {
        return $this->sendXmlRpcRequest('ippool.getIPpoolInfo', [
            'ippool_name' => $ippool_name
        ]);
    }

    public function deleteIPpool($ippool_name) {
        return $this->sendXmlRpcRequest('ippool.deleteIPpool', [
            'ippool_name' => $ippool_name
        ]);
    }

    public function delIPfromPool($ippool_name, $ip) {
        return $this->sendXmlRpcRequest('ippool.delIPfromPool', [
            'ippool_name' => $ippool_name,
            'ip' => $ip
        ]);
    }

    public function addIPtoPool($ippool_name, $ip) {
        return $this->sendXmlRpcRequest('ippool.addIPtoPool', [
            'ippool_name' => $ippool_name,
            'ip' => $ip
        ]);
    }

    // =============================================
    // User Management Methods
    // =============================================

    public function addNewUser($count, $credit, $group_name, $owner_name,$credit_comment = "") {
        return $this->sendXmlRpcRequest('user.addNewUsers', [
            'count' => $count,
            'credit' => $credit,
            'group_name' => $group_name,
            'owner_name' =>$owner_name,
            'credit_comment' => $credit_comment
        ]);
    }

    public function setUserAttribute($user_id, $attrs) {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => $attrs
        ]);
    }


    public function getUserInfoByID($user_id) {
        return $this->sendXmlRpcRequest('user.getUserInfo', [
            'user_id' => (string)$user_id
        ]);
    }

    public function getUserInfoByUsername($username) {
        return $this->sendXmlRpcRequest('user.getUserInfo', [
            'normal_username' => $username
        ]);
    }

    public function updateUserAttrs($user_id, $attrs, $to_del_attrs = []) {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => $attrs,
            'to_del_attrs' => $to_del_attrs
        ]);
    }

    public function checkNormalUsernameForAdd($normal_username, $current_username = '') {
        return $this->sendXmlRpcRequest('normal_user.checkNormalUsernameForAdd', [
            'normal_username' => $normal_username,
            'current_username' => $current_username
        ]);
    }

    public function checkVoIPUsernameForAdd($voip_username, $current_username = '') {
        return $this->sendXmlRpcRequest('voip_user.checkVoIPUsernameForAdd', [
            'voip_username' => $voip_username,
            'current_username' => $current_username
        ]);
    }

    public function changeUserCredit($user_id, $credit, $credit_comment = '') {
        return $this->sendXmlRpcRequest('user.changeCredit', [
            'user_id' => (string)$user_id,
            'credit' => $credit,
            'credit_comment' => $credit_comment
        ]);
    }

    public function delUser($user_id, $delete_comment = '', $del_connection_logs = false, $del_audit_logs = false) {
        return $this->sendXmlRpcRequest('user.delUser', [
            'user_id' => (string)$user_id,
            'delete_comment' => $delete_comment,
            'del_connection_logs' => $del_connection_logs,
            'del_audit_logs' => $del_audit_logs
        ]);
    }

    public function killUser($user_id, $ras_ip, $unique_id_val, $kill = true) {
        return $this->sendXmlRpcRequest('user.killUser', [
            'user_id' => (string)$user_id,
            'ras_ip' => $ras_ip,
            'unique_id_val' => $unique_id_val,
            'kill' => $kill
        ]);
    }

    public function searchAddUserSaves($conds = [], $from = 0, $to = 10, $order_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('addUserSave.searchAddUserSaves', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'order_by' => $order_by,
            'desc' => $desc
        ]);
    }

    public function deleteAddUserSaves($add_user_save_ids) {
        return $this->sendXmlRpcRequest('addUserSave.deleteAddUserSaves', [
            'add_user_save_ids' => $add_user_save_ids
        ]);
    }

    public function changeNormalUserPassword($normal_username, $password, $old_password = '') {
        return $this->sendXmlRpcRequest('normal_user.changePassword', [
            'normal_username' => $normal_username,
            'password' => $password,
            'old_password' => $old_password
        ]);
    }
    public function changePassword($user_id, $username, $password) {
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
    public function changeVoIPUserPassword($voip_username, $password, $old_password = '') {
        return $this->sendXmlRpcRequest('voip_user.changePassword', [
            'voip_username' => $voip_username,
            'password' => $password,
            'old_password' => $old_password
        ]);
    }

    public function calcApproxDuration($user_id) {
        return $this->sendXmlRpcRequest('user.calcApproxDuration', [
            'user_id' => (string)$user_id
        ]);
    }

    public function searchUser($conds = [], $from = 0, $to = 10, $order_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('user.searchUser', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'order_by' => $order_by,
            'desc' => $desc
        ]);
    }

    // Custom User Methods
    public function changeUsergroup($user_id, $new_group_name) {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => ['group_name' => $new_group_name],
            'to_del_attrs' => []
        ]);
    }

    public function changeUserMultiLogin($user_id, $multi_login) {
        return $this->sendXmlRpcRequest('user.updateUserAttrs', [
            'user_id' => (string)$user_id,
            'attrs' => ['multi_login' => $multi_login],
            'to_del_attrs' => []
        ]);
    }

    public function setUserCustomField($user_id, $field_name, $field_value) {
        return $this->updateUserAttrs($user_id, [
            'custom_fields' => [$field_name => $field_value]
        ]);
    }

    public function lockUser($user_id, $lock_reason = '') {
        return $this->updateUserAttrs($user_id, [
            'lock' => $lock_reason ?: 'Locked by system'
        ]);
    }

    public function unlockUser($user_id) {
        return $this->updateUserAttrs($user_id, [], ['lock']);
    }

    public function resetFirstloginUser($user_id) {
        return $this->updateUserAttrs($user_id, [
            'basic_info' => ['first_login' => '']
        ], ['first_login']);
    }

    // =============================================
    // Messaging Methods
    // =============================================

    public function multiStrGetAll($str, $left_pad = false) {
        return $this->sendXmlRpcRequest('util.multiStrGetAll', [
            'str' => $str,
            'left_pad' => $left_pad
        ]);
    }

    public function postMessageToUser($user_ids, $message) {
        return $this->sendXmlRpcRequest('message.postMessageToUser', [
            'user_ids' => $user_ids,
            'message' => $message
        ]);
    }

    public function postMessageToAdmin($message) {
        return $this->sendXmlRpcRequest('message.postMessageToAdmin', [
            'message' => $message
        ]);
    }

    public function getAdminMessages($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('message.getAdminMessages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function getUserMessages($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('message.getUserMessages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function deleteUserMessages($message_ids) {
        return $this->sendXmlRpcRequest('message.deleteUserMessages', [
            'message_ids' => $message_ids
        ]);
    }

    public function deleteAdminMessages($message_ids, $table) {
        return $this->sendXmlRpcRequest('message.deleteMessages', [
            'message_ids' => $message_ids,
            'table' => $table
        ]);
    }

    public function getUserLastMessageID() {
        return $this->sendXmlRpcRequest('message.getLastMessageID');
    }

    // =============================================
    // RAS Management Methods
    // =============================================

    public function addNewRas($ras_ip, $ras_description, $ras_type, $radius_secret, $comment = '') {
        return $this->sendXmlRpcRequest('ras.addNewRas', [
            'ras_ip' => $ras_ip,
            'ras_description' => $ras_description,
            'ras_type' => $ras_type,
            'radius_secret' => $radius_secret,
            'comment' => $comment
        ]);
    }

    public function getRasInfo($ras_ip) {
        return $this->sendXmlRpcRequest('ras.getRasInfo', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function getActiveRasIPs() {
        return $this->sendXmlRpcRequest('ras.getActiveRasIPs');
    }

    public function getRasDescriptions() {
        return $this->sendXmlRpcRequest('ras.getRasDescriptions');
    }

    public function getInActiveRases() {
        return $this->sendXmlRpcRequest('ras.getInActiveRases');
    }

    public function getRasTypes() {
        return $this->sendXmlRpcRequest('ras.getRasTypes');
    }

    public function getRasAttributes($ras_ip) {
        return $this->sendXmlRpcRequest('ras.getRasAttributes', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function getRasPorts($ras_ip) {
        return $this->sendXmlRpcRequest('ras.getRasPorts', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function updateRasInfo($ras_id, $ras_ip, $ras_description, $ras_type, $radius_secret, $comment = '') {
        return $this->sendXmlRpcRequest('ras.updateRasInfo', [
            'ras_id' => $ras_id,
            'ras_ip' => $ras_ip,
            'ras_description' => $ras_description,
            'ras_type' => $ras_type,
            'radius_secret' => $radius_secret,
            'comment' => $comment
        ]);
    }

    public function updateRasAttributes($ras_ip, $attrs) {
        return $this->sendXmlRpcRequest('ras.updateAttributes', [
            'ras_ip' => $ras_ip,
            'attrs' => $attrs
        ]);
    }

    public function resetRasAttributes($ras_ip) {
        return $this->sendXmlRpcRequest('ras.resetAttributes', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function addRasPort($ras_ip, $port_name, $phone, $type, $comment = '') {
        return $this->sendXmlRpcRequest('ras.addPort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name,
            'phone' => $phone,
            'type' => $type,
            'comment' => $comment
        ]);
    }

    public function getPortTypes() {
        return $this->sendXmlRpcRequest('ras.getPortTypes');
    }

    public function delRasPort($ras_ip, $port_name) {
        return $this->sendXmlRpcRequest('ras.delPort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name
        ]);
    }

    public function getRasPortInfo($ras_ip, $port_name) {
        return $this->sendXmlRpcRequest('ras.getRasPortInfo', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name
        ]);
    }

    public function updateRasPort($ras_ip, $port_name, $phone, $type, $comment = '') {
        return $this->sendXmlRpcRequest('ras.updatePort', [
            'ras_ip' => $ras_ip,
            'port_name' => $port_name,
            'phone' => $phone,
            'type' => $type,
            'comment' => $comment
        ]);
    }

    public function deActiveRas($ras_ip) {
        return $this->sendXmlRpcRequest('ras.deActiveRas', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function reActiveRas($ras_ip) {
        return $this->sendXmlRpcRequest('ras.reActiveRas', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function getRasIPpools($ras_ip) {
        return $this->sendXmlRpcRequest('ras.getRasIPpools', [
            'ras_ip' => $ras_ip
        ]);
    }

    public function addIPpoolToRas($ras_ip, $ippool_name) {
        return $this->sendXmlRpcRequest('ras.addIPpoolToRas', [
            'ras_ip' => $ras_ip,
            'ippool_name' => $ippool_name
        ]);
    }

    public function delIPpoolFromRas($ras_ip, $ippool_name) {
        return $this->sendXmlRpcRequest('ras.delIPpoolFromRas', [
            'ras_ip' => $ras_ip,
            'ippool_name' => $ippool_name
        ]);
    }

    // =============================================
    // VoIP Tariff Management Methods
    // =============================================

    public function addNewVoIPTariff($tariff_name, $comment = '') {
        return $this->sendXmlRpcRequest('voip_tariff.addNewTariff', [
            'tariff_name' => $tariff_name,
            'comment' => $comment
        ]);
    }

    public function updateVoIPTariff($tariff_name, $tariff_id, $comment = '') {
        return $this->sendXmlRpcRequest('voip_tariff.updateTariff', [
            'tariff_name' => $tariff_name,
            'tariff_id' => $tariff_id,
            'comment' => $comment
        ]);
    }

    public function deleteVoIPTariff($tariff_name) {
        return $this->sendXmlRpcRequest('voip_tariff.deleteTariff', [
            'tariff_name' => $tariff_name
        ]);
    }

    public function addVoIPTariffPrefix($tariff_name, $prefix_codes, $prefix_names, $cpms, $free_seconds, $min_durations, $round_tos, $min_chargable_durations) {
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

    public function updateVoIPTariffPrefix($tariff_name, $prefix_id, $prefix_code, $prefix_name, $cpm, $free_seconds, $min_duration) {
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

    public function deleteVoIPTariffPrefix($tariff_name, $prefix_code) {
        return $this->sendXmlRpcRequest('voip_tariff.deletePrefix', [
            'tariff_name' => $tariff_name,
            'prefix_code' => $prefix_code
        ]);
    }

    public function getVoIPTariffInfo($tariff_name, $include_prefixes = false, $name_regex = '') {
        return $this->sendXmlRpcRequest('voip_tariff.getTariffInfo', [
            'tariff_name' => $tariff_name,
            'include_prefixes' => $include_prefixes,
            'name_regex' => $name_regex
        ]);
    }

    public function listVoIPTariffs() {
        return $this->sendXmlRpcRequest('voip_tariff.listTariffs');
    }

    // =============================================
    // Definitions Management Methods
    // =============================================

    public function getAllDefs() {
        return $this->sendXmlRpcRequest('ibs_defs.getAllDefs');
    }

    public function saveDefs($defs) {
        return $this->sendXmlRpcRequest('ibs_defs.saveDefs', [
            'defs' => $defs
        ]);
    }

    // =============================================
    // Permission Management Methods
    // =============================================

    public function hasPerm($perm_name, $admin_username) {
        return $this->sendXmlRpcRequest('perm.hasPerm', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username
        ]);
    }

    public function adminCanDo($perm_name, $admin_username, $params = []) {
        return $this->sendXmlRpcRequest('perm.canDo', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username,
            'params' => $params
        ]);
    }

    public function getAdminPermVal($perm_name, $admin_username) {
        return $this->sendXmlRpcRequest('perm.getAdminPermVal', [
            'perm_name' => $perm_name,
            'admin_username' => $admin_username
        ]);
    }

    public function getPermsOfAdmin($admin_username) {
        return $this->sendXmlRpcRequest('perm.getPermsOfAdmin', [
            'admin_username' => $admin_username
        ]);
    }

    public function getAllPerms($category = '') {
        return $this->sendXmlRpcRequest('perm.getAllPerms', [
            'category' => $category
        ]);
    }

    public function changePermission($admin_username, $perm_name, $perm_value) {
        return $this->sendXmlRpcRequest('perm.changePermission', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name,
            'perm_value' => $perm_value
        ]);
    }

    public function delPermission($admin_username, $perm_name) {
        return $this->sendXmlRpcRequest('perm.delPermission', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name
        ]);
    }

    public function deletePermissionValue($admin_username, $perm_name, $perm_value) {
        return $this->sendXmlRpcRequest('perm.delPermissionValue', [
            'admin_username' => $admin_username,
            'perm_name' => $perm_name,
            'perm_value' => $perm_value
        ]);
    }

    public function savePermsOfAdminToTemplate($admin_username, $perm_template_name) {
        return $this->sendXmlRpcRequest('perm.savePermsOfAdminToTemplate', [
            'admin_username' => $admin_username,
            'perm_template_name' => $perm_template_name
        ]);
    }

    public function getListOfPermTemplates() {
        return $this->sendXmlRpcRequest('perm.getListOfPermTemplates');
    }

    public function getPermsOfTemplate($perm_template_name) {
        return $this->sendXmlRpcRequest('perm.getPermsOfTemplate', [
            'perm_template_name' => $perm_template_name
        ]);
    }

    public function loadPermTemplateToAdmin($admin_username, $perm_template_name) {
        return $this->sendXmlRpcRequest('perm.loadPermTemplateToAdmin', [
            'admin_username' => $admin_username,
            'perm_template_name' => $perm_template_name
        ]);
    }

    public function deletePermTemplate($perm_template_name) {
        return $this->sendXmlRpcRequest('perm.deletePermTemplate', [
            'perm_template_name' => $perm_template_name
        ]);
    }

    // =============================================
    // Bandwidth Management Methods
    // =============================================

    public function addBwInterface($interface_name, $comment = '') {
        return $this->sendXmlRpcRequest('bw.addInterface', [
            'interface_name' => $interface_name,
            'comment' => $comment
        ]);
    }

    public function addBwNode($interface_name, $parent_id, $rate_kbits, $ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.addNode', [
            'interface_name' => $interface_name,
            'parent_id' => $parent_id,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }

    public function addBwLeaf($leaf_name, $parent_id, $default_rate_kbits, $default_ceil_kbits, $total_rate_kbits, $total_ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.addLeaf', [
            'leaf_name' => $leaf_name,
            'parent_id' => $parent_id,
            'default_rate_kbits' => $default_rate_kbits,
            'default_ceil_kbits' => $default_ceil_kbits,
            'total_rate_kbits' => $total_rate_kbits,
            'total_ceil_kbits' => $total_ceil_kbits
        ]);
    }

    public function addBwLeafService($leaf_name, $protocol, $filter, $rate_kbits, $ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.addLeafService', [
            'leaf_name' => $leaf_name,
            'protocol' => $protocol,
            'filter' => $filter,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }

    public function getBwInterfaces() {
        return $this->sendXmlRpcRequest('bw.getInterfaces');
    }

    public function getBwNodeInfo($node_id) {
        return $this->sendXmlRpcRequest('bw.getNodeInfo', [
            'node_id' => $node_id
        ]);
    }

    public function getBwLeafInfo($leaf_name) {
        return $this->sendXmlRpcRequest('bw.getLeafInfo', [
            'leaf_name' => $leaf_name
        ]);
    }

    public function getBwTree($interface_name) {
        return $this->sendXmlRpcRequest('bw.getTree', [
            'interface_name' => $interface_name
        ]);
    }

    public function delBwLeafService($leaf_name, $leaf_service_id) {
        return $this->sendXmlRpcRequest('bw.delLeafService', [
            'leaf_name' => $leaf_name,
            'leaf_service_id' => $leaf_service_id
        ]);
    }

    public function getAllBwLeafNames() {
        return $this->sendXmlRpcRequest('bw.getAllLeafNames');
    }

    public function delBwNode($node_id) {
        return $this->sendXmlRpcRequest('bw.delNode', [
            'node_id' => $node_id
        ]);
    }

    public function delBwLeaf($leaf_name) {
        return $this->sendXmlRpcRequest('bw.delLeaf', [
            'leaf_name' => $leaf_name
        ]);
    }

    public function delBwInterface($interface_name) {
        return $this->sendXmlRpcRequest('bw.delInterface', [
            'interface_name' => $interface_name
        ]);
    }

    public function updateBwInterface($interface_id, $interface_name, $comment = '') {
        return $this->sendXmlRpcRequest('bw.updateInterface', [
            'interface_id' => $interface_id,
            'interface_name' => $interface_name,
            'comment' => $comment
        ]);
    }

    public function updateBwNode($node_id, $rate_kbits, $ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.updateNode', [
            'node_id' => $node_id,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }

    public function updateBwLeaf($leaf_id, $leaf_name, $default_rate_kbits, $default_ceil_kbits, $total_rate_kbits, $total_ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.updateLeaf', [
            'leaf_id' => $leaf_id,
            'leaf_name' => $leaf_name,
            'default_rate_kbits' => $default_rate_kbits,
            'default_ceil_kbits' => $default_ceil_kbits,
            'total_rate_kbits' => $total_rate_kbits,
            'total_ceil_kbits' => $total_ceil_kbits
        ]);
    }

    public function updateBwLeafService($leaf_name, $leaf_service_id, $protocol, $filter, $rate_kbits, $ceil_kbits) {
        return $this->sendXmlRpcRequest('bw.updateLeafService', [
            'leaf_name' => $leaf_name,
            'leaf_service_id' => $leaf_service_id,
            'protocol' => $protocol,
            'filter' => $filter,
            'rate_kbits' => $rate_kbits,
            'ceil_kbits' => $ceil_kbits
        ]);
    }

    public function addBwStaticIP($ip_addr, $tx_leaf_name, $rx_leaf_name) {
        return $this->sendXmlRpcRequest('bw.addBwStaticIP', [
            'ip_addr' => $ip_addr,
            'tx_leaf_name' => $tx_leaf_name,
            'rx_leaf_name' => $rx_leaf_name
        ]);
    }

    public function updateBwStaticIP($ip_addr, $tx_leaf_name, $rx_leaf_name, $static_ip_id) {
        return $this->sendXmlRpcRequest('bw.updateBwStaticIP', [
            'ip_addr' => $ip_addr,
            'tx_leaf_name' => $tx_leaf_name,
            'rx_leaf_name' => $rx_leaf_name,
            'static_ip_id' => $static_ip_id
        ]);
    }

    public function delBwStaticIP($ip_addr) {
        return $this->sendXmlRpcRequest('bw.delBwStaticIP', [
            'ip_addr' => $ip_addr
        ]);
    }

    public function getAllBwStaticIPs() {
        return $this->sendXmlRpcRequest('bw.getAllBwStaticIPs');
    }

    public function getBwStaticIPInfo($ip_addr) {
        return $this->sendXmlRpcRequest('bw.getBwStaticIPInfo', [
            'ip_addr' => $ip_addr
        ]);
    }

    public function getAllActiveBwLeaves() {
        return $this->sendXmlRpcRequest('bw.getActiveLeaves');
    }

    public function getBwLeafCharges($leaf_name) {
        return $this->sendXmlRpcRequest('bw.getLeafCharges', [
            'leaf_name' => $leaf_name
        ]);
    }

    // =============================================
    // Admin Management Methods
    // =============================================

    public function addNewAdmin($username, $password, $name, $comment = '') {
        return $this->sendXmlRpcRequest('admin.addNewAdmin', [
            'username' => $username,
            'password' => $password,
            'name' => $name,
            'comment' => $comment
        ]);
    }

    public function getAdminInfo($admin_username) {
        return $this->sendXmlRpcRequest('admin.getAdminInfo', [
            'admin_username' => $admin_username
        ]);
    }

    public function getAllAdminUsernames() {
        return $this->sendXmlRpcRequest('admin.getAllAdminUsernames');
    }

    public function changeAdminPassword($admin_username, $new_password) {
        return $this->sendXmlRpcRequest('admin.changePassword', [
            'admin_username' => $admin_username,
            'new_password' => $new_password
        ]);
    }

    public function updateAdminInfo($params) {
        return $this->sendXmlRpcRequest('admin.updateAdminInfo', $params);
    }

    public function changeAdminDeposit($admin_username, $deposit_change, $comment = '') {
        return $this->sendXmlRpcRequest('admin.changeDeposit', [
            'admin_username' => $admin_username,
            'deposit_change' => $deposit_change,
            'comment' => $comment
        ]);
    }

    public function deleteAdmin($admin_username) {
        return $this->sendXmlRpcRequest('admin.deleteAdmin', [
            'admin_username' => $admin_username
        ]);
    }

    public function lockAdmin($admin_username, $reason = '') {
        return $this->sendXmlRpcRequest('admin.lockAdmin', [
            'admin_username' => $admin_username,
            'reason' => $reason
        ]);
    }

    public function unlockAdmin($admin_username, $lock_id) {
        return $this->sendXmlRpcRequest('admin.unlockAdmin', [
            'admin_username' => $admin_username,
            'lock_id' => $lock_id
        ]);
    }

    // =============================================
    // Snapshot Management Methods
    // =============================================

    public function getRealTimeSnapShot($name) {
        return $this->sendXmlRpcRequest('snapshot.getRealTimeSnapShot', [
            'name' => $name
        ]);
    }

    public function getBWSnapShotForUser($user_id, $ras_ip, $unique_id_val) {
        return $this->sendXmlRpcRequest('snapshot.getBWSnapShotForUser', [
            'user_id' => (string)$user_id,
            'ras_ip' => $ras_ip,
            'unique_id_val' => $unique_id_val
        ]);
    }

    public function getOnlinesSnapShot($conds = [], $type = '') {
        return $this->sendXmlRpcRequest('snapshot.getOnlinesSnapShot', [
            'conds' => $conds,
            'type' => $type
        ]);
    }

    public function getBWSnapShot($conds = []) {
        return $this->sendXmlRpcRequest('snapshot.getBWSnapShot', [
            'conds' => $conds
        ]);
    }

    // =============================================
    // Report Management Methods
    // =============================================

    public function getOnlineUsers($normal_sort_by = '', $normal_desc = false, $voip_sort_by = '', $voip_desc = false, $conds = []) {
        return $this->sendXmlRpcRequest('report.getOnlineUsers', [
            'normal_sort_by' => $normal_sort_by,
            'normal_desc' => $normal_desc,
            'voip_sort_by' => $voip_sort_by,
            'voip_desc' => $voip_desc,
            'conds' => $conds
        ]);
    }

    public function getConnections($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('report.getConnections', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function getDurations($conds = []) {
        return $this->sendXmlRpcRequest('report.getDurations', [
            'conds' => $conds
        ]);
    }

    public function getGroupUsages($conds = []) {
        return $this->sendXmlRpcRequest('report.getGroupUsages', [
            'conds' => $conds
        ]);
    }

    public function getRasUsages($conds = []) {
        return $this->sendXmlRpcRequest('report.getRasUsages', [
            'conds' => $conds
        ]);
    }

    public function getAdminUsages($conds = []) {
        return $this->sendXmlRpcRequest('report.getAdminUsages', [
            'conds' => $conds
        ]);
    }

    public function getVoIPDisconnectCausesCount($conds = []) {
        return $this->sendXmlRpcRequest('report.getVoIPDisconnectCauses', [
            'conds' => $conds
        ]);
    }

    public function getSuccessfulCounts($conds = []) {
        return $this->sendXmlRpcRequest('report.getSuccessfulCounts', [
            'conds' => $conds
        ]);
    }

    public function getCreditChanges($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('report.getCreditChanges', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function getUserAuditLogs($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('report.getUserAuditLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function getAdminDepositChangeLogs($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('report.getAdminDepositChangeLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function delReports($table, $date, $date_unit) {
        return $this->sendXmlRpcRequest('report.delReports', [
            'table' => $table,
            'date' => $date,
            'date_unit' => $date_unit
        ]);
    }

    public function autoCleanReports($connection_log_clean, $connection_log_unit, $credit_change_clean, $credit_change_unit, $user_audit_log_clean, $user_audit_log_unit, $snapshots_clean) {
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

    public function getAutoCleanDates() {
        return $this->sendXmlRpcRequest('report.getAutoCleanDates');
    }

    public function getWebAnalyzerReport($conds = [], $from = 0, $to = 10, $sort_by = '', $desc = false) {
        return $this->sendXmlRpcRequest('web_analyzer.getWebAnalyzerLogs', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to,
            'sort_by' => $sort_by,
            'desc' => $desc
        ]);
    }

    public function getTopVisited($conds = [], $from = 0, $to = 10) {
        return $this->sendXmlRpcRequest('web_analyzer.getTopVisited', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function getInOutUsages($conds = [], $from = 0, $to = 10) {
        return $this->sendXmlRpcRequest('report.getInOutUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function getCreditUsages($conds = [], $from = 0, $to = 10) {
        return $this->sendXmlRpcRequest('report.getCreditUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function getDurationUsages($conds = [], $from = 0, $to = 10) {
        return $this->sendXmlRpcRequest('report.getDurationUsages', [
            'conds' => $conds,
            'from' => $from,
            'to' => $to
        ]);
    }

    // =============================================
    // Charge Management Methods
    // =============================================

    public function addNewCharge($name, $comment = '', $charge_type = '', $visible_to_all = true) {
        return $this->sendXmlRpcRequest('charge.addNewCharge', [
            'name' => $name,
            'comment' => $comment,
            'charge_type' => $charge_type,
            'visible_to_all' => $visible_to_all
        ]);
    }

    public function updateCharge($charge_id, $charge_name, $comment = '', $visible_to_all = true) {
        return $this->sendXmlRpcRequest('charge.updateCharge', [
            'charge_id' => $charge_id,
            'charge_name' => $charge_name,
            'comment' => $comment,
            'visible_to_all' => $visible_to_all
        ]);
    }

    public function getChargeInfo($params = []) {
        return $this->sendXmlRpcRequest('charge.getChargeInfo', $params);
    }

    public function addInternetChargeRule($charge_name, $rule_start, $rule_end, $cpm, $cpk, $assumed_kps, $bandwidth_limit_kbytes) {
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

    public function listChargeRules($charge_name) {
        return $this->sendXmlRpcRequest('charge.listChargeRules', [
            'charge_name' => $charge_name
        ]);
    }

    public function listCharges($charge_type = '') {
        return $this->sendXmlRpcRequest('charge.listCharges', [
            'charge_type' => $charge_type
        ]);
    }

    public function updateInternetChargeRule($charge_name, $charge_rule_id, $rule_start, $rule_end, $cpm, $cpk, $assumed_kps, $bandwidth_limit_kbytes) {
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

    public function delChargeRule($charge_rule_id, $charge_name) {
        return $this->sendXmlRpcRequest('charge.delChargeRule', [
            'charge_rule_id' => $charge_rule_id,
            'charge_name' => $charge_name
        ]);
    }

    public function delCharge($charge_name) {
        return $this->sendXmlRpcRequest('charge.delCharge', [
            'charge_name' => $charge_name
        ]);
    }

    public function addVoIPChargeRule($charge_name, $rule_start, $rule_end, $tariff_name, $ras = '', $ports = '', $dows = '') {
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

    public function updateVoIPChargeRule($charge_name, $charge_rule_id, $rule_start, $rule_end, $tariff_name, $ras = '', $ports = '') {
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

    public function addNewGroup($group_name, $comment = '') {
        return $this->sendXmlRpcRequest('group.addNewGroup', [
            'group_name' => $group_name,
            'comment' => $comment
        ]);
    }

    public function listGroups() {
        return $this->sendXmlRpcRequest('group.listGroups');
    }

    public function getGroupInfo($group_name) {
        return $this->sendXmlRpcRequest('group.getGroupInfo', [
            'group_name' => $group_name
        ]);
    }

    public function updateGroup($group_id, $group_name, $comment = '', $owner_name = '') {
        return $this->sendXmlRpcRequest('group.updateGroup', [
            'group_id' => $group_id,
            'group_name' => $group_name,
            'comment' => $comment,
            'owner_name' => $owner_name
        ]);
    }

    public function updateGroupAttrs($group_name, $attrs, $to_del_attrs = []) {
        return $this->sendXmlRpcRequest('group.updateGroupAttrs', [
            'group_name' => $group_name,
            'attrs' => $attrs,
            'to_del_attrs' => $to_del_attrs
        ]);
    }

    public function delGroup($group_name) {
        return $this->sendXmlRpcRequest('group.delGroup', [
            'group_name' => $group_name
        ]);
    }

    // =============================================
    // Miscellaneous Methods
    // =============================================

    public function getConsoleBuffer() {
        return $this->sendXmlRpcRequest('log_console.getConsoleBuffer');
    }
}