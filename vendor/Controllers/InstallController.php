<?php

namespace Controllers;

use Core\Request;

/**
 * 安装控制器 - 处理系统安装和配置
 * 
 * 该控制器负责处理系统的初始安装过程，包括配置文件的创建和基本设置
 */
class InstallController extends BaseController {
    
    /**
     * 构造函数
     * 
     * @param array $config 配置数组
     */
    public function __construct($config = []) {
        parent::__construct($config);
        $this->checkInstallStatus();
    }
    
    /**
     * 检查安装状态
     * 
     * 如果配置文件已存在，则显示提示信息
     * 如果是安装页面且配置文件已存在，则提示需要先删除配置文件
     */
    private function checkInstallStatus() {
        $configFile = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
        $isConfigExists = file_exists($configFile);
        
        // 如果配置文件已存在且当前是安装页面，则显示提示信息
        if ($isConfigExists && Request::get('controller') === 'install') {
            $this->template->assign('error', '系统已经安装，如需重新安装，请先删除 config/config.inc.php 文件');
        }
    }
    
    /**
     * 安装首页
     * 
     * 显示安装表单，包含默认配置信息
     */
    public function indexAction() {
        $configFile = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
        
        // 如果配置文件已存在，显示错误信息
        if (file_exists($configFile)) {
            $this->display('install');
            return;
        }
        
        // 将默认配置传递给模板
        $this->template->assign('config', $this->config);
        $this->display('install');
    }
    
    /**
     * 保存配置
     * 
     * 处理安装表单提交，创建配置文件
     */
    public function saveAction() {
        $configFile = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
        
        // 如果配置文件已存在，显示错误信息
        if (file_exists($configFile)) {
            $this->template->assign('error', '系统已经安装，如需重新安装，请先删除 config/config.inc.php 文件');
            $this->display('install');
            return;
        }
        
        // 验证CSRF令牌
        $token = Request::post('csrf_token');
        if (!verify_csrf_token($token)) {
            $this->template->assign('error', 'CSRF令牌验证失败，请刷新页面重试');
            $this->display('install');
            return;
        }
        
        // 获取表单提交的配置信息
        $sitename = Request::post('sitename', $this->config['sitename']);
        $panel = Request::post('panel', $this->config['panel']);
        $apikey = Request::post('apikey', $this->config['apikey']);
        $webmail = Request::post('webmail', $this->config['webmail']);
        $openRegister = Request::post('openRegister') === 'on' ? 'true' : 'false';
        $openReplace = Request::post('openReplace') === 'on' ? 'true' : 'false';
        $nameLength = (int)Request::post('nameLength', $this->config['nameLength']);
        $emailQuota = (int)Request::post('emailQuota', $this->config['emailQuota']);
        $emailQuotaUnit = Request::post('emailQuotaUnit', $this->config['emailQuotaUnit']);
        $superKey = Request::post('superKey', $this->config['superKey']);
        $footer = Request::post('footer', $this->config['footer']);
        $excludeDomains = Request::post('exclude', '');
        
        // 处理排除域名列表
        $excludeArray = [];
        if (!empty($excludeDomains)) {
            $domains = explode("\n", str_replace("\r\n", "\n", $excludeDomains));
            foreach ($domains as $domain) {
                $domain = trim($domain);
                if (!empty($domain)) {
                    $excludeArray[] = $domain;
                }
            }
        }
        
        // 生成配置文件内容
        $configContent = "<?php\n// 自定义配置（会覆盖默认配置）\n\n";
        $configContent .= "// 站点名称\n";
        $configContent .= "\$config['sitename'] = \"$sitename\";\n";
        $configContent .= "// 宝塔面板地址\n";
        $configContent .= "\$config['panel'] = \"$panel\";\n";
        $configContent .= "// Api接口秘钥\n";
        $configContent .= "\$config['apikey'] = \"$apikey\";\n";
        $configContent .= "// 邮箱Webmail登录地址\n";
        $configContent .= "\$config['webmail'] = \"$webmail\";\n";
        $configContent .= "// 注册开关\n";
        $configContent .= "\$config['openRegister'] = $openRegister;\n";
        $configContent .= "// 改密开关\n";
        $configContent .= "\$config['openReplace'] = $openReplace;\n";
        $configContent .= "// 允许注册名称最低长度\n";
        $configContent .= "\$config['nameLength'] = $nameLength;\n";
        $configContent .= "// 邮箱容量设置\n";
        $configContent .= "\$config['emailQuota'] = $emailQuota;\n";
        $configContent .= "// 邮箱容量单位\n";
        $configContent .= "\$config['emailQuotaUnit'] = \"$emailQuotaUnit\";\n";
        $configContent .= "// 超级秘钥\n";
        $configContent .= "\$config['superKey'] = \"$superKey\";\n";
        $configContent .= "// 底部版权信息\n";
        $configContent .= "\$config['footer'] = \"$footer\";\n";
        
        // 添加排除域名列表
        if (!empty($excludeArray)) {
            $configContent .= "// 域名过滤表（隐藏/禁用）\n";
            $configContent .= "\$config['exclude'] = [\n";
            foreach ($excludeArray as $domain) {
                $configContent .= "    '$domain',\n";
            }
            $configContent .= "];\n";
        } else {
            $configContent .= "// 域名过滤表（隐藏/禁用）\n";
            $configContent .= "\$config['exclude'] = [];\n";
        }
        
        // 写入配置文件
        if (file_put_contents($configFile, $configContent)) {
            // 安装成功，重定向到首页
            $this->redirect('./?controller=home&action=index');
        } else {
            // 安装失败，显示错误信息
            $this->template->assign('error', '配置文件写入失败，请检查目录权限');
            $this->template->assign('config', $this->config);
            $this->display('install');
        }
    }
}