<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 启动会话
session_start();
header('Content-type:text/json');

// 包含配置文件和通用函数库
include("config.php");
include("common.php");

/**
 * 验证输入字符串是否符合指定长度范围
 * 
 * @param string $input 待验证的输入字符串
 * @param int $minLength 最小长度，默认为1
 * @param int $maxLength 最大长度，默认为255
 * @return bool 返回验证结果，符合长度范围返回true，否则返回false
 */
function validateInput($input, $minLength = 1, $maxLength = 255) {
    return is_string($input) && strlen($input) >= $minLength && strlen($input) <= $maxLength;
}

// 验证用户输入的邮箱格式
if (!isset($_POST['_user']) || !validateInput($_POST['_user'])) {
    handleMsg(false, ERROR_INVALID_EMAIL);
}
// 验证用户输入的密钥格式
if (!isset($_POST['_key']) || !validateInput($_POST['_key'])) {
    handleMsg(false, ERROR_INVALID_KEY);
}

// 进一步验证邮箱格式并获取用户名部分
$_user = filter_var($_POST['_user'], FILTER_VALIDATE_EMAIL);
if (false === $_user) {
    handleMsg(false, ERROR_INVALID_EMAIL);
}

// 获取密钥
$_key = $_POST['_key'];

// 检查邮箱是否在配置库
if (!isValidEmailDomain($_user, $config['hosts'])) {
    handleMsg(false, ERROR_EMAIL_NOT_FOUND);
}

// 获取邮箱用户名和域名的分隔位置
$atPos = strpos($_user, '@');
if ($atPos === false) {
    handleMsg(false, ERROR_ACCOUNT_NAME);
}

// 提取邮箱用户名部分
$full_name = substr($_user, 0, $atPos);

// 验证邮箱和密钥是否非空
if (empty($_user) || empty($_key)) {
    handleMsg(false, ERROR_ALL_FIELDS_REQUIRED);
}

// 验证管理员密钥
if ($_key !== $config["adminKey"]) {
    handleMsg(false, ERROR_ADMIN_KEY_INVALID);
}

// 尝试执行删除邮箱操作
try {
    // 构造请求URL和数据
    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=delete_mailbox';
    $p_data = get_key_data($config["apikey"]) + array(
        'username' => $_user
    );

    // 发起HTTP请求
    $result = curl_senior($url, "POST", http_build_query($p_data));

    // 检查请求结果
    if ($result === FALSE) {
        throw new Exception(ERROR_REQUEST_FAILED);
    }

    // 解析响应数据
    $response = json_decode($result, true);
    if ($response['status']) {
        echo json_encode(array("status" => true, "msg" => SUCCESS_DELETE_MAILBOX));
    } else {
        echo json_encode(array("status" => false, "msg" => $response['msg'] ?? ERROR_UNKNOWN));
    }
} catch (Exception $e) {
    handleMsg(false, $e->getMessage());
}
