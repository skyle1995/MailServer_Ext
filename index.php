<?php
/**
 * 入口文件：动态加载控制器和处理请求
 * 
 * 本文件作为应用程序的入口点，负责初始化环境、路由请求到相应的控制器和方法
 */

// 引入自动加载器
require_once __DIR__ . '/vendor/autoload.php';

// 使用相关类
use Core\Request;

// 使用全局配置变量
global $config;

// 注意：现在只使用查询参数形式访问API（例如：?controller=api&action=processResult）
// 不再支持/api/xxx形式的路径访问

// 处理action参数中的短横线，转换为驼峰命名
if (isset($_GET['action']) && strpos($_GET['action'], '-') !== false) {
    $action = $_GET['action'];
    // 将短横线转换为下划线
    $action = str_replace('-', '_', $action);
    
    // 将下划线形式转换为驼峰式命名
    $parts = explode('_', $action);
    $newAction = strtolower($parts[0]);
    for ($i = 1; $i < count($parts); $i++) {
        $newAction .= ucfirst(strtolower($parts[$i]));
    }
    
    $_GET['action'] = $newAction;
    
    // 调试输出
    error_log("转换action参数: {$action} -> {$_GET['action']}");
}

// 获取请求的控制器和方法
$controllerName = Request::get('controller', 'Home');
$actionName = Request::get('action', 'index');

// 规范化控制器名称（首字母大写）
$controllerName = ucfirst(strtolower($controllerName));

// 构建完整的控制器类名
$controllerClass = "Controllers\\{$controllerName}Controller";

// 构建方法名
$methodName = strtolower($actionName) . 'Action';

// 检查控制器类是否存在
if (!class_exists($controllerClass)) {
    // 控制器不存在，使用默认控制器
    $controllerClass = "Controllers\\HomeController";
    $methodName = 'indexAction';
}

// 创建控制器实例
$controller = new $controllerClass($config);

// 检查方法是否存在
if (!method_exists($controller, $methodName)) {
    // 方法不存在，使用默认方法
    error_log("方法不存在: {$controllerClass}::{$methodName}，使用默认方法indexAction");
    $methodName = 'indexAction';
}

// 重置响应类型为控制器默认值，确保每个Action可以独立控制其响应类型
if (method_exists($controller, 'resetResponseType')) {
    $controller->resetResponseType();
}

// 调用控制器方法处理请求
$controller->$methodName();