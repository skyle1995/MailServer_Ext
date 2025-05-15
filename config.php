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
    // 1 = imap 服务验证模式
    // 2 = Roundcube Webmail Web验证模式 
    // 3 = Api校验模式（需要修改增加邮局接口）
    "verifyType" => 3,
    // imap服务验证模式填imap地址
    // imap 普通模式无需加端口，类型：字符串，如：mail.163.com
    // imap 安全模式需要加端口，类型：字符串，如：mail.163.com:993
    // Web验证模式网页登录地址，类型：字符串，如：https://mail.163.com
    // Api校验模式可不填此参数，类型：布尔型，如：true（开启精确搜索模式）
    "verifyAddr" => true,
    // 注册开关，true 开启 false 关闭
    // 注册开关，true 开启 false 关闭
    "openReg" => false,
    // 允许注册名称最低长度
    "regLength" => 5,
    // 重置开关，true 开启 false 关闭
    "openRep" => true,
    // 超级秘钥（自定义，请勿泄露，否则会造成安全问题）
    // 用于不知道原始邮箱密码的情况下强制修改密码
    "adminKey" => "12345678"
];