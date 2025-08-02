<div class="panel panel-primary" style="max-width: 480px; margin: 0 auto; border-radius: 6px; box-shadow: none;">
    <div class="panel-heading" style="text-align: center; border-radius: 6px 6px 0 0; border-bottom: 0px solid rgb(120, 194, 255);">
        <h3 class="panel-title">注册邮箱账号</h3>
    </div>
    <div class="panel-body">
        <div id="server-messages"></div>
        <form id="registerForm">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="请输入账号名" value="<?php echo isset($username) ? $username : ''; ?>" required>
                </div>
                <?php if (isset($errors['username'])): ?>
                <span class="help-block text-danger"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                    <select class="form-control" id="domain" name="domain" required>
                        <option value="">请选择邮箱后缀</option>
                        <?php if (isset($domains) && is_array($domains)): ?>
                            <?php foreach ($domains as $domain): ?>
                            <option value="<?php echo $domain; ?>" <?php echo (isset($selected_domain) && $selected_domain == $domain) ? 'selected' : ''; ?>>
                                <?php echo $domain; ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="请输入您的昵称" value="<?php echo isset($full_name) ? $full_name : ''; ?>" required>
                </div>
                <?php if (isset($errors['full_name'])): ?>
                <span class="help-block text-danger"><?php echo $errors['full_name']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="请输入注册密码" required>
                </div>
                <?php if (isset($errors['password'])): ?>
                <span class="help-block text-danger"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="请再次输入密码" required>
                </div>
                <?php if (isset($errors['confirm_password'])): ?>
                <span class="help-block text-danger"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>
                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入验证码" required>
                    <span class="input-group-addon" style="padding: 0; background: none; border: none;">
                        <img id="captcha_img" src="?controller=home&action=captcha" alt="验证码" style="height: 40px; cursor: pointer; border: 1px solid #eaeaea; border-radius: 4px;" onclick="refreshCaptcha()">
                    </span>
                </div>
                <?php if (isset($errors['captcha'])): ?>
                <span class="help-block text-danger"><?php echo $errors['captcha']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="text-align: center; margin-top: 20px;">
                <button type="submit" class="btn btn-primary btn-lg">确认注册</button>
                <a href="." class="btn btn-default btn-lg">返回首页</a>
            </div>
        </form>
    </div>
</div>

<script>
// 刷新验证码函数
function refreshCaptcha() {
    document.getElementById('captcha_img').src = '?controller=home&action=captcha&t=' + new Date().getTime();
}

document.addEventListener('DOMContentLoaded', function() {
    // 确保 jQuery 已加载
    if (typeof $ !== 'undefined') {
        // 检查是否禁用注册功能
        <?php if (isset($disabled) && $disabled): ?>
        layer.alert('<?php echo isset($disabled_message) ? $disabled_message : "注册功能当前未开放"; ?>', {
            icon: 0,
            title: '提示',
            closeBtn: 0,
            btn: ['返回首页'],
            yes: function(index, layero) {
                window.location.href = '.';
            }
        });
        <?php endif; ?>
        // 处理表单提交

        // 表单提交前验证并通过AJAX提交
        $('#registerForm').on('submit', function(e) {
            e.preventDefault(); // 阻止表单默认提交行为
            
            var username = $('#username').val().trim();
            var domain = $('#domain').val();
            var password = $('#password').val();
            var confirmPassword = $('#confirm_password').val();
            var fullName = $('#full_name').val().trim();
            var captcha = $('#captcha').val().trim();
            var isValid = true;
            
            // 清除之前的错误提示
            $('.help-block.text-danger').remove();
            
            var errorMessage = '';
            
            // 验证用户名
            if (username === '') {
                errorMessage = '请输入账号名';
                isValid = false;
            } else if (username.length < 3) {
                errorMessage = '账号名长度不能少于3个字符';
                isValid = false;
            }
            
            // 验证域名
            if (domain === '' && isValid) {
                errorMessage = '请选择后缀';
                isValid = false;
            }
            
            // 验证昵称
            if (fullName === '' && isValid) {
                errorMessage = '请输入您的昵称';
                isValid = false;
            }
            
            // 验证密码
            if (password === '' && isValid) {
                errorMessage = '请输入密码';
                isValid = false;
            } else if (password.length < 8 && isValid) {
                errorMessage = '密码长度不能少于8个字符';
                isValid = false;
            }
            
            // 验证确认密码
            if (confirmPassword === '' && isValid) {
                errorMessage = '请再次输入密码';
                isValid = false;
            } else if (password !== confirmPassword && isValid) {
                errorMessage = '两次输入的密码不一致';
                isValid = false;
            }
            
            // 验证验证码
            if (captcha === '' && isValid) {
                errorMessage = '请输入验证码';
                isValid = false;
            }
            
            // 如果验证失败，显示错误信息
            if (!isValid) {
                layer.msg(errorMessage, {
                    icon: 2,  // 错误图标
                    time: 3000,  // 3秒后自动关闭
                    anim: 6,  // 抖动动画
                    shade: [0.3, '#000']  // 遮罩
                });
                return false;
            }
            
            if (isValid) {
                // 显示加载层
                var loadingIndex = layer.load(1, {
                    shade: [0.3, '#fff'] // 0.3透明度的白色背景
                });
                
                // 通过AJAX提交表单
                $.ajax({
                    url: '?controller=api&action=register',
                    type: 'POST',
                    data: {
                        username: username,
                        domain: domain,
                        full_name: fullName,
                        password: password,
                        confirm_password: confirmPassword,
                        captcha: captcha
                    },
                    dataType: 'json',
                    success: function(response) {
                        // 关闭加载层
                        layer.close(loadingIndex);
                        
                        if (response.status === 'success') {
                            // 清理表单
                            $('#registerForm')[0].reset();
                            
                            // 使用弹出层显示成功信息
                            layer.alert('<div style="text-align:center;padding:20px;">'
                                    + '<p style="font-size:20px;margin-bottom:10px;color:green;">注册邮箱成功！</p>'
                                    + '<p>您的邮箱地址是：<strong>' + response.data.email + '</strong></p>'
                                    + '<p>您现在可以使用新账户登录邮箱系统。</p>'
                                    + '</div>', {
                                title: '注册成功',
                                btn: ['关闭'],
                                area: ['400px', 'auto'],
                                shade: 0.6,
                                shadeClose: false
                            });
                        } else {
                            // 注册失败，使用layer提示框显示错误信息
                            layer.msg(response.message, {
                                icon: 2,  // 错误图标
                                time: 3000,  // 3秒后自动关闭
                                anim: 6,  // 抖动动画
                                shade: [0.3, '#000']  // 遮罩
                            });
                            
                            // 如果有字段错误，使用layer.tips显示错误信息
                            if (response.errors) {
                                $.each(response.errors, function(field, error) {
                                    layer.tips(error, '#' + field, {
                                        tips: [2, '#FF5722'],  // 右侧显示，红色背景
                                        time: 4000  // 4秒后自动关闭
                                    });
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // 关闭加载层
                        layer.close(loadingIndex);
                        
                        // 显示错误信息
                        var errorMessage = '请求失败，请稍后再试';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('解析响应失败', e);
                        }
                        
                        layer.msg(errorMessage, {
                            icon: 2,  // 错误图标
                            time: 3000,  // 3秒后自动关闭
                            anim: 6,  // 抖动动画
                            shade: [0.3, '#000']  // 遮罩
                        });
                    }
                });
            }
        });
    } else {
        console.error('jQuery 未加载，表单验证无法工作');
    }
});
</script>