<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Core;

/**
 * Validator类 - 提供表单验证功能
 * 
 * 该类包含了验证表单输入、管理错误信息等功能
 */
class Validator {
    /**
     * 存储验证错误信息
     * @var array
     */
    private $errors = [];
    
    /**
     * 验证输入值
     * 
     * @param mixed $value 需要验证的值
     * @param string $rules 验证规则，多个规则用|分隔
     * @param string $messages 错误消息，多个消息用|分隔，与规则一一对应
     * @return bool 验证通过返回true，否则返回false
     */
    public function validate($value, $rules, $messages = '') {
        // 将规则和消息拆分为数组
        $ruleArray = explode('|', $rules);
        $messageArray = $messages ? explode('|', $messages) : [];
        
        // 遍历规则进行验证
        foreach ($ruleArray as $index => $rule) {
            // 检查规则是否包含参数
            if (strpos($rule, ':') !== false) {
                list($ruleName, $ruleParam) = explode(':', $rule, 2);
            } else {
                $ruleName = $rule;
                $ruleParam = null;
            }
            
            // 获取对应的错误消息
            $errorMessage = isset($messageArray[$index]) ? $messageArray[$index] : $this->getDefaultErrorMessage($ruleName, $ruleParam);
            
            // 根据规则名称调用相应的验证方法
            $methodName = 'validate' . ucfirst($ruleName);
            if (method_exists($this, $methodName)) {
                $isValid = $this->$methodName($value, $ruleParam);
                if (!$isValid) {
                    $this->errors[] = $errorMessage;
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * 检查是否有验证错误
     * 
     * @return bool 有错误返回true，否则返回false
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * 获取所有验证错误
     * 
     * @return array 错误消息数组
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * 获取第一个验证错误
     * 
     * @return string|null 第一个错误消息，如果没有错误则返回null
     */
    public function getFirstError() {
        return !empty($this->errors) ? $this->errors[0] : null;
    }
    
    /**
     * 验证必填项
     * 
     * @param mixed $value 需要验证的值
     * @param mixed $param 规则参数（此规则不需要参数）
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateRequired($value, $param) {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 验证最小长度
     * 
     * @param mixed $value 需要验证的值
     * @param int $param 最小长度
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateMin($value, $param) {
        if (!is_numeric($param)) {
            return false;
        }
        
        $length = is_string($value) ? mb_strlen($value, 'UTF-8') : count($value);
        return $length >= (int)$param;
    }
    
    /**
     * 验证最大长度
     * 
     * @param mixed $value 需要验证的值
     * @param int $param 最大长度
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateMax($value, $param) {
        if (!is_numeric($param)) {
            return false;
        }
        
        $length = is_string($value) ? mb_strlen($value, 'UTF-8') : count($value);
        return $length <= (int)$param;
    }
    
    /**
     * 验证两个值是否相同
     * 
     * @param mixed $value 需要验证的值
     * @param mixed $param 比较的值
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateSame($value, $param) {
        return $value === $param;
    }
    
    /**
     * 验证邮箱格式
     * 
     * @param string $value 需要验证的值
     * @param mixed $param 规则参数（此规则不需要参数）
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateEmail($value, $param) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * 验证数字
     * 
     * @param mixed $value 需要验证的值
     * @param mixed $param 规则参数（此规则不需要参数）
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateNumeric($value, $param) {
        return is_numeric($value);
    }
    
    /**
     * 获取默认错误消息
     * 
     * @param string $rule 规则名称
     * @param mixed $param 规则参数
     * @return string 默认错误消息
     */
    /**
     * 验证用户名格式（小写字母和数字，字母开头）
     * 
     * @param string $value 需要验证的值
     * @param mixed $param 规则参数（此规则不需要参数）
     * @return bool 验证通过返回true，否则返回false
     */
    protected function validateUsername($value, $param) {
        // 验证用户名是否以小写字母开头，且只包含小写字母和数字
        return preg_match('/^[a-z][a-z0-9]*$/', $value) === 1;
    }
    
    protected function getDefaultErrorMessage($rule, $param) {
        $messages = [
            'required' => '此字段不能为空',
            'min' => '此字段长度不能小于' . $param,
            'max' => '此字段长度不能大于' . $param,
            'same' => '此字段必须与指定值相同',
            'email' => '请输入有效的电子邮件地址',
            'numeric' => '此字段必须是数字',
            'username' => '用户名必须以小写字母开头，且只能包含小写字母和数字'
        ];
        
        return isset($messages[$rule]) ? $messages[$rule] : '验证失败';
    }
}