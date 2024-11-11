<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

// 插件配置

$config = [
    // 站点名称
    "sitename" => "Roundcube Webmail",
    // 宝塔面板地址
    "panel" => "https://127.0.0.1:8888",
    // Api接口秘钥
    "apikey" => "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    // 提供注册的邮箱后缀(数组成员，有多个可以继续向下添加)
    "hosts" => [
        "@163.com",
        "@189.com"
    ],
    // 1 = imap 服务验证模式 / 2 = Roundcube Webmail 网页验证模式 
    "VerifyType" => 2,
    // imap服务验证模式填imap地址
    // imap 普通模式无需加端口，如：mail.163.com
    // imap ssl模式需要加端口，如：mail.163.com:993
    // 网页验证模式填网页登录地址，如：https://mail.163.com
    "VerifyAddr" => "https://mail.163.com",
    // 注册开关，true 开启 false 关闭
    "openreg" => false,
    // 允许注册名称最低长度
    "RegLength" => 5,
    // 重置开关，true 开启 false 关闭
    "openrep" => true,
    // 超级秘钥（自定义，请勿泄露，否则会造成安全问题）
    // 用于不知道原始邮箱密码的情况下强制修改密码
    "adminkey" => "12345678"
];