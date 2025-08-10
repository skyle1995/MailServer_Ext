<div class="layui-panel form-panel">
    <div class="panel-heading">
        <h3 class="panel-title">注册邮箱账号</h3>
    </div>
    <div class="panel-body">
        <div id="server-messages"></div>
        <form class="layui-form" id="registerForm" lay-filter="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="text" class="layui-input" id="username" name="username" placeholder="请输入账号名" value="<?php echo isset($username) ? $username : ''; ?>" lay-verify="required" lay-reqtext="请输入账号名" autocomplete="off" lay-affix="clear">
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-globe"></i>
                    </div>
                    <select class="layui-select" id="domain" name="domain" lay-verify="required" lay-reqtext="请选择邮箱后缀">
                        <option value="">请选择邮箱后缀</option>
                        <!-- 域名列表将通过AJAX动态加载 -->
                    </select>
                </div>
            </div>
            
            <!-- 昵称字段已移除，将自动使用账号名作为昵称 -->
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" class="layui-input" id="password" name="password" placeholder="请输入注册密码" lay-verify="required" lay-reqtext="请输入注册密码" autocomplete="off" lay-affix="eye">
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <input type="password" class="layui-input" id="confirm_password" name="confirm_password" placeholder="请再次输入密码" lay-verify="required" lay-reqtext="请再次输入密码" autocomplete="off" lay-affix="eye">
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item">
                <div style="display: flex; align-items: center;">
                    <div style="flex: 1; margin-right: 10px;">
                        <div class="layui-input-wrap">
                            <div class="layui-input-prefix">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <input type="text" class="layui-input" id="captcha" name="captcha" placeholder="请输入验证码" lay-verify="required" lay-reqtext="请输入验证码" autocomplete="off" lay-affix="clear">
                        </div>
                    </div>
                    <div style="flex: 0 0 auto;">
                        <img id="captcha_img" src="?controller=base&action=captcha" alt="验证码" class="captcha-img" onclick="refreshCaptcha()">
                    </div>
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item form-button-container">
                <button type="submit" class="layui-btn layui-btn-normal layui-btn-lg form-button" lay-submit lay-filter="registerForm">确认注册</button>
            </div>
        </form>
    </div>
</div>

<div style="text-align: center; margin-top: 15px;">
    <a href="." style="color: #1E9FFF; text-decoration: none; font-size: 16px;"><i class="fas fa-home"></i> 返回首页</a>
</div>

<script>
// 刷新验证码函数
function refreshCaptcha() {
    document.getElementById('captcha_img').src = '?controller=base&action=captcha&t=' + new Date().getTime();
}

// 切换密码可见性函数
// 使用 Layui 原生的 lay-affix="eye" 功能实现密码显示/隐藏

// 等待页面加载完成
window.onload = function() {
    // 确保 layui 已加载
    if (typeof layui !== 'undefined') {
        layui.use(['form', 'layer', 'jquery'], function() {
    var form = layui.form;
    var layer = layui.layer;
    
    // 表单验证规则
    form.verify({
        username: function(value) {
            if (value.length < 3) {
                return '账号名长度不能少于3个字符';
            }
            if (!/^[a-z][a-z0-9]*$/.test(value)) {
                return '用户名必须以小写字母开头，且只能包含小写字母和数字';
            }
        },
        password: function(value) {
            if (value.length < 8) {
                return '密码长度不能少于8个字符';
            }
        },
        confirmPassword: function(value) {
            var password = $('#password').val();
            if (value !== password) {
                return '两次输入的密码不一致';
            }
        }
    });
    
    // 加载域名列表
    function loadDomains() {
        // 使用layer.msg显示加载提示，设置黑色透明背景并禁止操作
        var loadIndex = layer.msg('加载中', {
            icon: 16,
            shade: [0.3, '#000'], // 0.3透明度的黑色背景
            shadeClose: false // 禁止点击遮罩关闭
        });
            
            $.ajax({
                url: '?controller=api&action=domains',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // 关闭加载层
                    layer.close(loadIndex);
                    
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        var domainSelect = $('#domain');
                        domainSelect.find('option:not(:first)').remove();
                        
                        $.each(response.data, function(index, domain) {
                            domainSelect.append($('<option></option>').val(domain).text(domain));
                        });
                        
                        // 重新渲染表单
                        form.render('select');
                    } else {
                        layer.alert(response.message || '无法获取可用后缀列表，请刷新页面重试。', {
                            icon: 2,
                            title: '错误提示',
                            btn: ['确定']
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // 关闭加载层
                    layer.close(loadIndex);
                    layer.alert('加载域名列表失败，请刷新页面重试。', {
                        icon: 2,
                        title: '错误提示',
                        btn: ['确定']
                    });
                    console.error('加载域名列表失败:', error);
                }
            });
        }
        
        // 页面加载时获取域名列表
        loadDomains();
        
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

        // 表单提交事件
        form.on('submit(registerForm)', function(data) {
            // 阻止表单默认提交行为
            var field = data.field;
            
            // 获取表单数据
            var username = field.username.trim();
            var domain = field.domain;
            var password = field.password;
            var confirmPassword = field.confirm_password;
            var captcha = field.captcha.trim();
            // 昵称字段已移除，将由后端自动使用账号名
            
            // 表单验证已由Layui的verify处理，这里只需要进行AJAX提交
                // 显示加载层，使用layer.msg，设置黑色透明背景并禁止操作
                var loadingIndex = layer.msg('加载中', {
                    icon: 16,
                    shade: [0.3, '#000'], // 0.3透明度的黑色背景
                    shadeClose: false // 禁止点击遮罩关闭
                });
                
                // 通过AJAX提交表单
                $.ajax({
                    url: '?controller=api&action=register',
                    type: 'POST',
                    data: field, // 直接使用Layui表单收集的数据
                    dataType: 'json',
                    success: function(response) {
                        // 关闭加载层
                        layer.close(loadingIndex);
                        
                        // 刷新验证码
                        refreshCaptcha();
                        
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
                                $.each(response.errors, function(fieldName, error) {
                                    layer.tips(error, '#' + fieldName, {
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
                        
                        // 刷新验证码
                        refreshCaptcha();
                        
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
            
            return false; // 阻止表单默认提交
        });
    });
}
};
</script>