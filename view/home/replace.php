<div class="layui-panel form-panel">
    <div class="panel-heading">
        <h3 class="panel-title">修改邮箱密码</h3>
    </div>
    <div class="panel-body">
        <div id="server-messages"></div>
        <form class="layui-form" id="replaceForm" lay-filter="replaceForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="text" class="layui-input" id="username" name="username" placeholder="请输入邮箱账号" value="<?php echo isset($username) ? $username : ''; ?>" lay-verify="required|email" lay-reqtext="请输入邮箱账号" autocomplete="off" lay-affix="clear">
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" class="layui-input" id="password" name="password" placeholder="请输入当前密码" lay-verify="required" lay-reqtext="请输入当前密码" autocomplete="off" lay-affix="eye">
                </div>
                <!-- 错误信息通过JavaScript的layer.tips显示 -->
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="fas fa-key"></i>
                    </div>
                    <input type="password" class="layui-input" id="new_password" name="new_password" placeholder="请输入新的密码" lay-verify="required" lay-reqtext="请输入新密码" autocomplete="off" lay-affix="eye">
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
                <button type="submit" class="layui-btn layui-btn-normal layui-btn-lg form-button" lay-submit lay-filter="replaceForm">确认修改</button>
            </div>
        </form>
    </div>
</div>

<div class="back-link">
    <a href="."><i class="fas fa-home"></i> 返回首页</a>
</div>

<script>
// 刷新验证码函数
function refreshCaptcha() {
    document.getElementById('captcha_img').src = '?controller=base&action=captcha&t=' + new Date().getTime();
}

// 等待页面加载完成
window.onload = function() {
    // 确保 layui 已加载
    if (typeof layui !== 'undefined') {
        // 使用 layui 的方式处理 DOM 就绪事件
        layui.use(['form', 'layer', 'jquery'], function() {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery; // 使用 layui 内置的 jQuery
    
    // 表单验证规则
    form.verify({
        email: function(value) {
            if (!/^[\w.%+-]+@[\w.-]+\.[a-zA-Z]{2,}$/.test(value)) {
                return '请输入有效的邮箱地址';
            }
        },
        password: function(value) {
            if (value.length < 8) {
                return '密码长度不能少于8个字符';
            }
        },
        confirmPassword: function(value) {
            var password = $('#new_password').val();
            if (value !== password) {
                return '两次输入的密码不一致';
            }
        }
    });
    
    // 域名列表加载已移除，现在使用完整邮箱地址
    
    // 检查是否禁用修改密码功能
    <?php if (isset($disabled) && $disabled): ?>
    layer.alert('<?php echo isset($disabled_message) ? $disabled_message : "修改密码功能当前未开放"; ?>', {
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
    form.on('submit(replaceForm)', function(data) {
        // 获取表单数据
        var field = data.field;
        
        // 表单验证已由Layui的verify处理，这里只需要进行AJAX提交
        // 显示加载层，使用layer.msg，设置黑色透明背景并禁止操作
        var loadingIndex = layer.msg('加载中', {
            icon: 16,
            shade: [0.3, '#000'], // 0.3透明度的黑色背景
            shadeClose: false // 禁止点击遮罩关闭
        });
                
        // 通过AJAX提交表单
        $.ajax({
            url: '?controller=api&action=replace',
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
                    $('#replaceForm')[0].reset();
                    
                    // 使用弹出层显示成功信息
                    layer.alert('<div style="text-align:center;padding:20px;">'
                            + '<p style="font-size:20px;margin-bottom:10px;color:green;">修改密码成功！</p>'
                            + '<p>您的邮箱地址是：<strong>' + response.data.email + '</strong></p>'
                            + '<p>您现在可以使用新密码登录邮箱系统。</p>'
                            + '</div>', {
                        title: '修改成功',
                        btn: ['关闭'],
                        area: ['400px', 'auto'],
                        shade: 0.6,
                        shadeClose: false
                    });
                } else {
                    // 修改失败，使用layer提示框显示错误信息
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