# Baota_MailServer_Plugin
宝塔面板Linux邮局PHP插件补丁  

注意：本项目仅用于学习交流使用，禁止用于非法用途！  
使用本插件、补丁产生的任何法律责任与本人无关，由使用者本人自行承担  
  
学习交流QQ群：757159644 （群文件有配套注册机工具）  
  
1.关于页面显示：  
如果界面无法正常显示内容，请自行修改静态资源调用（原静态资源来自Roundcube Webmail）  

2.关于重置密码：  
由于宝塔api的没有密码校验，只能通过模拟imap登录的方式校验密码  
或者自行修改启用php自带的imap扩展库，代码方案可以直接百度查询  
或者对接Roundcube Webmail进行网页登录验证（推荐使用网页验证）
  
3.额外内容：  
站点nginx配置添加以下规则，可以无需加.php访问  

    # 允许访问时省略掉webmail目录下的.php文件后缀  
    location /webmail/ {
      try_files $uri $uri/ $uri.php$is_args$args;
    }

/webmail/ 为插件补丁安装路径  
