<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 插件配置

// 初始化
$config = [];
// 站点名称
$config['sitename'] = "Anonymous Online Webmail";
// 宝塔面板地址
$config['panel'] = "https://127.0.0.1:8888";
// Api接口秘钥
$config['apikey'] = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

// 邮箱Webmail登录地址(注意：留空则不显示前往登录按钮)
$config['webmail'] = "https://127.0.0.1";
// 注册开关，类型：布尔型（true 开启 false 关闭）
$config['openRegister'] =  false;
// 改密开关，类型：布尔型（true 开启 false 关闭）
$config['openReplace'] =  false;
// 允许注册名称最低长度，建议不要太短
$config['nameLength'] =  5;
// 邮箱容量设置
$config['emailQuota'] =  5;
// 邮箱容量单位：MB 或 GB
$config['emailQuotaUnit'] =  "GB";
// 超级秘钥（自定义，请勿泄露，否则会造成安全问题）
// API对接专用密钥，可用于强制修改密码，强制查看邮件等
$config['superKey'] =  "12345678";

// 关于页面相关设置
$config['about'] = [];
$config['about']['tips'] = '这是一个简单的邮件服务器管理扩展插件，提供开放注册邮箱和修改密码功能。';
$config['about']['email'] = 'support@example.com';
$config['about']['phone'] = '123-456-7890';
$config['about']['address'] = '北京市海淀区中关村科技园';

// 如果有需要隐藏的域名，使用这个过滤表
$config['exclude'] = [
    'example.com',
    'example1.com',
    'example2.com',
];