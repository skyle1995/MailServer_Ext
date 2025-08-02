# 宝塔邮件服务器扩展插件

## 项目介绍

宝塔邮件服务器扩展插件是一个基于PHP开发的Web应用程序，为宝塔面板的邮件服务器提供了额外的功能扩展。该插件主要提供了开放注册邮箱和修改密码的功能，使用户能够自助管理邮箱账户，减轻管理员的工作负担。

## 主要功能

- **邮箱账户注册**：用户可以自助注册邮箱账户
- **密码修改**：用户可以自助修改邮箱密码
- **管理员配置**：管理员可以通过配置文件控制功能开关和参数设置
- **响应式界面**：基于Bootstrap的响应式设计，适配各种设备

## 系统要求

- PHP 7.0 或更高版本
- 宝塔面板及其邮件服务器插件
- Web服务器（Apache/Nginx）

## 安装说明

1. 先安装提供的宝塔邮局扩展插件
2. 将项目文件上传到Web服务器目录
3. 修改 `config/config.inc.php` 文件中的配置参数
4. 确保Web服务器有权限访问项目文件
5. 通过浏览器访问项目URL

## 配置说明

配置文件位于 `config/config.inc.php`，主要配置项包括：

```php
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
$config['superKey'] =  "12345678";
```

## 使用说明

### 邮箱注册

1. 访问注册页面
2. 填写账号名、选择域名后缀、输入昵称和密码
3. 提交表单后系统会自动创建邮箱账户

### 密码修改

1. 访问修改密码页面
2. 填写完整邮箱地址、当前密码和新密码
3. 提交表单后系统会自动更新密码

## 项目结构

```
├── assets/            # 静态资源文件
│   ├── bootstrap/     # Bootstrap框架文件
│   ├── css/           # 自定义CSS样式
│   ├── images/        # 图片资源
│   ├── js/            # JavaScript脚本
│   └── layer/         # Layer弹出层组件
├── config/            # 配置文件目录
│   └── config.inc.php # 主配置文件
├── template/          # 模板文件目录
│   ├── home.php       # 首页模板
│   ├── layout.php     # 布局模板
│   ├── register.php   # 注册页面模板
│   └── replace.php    # 修改密码页面模板
├── vendor/            # 核心代码目录
│   ├── Controllers/   # 控制器类
│   ├── Core/          # 核心功能类
│   ├── View/          # 视图相关类
│   └── autoload.php   # 自动加载文件
└── index.php          # 入口文件
```

## 安全说明

- 请确保配置文件中的API密钥和超级密钥安全保存
- 建议在生产环境中启用HTTPS
- 定期更新系统和依赖库以修复潜在的安全漏洞

## 注意事项

- 本项目仅用于学习交流使用，禁止用于非法用途
- 使用本插件产生的任何法律责任与作者无关，由使用者本人自行承担
- 建议在使用前备份邮件服务器数据

## 联系方式

如有问题或建议，请通过以下方式联系：

- 邮箱：skyle1995@163.com
- QQ群：757159644（已满）/ 758502978（未满）