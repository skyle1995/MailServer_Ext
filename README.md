# 宝塔邮件服务器扩展插件

## 项目介绍

宝塔邮件服务器扩展插件是一个基于PHP开发的Web应用程序，为宝塔面板的邮件服务器提供了额外的功能扩展。该插件主要提供了开放注册邮箱和修改密码的功能，使用户能够自助管理邮箱账户，减轻管理员的工作负担。

## 主要功能

- **邮箱账户注册**：用户可以自助注册邮箱账户
- **密码修改**：用户可以自助修改邮箱密码
- **管理员配置**：管理员可以通过配置文件控制功能开关和参数设置
- **响应式界面**：基于Bootstrap的响应式设计，适配各种设备
- **友好的用户提示**：使用Layer弹窗组件提供直观的操作反馈

## 系统要求

- PHP 5.4.0 或更高版本（推荐使用PHP 7.0或更高版本）
- 宝塔面板及其邮件服务器插件
- Web服务器（Apache/Nginx）

## 安装说明

1. 先安装提供的宝塔邮局扩展插件
2. 将项目文件上传到Web服务器目录
3. 修改 `config/default.inc.php` 文件中的配置参数
4. 确保Web服务器有权限访问项目文件
5. 通过浏览器访问项目URL

## 配置说明

配置文件位于 `config` 目录，主要配置项包括：

```php
// 站点名称
$config['sitename'] = "Anonymous Online Webmail";
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
// 底部版权信息
$config['footer'] = "© " . date('Y') . " Anonymous Online Webmail. 保留所有权利。";
```

## 使用说明

### 邮箱注册

1. 访问注册页面
2. 填写账号名、选择域名后缀、输入昵称和密码
3. 提交表单后系统会自动创建邮箱账户
4. 操作过程中的错误和成功信息会通过弹窗提示

### 密码修改

1. 访问修改密码页面
2. 填写完整邮箱地址、当前密码和新密码
3. 提交表单后系统会自动更新密码
4. 操作过程中的错误和成功信息会通过弹窗提示

### 错误处理

- 表单验证错误：直接在界面上显示错误信息
- 加载失败错误：通过Layer弹窗提示用户
- 提交处理错误：通过Layer弹窗提示用户
- 操作成功提示：使用Layer弹窗显示成功信息

## 项目结构

```
├── assets/            # 静态资源文件
│   ├── bootstrap/     # Bootstrap框架文件
│   │   ├── css/       # Bootstrap CSS文件
│   │   ├── fonts/     # Bootstrap 字体文件
│   │   └── js/        # Bootstrap JavaScript文件
│   ├── css/           # 自定义CSS样式
│   ├── font/          # 字体资源
│   ├── images/        # 图片资源
│   ├── js/            # JavaScript脚本
│   └── layer/         # Layer弹出层组件
├── config/            # 配置文件目录
│   └── default.inc.php # 主配置文件
├── view/              # 视图文件目录
│   ├── home/          # 首页相关视图
│   │   ├── index.php  # 首页视图
│   │   ├── layout.php # 首页布局
│   │   ├── register.php # 注册页面视图
│   │   └── replace.php  # 修改密码页面视图
│   └── install/       # 安装相关视图
│       └── index.php  # 安装页面视图
├── vendor/            # 核心代码目录
│   ├── Captcha/       # 验证码相关类
│   ├── Controller/    # 控制器类
│   │   ├── ApiController.php     # API控制器
│   │   ├── BaseController.php    # 基础控制器
│   │   ├── HomeController.php    # 首页控制器
│   │   └── InstallController.php # 安装控制器
│   ├── Core/          # 核心功能类
│   │   ├── Common.php  # 公共函数
│   │   ├── Network.php # 网络相关
│   │   ├── Request.php # 请求处理
│   │   └── Validator.php # 数据验证
│   ├── View/          # 视图相关类
│   │   ├── Helper.php  # 视图助手
│   │   └── Template.php # 模板引擎
│   └── autoload.php   # 自动加载文件
├── .gitignore         # Git忽略文件
├── LICENSE            # 许可证文件
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

## 更新日志

### 2023-08-03
- 优化用户体验：将HTML错误提示替换为Layer弹窗提示
- 改进错误处理：使用统一的弹窗样式展示错误信息
- 优化加载提示：使用带有黑色透明背景的加载提示，并禁止用户在加载过程中进行操作
- 增强兼容性：添加PHP版本检查，确保系统在不支持session_status函数的环境下给出明确提示

## 联系方式

如有问题或建议，请通过以下方式联系：

- 邮箱：skyle1995@163.com
- QQ群：757159644（已满）/ 758502978（未满）