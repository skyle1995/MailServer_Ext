<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 插件默认配置

// 初始化
$config = [];
// 站点名称
$config['sitename'] = "Anonymous Online Webmail";
// 底部版权信息
$config['footer'] = "© " . date('Y') . " Anonymous Online Webmail. 保留所有权利。";
// 宝塔面板地址
$config['panel'] = "https://127.0.0.1:8888";
// Api接口秘钥
$config['apikey'] = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
// 邮箱Webmail登录地址(注意：留空则不显示登录邮箱按钮)
$config['webmail'] = "https://127.0.0.1";
// 注册开关，类型：布尔型（true 开启 false 关闭）
$config['openRegister'] =  true;
// 改密开关，类型：布尔型（true 开启 false 关闭）
$config['openReplace'] =  true;
// 允许注册名称最低长度，建议不要太短
$config['nameLength'] =  3;
// 邮箱容量设置
$config['emailQuota'] =  5;
// 邮箱容量单位：MB 或 GB
$config['emailQuotaUnit'] =  "GB";
// 超级秘钥（自定义，请勿泄露，否则会造成安全问题）
// API对接软件认证专用，暂时先预置
$config['superKey'] =  "";
// 域名过滤表（隐藏/禁用）
$config['exclude'] = [];