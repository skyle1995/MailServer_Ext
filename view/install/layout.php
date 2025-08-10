<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : '邮件服务器插件'; ?></title>
    <!-- 网站图标 -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Layui CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/layui/2.11.5/css/layui.min.css">
    <!-- FontAwesome 图标库 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- 自定义样式 -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="layui-layout">
        <div class="layui-container">
            <div class="content-wrapper">
                <div class="logo-container">
                    <img src="assets/images/logo.svg" alt="Logo">
                </div>
                <div class="layui-col-md8 layui-col-md-offset-2">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
        
        <div class="layui-footer">
            <div class="layui-container">
                <p class="footer-text"><?php echo isset($footer) ? $footer : '© ' . date('Y') . ' 邮件服务器插件. 保留所有权利。'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Layui JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/layui/2.11.5/layui.min.js"></script>
    <!-- 粒子效果库 (CDN版本) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <!-- 自定义脚本 -->
    <script src="assets/js/main.js"></script>
</body>
</html>