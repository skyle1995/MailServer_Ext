<div style="text-align: center; margin-bottom: 15px;">
    <h2 class="site-title"><?php echo isset($sitename) ? $sitename : 'Anonymous Online Webmail'; ?></h2>
</div>

<div class="action-buttons">
    <?php if (isset($webmail) && !empty($webmail)): ?>
    <a href="<?php echo $webmail; ?>" class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt"></i> 登录邮箱</a>
    <?php endif; ?>
    <a href="?controller=home&action=register" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> 注册邮箱</a>
    <a href="?controller=home&action=replace" class="btn btn-default btn-lg"><i class="fas fa-key"></i> 修改密码</a>
</div>