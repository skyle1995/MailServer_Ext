<?php
// 注意：本项目仅用于学习交流使用，禁止用于非法用途！
// 使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担

namespace View;

/**
 * Template类 - 提供模板渲染功能
 * 
 * 该类提供了模板渲染、变量替换、布局模板和部分视图渲染等功能
 */
class Template {
    /**
     * 模板目录路径
     * @var string
     */
    private $templatePath;
    
    /**
     * 布局模板文件名
     * @var string|null
     */
    private $layout = null;
    
    /**
     * 模板变量数组
     * @var array
     */
    private $vars = [];
    
    /**
     * 构造函数
     * 
     * @param string $templatePath 模板目录路径，默认为项目根目录下的template目录
     */
    public function __construct($templatePath = null) {
        // 如果未指定模板路径，则使用默认路径
        if ($templatePath === null) {
            $this->templatePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'template';
        } else {
            $this->templatePath = rtrim($templatePath, '/\\');
        }
        
        // 确保模板目录存在
        if (!is_dir($this->templatePath)) {
            mkdir($this->templatePath, 0755, true);
        }
    }
    
    /**
     * 设置模板变量
     * 
     * @param string|array $name 变量名或包含变量的关联数组
     * @param mixed $value 变量值（当$name为字符串时使用）
     * @return $this 支持链式调用
     */
    public function assign($name, $value = null) {
        // 如果$name是数组，则批量设置变量
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
        
        return $this;
    }
    
    /**
     * 设置布局模板
     * 
     * @param string|null $layout 布局模板文件名（不含扩展名），null表示不使用布局
     * @return $this 支持链式调用
     */
    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * 渲染模板并返回结果
     * 
     * @param string $template 模板文件名（不含扩展名）
     * @param array $vars 附加的模板变量（会与之前设置的变量合并）
     * @return string 渲染后的HTML内容
     */
    public function render($template, array $vars = []) {
        // 合并变量
        $vars = array_merge($this->vars, $vars);
        
        // 提取变量到当前作用域
        extract($vars);
        
        // 启动输出缓冲
        ob_start();
        
        // 包含模板文件
        $templateFile = $this->templatePath . DIRECTORY_SEPARATOR . $template . '.php';
        if (!file_exists($templateFile)) {
            throw new \Exception("Template file not found: {$templateFile}");
        }
        
        include $templateFile;
        
        // 获取渲染内容
        $content = ob_get_clean();
        
        // 如果设置了布局模板，则渲染布局
        if ($this->layout !== null) {
            // 将内容变量传递给布局模板
            $layoutVars = array_merge($vars, ['content' => $content]);
            
            // 渲染布局模板
            $layoutFile = $this->templatePath . DIRECTORY_SEPARATOR . $this->layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout template file not found: {$layoutFile}");
            }
            
            // 提取变量到当前作用域
            extract($layoutVars);
            
            // 启动新的输出缓冲
            ob_start();
            include $layoutFile;
            $content = ob_get_clean();
        }
        
        return $content;
    }
    
    /**
     * 渲染模板并直接输出
     * 
     * @param string $template 模板文件名（不含扩展名）
     * @param array $vars 附加的模板变量（会与之前设置的变量合并）
     */
    public function display($template, array $vars = []) {
        echo $this->render($template, $vars);
    }
    
    /**
     * 渲染部分视图并返回结果
     * 
     * @param string $partial 部分视图文件名（不含扩展名）
     * @param array $vars 模板变量
     * @return string 渲染后的HTML内容
     */
    public function renderPartial($partial, array $vars = []) {
        // 合并变量
        $vars = array_merge($this->vars, $vars);
        
        // 提取变量到当前作用域
        extract($vars);
        
        // 启动输出缓冲
        ob_start();
        
        // 包含部分视图文件
        $partialFile = $this->templatePath . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $partial . '.php';
        if (!file_exists($partialFile)) {
            throw new \Exception("Partial view file not found: {$partialFile}");
        }
        
        include $partialFile;
        
        // 获取渲染内容
        return ob_get_clean();
    }
    
    /**
     * 检查模板文件是否存在
     * 
     * @param string $template 模板文件名（不含扩展名）
     * @return bool 模板文件是否存在
     */
    public function templateExists($template) {
        $templateFile = $this->templatePath . DIRECTORY_SEPARATOR . $template . '.php';
        return file_exists($templateFile);
    }
    
    /**
     * 获取当前模板目录路径
     * 
     * @return string 模板目录路径
     */
    public function getTemplatePath() {
        return $this->templatePath;
    }
    
    /**
     * 设置模板目录路径
     * 
     * @param string $path 新的模板目录路径
     * @return $this 支持链式调用
     */
    public function setTemplatePath($path) {
        $this->templatePath = rtrim($path, '/\\');
        
        // 确保模板目录存在
        if (!is_dir($this->templatePath)) {
            mkdir($this->templatePath, 0755, true);
        }
        
        return $this;
    }
}