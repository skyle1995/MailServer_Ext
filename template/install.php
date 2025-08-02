<div class="panel panel-primary" style="max-width: 480px; margin: 0 auto; border-radius: 6px; box-shadow: none;">
    <div class="panel-heading" style="text-align: center; border-radius: 6px 6px 0 0; border-bottom: 0px solid rgb(120, 194, 255);">
        <h3 class="panel-title">系统安装</h3>
    </div>
    <div class="panel-body">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <strong>错误：</strong> <?php echo $error; ?>
        </div>
        <?php else: ?>
        <form method="post" action="?controller=install&action=save">
            <div class="form-group">
                <label for="sitename">站点名称</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                    <input type="text" class="form-control" id="sitename" name="sitename" placeholder="Anonymous Online Webmail" value="" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="panel">宝塔面板地址</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                    <input type="text" class="form-control" id="panel" name="panel" placeholder="https://127.0.0.1:8888" value="" required>
                </div>
                <p class="help-block">请输入您的宝塔面板访问地址</p>
            </div>
            
            <div class="form-group">
                <label for="apikey">API接口密钥</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="text" class="form-control" id="apikey" name="apikey" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="" required>
                </div>
                <p class="help-block">请在宝塔面板API接口中获取密钥</p>
            </div>
            
            <div class="form-group">
                <label for="webmail">邮箱Webmail登录地址</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    <input type="text" class="form-control" id="webmail" name="webmail" placeholder="https://127.0.0.1" value="">
                </div>
                <p class="help-block">请输入您的Webmail访问地址，留空则不显示登录邮箱按钮</p>
            </div>
            <div class="form-group">
                <label>功能开关</label>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="openRegister" <?php echo isset($config['openRegister']) && $config['openRegister'] ? 'checked' : ''; ?>> 开启注册邮箱功能
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="openReplace" <?php echo isset($config['openReplace']) && $config['openReplace'] ? 'checked' : ''; ?>> 开启修改密码功能
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="nameLength">注册名称最低长度</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-text-width"></i></span>
                    <input type="number" class="form-control" id="nameLength" name="nameLength" value="<?php echo isset($config['nameLength']) ? $config['nameLength'] : '5'; ?>" min="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="emailQuota">邮箱容量</label>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-hdd"></i></span>
                            <input type="number" class="form-control" id="emailQuota" name="emailQuota" value="<?php echo isset($config['emailQuota']) ? $config['emailQuota'] : '5'; ?>" min="1" required>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <select class="form-control" name="emailQuotaUnit" style="height: 40px;">
                            <option value="MB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'MB' ? 'selected' : ''; ?>>MB</option>
                            <option value="GB" <?php echo isset($config['emailQuotaUnit']) && $config['emailQuotaUnit'] === 'GB' ? 'selected' : ''; ?>>GB</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="superKey">超级密钥</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="text" class="form-control" id="superKey" name="superKey" value="" required>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" style="height: 40px;" onclick="generateRandomKey()"><span class="glyphicon glyphicon-random"></span> 随机生成</button>
                    </span>
                </div>
                <p class="help-block">用于API对接认证，请设置复杂密钥并妥善保管</p>
            </div>
            <div class="form-group">
                <label for="exclude">域名过滤表（每行一个域名）</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-filter"></i></span>
                    <textarea class="form-control" id="exclude" name="exclude" rows="5"></textarea>
                </div>
                <p class="help-block">这些域名将被隐藏或禁用</p>
            </div>
            
            <div class="form-group">
                <label for="footer">底部版权信息</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-copyright-mark"></i></span>
                    <input type="text" class="form-control" id="footer" name="footer" placeholder="© <?php echo date('Y'); ?> Anonymous Online Webmail. 保留所有权利。" value="">
                </div>
                <p class="help-block">显示在网站底部的版权信息</p>
            </div>
            
            <div class="form-group" style="text-align: center; margin-top: 20px;">
                <button type="submit" class="btn btn-primary btn-lg">保存配置并安装</button>
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
</script>