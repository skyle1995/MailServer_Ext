<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Core;

/**
 * Request类 - 提供请求数据处理功能
 * 
 * 该类包含了清理输入数据、验证表单输入、获取请求参数等功能
 */
class Request {
    /**
     * 清理输入数据
     * 使用trim函数去除输入数据两端的空白字符，然后使用htmlspecialchars函数转换特殊字符为HTML实体
     * 这样可以防止XSS攻击，确保输入的数据在HTML环境中安全展示
     * 
     * @param mixed $input 需要清理的输入数据
     * @return mixed 清理后的输入数据
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return is_string($input) ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : $input;
    }

    /**
     * 验证表单输入字段
     * 检查每个指定的字段是否已提交且不为空
     * 如果任何一个字段未提交或为空，则返回false
     * 
     * @param array $p_data 表单数据数组
     * @param array $fields 需要验证的表单字段数组
     * @return bool 验证通过返回true，否则返回false
     */
    public static function validateFormInputs($p_data, $fields) {
        foreach ($fields as $field) {
            if (!isset($p_data[$field]) || trim($p_data[$field]) === '') {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 获取GET参数
     * 
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function get($key, $default = null) {
        return isset($_GET[$key]) ? self::sanitizeInput($_GET[$key]) : $default;
    }
    
    /**
     * 获取POST参数
     * 
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function post($key, $default = null) {
        return isset($_POST[$key]) ? self::sanitizeInput($_POST[$key]) : $default;
    }
    
    /**
     * 获取REQUEST参数（GET或POST）
     * 
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值或默认值
     */
    public static function input($key, $default = null) {
        return isset($_REQUEST[$key]) ? self::sanitizeInput($_REQUEST[$key]) : $default;
    }
    
    /**
     * 检查是否为POST请求
     * 
     * @return bool 是否为POST请求
     */
    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * 检查是否为GET请求
     * 
     * @return bool 是否为GET请求
     */
    public static function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * 检查是否为AJAX请求
     * 
     * @return bool 是否为AJAX请求
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}