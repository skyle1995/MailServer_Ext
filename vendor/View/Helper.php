<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace View;

/**
 * Helper类 - 提供视图助手方法
 * 
 * 该类提供了常用的HTML生成方法，如表单元素、链接、列表等
 */
class Helper {
    /**
     * 转义HTML特殊字符
     * 
     * @param string $text 需要转义的文本
     * @return string 转义后的文本
     */
    public static function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * 生成HTML链接
     * 
     * @param string $url 链接URL
     * @param string $text 链接文本
     * @param array $attributes 链接属性
     * @return string 生成的HTML链接
     */
    public static function link($url, $text, array $attributes = []) {
        $escapedText = self::escape($text);
        $escapedUrl = self::escape($url);
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<a href=\"{$escapedUrl}\"{$attributesStr}>{$escapedText}</a>";
    }
    
    /**
     * 生成图片标签
     * 
     * @param string $src 图片源URL
     * @param string $alt 替代文本
     * @param array $attributes 图片属性
     * @return string 生成的HTML图片标签
     */
    public static function image($src, $alt = '', array $attributes = []) {
        $escapedSrc = self::escape($src);
        $escapedAlt = self::escape($alt);
        
        $attributes['alt'] = $escapedAlt;
        $attributesStr = self::buildAttributes($attributes);
        
        return "<img src=\"{$escapedSrc}\"{$attributesStr}>";
    }
    
    /**
     * 生成表单开始标签
     * 
     * @param string $action 表单提交URL
     * @param string $method 表单提交方法
     * @param array $attributes 表单属性
     * @return string 生成的HTML表单开始标签
     */
    public static function formStart($action = '', $method = 'post', array $attributes = []) {
        $escapedAction = self::escape($action);
        $escapedMethod = strtolower($method) === 'get' ? 'get' : 'post';
        
        $attributes['action'] = $escapedAction;
        $attributes['method'] = $escapedMethod;
        
        $attributesStr = self::buildAttributes($attributes);
        
        $html = "<form{$attributesStr}>";
        
        // 如果方法不是GET或POST，添加隐藏字段
        if (!in_array(strtolower($method), ['get', 'post'])) {
            $html .= "\n    <input type=\"hidden\" name=\"_method\" value=\"" . self::escape($method) . "\">";
        }
        
        // 添加CSRF令牌（如果函数存在且可调用）
        if (function_exists('csrf_token') && is_callable('csrf_token')) {
            try {
                $token = csrf_token();
                $html .= "\n    <input type=\"hidden\" name=\"_token\" value=\"" . self::escape($token) . "\">";
            } catch (\Exception $e) {
                // 如果调用失败，不添加CSRF令牌
            }
        }
        
        return $html;
    }
    
    /**
     * 生成表单结束标签
     * 
     * @return string 表单结束标签
     */
    public static function formEnd() {
        return "</form>";
    }
    
    /**
     * 生成文本输入框
     * 
     * @param string $name 输入框名称
     * @param string $value 输入框值
     * @param array $attributes 输入框属性
     * @return string 生成的HTML输入框
     */
    public static function textInput($name, $value = '', array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['type'] = 'text';
        $attributes['name'] = $escapedName;
        $attributes['value'] = $escapedValue;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成密码输入框
     * 
     * @param string $name 输入框名称
     * @param array $attributes 输入框属性
     * @return string 生成的HTML密码输入框
     */
    public static function passwordInput($name, array $attributes = []) {
        $escapedName = self::escape($name);
        
        $attributes['type'] = 'password';
        $attributes['name'] = $escapedName;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成隐藏输入框
     * 
     * @param string $name 输入框名称
     * @param string $value 输入框值
     * @param array $attributes 输入框属性
     * @return string 生成的HTML隐藏输入框
     */
    public static function hiddenInput($name, $value = '', array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['type'] = 'hidden';
        $attributes['name'] = $escapedName;
        $attributes['value'] = $escapedValue;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成文本区域
     * 
     * @param string $name 文本区域名称
     * @param string $value 文本区域内容
     * @param array $attributes 文本区域属性
     * @return string 生成的HTML文本区域
     */
    public static function textarea($name, $value = '', array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['name'] = $escapedName;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<textarea{$attributesStr}>{$escapedValue}</textarea>";
    }
    
    /**
     * 生成选择框
     * 
     * @param string $name 选择框名称
     * @param array $options 选项数组，格式为 [值 => 显示文本]
     * @param string|array $selected 已选中的值或值数组
     * @param array $attributes 选择框属性
     * @return string 生成的HTML选择框
     */
    public static function select($name, array $options, $selected = null, array $attributes = []) {
        $escapedName = self::escape($name);
        
        $attributes['name'] = $escapedName;
        
        // 处理多选情况
        if (isset($attributes['multiple']) && $attributes['multiple'] && substr($escapedName, -2) !== '[]') {
            $attributes['name'] .= '[]';
        }
        
        $attributesStr = self::buildAttributes($attributes);
        
        $html = "<select{$attributesStr}>";
        
        foreach ($options as $value => $text) {
            $escapedValue = self::escape($value);
            $escapedText = self::escape($text);
            
            $isSelected = '';
            if (is_array($selected) && in_array($value, $selected)) {
                $isSelected = ' selected';
            } elseif (!is_array($selected) && (string)$value === (string)$selected) {
                $isSelected = ' selected';
            }
            
            $html .= "\n    <option value=\"{$escapedValue}\"{$isSelected}>{$escapedText}</option>";
        }
        
        $html .= "\n</select>";
        
        return $html;
    }
    
    /**
     * 生成复选框
     * 
     * @param string $name 复选框名称
     * @param string $value 复选框值
     * @param bool $checked 是否选中
     * @param array $attributes 复选框属性
     * @return string 生成的HTML复选框
     */
    public static function checkbox($name, $value = '1', $checked = false, array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['type'] = 'checkbox';
        $attributes['name'] = $escapedName;
        $attributes['value'] = $escapedValue;
        
        if ($checked) {
            $attributes['checked'] = 'checked';
        }
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成单选按钮
     * 
     * @param string $name 单选按钮名称
     * @param string $value 单选按钮值
     * @param bool $checked 是否选中
     * @param array $attributes 单选按钮属性
     * @return string 生成的HTML单选按钮
     */
    public static function radio($name, $value, $checked = false, array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['type'] = 'radio';
        $attributes['name'] = $escapedName;
        $attributes['value'] = $escapedValue;
        
        if ($checked) {
            $attributes['checked'] = 'checked';
        }
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成提交按钮
     * 
     * @param string $text 按钮文本
     * @param array $attributes 按钮属性
     * @return string 生成的HTML提交按钮
     */
    public static function submitButton($text = '提交', array $attributes = []) {
        $escapedText = self::escape($text);
        
        $attributes['type'] = 'submit';
        $attributes['value'] = $escapedText;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成按钮
     * 
     * @param string $text 按钮文本
     * @param array $attributes 按钮属性
     * @return string 生成的HTML按钮
     */
    public static function button($text, array $attributes = []) {
        $escapedText = self::escape($text);
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<button{$attributesStr}>{$escapedText}</button>";
    }
    
    /**
     * 生成标签
     * 
     * @param string $for 关联的表单元素ID
     * @param string $text 标签文本
     * @param array $attributes 标签属性
     * @return string 生成的HTML标签
     */
    public static function label($for, $text, array $attributes = []) {
        $escapedFor = self::escape($for);
        $escapedText = self::escape($text);
        
        $attributes['for'] = $escapedFor;
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<label{$attributesStr}>{$escapedText}</label>";
    }
    
    /**
     * 生成无序列表
     * 
     * @param array $items 列表项数组
     * @param array $attributes 列表属性
     * @return string 生成的HTML无序列表
     */
    public static function ul(array $items, array $attributes = []) {
        return self::buildList('ul', $items, $attributes);
    }
    
    /**
     * 生成有序列表
     * 
     * @param array $items 列表项数组
     * @param array $attributes 列表属性
     * @return string 生成的HTML有序列表
     */
    public static function ol(array $items, array $attributes = []) {
        return self::buildList('ol', $items, $attributes);
    }
    
    /**
     * 构建HTML属性字符串
     * 
     * @param array $attributes 属性数组
     * @return string 属性字符串
     */
    private static function buildAttributes(array $attributes) {
        $attributesStr = '';
        
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $attributesStr .= " {$key}";
            } elseif ($value !== false && $value !== null) {
                $escapedValue = self::escape($value);
                $attributesStr .= " {$key}=\"{$escapedValue}\"";
            }
        }
        
        return $attributesStr;
    }
    
    /**
     * 构建列表HTML
     * 
     * @param string $type 列表类型（ul或ol）
     * @param array $items 列表项数组
     * @param array $attributes 列表属性
     * @return string 生成的HTML列表
     */
    private static function buildList($type, array $items, array $attributes) {
        $attributesStr = self::buildAttributes($attributes);
        
        $html = "<{$type}{$attributesStr}>";
        
        foreach ($items as $item) {
            if (is_array($item)) {
                // 如果是数组，则第一个元素是内容，第二个元素是属性
                $content = isset($item[0]) ? $item[0] : '';
                $itemAttributes = isset($item[1]) && is_array($item[1]) ? $item[1] : [];
                
                $itemAttributesStr = self::buildAttributes($itemAttributes);
                $escapedContent = self::escape($content);
                
                $html .= "\n    <li{$itemAttributesStr}>{$escapedContent}</li>";
            } else {
                // 如果不是数组，则直接作为内容
                $escapedItem = self::escape($item);
                $html .= "\n    <li>{$escapedItem}</li>";
            }
        }
        
        $html .= "\n</{$type}>";
        
        return $html;
    }
    
    /**
     * 生成通用输入框
     * 
     * @param string $type 输入框类型（text, password, email, number等）
     * @param string $name 输入框名称
     * @param string $value 输入框值
     * @param array $attributes 输入框属性
     * @return string 生成的HTML输入框
     */
    public static function input($type, $name, $value = '', array $attributes = []) {
        $escapedName = self::escape($name);
        $escapedValue = self::escape($value);
        
        $attributes['type'] = $type;
        $attributes['name'] = $escapedName;
        
        // 对于非隐藏字段且非按钮类型的输入框，设置value属性
        if ($type !== 'submit' && $type !== 'button' && $type !== 'reset' && $value !== '') {
            $attributes['value'] = $escapedValue;
        }
        
        $attributesStr = self::buildAttributes($attributes);
        
        return "<input{$attributesStr}>";
    }
    
    /**
     * 生成div元素
     * 
     * @param string $content div内容
     * @param array $attributes div属性
     * @return string 生成的HTML div元素
     */
    public static function div($content = '', array $attributes = []) {
        $attributesStr = self::buildAttributes($attributes);
        
        // 如果内容为空，直接返回自闭合的div
        if (empty($content)) {
            return "<div{$attributesStr}></div>";
        }
        
        return "<div{$attributesStr}>{$content}</div>";
    }
    
    /**
     * 生成表单组元素（用于Bootstrap表单）
     * 
     * @param string $label 标签HTML
     * @param string $input 输入框HTML
     * @param array $attributes 表单组属性
     * @return string 生成的HTML表单组
     */
    public static function formGroup($label = '', $input = '', array $attributes = []) {
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-group' : 'form-group';
        $attributesStr = self::buildAttributes($attributes);
        
        return "<div{$attributesStr}>{$label}{$input}</div>";
    }
}