<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Core;

/**
 * Common类 - 提供通用功能函数
 * 
 * 该类包含了处理消息响应、生成请求密钥等通用功能
 */
class Common {
    /**
     * 处理消息响应
     *
     * @param mixed $code 响应的状态码
     * @param array $data 响应的数据
     */
    public static function handleMsg($code, $data) {
        header('Content-type:text/json');
        $msg = isset($data['msg']) ? $data['msg'] : '';
        exit(json_encode(array("code" => $code, "msg" => $msg, "data" => $data, "time" => time())));
    }

    /**
     * 生成请求密钥数据
     *
     * 该方法根据给定的API密钥生成一个包含请求令牌和请求时间的数组
     * 请求令牌是通过对当前时间戳和API密钥的MD5值进行再次MD5处理生成的
     * 这种方式旨在提供一个简单的方法来验证请求的合法性和时间性
     *
     * @param string $apikey API密钥，用于生成请求令牌
     * @return array 包含请求令牌（request_token）和请求时间（request_time）的数组
     */
    public static function getKeyData($apikey) {
        // 获取当前时间戳
        $now_time = time();
        
        // 返回包含请求令牌和请求时间的数组
        // 请求令牌通过将当前时间戳与API密钥的MD5值连接后再次进行MD5加密生成
        // 这样做可以确保每个请求的唯一性和时间性，用于验证请求的合法性和时效性
        return array(
            'request_token' => md5($now_time . md5($apikey)),
            'request_time' => $now_time,
        );
    }
    
    /**
     * 生成或获取CSRF令牌
     * 
     * 该方法用于生成或获取CSRF令牌，用于防止跨站请求伪造攻击
     * 令牌存储在会话中，确保跨请求的一致性
     * 
     * @return string CSRF令牌
     */
    public static function csrf_token() {
        // 确保会话已启动
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 如果会话中不存在CSRF令牌，则生成一个新的
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * 验证CSRF令牌
     * 
     * 该方法用于验证提交的CSRF令牌是否有效
     * 
     * @param string $token 要验证的CSRF令牌
     * @return bool 如果令牌有效则返回true，否则返回false
     */
    public static function verify_csrf_token($token) {
        // 确保会话已启动
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 如果会话中不存在CSRF令牌，则验证失败
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        // 验证提交的令牌是否与会话中的令牌匹配
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}