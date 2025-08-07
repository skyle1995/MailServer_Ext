<div style="text-align: center; margin-bottom: 15px;">
    <h2 class="site-title"><?php echo isset($sitename) ? $sitename : 'Anonymous Online Webmail'; ?></h2>
</div>

<div class="action-buttons">
    <?php if (isset($webmail) && !empty($webmail)): ?>
    <a href="<?php echo $webmail; ?>" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-log-in"></span> 登录邮箱</a>
    <?php endif; ?>
    <a href="?controller=home&action=register" class="btn btn-primary btn-lg" data-ajax="true"><span class="glyphicon glyphicon-user"></span> 注册邮箱</a>
    <a href="?controller=home&action=replace" class="btn btn-default btn-lg" data-ajax="true"><span class="glyphicon glyphicon-lock"></span> 修改密码</a>
</div>