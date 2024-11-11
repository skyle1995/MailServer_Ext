<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 重置/改密界面

session_start();

include("config.php");
include("common.php");

$title = "重置密码";

function submit_form($config) {
    $_user = $_POST['_user'];
    $_oldpass = $_POST['_oldpass'];
    $_newpass = $_POST['_newpass'];
    $_quota = $_POST['_quota'];
    
    $username = $_user;
    $password =  $_newpass;
    
    if (!is_numeric($_quota) or $_quota <= 0 or empty($_quota)) {
        $_quota = "5";
    }
    
    // 使用filter_var()函数和FILTER_VALIDATE_EMAIL过滤器来判断（冷门域名会判断错误，已弃用）
    // if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    //     $response = array("status" => false, "msg" => "提交邮箱不合法，请检查后再试！");
    //     exit(json_encode($response));
    // }
    
    // 检查邮箱是否在配置库
    if (!isValidEmailDomain($username, $config['hosts'])) {
        $response = array("status" => false, "msg" => "提交邮箱不存在，请检查后再试！");
        exit(json_encode($response));
    }
    
    $atPos = strpos($username, '@');
    // 如果找到了@符号
    if ($atPos !== false) {
        // 使用substr()获取@符号左边的文本
        $full_name = substr($username, 0, $atPos);
    } else {
        // 如果没有找到@符号
        $response = array("status" => false, "msg" => "未识别到账号名，请检查后再试！");
        exit(json_encode($response));
    }
    
    if(!PasswordConfirmation($password)) {
        $response = array("status" => false, "msg" => "密码包括大小写字母和数字且长度不小于8");
        exit(json_encode($response));
    }

    if (empty($username) || empty($password) || empty($full_name)) {
        $response = array("status" => false, "msg" => "所有字段参数都必须填写！");
        exit(json_encode($response));
    }
    
    if($_oldpass === $_newpass) {
        $response = array("status" => false, "msg" => "新密码与旧密码相同，取消修改！");
        exit(json_encode($response));
    }
    
    if($_oldpass !== $config["adminkey"]){
        if($config["VerifyType"] === 1){
            $ret = CheckMailbox($config["VerifyAddr"], $username, $_oldpass);
            if ($ret !== true) {
                $response = array("status" => false, "msg" => "账号或密码错误，请重新尝试！");
                exit(json_encode($response));
            }
        } elseif($config["VerifyType"] === 2) {
            $domain = $config["VerifyAddr"] . "/?_task=login";
            $ret = curl_senior($domain,"GET",0,0,0,0,0,0,1);
            
            // 处理结果内容
            $val =  explode("\r\n\r\n",$ret,2);
            $header = $val[0];
            $body = $val[1];
            
            // 取出cookie信息
            $cookie = getHeader($header,"Set-Cookie");
            
            // 从body中提取 Token
            preg_match('/name="_token" value="(.*?)"/', $body, $token_match);
            $token = isset($token_match[1]) ? $token_match[1] : '';
            if(empty($token)) {
                $response = array("status" => false, "msg" => "发生错误，获取Token失败！");
                exit(json_encode($response));
            }
            
            // 登录数据
            $post_fields = [
                '_task' => 'login',
                '_token' => $token,
                '_action' => 'login',
                '_timezone' => 'Asia/Shanghai',
                '_url' => '',
                '_user' => $username,
                '_pass' => $_oldpass
            ];
            
            $ret = curl_senior($domain,"POST",http_build_query($post_fields),$cookie,0,0,0,0,1);
            
            $val =  explode("\r\n\r\n",$ret,2);
            
            $header = $val[0];
            $body = $val[1];
            
            // 取出cookie信息
            $cookie = getHeader($header,"Set-Cookie");
            $location = getHeader($header,"Location");
            
            // 验证成功后会跳转，直接判断状态码
            if (getHttpStatusCode($header) === 302) {
                if(empty($location)) {
                    $response = array("status" => false, "msg" => "账号密码校验失败，状态异常！");
                    exit(json_encode($response));
                }
            } else {
                // 这里也可以改为账号或密码错误的提示，反正是验证账号密码失败了
                $response = array("status" => false, "msg" => "账号密码校验失败，错误码：" . getHttpStatusCode($header));
                exit(json_encode($response));
            }
        } else {
            $response = array("status" => false, "msg" => "配置错误，请联系管理员！");
            exit(json_encode($response));
        }
        
    }

    $url = $config["panel"] . '/plugin?action=a&name=mail_sys&s=update_mailbox';
    $p_data = get_key_data($config["apikey"]) + array(
        'username' => $username,
        'password' => $password,
        'full_name' => $full_name,
        'quota' => $_quota . ' GB',
        'active' => 1,
        'is_admin' => 0
    );

    $result = curl_senior($url, "POST", http_build_query($p_data));

    if ($result === FALSE) {
        $response = array("status" => false, "msg" => "请求失败");
    } else {
        $response = json_decode($result, true);
        if ($response['status']) {
            $response = array("status" => true, "msg" => "重置密码成功！");
        } else {
            $response = array("status" => false, "msg" => $result);
        }
    }

    exit(json_encode($response));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($config["openrep"]) {
        submit_form($config);
    } else {
        $response = array("status" => false, "msg" => "重置密码已被禁用，请联系管理员处理！");
        exit(json_encode($response));
    }
}

include("header.php");
?>
<body class="task-login action-none">
    <div id="layout">
        <h1 class="voice">Roundcube Webmail 重置密码</h1>
        <div id="layout-content" class="selected no-navbar" role="main">
            <img src="skins/elastic/images/logo.svg" id="logo" alt="Logo">
            <form id="login-form" name="login-form" method="post" class="propform">
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
                    <?=$config["sitename"]?> · <a href="/">登录邮箱</a> · <a href="/webmail/reg">注册邮箱</a>
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
            const idsToAppend = ['_user', '_oldpass', '_newpass'];
            
            // 遍历这些ID，并将它们添加到 formData 中
            idsToAppend.forEach(id => {
                const element = form.querySelector(`#${id}`);
                if (element) {
                    // 注意这里我们使用元素的 name 属性来添加到 formData，
                    // 但我们可以使用任何唯一键，这里仅为了示例保持使用 name
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