<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace Controllers;

use Core\Network;
use Core\Common;

/**
 * HomeController类 - 处理首页相关请求
 * 
 * 该类处理网站首页的显示和相关功能
 */
class HomeController extends BaseController {
    /**
     * 构造函数
     * 
     * @param array $config 配置数组
     */
    public function __construct($config = []) {
        parent::__construct($config);
        
        // 设置该控制器的默认响应类型为HTML
        $this->setDefaultResponseType(self::RESPONSE_TYPE_HTML);
    }

    /**
     * 首页方法
     */
    public function indexAction() {
        // 设置页面标题
        $this->template->assign([
            'title' => '首页 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 渲染首页模板并输出
        $this->display('home/index');
    }
    
    /**
     * 注册邮箱账户方法
     * 
     * 仅负责显示注册页面，表单提交通过AJAX处理
     */
    public function registerAction() {
        // 检查是否允许注册
        if (!isset($this->config['openRegister']) || !$this->config['openRegister']) {
            // 设置页面标题
            $this->template->assign([
                'title' => '注册邮箱 - ' . ($this->config['sitename'] ?? '邮件服务器插件'),
                'disabled' => true,
                'disabled_message' => '注册功能当前未开放'
            ]);
            
            // 显示注册表单（禁用状态）
            $this->display('home/register');
            return;
        }
        
        // 设置页面标题
        $this->template->assign([
            'title' => '注册邮箱 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 显示注册表单（域名列表通过AJAX获取）
        $this->display('home/register');
    }
    
    /**
     * 修改密码页面方法
     * 
     * 仅负责显示修改密码页面，表单提交通过AJAX处理
     */
    public function replaceAction() {
        // 检查是否允许修改密码
        if (!isset($this->config['openReplace']) || !$this->config['openReplace']) {
            // 设置页面标题
            $this->template->assign([
                'title' => '修改密码 - ' . ($this->config['sitename'] ?? '邮件服务器插件'),
                'disabled' => true,
                'disabled_message' => '修改密码功能当前未开放'
            ]);
            
            // 显示修改密码表单（禁用状态）
            $this->display('home/replace');
            return;
        }
        
        // 设置页面标题
        $this->template->assign([
            'title' => '修改密码 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 显示修改密码表单（域名列表通过AJAX获取）
        $this->display('home/replace');
    }
}