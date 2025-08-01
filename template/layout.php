<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : '邮件服务器插件'; ?></title>
    <!-- 网站图标 -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- 自定义样式 -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <!-- 品牌和切换按钮 -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar" aria-expanded="false">
                        <span class="sr-only">切换导航</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/images/logo.svg" alt="Logo" style="height: 25px; display: inline-block; margin-right: 5px; vertical-align: middle;">
                        <?php echo isset($sitename) ? $sitename : '邮件服务器插件'; ?>
                    </a>
                </div>

                <!-- 导航链接 -->
                <div class="collapse navbar-collapse" id="main-navbar">
                    <?php 
                    // 获取当前控制器和方法
                    $currentController = isset($_GET['controller']) ? $_GET['controller'] : 'home';
                    $currentAction = isset($_GET['action']) ? $_GET['action'] : 'index';
                    ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="<?php echo ($currentController == 'home' && $currentAction == 'index') ? 'active' : ''; ?>"><a href=".">首页 <?php echo ($currentController == 'home' && $currentAction == 'index') ? '<span class="sr-only">(当前)</span>' : ''; ?></a></li>
                        <li class="<?php echo ($currentController == 'home' && $currentAction == 'register') ? 'active' : ''; ?>"><a href="?controller=home&action=register">注册邮箱 <?php echo ($currentController == 'home' && $currentAction == 'register') ? '<span class="sr-only">(当前)</span>' : ''; ?></a></li>
                        <li class="<?php echo ($currentController == 'home' && $currentAction == 'replace') ? 'active' : ''; ?>"><a href="?controller=home&action=replace">修改密码 <?php echo ($currentController == 'home' && $currentAction == 'replace') ? '<span class="sr-only">(当前)</span>' : ''; ?></a></li>
                        <li class="<?php echo ($currentController == 'home' && $currentAction == 'about') ? 'active' : ''; ?>"><a href="?controller=home&action=about">关于我们 <?php echo ($currentController == 'home' && $currentAction == 'about') ? '<span class="sr-only">(当前)</span>' : ''; ?></a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container -->
        </nav>
    </header>
    
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer" style="margin-top: 30px; padding: 20px 0;">
        <div class="container">
            <p class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo isset($sitename) ? $sitename : '邮件服务器插件'; ?>. 保留所有权利。</p>
        </div>
    </footer>
    
    <!-- jQuery (Bootstrap 的依赖) -->
    <script src="assets/js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- Layer弹出层 -->
    <script src="assets/layer/layer/layer.js"></script>
    <!-- 自定义脚本 -->
    <script src="assets/js/main.js"></script>
</body>
</html>