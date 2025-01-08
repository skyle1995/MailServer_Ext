<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 注册界面

session_start();

include("config.php");
include("common.php");

$title = "注册邮箱";

function submit_form($config) {
    if(!isset($_POST['_user'])) exit(json_encode(array("status" => false, "msg" => "请正确提交邮箱名称！")));
    if(!isset($_POST['_pass'])) exit(json_encode(array("status" => false, "msg" => "请正确提交邮箱密码！")));
    if(!isset($_POST['_host'])) exit(json_encode(array("status" => false, "msg" => "请正确提交邮箱后缀！")));

    $_user = $_POST['_user'];
    $_pass = $_POST['_pass'];
    $_host = $_POST['_host'];

    if(isset($_POST['_quota'])) {
        $_quota = $_POST['_quota'];
        if (!is_numeric($_quota) or $_quota <= 0 or empty($_quota)) {
            $_quota = "5";
        }
    }else{
        $_quota = "5";
    }
    
    $atPos = strpos($_user, '@');
    // 如果找到了@符号
    if ($atPos !== false) {
        // 使用substr()获取@符号左边的文本
        $_user = substr($_user, 0, $atPos);
    }
    
    $_index = findEmailHostIndex($config["hosts"], $_host);
    if ($_index === null) {
        $response = array("status" => false, "msg" => "邮箱后缀不合法，请检查后再试！", "index" => $_index);
        exit(json_encode($response));
    }
    
    if(!AccountConfirmation($_user, $config["RegLength"])) {
        $response = array("status" => false, "msg" => "账号包括小写字母和数字且长度不小于" . $config["RegLength"]);
        exit(json_encode($response));
    }
    
    if(!PasswordConfirmation($_pass)) {
        $response = array("status" => false, "msg" => "密码包括大小写字母和数字且长度不小于8");
        exit(json_encode($response));
    }
    
    $username = $_user . $config["hosts"][intval($_index)];
    $password = $_pass;
    $full_name = $_user;

    if (empty($username) || empty($password) || empty($full_name)) {
        $response = array("status" => false, "msg" => "所有字段参数都必须填写！");
        exit(json_encode($response));
    }

    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=add_mailbox';
    $p_data = get_key_data($config["apikey"]) + array(
        'username' => $username,
        'password' => $password,
        'full_name' => $full_name,
        'quota' => $_quota . ' GB',
        'is_admin' => "0"
    );

    $result = curl_senior($url, "POST", http_build_query($p_data));

    if ($result === FALSE) {
        $response = array("status" => false, "msg" => "请求失败！");
    } else {
        $response = json_decode($result, true);
        if ($response['status']) {
            $response = array("status" => true, "msg" => "注册邮箱成功！");
        } else {
            if(!empty($response['msg'])){
                $response = array("status" => false, "msg" => $response['msg']);
            } else {
                $response = array("status" => false, "msg" => $result);
            }
        }
    }
    exit(json_encode($response));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($config["openreg"]) {
        submit_form($config);
    } else {
        $response = array("status" => false, "msg" => "注册邮箱已被禁用，请联系管理员处理！");
        exit(json_encode($response));
    }
}

include("header.php");
?>
<body class="task-login action-none">
    <div id="layout">
        <h1 class="voice">Roundcube Webmail 注册邮箱</h1>
        <div id="layout-content" class="selected no-navbar" role="main">
            <img src="skins/elastic/images/logo.svg" id="logo" alt="Logo">
            <form id="login-form" name="login-form" method="post" class="propform">
                <table>
                    <tbody>
                        <tr>
                            <td class="title">
                                <label for="rcmloginuser">邮箱前缀</label>
                            </td>
                            <td class="input">
                                <input name="_user" id="rcmloginuser" required size="40" class="form-control" autocapitalize="off" autocomplete="off" value="" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td class="title">
                                <label for="rcmloginpwd">邮箱密码</label>
                            </td>
                            <td class="input">
                                <input name="_pass" id="rcmloginpwd" required size="40" class="form-control" autocapitalize="off" autocomplete="off" type="password">
                            </td>
                        </tr>
                        <tr>
                            <td class="title">
                                <label for="rcmloginhost">邮箱后缀</label>
                            </td>
                            <td class="input">
                                <select name="_host" id="rcmloginhost" class="custom-select">
<?php
    foreach ($config["hosts"] as $key => $val) {
        echo "<option value='$key'>$val</option>";
    }
?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="formbuttons"><button type="submit" id="rcmloginsubmit" class="button mainaction submit">注册</button></p>
                <div class="message" id="message"></div>
                <div id="login-footer" role="contentinfo" style="margin-top: 10px;">
                    <?=$config["beian"]?>
                    <?=$config["sitename"]?> · <a href="/">登录邮箱</a> · <a href="/webmail/rep">重置密码</a>
                </div>
            </form>
        </div>
    </div>
    <script src="skins/elastic/deps/bootstrap.bundle.min.js"></script>
    <script src="skins/elastic/ui.min.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault(); // 防止表单默认提交
            
            var messageDiv = document.getElementById('message');
            messageDiv.style.display = 'block'; // 显示消息区域
            messageDiv.textContent = "正在注册邮箱...";
            messageDiv.className = 'message loading'; // 成功消息样式

            var formData = new FormData(this);
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                if (data.status) {
                    messageDiv.textContent = data.msg;
                    messageDiv.className = 'message success'; // 成功消息样式
                    // setTimeout(function() {
                    //     window.location.href = '/'; // 注册成功后跳回根目录
                    // }, 1000); // 3秒后跳转到主页
                } else {
                    messageDiv.textContent = data.msg;
                    messageDiv.className = 'message'; // 错误消息样式
                }
            })
            .catch(error => {
                messageDiv.textContent = '发生错误';
                messageDiv.className = 'message'; // 错误消息样式
            });
        });
    </script>
</body>
</html>