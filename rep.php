<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 重置/改密界面

session_start();
header('Content-type:text/json');

include("config.php");
include("common.php");

$title = "重置密码";

function submit_form($config) {
    validateFormInputs($_POST, ['_user', '_oldpass', '_newpass']);

    $_user = sanitizeInput($_POST['_user']);
    $_oldpass = isset($_POST['_key']) ? sanitizeInput($_POST['_key']) : sanitizeInput($_POST['_oldpass']);
    $_newpass = sanitizeInput($_POST['_newpass']);

    // 处理配额
    $_quota = isset($_POST['_quota']) && is_numeric($_POST['_quota']) && $_POST['_quota'] > 0 ? $_POST['_quota'] : $config['Default_Quota'];

    // 检查用户输入的邮箱域名是否在配置的合法主机列表中
    if (!isValidEmailDomain($_user, $config['Hosts'])) {
        // 如果邮箱域名不合法，则发送错误消息提示用户
        handleMsg(false, ERROR_EMAIL_NOT_FOUND);
    }
    // 检查用户输入的电子邮件地址中是否包含 '@' 符号
    $atPos = strpos($_user, '@');
    if ($atPos === false) {
        // 如果没有找到 '@' 符号，提示用户电子邮件地址格式不正确
        handleMsg(false, ERROR_ACCOUNT_NAME);
    }

    // 提取电子邮件地址中的用户名部分
    $full_name = substr($_user, 0, $atPos);

    // 验证新密码是否符合复杂性要求
    if (!PasswordConfirmation($_newpass)) {
        // 如果密码不符合要求，提示用户密码需要包含大小写字母和数字，且长度不小于8
        handleMsg(false, ERROR_INVALID_ACCOUNT);
    }

    // 检查新旧密码是否相同
    if ($_oldpass === $_newpass) {
        // 如果新密码与旧密码相同，取消修改操作
        handleMsg(false, ERROR_SAME_PASSWORD);
    }

    if ($_oldpass !== $config["adminKey"]) {
        if ($config["verifyType"] === 1) {
            if (!CheckMailbox($config["verifyAddr"], $_user, $_oldpass)) {
                handleMsg(false, ERROR_INVALID_OLD_PASSWORD);
            }
        } elseif ($config["verifyType"] === 2) {
            // 简化并优化 curl_senior 调用
            $domain = $config["verifyAddr"] . "/?_task=login";
            $ret = curl_senior($domain, "GET", 0, 0, 0, 0, 0, 0, 1);
            $val = explode("\r\n\r\n", $ret, 2);
            $header = $val[0];
            $body = $val[1];

            $cookie = getHeader($header, "Set-Cookie");
            preg_match('/name="_token" value="(.*?)"/', $body, $token_match);
            $token = isset($token_match[1]) ? $token_match[1] : '';

            if (empty($token)) {
                handleMsg(false, ERROR_TOKEN_FAILURE);
            }

            $post_fields = [
                '_task' => 'login',
                '_token' => $token,
                '_action' => 'login',
                '_timezone' => 'Asia/Shanghai',
                '_url' => '',
                '_user' => $_user,
                '_pass' => $_oldpass
            ];

            $ret = curl_senior($domain, "POST", http_build_query($post_fields), $cookie, 0, 0, 0, 0, 1);
            $val = explode("\r\n\r\n", $ret, 2);
            $header = $val[0];
            $body = $val[1];

            $cookie = getHeader($header, "Set-Cookie");
            $location = getHeader($header, "Location");

            if (getHttpStatusCode($header) !== 302 || empty($location)) {
                handleMsg(false, ERROR_LOGIN_FAILURE);
            }
        } elseif ($config["verifyType"] === 3) {
            $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=get_mailuser';
            $data = ['username' => $_user];
            if ($config["verifyAddr"] === true) {
                $data['domain'] = substr(strrchr($_user, '@'), 1);
            }

            $p_data = get_key_data($config["apikey"]) + $data;
            $result = curl_senior($url, "POST", http_build_query($p_data));

            $response = json_decode($result, true);
            if (count($response["data"]['data']) < 1 || $_user !== $response["data"]['data'][0]['username'] || $_oldpass !== $response["data"]['data'][0]['password']) {
                handleMsg(false, ERROR_INVALID_CREDENTIALS);
            }
        } else {
            handleMsg(false, ERROR_CONFIG_ERROR);
        }
    }

    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=update_mailbox';
    $p_data = get_key_data($config["apikey"]) + [
        'username' => $_user,
        'password' => $_newpass,
        'full_name' => $full_name,
        'quota' => $_quota . ' GB',
        'active' => 1,
        'is_admin' => 0
    ];

    $result = curl_senior($url, "POST", http_build_query($p_data));

    if ($result === FALSE) {
        handleMsg(false, ERROR_REQUEST_FAILED);
    }

    $response = json_decode($result, true);
    if ($response['status']) {
        handleMsg(true, SUCCESS_PASSWORD_RESET);
    } else {
        handleMsg(false, $response);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$config["openRep"]) {
        handleMsg(false, ERROR_PASSWORD_RESET_DISABLED);
    }

    if (!isset($_POST['_key']) && !isset($_POST['_csrf'])) {
        exit(json_encode(array("status" => false, "msg" => ERROR_CSRF_TOKEN)));
    }
    
    if (!isset($_POST['_key']) && !validateCsrfToken($_POST['_csrf'])) {
        exit(json_encode(array("status" => false, "msg" => ERROR_CSRF_TOKEN)));
    }
    
    if(isset($_POST['_key']) && $_POST['_key'] !== $config["adminKey"]) {
        exit(json_encode(array("status" => false, "msg" => ERROR_ADMIN_KEY_INVALID)));
    } else {
        $_POST['_oldpass'] = $_POST['_key'];
    }

    submit_form($config);
}

header('Content-type:text/html; charset=UTF-8');
include("header.php");
?>
<body class="task-login action-none">
    <div id="layout">
        <h1 class="voice">Roundcube Webmail 重置密码</h1>
        <div id="layout-content" class="selected no-navbar" role="main">
            <img src="skins/elastic/images/logo.svg" id="logo" alt="Logo">
            <form id="login-form" name="login-form" method="post" class="propform">
                <input type="hidden" id="_csrf" name="_csrf" value="<?= generateCsrfToken(); ?>">
                <table>
                    <tbody>
                        <tr>
                            <td class="title">
                                <label for="rcmloginuser">邮箱账号</label>
                            </td>
                            <td class="input">
                                <input id="_user" name="_user" required size="40" class="form-control" autocapitalize="off" autocomplete="off" value="" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td class="title">
                                <label for="rcmloginpwd">原始密码</label>
                            </td>
                            <td class="input">
                                <input id="_oldpass" name="_key" required size="40" class="form-control" autocapitalize="off" autocomplete="off" type="password">
                            </td>
                        </tr>
                        <tr>
                            <td class="title">
                                <label for="rcmloginpwd">新的密码</label>
                            </td>
                            <td class="input">
                                <input id="_newpass" name="_pass" required size="40" class="form-control" autocapitalize="off" autocomplete="off" type="password">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="formbuttons"><button type="submit" id="rcmloginsubmit" class="button mainaction submit">重置</button></p>
                <div class="message" id="message"></div>
                <div id="login-footer" role="contentinfo" style="margin-top: 10px;">
                    <?= sanitizeInput($config["sitename"]); ?> · <a href="/">登录邮箱</a> · <a href="reg">注册邮箱</a>
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
            messageDiv.textContent = "正在重置密码...";
            messageDiv.className = 'message loading'; // 成功消息样式
            
            // 懒得修css了 直接进行魔改
            
            // 创建一个空的 FormData 对象
            let formData = new FormData();
        
            // 获取表单元素
            const form = document.getElementById('login-form');
        
            // 要处理的元素ID
            const idsToAppend = ['_csrf', '_user', '_oldpass', '_newpass'];
            
            // 遍历这些ID，并将它们添加到 formData 中
            idsToAppend.forEach(id => {
                const element = form.querySelector(`#${id}`);
                if (element) {
                    // 注意这里我们使用元素的 name 属性来添加到 formData，但我们可以使用任何唯一键，这里仅为了示例保持使用 name
                    // formData.append(element.name, element.value);
                    // 如果你确实需要基于ID作为键，可以这样做：
                    formData.append(id, element.value);
                }
            });
            
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
