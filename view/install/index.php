<div class="layui-panel form-panel">
    <div class="panel-heading">
        <h3 class="panel-title">系统安装</h3>
    </div>
    <div class="panel-body">
        <?php if (isset($error)): ?>
            <div style="padding: 10px; border-radius: 4px; margin-bottom: 15px;background-color: #c2c2c2;">
                <strong>错误：</strong> <?php echo $error; ?>
            </div>
            <div class="layui-form-item form-button-container">
                <a href="./?controller=home&action=index" class="layui-btn layui-btn-primary"><i class="fas fa-home"></i> 返回首页</a>
            </div>
        <?php else: ?>
            <form class="layui-form" method="post" action="?controller=install&action=save" lay-filter="installForm" id="installForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="layui-form-item">
                    <label for="sitename">站点名称</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-globe"></i>
                        </div>
                        <input type="text" class="layui-input" id="sitename" name="sitename" placeholder="Anonymous Online Webmail" value="" lay-verify="required" lay-reqtext="请输入站点名称" autocomplete="off" lay-affix="clear">
                    </div>
                    <div class="layui-form-mid layui-word-aux">请输入您的站点名称，例如：Anonymous Online Webmail</div>
                </div>

                <div class="layui-form-item">
                    <label for="panel">宝塔面板地址</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-link"></i>
                        </div>
                        <input type="text" class="layui-input" id="panel" name="panel" placeholder="https://127.0.0.1:8888" value="" lay-verify="required" lay-reqtext="请输入宝塔面板地址" autocomplete="off" lay-affix="clear">
                    </div>
                    <div class="layui-form-mid layui-word-aux">面板访问地址，例如：https://127.0.0.1:8888（不含/）</div>
                </div>

                <div class="layui-form-item">
                    <label for="apikey">API接口密钥</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="text" class="layui-input" id="apikey" name="apikey" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="" lay-verify="required" lay-reqtext="请输入API接口密钥" autocomplete="off" lay-affix="clear">
                    </div>
                    <div class="layui-form-mid layui-word-aux">请在宝塔面板API接口中获取密钥</div>
                </div>

                <div class="layui-form-item">
                    <label for="webmail">邮箱Webmail登录地址</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="text" class="layui-input" id="webmail" name="webmail" placeholder="https://127.0.0.1" value="" autocomplete="off" lay-affix="clear">
                    </div>
                    <div class="layui-form-mid layui-word-aux">请输入您的Webmail访问地址，留空则不显示登录邮箱按钮</div>
                </div>
                <div class="layui-form-item">
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="openRegister" title="开启注册" lay-skin="primary" <?php echo isset($config['openRegister']) && $config['openRegister'] ? 'checked' : ''; ?>>
                        <input type="checkbox" name="openReplace" title="开启改密" lay-skin="primary" <?php echo isset($config['openReplace']) && $config['openReplace'] ? 'checked' : ''; ?>>
                    </div>
                    <div class="layui-form-mid layui-word-aux">开关功能，开放注册和改密功能</div>
                </div>

                <div class="layui-form-item">
                    <label for="nameLength">注册名称最低长度</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-text-width"></i>
                        </div>
                        <input type="number" class="layui-input" id="nameLength" name="nameLength" value="<?php echo isset($config['nameLength']) ? $config['nameLength'] : '5'; ?>" min="1" lay-verify="required" lay-reqtext="请输入注册名称最低长度" autocomplete="off">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="emailQuota">邮箱容量</label>
                    <div class="layui-row">
                        <div class="layui-col-xs6" style="padding-right: 5px;">
                            <div class="layui-input-wrap">
                                <div class="layui-input-prefix">
                                    <i class="fas fa-hdd"></i>
                                </div>
                                <input type="number" class="layui-input" id="emailQuota" name="emailQuota" value="<?php echo isset($config['emailQuota']) ? $config['emailQuota'] : '5'; ?>" min="1" lay-verify="required" lay-reqtext="请输入邮箱容量" autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-col-xs6" style="padding-left: 5px;">
                            <select class="layui-select" name="emailQuotaUnit">
                                <option value="MB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'MB' ? 'selected' : ''; ?>>MB</option>
                                <option value="GB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'GB' ? 'selected' : ''; ?>>GB</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="superKey">超级密钥</label>
                    <div style="display: flex;">
                        <div style="flex: 1; margin-right: 10px;">
                            <div class="layui-input-wrap">
                                <div class="layui-input-prefix">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="text" class="layui-input" id="superKey" name="superKey" value="" lay-verify="required" lay-reqtext="请输入超级密钥" autocomplete="off">
                            </div>
                        </div>
                        <div style="flex: 0 0 auto;">
                            <button type="button" class="layui-btn" onclick="generateRandomKey()"><i class="fas fa-random"></i> 随机生成</button>
                        </div>
                    </div>
                    <div class="layui-form-mid layui-word-aux">用于API对接认证，请设置复杂密钥并妥善保管</div>
                </div>
                <div class="layui-form-item">
                    <label for="exclude">域名过滤表（每行一个域名）</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-filter"></i>
                        </div>
                        <textarea class="layui-textarea" id="exclude" name="exclude" placeholder="每行输入一个域名"></textarea>
                    </div>
                    <div class="layui-form-mid layui-word-aux">这些域名将被隐藏或禁用</div>
                </div>

                <div class="layui-form-item">
                    <label for="footer">版权信息</label>
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="fas fa-copyright"></i>
                        </div>
                        <input type="text" class="layui-input" id="footer" name="footer" placeholder="© <?php echo date('Y'); ?> Anonymous Online Webmail. 保留所有权利。" value="" autocomplete="off" lay-affix="clear">
                    </div>
                    <div class="layui-form-mid layui-word-aux">显示在网站底部的版权信息</div>
                </div>

                <div class="layui-form-item" style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="layui-btn layui-btn-fluid" lay-submit lay-filter="installForm"><i class="fas fa-save"></i> 保存配置并安装</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
    /**
     * 生成16位随机字符串（包含大小写英文字母和数字）
     */
    const generateRandomKey = () => {
        // 定义可能的字符集
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';

        // 生成16位随机字符
        for (let i = 0; i < 16; i++) {
            const randomIndex = Math.floor(Math.random() * chars.length);
            result += chars.charAt(randomIndex);
        }

        // 设置到输入框
        document.getElementById('superKey').value = result;
    };
    
    // 当文档加载完成后执行
    window.onload = function() {
        // 确保 layui 已加载
        if (typeof layui !== 'undefined') {
            layui.use(['form', 'layer'], function() {
                var form = layui.form;
                var layer = layui.layer;
                
                // 监听表单提交
                form.on('submit(installForm)', function(data) {
                    // 显示加载层
                    var loadingIndex = layer.msg('正在安装中...', {
                        icon: 16,
                        shade: [0.3, '#000'], // 0.3透明度的黑色背景
                        shadeClose: false // 禁止点击遮罩关闭
                    });
                    
                    // 提交表单
                    $.ajax({
                        url: '?controller=install&action=save',
                        type: 'POST',
                        data: $('#installForm').serialize(),
                        dataType: 'json',
                        success: function(response) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            // 获取消息文本，默认为'安装配置成功！'
                            var message = '安装配置成功！';
                            if (response && response.message) {
                                message = response.message;
                            }
                            
                            // 获取重定向URL，默认为首页
                            var redirectUrl = './?controller=home&action=index';
                            if (response && response.redirect) {
                                redirectUrl = response.redirect;
                            }
                            
                            // 显示安装成功弹窗
                            layer.alert(message, {
                                icon: 1,
                                title: '安装成功',
                                btn: ['确定'],
                                yes: function(index) {
                                    layer.close(index);
                                    // 跳转到指定页面
                                    window.location.href = redirectUrl;
                                }
                            });
                        },
                        error: function(xhr) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            // 尝试解析错误信息
                            var errorMessage = '安装失败，请检查配置并重试';
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                // 如果响应不是JSON格式，尝试从HTML中提取错误信息
                                var match = xhr.responseText.match(/<div class="layui-bg-red"[^>]*>\s*<strong>错误：<\/strong>\s*([^<]+)<\/div>/);
                                if (match && match[1]) {
                                    errorMessage = match[1].trim();
                                }
                            }
                            
                            // 显示错误信息
                            layer.alert(errorMessage, {
                                icon: 2,
                                title: '安装失败'
                            });
                        }
                    });
                    
                    return false; // 阻止表单默认提交
                });
            });
        }
    };
</script>