<div class="jumbotron">
    <h2><?php echo isset($sitename) ? $sitename : '邮件服务器插件'; ?></h2>
    <p class="lead">宝塔邮件服务器扩展插件，提供开放注册邮箱和修改密码功能。</p>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">系统主要功能</h3>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item"><span class="glyphicon glyphicon-user"></span> 邮箱账户管理</li>
                    <li class="list-group-item"><span class="glyphicon glyphicon-envelope"></span> 邮件发送与接收</li>
                    <li class="list-group-item"><span class="glyphicon glyphicon-filter"></span> 垃圾邮件过滤</li>
                    <li class="list-group-item"><span class="glyphicon glyphicon-hdd"></span> 邮件备份与恢复</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">系统信息</h3>
            </div>
            <div class="panel-body">
                <div class="action-buttons">
                    <?php if (isset($webmail) && !empty($webmail)): ?>
                    <a href="<?php echo $webmail; ?>" class="btn btn-success btn-block"><span class="glyphicon glyphicon-log-in"></span> 前往登录</a>
                    <?php endif; ?>
                    <a href="?controller=home&action=register" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-user"></span> 立即注册</a>
                    <a href="?controller=home&action=replace" class="btn btn-default btn-block"><span class="glyphicon glyphicon-lock"></span> 修改密码</a>
                </div>
            </div>
        </div>
    </div>
</div>