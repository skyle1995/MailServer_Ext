<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title><?=$config["sitename"]?> :: <?=$title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0">
    <meta name="theme-color" content="#f4f4f4">
    <meta name="msapplication-navbutton-color" content="#f4f4f4">
    <link rel="shortcut icon" href="skins/elastic/images/favicon.ico">
    <link rel="stylesheet" href="skins/elastic/deps/bootstrap.min.css">
    <link rel="stylesheet" href="skins/elastic/styles/styles.min.css">
    <link rel="stylesheet" href="plugins/jqueryui/themes/elastic/jquery-ui.min.css">
    <!--<link rel="stylesheet" href="//cdn.staticfile.net/layui/2.9.4/css/layui.min.css">-->
    <script src="program/js/jquery.min.js"></script>
    <script src="program/js/common.min.js"></script>
    <script src="program/js/app.min.js"></script>
    <script src="plugins/jqueryui/js/jquery-ui.min.js"></script>
    <!--<script src="//cdn.staticfile.net/layui/2.9.4/layui.min.js"></script>-->
    <script>
        var rcmail = new rcube_webmail();
        rcmail.set_env({
            "task": "login",
            "skin": "elastic"
        });
    </script>
    <style>
        .message {
            /*margin-top: 20px;*/
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            display: none; /* 默认隐藏 */
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .message.loading {
            background-color: #FFFFE0;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>