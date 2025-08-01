<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Controllers;

use View\Template;
use Core\Request;

/**
 * BaseController类 - 所有控制器的基类
 * 
 * 该类提供了控制器的基本功能，如模板渲染、重定向等
 */
class BaseController {
    /**
     * 模板实例
     * @var Template
     */
    protected $template;
    
    /**
     * 配置数组
     * @var array
     */
    protected $config;
    
    /**
     * 构造函数
     * 
     * @param array $config 配置数组
     */
    public function __construct($config = []) {
        // 创建模板实例
        $this->template = new Template();
        
        // 设置默认布局
        $this->template->setLayout('layout');
        
        // 如果传入了配置，使用传入的配置，否则加载配置文件
        if (!empty($config)) {
            $this->config = $config;
        } else {
            // 加载配置
            $this->loadConfig();
        }
        
        // 设置基本模板变量
        $this->template->assign([
            'sitename' => $this->config['sitename'] ?? '邮件服务器插件',
            'webmail' => $this->config['webmail'] ?? '',
            'open_register' => $this->config['openRegister'] ?? false,
            'open_replace' => $this->config['openReplace'] ?? false
        ]);
        
        // 初始化默认响应类型
        $this->defaultResponseType = self::RESPONSE_TYPE_HTML;
        $this->responseType = $this->defaultResponseType;
    }
    
    /**
     * 加载配置文件
     */
    protected function loadConfig() {
        global $config;
        
        // 使用全局配置变量
        if (isset($config) && is_array($config)) {
            $this->config = $config;
        } else {
            // 如果全局配置不存在，尝试直接加载配置文件
            $configFile = dirname(dirname(__DIR__)) . '/config/config.inc.php';
            if (file_exists($configFile)) {
                // 包含配置文件前先清空$config变量
                $config = [];
                require $configFile;
                $this->config = $config;
            } else {
                $this->config = [];
            }
        }
        
        // 确保配置键名兼容性
        if (!isset($this->config['open_register']) && isset($this->config['openRegister'])) {
            $this->config['open_register'] = $this->config['openRegister'];
        }
        
        if (!isset($this->config['open_replace']) && isset($this->config['openReplace'])) {
            $this->config['open_replace'] = $this->config['openReplace'];
        }
    }
    
    /**
     * 响应类型枚举
     */
    const RESPONSE_TYPE_HTML = 'html';
    const RESPONSE_TYPE_JSON = 'json';
    const RESPONSE_TYPE_TEXT = 'text';
    
    /**
     * 当前响应类型
     * @var string
     */
    protected $responseType = self::RESPONSE_TYPE_HTML;
    
    /**
     * 默认响应类型
     * @var string
     */
    protected $defaultResponseType = self::RESPONSE_TYPE_HTML;
    
    /**
     * 设置控制器默认响应类型
     * 
     * 在控制器构造函数中调用此方法可以设置整个控制器的默认响应类型
     * 
     * @param string $type 响应类型（html, json, text）
     * @return $this 支持链式调用
     */
    protected function setDefaultResponseType($type) {
        $this->defaultResponseType = $type;
        $this->responseType = $type; // 同时设置当前响应类型
        
        return $this;
    }
    
    /**
     * 设置当前Action的响应类型
     * 
     * 在Action方法中调用此方法可以覆盖控制器默认的响应类型
     * 
     * @param string $type 响应类型（html, json, text）
     * @return $this 支持链式调用
     */
    protected function setResponseType($type) {
        $this->responseType = $type;
        
        // 设置相应的Content-Type头
        switch ($type) {
            case self::RESPONSE_TYPE_JSON:
                header('Content-Type: application/json; charset=utf-8');
                break;
            case self::RESPONSE_TYPE_TEXT:
                header('Content-Type: text/plain; charset=utf-8');
                break;
            case self::RESPONSE_TYPE_HTML:
            default:
                header('Content-Type: text/html; charset=utf-8');
                break;
        }
        
        return $this;
    }
    
    /**
     * 获取当前响应类型
     * 
     * @param bool $getDefault 是否获取默认响应类型
     * @return string 响应类型
     */
    protected function getResponseType($getDefault = false) {
        return $getDefault ? $this->defaultResponseType : $this->responseType;
    }
    
    /**
     * 重置响应类型为默认值
     * 
     * 在每个Action执行前调用此方法可以确保响应类型重置为控制器默认值
     * 
     * @return $this 支持链式调用
     */
    public function resetResponseType() {
        $this->responseType = $this->defaultResponseType;
        
        // 设置相应的Content-Type头
        switch ($this->responseType) {
            case self::RESPONSE_TYPE_JSON:
                header('Content-Type: application/json; charset=utf-8');
                break;
            case self::RESPONSE_TYPE_TEXT:
                header('Content-Type: text/plain; charset=utf-8');
                break;
            case self::RESPONSE_TYPE_HTML:
            default:
                header('Content-Type: text/html; charset=utf-8');
                break;
        }
        
        return $this;
    }
    
    /**
     * 渲染视图
     * 
     * @param string $view 视图名称
     * @param array $data 视图数据
     * @return string 渲染后的HTML
     */
    protected function render($view, array $data = []) {
        return $this->template->render($view, $data);
    }
    
    /**
     * 显示视图
     * 
     * @param string $view 视图名称
     * @param array $data 视图数据
     */
    protected function display($view, array $data = []) {
        // 根据响应类型决定是否渲染页面
        if ($this->responseType === self::RESPONSE_TYPE_HTML) {
            $this->template->display($view, $data);
        } else {
            // 非HTML类型，返回JSON格式的错误信息
            $this->jsonResponse([
                'status' => 'error',
                'message' => '当前响应类型不支持渲染HTML内容'
            ]);
        }
    }
    
    /**
     * 重定向到指定URL
     * 
     * @param string $url 目标URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * 获取请求参数
     * 
     * @param string $name 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值
     */
    protected function getParam($name, $default = null) {
        return Request::input($name, $default);
    }
    
    /**
     * 获取GET参数
     * 
     * @param string $name 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值
     */
    protected function getGetParam($name, $default = null) {
        return Request::get($name, $default);
    }
    
    /**
     * 获取POST参数
     * 
     * @param string $name 参数名
     * @param mixed $default 默认值
     * @return mixed 参数值
     */
    protected function getPostParam($name, $default = null) {
        return Request::post($name, $default);
    }
    
    /**
     * 检查是否为POST请求
     * 
     * @return bool 是否为POST请求
     */
    protected function isPost() {
        return Request::isPost();
    }
    
    /**
     * 检查是否为GET请求
     * 
     * @return bool 是否为GET请求
     */
    protected function isGet() {
        return Request::isGet();
    }
    
    /**
     * 检查是否为AJAX请求
     * 
     * @return bool 是否为AJAX请求
     */
    protected function isAjax() {
        return Request::isAjax();
    }
    
    /**
     * 发送JSON响应
     * 直接输出JSON数据并退出
     * 
     * @param array $data 响应数据
     * @param int $statusCode HTTP状态码
     */
    protected function jsonResponse($data, $statusCode = 200) {
        // 设置响应类型为JSON
        $this->setResponseType(self::RESPONSE_TYPE_JSON);
        
        // 设置HTTP状态码
        http_response_code($statusCode);
        
        // 输出JSON数据
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG);
        exit;
    }
    
    /**
     * 发送文本响应
     * 直接输出文本数据并退出
     * 
     * @param string $text 响应文本
     * @param int $statusCode HTTP状态码
     */
    protected function textResponse($text, $statusCode = 200) {
        // 设置响应类型为文本
        $this->setResponseType(self::RESPONSE_TYPE_TEXT);
        
        // 设置HTTP状态码
        http_response_code($statusCode);
        
        // 输出文本数据
        echo $text;
        exit;
    }
}