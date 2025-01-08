<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 删除邮箱接口（无界面）

session_start();

include("config.php");
include("common.php");

if(!isset($_POST['_user'])) exit(json_encode(array("status" => false, "msg" => "请正确提交邮箱账号！")));
if(!isset($_POST['_key'])) exit(json_encode(array("status" => false, "msg" => "请正确提交超级密钥！")));

$_user = $_POST['_user'];
$_key = $_POST['_key'];

// 检查邮箱是否在配置库
if (!isValidEmailDomain($_user, $config['hosts'])) {
    $response = array("status" => false, "msg" => "提交邮箱不存在，请检查后再试！");
    exit(json_encode($response));
}

$atPos = strpos($_user, '@');
// 如果找到了@符号
if ($atPos !== false) {
    // 使用substr()获取@符号左边的文本
    $full_name = substr($_user, 0, $atPos);
} else {
    // 如果没有找到@符号
    $response = array("status" => false, "msg" => "未识别到账号名，请检查后再试！");
    exit(json_encode($response));
}

if(!AccountConfirmation($full_name, $config["RegLength"])) {
    $response = array("status" => false, "msg" => "提交账号不合法！");
    exit(json_encode($response));
}

if (empty($_user) || empty($_key)) {
    $response = array("status" => false, "msg" => "所有字段参数都必须填写！");
    exit(json_encode($response));
}

if($_key === $config["adminkey"]){
    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=delete_mailbox';
    $p_data = get_key_data($config["apikey"]) + array(
        'username' => $_user
    );
    
    $result = curl_senior($url, "POST", http_build_query($p_data));

    if ($result === FALSE) {
        $response = array("status" => false, "msg" => "请求失败");
    } else {
        $response = json_decode($result, true);
        if ($response['status']) {
            $response = array("status" => true, "msg" => "删除邮箱成功！");
        } else {
            $response = array("status" => false, "msg" => $result);
        }
    }

    exit(json_encode($response));
} else {
    $response = array("status" => false, "msg" => "管理员秘钥错误！");
    exit(json_encode($response));
}
