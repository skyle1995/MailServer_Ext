/**
 * 自定义JavaScript脚本
 * 
 * 这个文件包含网站的自定义JavaScript功能
 */

// 当前页面的URL，用于历史记录管理
let currentPage = window.location.href;

// 当文档加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化提示框和弹出框（如果jQuery可用）
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
    
    // 禁用所有带有disabled类的按钮的默认点击行为
    var disabledButtons = document.querySelectorAll('.btn.disabled');
    for (var i = 0; i < disabledButtons.length; i++) {
        disabledButtons[i].addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });
    }
    
    // 初始化AJAX页面加载
    initAjaxPageLoad();
});

/**
 * 初始化AJAX页面加载功能
 * 
 * 拦截页面内的链接点击事件，使用AJAX加载页面内容
 */
const initAjaxPageLoad = () => {
    // 获取内容容器
    const contentContainer = document.querySelector('.col-md-8');
    if (!contentContainer) return;
    
    // 拦截所有内部链接的点击事件
    document.addEventListener('click', (e) => {
        // 查找被点击的链接元素
        let target = e.target;
        while (target && target.tagName !== 'A') {
            target = target.parentElement;
            if (!target) return;
        }
        
        // 确保是内部链接（带有data-ajax属性或以?controller开头的链接）
        const href = target.getAttribute('href');
        const isAjaxLink = target.getAttribute('data-ajax') === 'true';
        
        // 如果不是内部链接或不是标记为AJAX的链接，则不处理
        if (!href || (!isAjaxLink && !href.startsWith('?controller='))) return;
        
        // 如果是外部链接（如登录邮箱按钮），则不使用AJAX加载
        if (href.startsWith('http')) return;
        
        // 阻止默认行为
        e.preventDefault();
        
        // 加载页面内容
        loadPageContent(href);
    });
    
    // 处理浏览器的前进/后退按钮
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            loadPageContent(e.state.url, false);
        }
    });
};

/**
 * 通过AJAX加载页面内容
 * 
 * @param {string} url - 要加载的页面URL
 * @param {boolean} pushState - 是否将URL添加到浏览器历史记录中
 */
const loadPageContent = (url, pushState = true) => {
    // 显示加载中提示
    const contentContainer = document.querySelector('.col-md-8');
    if (!contentContainer) return;
    
    // 使用layer.js显示加载中
    const loadingIndex = layer.load(1, {
        shade: [0.1, '#fff']
    });
    
    // 发送AJAX请求
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // 创建临时DOM元素来解析HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // 提取页面标题
            const title = doc.querySelector('title')?.textContent || document.title;
            
            // 提取内容区域
            const newContent = doc.querySelector('.col-md-8')?.innerHTML;
            if (newContent) {
                // 更新页面内容
                contentContainer.innerHTML = newContent;
                
                // 更新页面标题
                document.title = title;
                
                // 更新URL历史记录
                if (pushState) {
                    const fullUrl = window.location.pathname + url;
                    window.history.pushState({ url: url }, title, fullUrl);
                    currentPage = fullUrl;
                }
                
                // 重新初始化页面上的交互元素
                reinitializePageElements();
            }
            
            // 关闭加载提示
            layer.close(loadingIndex);
        })
        .catch(error => {
            console.error('加载页面失败:', error);
            
            // 显示错误提示
            layer.msg('加载页面失败，请刷新重试', {
                icon: 2,
                time: 2000
            });
            
            // 关闭加载提示
            layer.close(loadingIndex);
        });
};

/**
 * 重新初始化页面元素
 * 
 * 在AJAX加载新内容后，重新初始化页面上的交互元素
 */
const reinitializePageElements = () => {
    // 重新初始化提示框和弹出框
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
    
    // 重新绑定表单提交事件
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        // 加载域名列表
        loadDomainList();
        
        // 刷新验证码
        if (typeof refreshCaptcha === 'function') {
            refreshCaptcha();
        } else {
            const captchaImg = document.getElementById('captcha_img');
            if (captchaImg) {
                captchaImg.src = '?controller=home&action=captcha&t=' + new Date().getTime();
                captchaImg.onclick = function() {
                    this.src = '?controller=home&action=captcha&t=' + new Date().getTime();
                };
            }
        }
        
        // 重新绑定注册表单提交事件
        if (typeof $ !== 'undefined') {
            $(registerForm).off('submit').on('submit', function(e) {
                e.preventDefault();
                
                // 表单验证和提交逻辑
                const username = $('#username').val().trim();
                const domain = $('#domain').val();
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                const fullName = $('#full_name').val().trim();
                const captcha = $('#captcha').val().trim();
                let isValid = true;
                
                // 清除之前的错误提示
                $('.help-block.text-danger').remove();
                
                let errorMessage = '';
                
                // 验证用户名
                if (username === '') {
                    errorMessage = '请输入账号名';
                    isValid = false;
                } else if (username.length < 3) {
                    errorMessage = '账号名长度不能少于3个字符';
                    isValid = false;
                } else if (!/^[a-z][a-z0-9]*$/.test(username)) {
                    errorMessage = '用户名必须以小写字母开头，且只能包含小写字母和数字';
                    isValid = false;
                }
                
                // 验证域名
                if (domain === '' && isValid) {
                    errorMessage = '请选择后缀';
                    isValid = false;
                }
                
                // 验证昵称
                if (fullName === '' && isValid) {
                    errorMessage = '请输入您的昵称';
                    isValid = false;
                }
                
                // 验证密码
                if (password === '' && isValid) {
                    errorMessage = '请输入密码';
                    isValid = false;
                } else if (password.length < 8 && isValid) {
                    errorMessage = '密码长度不能少于8个字符';
                    isValid = false;
                }
                
                // 验证确认密码
                if (confirmPassword === '' && isValid) {
                    errorMessage = '请再次输入密码';
                    isValid = false;
                } else if (password !== confirmPassword && isValid) {
                    errorMessage = '两次输入的密码不一致';
                    isValid = false;
                }
                
                // 验证验证码
                if (captcha === '' && isValid) {
                    errorMessage = '请输入验证码';
                    isValid = false;
                }
                
                // 如果验证失败，显示错误信息
                if (!isValid) {
                    layer.msg(errorMessage, {
                        icon: 2,  // 错误图标
                        time: 3000,  // 3秒后自动关闭
                        anim: 6,  // 抖动动画
                        shade: [0.3, '#000']  // 遮罩
                    });
                    return false;
                }
                
                if (isValid) {
                    // 显示加载层
                    const loadingIndex = layer.msg('加载中', {
                        icon: 16,
                        shade: [0.3, '#000'],
                        shadeClose: false
                    });
                    
                    // 通过AJAX提交表单
                    $.ajax({
                        url: '?controller=api&action=register',
                        type: 'POST',
                        data: {
                            username: username,
                            domain: domain,
                            full_name: fullName,
                            password: password,
                            confirm_password: confirmPassword,
                            captcha: captcha,
                            csrf_token: $('input[name="csrf_token"]').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            if (response.status === 'success') {
                                // 清理表单
                                $('#registerForm')[0].reset();
                                
                                // 使用弹出层显示成功信息
                                layer.alert('<div style="text-align:center;padding:20px;">' +
                                        '<p style="font-size:20px;margin-bottom:10px;color:green;">注册邮箱成功！</p>' +
                                        '<p>您的邮箱地址是：<strong>' + response.data.email + '</strong></p>' +
                                        '<p>您现在可以使用新账户登录邮箱系统。</p>' +
                                        '</div>', {
                                    title: '注册成功',
                                    btn: ['关闭'],
                                    area: ['400px', 'auto'],
                                    shade: 0.6,
                                    shadeClose: false
                                });
                            } else {
                                // 注册失败，显示错误信息
                                layer.msg(response.message, {
                                    icon: 2,
                                    time: 3000,
                                    anim: 6,
                                    shade: [0.3, '#000']
                                });
                                
                                // 如果有字段错误，显示错误信息
                                if (response.errors) {
                                    $.each(response.errors, function(field, error) {
                                        layer.tips(error, '#' + field, {
                                            tips: [2, '#FF5722'],
                                            time: 4000
                                        });
                                    });
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            // 显示错误信息
                            let errorMessage = '请求失败，请稍后再试';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('解析响应失败', e);
                            }
                            
                            layer.msg(errorMessage, {
                                icon: 2,
                                time: 3000,
                                anim: 6,
                                shade: [0.3, '#000']
                            });
                        }
                    });
                }
            });
        }
    }
    
    const replaceForm = document.getElementById('replaceForm');
    if (replaceForm) {
        // 加载域名列表
        loadDomainList();
        
        // 刷新验证码
        if (typeof refreshCaptcha === 'function') {
            refreshCaptcha();
        } else {
            const captchaImg = document.getElementById('captcha_img');
            if (captchaImg) {
                captchaImg.src = '?controller=home&action=captcha&t=' + new Date().getTime();
                captchaImg.onclick = function() {
                    this.src = '?controller=home&action=captcha&t=' + new Date().getTime();
                };
            }
        }
        
        // 重新绑定修改密码表单提交事件
        if (typeof $ !== 'undefined') {
            $(replaceForm).off('submit').on('submit', function(e) {
                e.preventDefault();
                
                // 表单验证和提交逻辑
                const username = $('#username').val().trim();
                const domain = $('#domain').val();
                const password = $('#password').val();
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                const captcha = $('#captcha').val().trim();
                let isValid = true;
                
                // 清除之前的错误提示
                $('.help-block.text-danger').remove();
                
                let errorMessage = '';
                
                // 验证用户名
                if (username === '') {
                    errorMessage = '请输入账号名';
                    isValid = false;
                }
                
                // 验证域名
                if (domain === '' && isValid) {
                    errorMessage = '请选择后缀';
                    isValid = false;
                }
                
                // 验证当前密码
                if (password === '' && isValid) {
                    errorMessage = '请输入当前密码';
                    isValid = false;
                }
                
                // 验证新密码
                if (newPassword === '' && isValid) {
                    errorMessage = '请输入新密码';
                    isValid = false;
                } else if (newPassword.length < 8 && isValid) {
                    errorMessage = '新密码长度不能少于8个字符';
                    isValid = false;
                }
                
                // 验证确认密码
                if (confirmPassword === '' && isValid) {
                    errorMessage = '请再次输入新密码';
                    isValid = false;
                } else if (newPassword !== confirmPassword && isValid) {
                    errorMessage = '两次输入的新密码不一致';
                    isValid = false;
                }
                
                // 验证验证码
                if (captcha === '' && isValid) {
                    errorMessage = '请输入验证码';
                    isValid = false;
                }
                
                // 如果验证失败，显示错误信息
                if (!isValid) {
                    layer.msg(errorMessage, {
                        icon: 2,
                        time: 3000,
                        anim: 6,
                        shade: [0.3, '#000']
                    });
                    return false;
                }
                
                if (isValid) {
                    // 显示加载层
                    const loadingIndex = layer.msg('加载中', {
                        icon: 16,
                        shade: [0.3, '#000'],
                        shadeClose: false
                    });
                    
                    // 通过AJAX提交表单
                    $.ajax({
                        url: '?controller=api&action=replace',
                        type: 'POST',
                        data: {
                            username: username,
                            domain: domain,
                            password: password,
                            new_password: newPassword,
                            confirm_password: confirmPassword,
                            captcha: captcha,
                            csrf_token: $('input[name="csrf_token"]').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            if (response.status === 'success') {
                                // 清理表单
                                $('#replaceForm')[0].reset();
                                
                                // 使用弹出层显示成功信息
                                layer.alert('<div style="text-align:center;padding:20px;">' +
                                        '<p style="font-size:20px;margin-bottom:10px;color:green;">修改密码成功！</p>' +
                                        '<p>您的邮箱密码已成功修改。</p>' +
                                        '<p>您现在可以使用新密码登录邮箱系统。</p>' +
                                        '</div>', {
                                    title: '修改成功',
                                    btn: ['关闭'],
                                    area: ['400px', 'auto'],
                                    shade: 0.6,
                                    shadeClose: false
                                });
                            } else {
                                // 修改失败，显示错误信息
                                layer.msg(response.message, {
                                    icon: 2,
                                    time: 3000,
                                    anim: 6,
                                    shade: [0.3, '#000']
                                });
                                
                                // 如果有字段错误，显示错误信息
                                if (response.errors) {
                                    $.each(response.errors, function(field, error) {
                                        layer.tips(error, '#' + field, {
                                            tips: [2, '#FF5722'],
                                            time: 4000
                                        });
                                    });
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            // 关闭加载层
                            layer.close(loadingIndex);
                            
                            // 显示错误信息
                            let errorMessage = '请求失败，请稍后再试';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('解析响应失败', e);
                            }
                            
                            layer.msg(errorMessage, {
                                icon: 2,
                                time: 3000,
                                anim: 6,
                                shade: [0.3, '#000']
                            });
                        }
                    });
                }
            });
        }
    }
    
    // 禁用所有带有disabled类的按钮
    var disabledButtons = document.querySelectorAll('.btn.disabled');
    for (var i = 0; i < disabledButtons.length; i++) {
        disabledButtons[i].addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });
    }
};

/**
 * 加载域名列表
 * 
 * 从API获取可用的邮箱域名列表
 */
const loadDomainList = () => {
    const domainSelect = document.getElementById('domain');
    if (!domainSelect) return;
    
    // 发送AJAX请求获取域名列表
    fetch('?controller=api&action=domains')
        .then(response => response.json())
        .then(data => {
            if (data && data.data && Array.isArray(data.data)) {
                // 清空现有选项（保留第一个默认选项）
                while (domainSelect.options.length > 1) {
                    domainSelect.remove(1);
                }
                
                // 添加新选项
                data.data.forEach(domain => {
                    const option = document.createElement('option');
                    option.value = domain;
                    option.textContent = '@' + domain;
                    domainSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('加载域名列表失败:', error);
        });
};

/**
 * 显示Layer弹出层消息
 * 
 * @param {string} message - 要显示的消息内容
 * @param {string} type - 消息类型（success, info, warning, danger）
 * @param {string} title - 弹出层标题
 */
var showModalMessage = (message, type, title) => {
    // 设置默认值
    type = type || 'success';
    title = title || '提示';
    
    // 根据类型设置图标
    var icon = 1; // 默认为成功图标
    
    if (type === 'success') {
        icon = 1; // 成功图标
    } else if (type === 'info') {
        icon = 0; // 信息图标
    } else if (type === 'warning') {
        icon = 3; // 警告图标
    } else if (type === 'danger') {
        icon = 2; // 错误图标
    }
    
    // 使用layer弹出层
     layer.open({
         type: 0, // 信息框
         title: title,
         content: message,
         icon: icon,
         btn: ['确定'],
         yes: function(index, layero) {
             // 点击确定按钮的回调
             layer.close(index);
         }
     });
};