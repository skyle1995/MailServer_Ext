<div class="site-title-container">
    <h2 class="site-title"><?php echo isset($sitename) ? $sitename : 'Anonymous Online Webmail'; ?></h2>
</div>

<div class="action-buttons">
    <?php if (isset($webmail) && !empty($webmail)): ?>
    <a href="<?php echo $webmail; ?>" class="layui-btn layui-btn-success layui-btn-lg"><i class="fas fa-sign-in-alt"></i> 登录邮箱</a>
    <?php endif; ?>
    <a href="?controller=home&action=register" class="layui-btn layui-btn-normal layui-btn-lg"><i class="fas fa-user-plus"></i> 注册邮箱</a>
    <a href="?controller=home&action=replace" class="layui-btn layui-btn-primary layui-btn-lg"><i class="fas fa-key"></i> 修改密码</a>
</div>