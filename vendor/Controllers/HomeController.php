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
     * 获取可用域名列表
     * 
     * 从宝塔API获取可用的邮箱域名列表
     * 
     * @return array 域名列表：{"data":["example.com","example1.com","example2.com","example3.com","example4.com"]}
     */
    private function getDomains() {
        // 调用宝塔API获取域名列表
        $url = $this->config["panel"] . '/plugin?action=a&name=mail_ext&s=get_domain';
        $p_data = Common::getKeyData($this->config["apikey"]);
        
        // 发送API请求
        $result = Network::curlSenior($url, 'POST', http_build_query($p_data));
        $response = json_decode($result, true);
        
        // 处理API响应
        if ($response && isset($response['status']) && $response['status'] && isset($response['data'])) {
            // 直接返回域名列表数组
            return $response['data'];
        }
        
        // 如果API请求失败，返回空数组
        return [];
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
        $this->display('home');
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
            $this->display('register');
            return;
        }
        
        // 设置页面标题
        $this->template->assign([
            'title' => '注册邮箱 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 获取域名列表
        $domains = $this->getDomains();
        
        // 如果没有可用域名，显示错误
        if (empty($domains)) {
            $this->display('register', [
                'error_message' => '无法获取可用后缀列表，请稍后再试或联系管理员。',
                'domains' => []
            ]);
            return;
        }
        
        // 显示注册表单
        $this->display('register', [
            'domains' => $domains
        ]);
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
            $this->display('replace');
            return;
        }
        
        // 设置页面标题
        $this->template->assign([
            'title' => '修改密码 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 获取域名列表
        $domains = $this->getDomains();
        
        // 如果没有可用域名，显示错误
        if (empty($domains)) {
            $this->display('replace', [
                'error_message' => '无法获取可用后缀列表，请稍后再试或联系管理员。',
                'domains' => []
            ]);
            return;
        }
        
        // 显示修改密码表单
        $this->display('replace', [
            'domains' => $domains
        ]);
    }
    
    /**
     * 关于页面方法
     */
    public function aboutAction() {
        // 设置页面标题
        $this->template->assign([
            'title' => '关于我们 - ' . ($this->config['sitename'] ?? '邮件服务器插件')
        ]);
        
        // 渲染合并后的关于和联系页面模板并输出
        $this->display('about', [
            'tips' => $this->config['about']['tips'] ?? '这是一个简单的邮件服务器管理插件，提供全面的邮件服务功能。',
            'email' => $this->config['about']['email'] ?? 'support@example.com',
            'phone' => $this->config['about']['phone'] ?? '123-456-7890',
            'address' => $this->config['about']['address'] ?? '北京市海淀区中关村科技园'
        ]);
    }
}