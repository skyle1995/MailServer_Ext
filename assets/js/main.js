/**
 * Layui自定义JavaScript脚本
 * 
 * 这个文件包含使用Layui重构的网站JavaScript功能
 */

// 当文档加载完成后执行
layui.use(['layer', 'form', 'element'], function() {
    var layer = layui.layer;
    var form = layui.form;
    var element = layui.element;
    
    // 初始化表单元素
    form.render();
    
    // 禁用所有带有disabled类的按钮的默认点击行为
    var disabledButtons = document.querySelectorAll('.layui-btn.layui-btn-disabled');
    for (var i = 0; i < disabledButtons.length; i++) {
        disabledButtons[i].addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });
    }
});

/**
 * 刷新验证码函数
 */
var refreshCaptcha = () => {
    document.getElementById('captcha_img').src = '?controller=base&action=captcha&t=' + new Date().getTime();
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
    layui.use('layer', function() {
        var layer = layui.layer;
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
    });
};

/**
 * 加载域名列表
 */
var loadDomains = () => {
    layui.use('layer', function() {
        var layer = layui.layer;
        
        // 显示加载层
        var loadIndex = layer.msg('加载中', {
            icon: 16,
            shade: [0.3, '#000'], // 0.3透明度的黑色背景
            shadeClose: false // 禁止点击遮罩关闭
        });
        
        // 发送AJAX请求获取域名列表
        $.ajax({
            url: '?controller=api&action=domains',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // 关闭加载层
                layer.close(loadIndex);
                
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    var domainSelect = $('#domain');
                    domainSelect.find('option:not(:first)').remove();
                    
                    $.each(response.data, function(index, domain) {
                        domainSelect.append($('<option></option>').val(domain).text(domain));
                    });
                    
                    // 重新渲染表单
                    layui.form.render('select');
                } else {
                    layer.alert(response.message || '无法获取可用后缀列表，请刷新页面重试。', {
                        icon: 2,
                        title: '错误提示',
                        btn: ['确定']
                    });
                }
            },
            error: function(xhr, status, error) {
                // 关闭加载层
                layer.close(loadIndex);
                layer.alert('加载域名列表失败，请刷新页面重试。', {
                    icon: 2,
                    title: '错误提示',
                    btn: ['确定']
                });
                console.error('加载域名列表失败:', error);
            }
        });
    });
};

/**
 * 初始化粒子效果
 */
var initParticles = () => {
    // 创建粒子容器
    const particlesContainer = document.createElement('div');
    particlesContainer.id = 'particles-js';
    document.body.insertBefore(particlesContainer, document.body.firstChild);
    
    // 初始化粒子效果
    if (typeof particlesJS !== 'undefined') {
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 100,
                    density: {
                        enable: true,
                        value_area: 1000
                    }
                },
                color: {
                    value: ["#2196F3", "#4CAF50", "#FF9800", "#9C27B0"]
                },
                shape: {
                    type: "circle",
                    stroke: {
                        width: 0,
                        color: "#000000"
                    }
                },
                opacity: {
                    value: 0.5,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 1,
                        opacity_min: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 3,
                    random: true,
                    anim: {
                        enable: false,
                        speed: 40,
                        size_min: 0.1,
                        sync: false
                    }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#2196F3",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 3,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "bounce",
                    bounce: true,
                    attract: {
                        enable: true,
                        rotateX: 600,
                        rotateY: 1200
                    }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "repulse"
                    },
                    onclick: {
                        enable: true,
                        mode: "bubble"
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 180,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    bubble: {
                        distance: 300,
                        size: 60,
                        duration: 2,
                        opacity: 0.8,
                        speed: 3
                    },
                    repulse: {
                        distance: 150,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 6
                    },
                    remove: {
                        particles_nb: 2
                    }
                }
            },
            retina_detect: true
        });
    } else {
        console.error('粒子效果需要particlesJS库支持');
    }
};

// 当文档加载完成后初始化粒子效果
document.addEventListener('DOMContentLoaded', function() {
    // 初始化粒子效果
    initParticles();
});