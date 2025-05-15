<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

session_start();
header('Content-type:text/json');

require_once("config.php");
require_once("common.php");

$title = "注册邮箱";

/**
 * 提交表单数据以创建新邮箱账户
 *
 * @param array $config 系统配置数组
 */
function submit_form($config) {
    // 输入验证
    validateFormInputs($_POST, ['_user', '_pass', '_host']);

    $_user = sanitizeInput($_POST['_user']);
    $_pass = sanitizeInput($_POST['_pass']);
    $_host = sanitizeInput($_POST['_host']);

    // 处理配额
    $_quota = isset($_POST['_quota']) && is_numeric($_POST['_quota']) && $_POST['_quota'] > 0 ? $_POST['_quota'] : 5;

    // 去除@符号及其后的部分
    if (strpos($_user, '@') !== false) {
        $_user = substr($_user, 0, strpos($_user, '@'));
    }

    // 验证邮箱主机名
    $_index = findEmailHostIndex($config["hosts"], $_host);
    if ($_index === null) {
        handleMsg(false, ERROR_INVALID_HOST);
    }

    // 验证账号格式
    if (!AccountConfirmation($_user, $config["regLength"])) {
        handleMsg(false, ERROR_INVALID_ACCOUNT . $config["regLength"]);
    }

    // 验证密码格式
    if (!PasswordConfirmation($_pass)) {
        handleMsg(false, ERROR_INVALID_PASSWORD);
    }

    $username = $_user . $config["hosts"][intval($_index)];

    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=add_mailbox';
    $p_data = get_key_data($config["apikey"]) + array(
        'username' => $username,
        'password' => $_pass,
        'full_name' => $_user,
        'quota' => $_quota . ' GB',
        'is_admin' => "0"
    );

    $result = curl_senior($url, "POST", http_build_query($p_data));

    if ($result === FALSE) {
        handleMsg(false, ERROR_REQUEST_FAILED);
    } else {
        $response = json_decode($result, true);
        if ($response['status']) {
            handleMsg(true, SUCCESS_REGISTER_MAILBOX);
        } else {
            handleMsg(false, !empty($response['msg']) ? $response['msg'] : ERROR_UNKNOWN);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$config["openRep"]) {
        handleMsg(false, ERROR_PASSWORD_REG_DISABLED);
    }

    if (!isset($_POST['_key']) && !isset($_POST['_csrf'])) {
        exit(json_encode(array("status" => false, "msg" => ERROR_CSRF_TOKEN)));
    }
    
    if (!isset($_POST['_key']) && !validateCsrfToken($_POST['_csrf'])) {
        exit(json_encode(array("status" => false, "msg" => ERROR_CSRF_TOKEN)));
    }
    
    if(isset($_POST['_key']) && $_POST['_key'] !== $config["adminKey"]) {
        exit(json_encode(array("status" => false, "msg" => ERROR_ADMIN_KEY_INVALID)));
    }
    
    submit_form($config);
}

header('Content-type:text/html; charset=UTF-8');
include("header.php");
?>
<body class="task-login action-none">
    <div id="layout">
        <h1 class="voice">Roundcube Webmail 注册邮箱</h1>
        <div id="layout-content" class="selected no-navbar" role="main">
            <img src="skins/elastic/images/logo.svg" id="logo" alt="Logo">
            <form id="login-form" name="login-form" method="post" class="propform">
                <input type="hidden" name="_csrf" value="<?= generateCsrfToken(); ?>">
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
                                        echo "<option value='$key'>" . htmlspecialchars($val) . "</option>";
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
                    <?php echo htmlspecialchars($config["beian"]); ?>
                    <?php echo htmlspecialchars($config["sitename"]); ?> · <a href="/">登录邮箱</a> · <a href="rep">重置密码</a>
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
