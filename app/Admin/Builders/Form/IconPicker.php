<?php

namespace App\Admin\Builders\Form;

use App\Admin\Builders\Form;

class IconPicker extends Field
{
    /**
     * The prefix registered on the controller.
     * 
     * @var string
     */
    public $prefix = '';

    /**
     * The suffix registered on the controller.
     * 
     * @var string
     */
    public $suffix = '';

    /** 
     * The value registered on the controller.
     * 
     * @var string
     */
    public $value = '';

    /**
     * The options registered on the controller.
     * 
     * @var array<IconPickerOption>
     */
    public $options;

    /**
     * The icons registered on the controller.
     * 
     * @var array
     */
    public $icons = [
        [
            "name" =>"Github",
            "code" =>"&#xe6a7;",
            "class" =>"layui-icon-github"
        ],
        [
            "name" =>"月亮",
            "code" =>"&#xe6c2;",
            "class" =>"layui-icon-moon"
        ],
        [
            "name" =>"错误",
            "code" =>"&#xe693;",
            "class" =>"layui-icon-error"
        ],
        [
            "name" =>"成功",
            "code" =>"&#xe697;",
            "class" =>"layui-icon-success"
        ],
        [
            "name" =>"问号",
            "code" =>"&#xe699;",
            "class" =>"layui-icon-question"
        ],
        [
            "name" =>"锁定",
            "code" =>"&#xe69a;",
            "class" =>"layui-icon-lock"
        ],
        [
            "name" =>"显示",
            "code" =>"&#xe695;",
            "class" =>"layui-icon-eye"
        ],
        [
            "name" =>"隐藏",
            "code" =>"&#xe696;",
            "class" =>"layui-icon-eye-invisible"
        ],
        [
            "name" =>"清空/删除",
            "code" =>"&#xe788;",
            "class" =>"layui-icon-clear"
        ],
        [
            "name" =>"退格",
            "code" =>"&#xe694;",
            "class" =>"layui-icon-backspace"
        ],
        [
            "name" =>"禁用",
            "code" =>"&#xe6cc;",
            "class" =>"layui-icon-disabled"
        ],
        [
            "name" =>"感叹号/提示",
            "code" =>"&#xeb2e;",
            "class" =>"layui-icon-tips-fill"
        ],
        [
            "name" =>"测试/K线图",
            "code" =>"&#xe692;",
            "class" =>"layui-icon-test"
        ],
        [
            "name" =>"音乐/音符",
            "code" =>"&#xe690;",
            "class" =>"layui-icon-music"
        ],
        [
            "name" =>"Chrome",
            "code" =>"&#xe68a;",
            "class" =>"layui-icon-chrome"
        ],
        [
            "name" =>"Firefox",
            "code" =>"&#xe686;",
            "class" =>"layui-icon-firefox"
        ],
        [
            "name" =>"Edge",
            "code" =>"&#xe68b;",
            "class" =>"layui-icon-edge"
        ],
        [
            "name" =>"IE",
            "code" =>"&#xe7bb;",
            "class" =>"layui-icon-ie"
        ],
        [
            "name" =>"实心",
            "code" =>"&#xe68f;",
            "class" =>"layui-icon-heart-fill"
        ],
        [
            "name" =>"空心",
            "code" =>"&#xe68c;",
            "class" =>"layui-icon-heart"
        ],
        [
            "name" =>"太阳/明亮",
            "code" =>"&#xe748;",
            "class" =>"layui-icon-light"
        ],
        [
            "name" =>"时间/历史",
            "code" =>"&#xe68d;",
            "class" =>"layui-icon-time"
        ],
        [
            "name" =>"蓝牙",
            "code" =>"&#xe689;",
            "class" =>"layui-icon-bluetooth"
        ],
        [
            "name" =>"@艾特",
            "code" =>"&#xe687;",
            "class" =>"layui-icon-at"
        ],
        [
            "name" =>"静音",
            "code" =>"&#xe685;",
            "class" =>"layui-icon-mute"
        ],
        [
            "name" =>"录音/麦克风",
            "code" =>"&#xe6dc;",
            "class" =>"layui-icon-mike"
        ],
        [
            "name" =>"密钥/钥匙",
            "code" =>"&#xe683;",
            "class" =>"layui-icon-key"
        ],
        [
            "name" =>"礼物/活动",
            "code" =>"&#xe627;",
            "class" =>"layui-icon-gift"
        ],
        [
            "name" =>"邮箱",
            "code" =>"&#xe618;",
            "class" =>"layui-icon-email"
        ],
        [
            "name" =>"RSS",
            "code" =>"&#xe808;",
            "class" =>"layui-icon-rss"
        ],
        [
            "name" =>"WiFi",
            "code" =>"&#xe7e0;",
            "class" =>"layui-icon-wifi"
        ],
        [
            "name" =>"退出/注销",
            "code" =>"&#xe682;",
            "class" =>"layui-icon-logout"
        ],
        [
            "name" =>"Android 安卓",
            "code" =>"&#xe684;",
            "class" =>"layui-icon-android"
        ],
        [
            "name" =>"Apple IOS 苹果",
            "code" =>"&#xe680;",
            "class" =>"layui-icon-ios"
        ],
        [
            "name" =>"Windows",
            "code" =>"&#xe67f;",
            "class" =>"layui-icon-windows"
        ],
        [
            "name" =>"穿梭框",
            "code" =>"&#xe691;",
            "class" =>"layui-icon-transfer"
        ],
        [
            "name" =>"客服",
            "code" =>"&#xe626;",
            "class" =>"layui-icon-service"
        ],
        [
            "name" =>"减",
            "code" =>"&#xe67e;",
            "class" =>"layui-icon-subtraction"
        ],
        [
            "name" =>"加",
            "code" =>"&#xe624;",
            "class" =>"layui-icon-addition"
        ],
        [
            "name" =>"滑块",
            "code" =>"&#xe714;",
            "class" =>"layui-icon-slider"
        ],
        [
            "name" =>"打印",
            "code" =>"&#xe66d;",
            "class" =>"layui-icon-print"
        ],
        [
            "name" =>"导出",
            "code" =>"&#xe67d;",
            "class" =>"layui-icon-export"
        ],
        [
            "name" =>"列",
            "code" =>"&#xe610;",
            "class" =>"layui-icon-cols"
        ],
        [
            "name" =>"退出全屏",
            "code" =>"&#xe758;",
            "class" =>"layui-icon-screen-restore"
        ],
        [
            "name" =>"全屏",
            "code" =>"&#xe622;",
            "class" =>"layui-icon-screen-full"
        ],
        [
            "name" =>"半星",
            "code" =>"&#xe6c9;",
            "class" =>"layui-icon-rate-half"
        ],
        [
            "name" =>"星星-空心",
            "code" =>"&#xe67b;",
            "class" =>"layui-icon-rate"
        ],
        [
            "name" =>"星星-实心",
            "code" =>"&#xe67a;",
            "class" =>"layui-icon-rate-solid"
        ],
        [
            "name" =>"手机",
            "code" =>"&#xe678;",
            "class" =>"layui-icon-cellphone"
        ],
        [
            "name" =>"验证码",
            "code" =>"&#xe679;",
            "class" =>"layui-icon-vercode"
        ],
        [
            "name" =>"微信",
            "code" =>"&#xe677;",
            "class" =>"layui-icon-login-wechat"
        ],
        [
            "name" =>"QQ",
            "code" =>"&#xe676;",
            "class" =>"layui-icon-login-qq"
        ],
        [
            "name" =>"微博",
            "code" =>"&#xe675;",
            "class" =>"layui-icon-login-weibo"
        ],
        [
            "name" =>"密码",
            "code" =>"&#xe673;",
            "class" =>"layui-icon-password"
        ],
        [
            "name" =>"用户名",
            "code" =>"&#xe66f;",
            "class" =>"layui-icon-username"
        ],
        [
            "name" =>"刷新-粗",
            "code" =>"&#xe9aa;",
            "class" =>"layui-icon-refresh-3"
        ],
        [
            "name" =>"授权",
            "code" =>"&#xe672;",
            "class" =>"layui-icon-auz"
        ],
        [
            "name" =>"左向右伸缩菜单",
            "code" =>"&#xe66b;",
            "class" =>"layui-icon-spread-left"
        ],
        [
            "name" =>"右向左伸缩菜单",
            "code" =>"&#xe668;",
            "class" =>"layui-icon-shrink-right"
        ],
        [
            "name" =>"雪花",
            "code" =>"&#xe6b1;",
            "class" =>"layui-icon-snowflake"
        ],
        [
            "name" =>"提示说明",
            "code" =>"&#xe702;",
            "class" =>"layui-icon-tips"
        ],
        [
            "name" =>"便签",
            "code" =>"&#xe66e;",
            "class" =>"layui-icon-note"
        ],
        [
            "name" =>"主页",
            "code" =>"&#xe68e;",
            "class" =>"layui-icon-home"
        ],
        [
            "name" =>"高级",
            "code" =>"&#xe674;",
            "class" =>"layui-icon-senior"
        ],
        [
            "name" =>"刷新",
            "code" =>"&#xe669;",
            "class" =>"layui-icon-refresh"
        ],
        [
            "name" =>"刷新",
            "code" =>"&#xe666;",
            "class" =>"layui-icon-refresh-1"
        ],
        [
            "name" =>"旗帜",
            "code" =>"&#xe66c;",
            "class" =>"layui-icon-flag"
        ],
        [
            "name" =>"主题",
            "code" =>"&#xe66a;",
            "class" =>"layui-icon-theme"
        ],
        [
            "name" =>"消息-通知",
            "code" =>"&#xe667;",
            "class" =>"layui-icon-notice"
        ],
        [
            "name" =>"网站",
            "code" =>"&#xe7ae;",
            "class" =>"layui-icon-website"
        ],
        [
            "name" =>"控制台",
            "code" =>"&#xe665;",
            "class" =>"layui-icon-console"
        ],
        [
            "name" =>"表情-惊讶",
            "code" =>"&#xe664;",
            "class" =>"layui-icon-face-surprised"
        ],
        [
            "name" =>"设置-空心",
            "code" =>"&#xe716;",
            "class" =>"layui-icon-set"
        ],
        [
            "name" =>"模板",
            "code" =>"&#xe656;",
            "class" =>"layui-icon-template-1"
        ],
        [
            "name" =>"应用",
            "code" =>"&#xe653;",
            "class" =>"layui-icon-app"
        ],
        [
            "name" =>"模板",
            "code" =>"&#xe663;",
            "class" =>"layui-icon-template"
        ],
        [
            "name" =>"赞",
            "code" =>"&#xe6c6;",
            "class" =>"layui-icon-praise"
        ],
        [
            "name" =>"踩",
            "code" =>"&#xe6c5;",
            "class" =>"layui-icon-tread"
        ],
        [
            "name" =>"男",
            "code" =>"&#xe662;",
            "class" =>"layui-icon-male"
        ],
        [
            "name" =>"女",
            "code" =>"&#xe661;",
            "class" =>"layui-icon-female"
        ],
        [
            "name" =>"相机-空心",
            "code" =>"&#xe660;",
            "class" =>"layui-icon-camera"
        ],
        [
            "name" =>"相机-实心",
            "code" =>"&#xe65d;",
            "class" =>"layui-icon-camera-fill"
        ],
        [
            "name" =>"菜单-水平",
            "code" =>"&#xe65f;",
            "class" =>"layui-icon-more"
        ],
        [
            "name" =>"菜单-垂直",
            "code" =>"&#xe671;",
            "class" =>"layui-icon-more-vertical"
        ],
        [
            "name" =>"金额-人民币",
            "code" =>"&#xe65e;",
            "class" =>"layui-icon-rmb"
        ],
        [
            "name" =>"金额-美元",
            "code" =>"&#xe659;",
            "class" =>"layui-icon-dollar"
        ],
        [
            "name" =>"钻石-等级",
            "code" =>"&#xe735;",
            "class" =>"layui-icon-diamond"
        ],
        [
            "name" =>"火",
            "code" =>"&#xe756;",
            "class" =>"layui-icon-fire"
        ],
        [
            "name" =>"返回",
            "code" =>"&#xe65c;",
            "class" =>"layui-icon-return"
        ],
        [
            "name" =>"位置-地图",
            "code" =>"&#xe715;",
            "class" =>"layui-icon-location"
        ],
        [
            "name" =>"办公-阅读",
            "code" =>"&#xe705;",
            "class" =>"layui-icon-read"
        ],
        [
            "name" =>"调查",
            "code" =>"&#xe6b2;",
            "class" =>"layui-icon-survey"
        ],
        [
            "name" =>"表情-微笑",
            "code" =>"&#xe6af;",
            "class" =>"layui-icon-face-smile"
        ],
        [
            "name" =>"表情-哭泣",
            "code" =>"&#xe69c;",
            "class" =>"layui-icon-face-cry"
        ],
        [
            "name" =>"购物车",
            "code" =>"&#xe698;",
            "class" =>"layui-icon-cart-simple"
        ],
        [
            "name" =>"购物车",
            "code" =>"&#xe657;",
            "class" =>"layui-icon-cart"
        ],
        [
            "name" =>"下一页",
            "code" =>"&#xe65b;",
            "class" =>"layui-icon-next"
        ],
        [
            "name" =>"上一页",
            "code" =>"&#xe65a;",
            "class" =>"layui-icon-prev"
        ],
        [
            "name" =>"上传-空心-拖拽",
            "code" =>"&#xe681;",
            "class" =>"layui-icon-upload-drag"
        ],
        [
            "name" =>"上传-实心",
            "code" =>"&#xe67c;",
            "class" =>"layui-icon-upload"
        ],
        [
            "name" =>"下载-圆圈",
            "code" =>"&#xe601;",
            "class" =>"layui-icon-download-circle"
        ],
        [
            "name" =>"组件",
            "code" =>"&#xe857;",
            "class" =>"layui-icon-component"
        ],
        [
            "name" =>"文件-粗",
            "code" =>"&#xe655;",
            "class" =>"layui-icon-file-b"
        ],
        [
            "name" =>"用户",
            "code" =>"&#xe770;",
            "class" =>"layui-icon-user"
        ],
        [
            "name" =>"发现-实心",
            "code" =>"&#xe670;",
            "class" =>"layui-icon-find-fill"
        ],
        [
            "name" =>"loading",
            "code" =>"&#xe63d;",
            "class" =>"layui-icon-loading"
        ],
        [
            "name" =>"loading",
            "code" =>"&#xe63e;",
            "class" =>"layui-icon-loading-1"
        ],
        [
            "name" =>"添加",
            "code" =>"&#xe654;",
            "class" =>"layui-icon-add-1"
        ],
        [
            "name" =>"播放",
            "code" =>"&#xe652;",
            "class" =>"layui-icon-play"
        ],
        [
            "name" =>"暂停",
            "code" =>"&#xe651;",
            "class" =>"layui-icon-pause"
        ],
        [
            "name" =>"音频-耳机",
            "code" =>"&#xe6fc;",
            "class" =>"layui-icon-headset"
        ],
        [
            "name" =>"视频",
            "code" =>"&#xe6ed;",
            "class" =>"layui-icon-video"
        ],
        [
            "name" =>"语音-声音",
            "code" =>"&#xe688;",
            "class" =>"layui-icon-voice"
        ],
        [
            "name" =>"消息-通知-喇叭",
            "code" =>"&#xe645;",
            "class" =>"layui-icon-speaker"
        ],
        [
            "name" =>"删除线",
            "code" =>"&#xe64f;",
            "class" =>"layui-icon-fonts-del"
        ],
        [
            "name" =>"代码",
            "code" =>"&#xe64e;",
            "class" =>"layui-icon-fonts-code"
        ],
        [
            "name" =>"HTML",
            "code" =>"&#xe64b;",
            "class" =>"layui-icon-fonts-html"
        ],
        [
            "name" =>"字体加粗",
            "code" =>"&#xe62b;",
            "class" =>"layui-icon-fonts-strong"
        ],
        [
            "name" =>"删除链接",
            "code" =>"&#xe64d;",
            "class" =>"layui-icon-unlink"
        ],
        [
            "name" =>"图片",
            "code" =>"&#xe64a;",
            "class" =>"layui-icon-picture"
        ],
        [
            "name" =>"链接",
            "code" =>"&#xe64c;",
            "class" =>"layui-icon-link"
        ],
        [
            "name" =>"表情-笑-粗",
            "code" =>"&#xe650;",
            "class" =>"layui-icon-face-smile-b"
        ],
        [
            "name" =>"左对齐",
            "code" =>"&#xe649;",
            "class" =>"layui-icon-align-left"
        ],
        [
            "name" =>"右对齐",
            "code" =>"&#xe648;",
            "class" =>"layui-icon-align-right"
        ],
        [
            "name" =>"居中对齐",
            "code" =>"&#xe647;",
            "class" =>"layui-icon-align-center"
        ],
        [
            "name" =>"字体-下划线",
            "code" =>"&#xe646;",
            "class" =>"layui-icon-fonts-u"
        ],
        [
            "name" =>"字体-斜体",
            "code" =>"&#xe644;",
            "class" =>"layui-icon-fonts-i"
        ],
        [
            "name" =>"Tabs 选项卡",
            "code" =>"&#xe62a;",
            "class" =>"layui-icon-tabs"
        ],
        [
            "name" =>"单选框-选中",
            "code" =>"&#xe643;",
            "class" =>"layui-icon-radio"
        ],
        [
            "name" =>"单选框-候选",
            "code" =>"&#xe63f;",
            "class" =>"layui-icon-circle"
        ],
        [
            "name" =>"编辑",
            "code" =>"&#xe642;",
            "class" =>"layui-icon-edit"
        ],
        [
            "name" =>"分享",
            "code" =>"&#xe641;",
            "class" =>"layui-icon-share"
        ],
        [
            "name" =>"删除",
            "code" =>"&#xe640;",
            "class" =>"layui-icon-delete"
        ],
        [
            "name" =>"表单",
            "code" =>"&#xe63c;",
            "class" =>"layui-icon-form"
        ],
        [
            "name" =>"手机-细体",
            "code" =>"&#xe63b;",
            "class" =>"layui-icon-cellphone-fine"
        ],
        [
            "name" =>"聊天 对话 沟通",
            "code" =>"&#xe63a;",
            "class" =>"layui-icon-dialogue"
        ],
        [
            "name" =>"文字格式化",
            "code" =>"&#xe639;",
            "class" =>"layui-icon-fonts-clear"
        ],
        [
            "name" =>"窗口",
            "code" =>"&#xe638;",
            "class" =>"layui-icon-layer"
        ],
        [
            "name" =>"日期",
            "code" =>"&#xe637;",
            "class" =>"layui-icon-date"
        ],
        [
            "name" =>"水 下雨",
            "code" =>"&#xe636;",
            "class" =>"layui-icon-water"
        ],
        [
            "name" =>"代码-圆圈",
            "code" =>"&#xe635;",
            "class" =>"layui-icon-code-circle"
        ],
        [
            "name" =>"轮播组图",
            "code" =>"&#xe634;",
            "class" =>"layui-icon-carousel"
        ],
        [
            "name" =>"翻页",
            "code" =>"&#xe633;",
            "class" =>"layui-icon-prev-circle"
        ],
        [
            "name" =>"布局",
            "code" =>"&#xe632;",
            "class" =>"layui-icon-layouts"
        ],
        [
            "name" =>"工具",
            "code" =>"&#xe631;",
            "class" =>"layui-icon-util"
        ],
        [
            "name" =>"选择模板",
            "code" =>"&#xe630;",
            "class" =>"layui-icon-templeate-1"
        ],
        [
            "name" =>"上传-圆圈",
            "code" =>"&#xe62f;",
            "class" =>"layui-icon-upload-circle"
        ],
        [
            "name" =>"树",
            "code" =>"&#xe62e;",
            "class" =>"layui-icon-tree"
        ],
        [
            "name" =>"表格",
            "code" =>"&#xe62d;",
            "class" =>"layui-icon-table"
        ],
        [
            "name" =>"图表",
            "code" =>"&#xe62c;",
            "class" =>"layui-icon-chart"
        ],
        [
            "name" =>"图标 报表 屏幕",
            "code" =>"&#xe629;",
            "class" =>"layui-icon-chart-screen"
        ],
        [
            "name" =>"引擎",
            "code" =>"&#xe628;",
            "class" =>"layui-icon-engine"
        ],
        [
            "name" =>"下三角",
            "code" =>"&#xe625;",
            "class" =>"layui-icon-triangle-d"
        ],
        [
            "name" =>"右三角",
            "code" =>"&#xe623;",
            "class" =>"layui-icon-triangle-r"
        ],
        [
            "name" =>"文件",
            "code" =>"&#xe621;",
            "class" =>"layui-icon-file"
        ],
        [
            "name" =>"设置-小型",
            "code" =>"&#xe620;",
            "class" =>"layui-icon-set-sm"
        ],
        [
            "name" =>"减少-圆圈",
            "code" =>"&#xe616;",
            "class" =>"layui-icon-reduce-circle"
        ],
        [
            "name" =>"添加-圆圈",
            "code" =>"&#xe61f;",
            "class" =>"layui-icon-add-circle"
        ],
        [
            "name" =>"404",
            "code" =>"&#xe61c;",
            "class" =>"layui-icon-404"
        ],
        [
            "name" =>"关于",
            "code" =>"&#xe60b;",
            "class" =>"layui-icon-about"
        ],
        [
            "name" =>"箭头 向上",
            "code" =>"&#xe619;",
            "class" =>"layui-icon-up"
        ],
        [
            "name" =>"箭头 向下",
            "code" =>"&#xe61a;",
            "class" =>"layui-icon-down"
        ],
        [
            "name" =>"箭头 向左",
            "code" =>"&#xe603;",
            "class" =>"layui-icon-left"
        ],
        [
            "name" =>"箭头 向右",
            "code" =>"&#xe602;",
            "class" =>"layui-icon-right"
        ],
        [
            "name" =>"圆点",
            "code" =>"&#xe617;",
            "class" =>"layui-icon-circle-dot"
        ],
        [
            "name" =>"搜索",
            "code" =>"&#xe615;",
            "class" =>"layui-icon-search"
        ],
        [
            "name" =>"设置-实心",
            "code" =>"&#xe614;",
            "class" =>"layui-icon-set-fill"
        ],
        [
            "name" =>"群组",
            "code" =>"&#xe613;",
            "class" =>"layui-icon-group"
        ],
        [
            "name" =>"好友",
            "code" =>"&#xe612;",
            "class" =>"layui-icon-friends"
        ],
        [
            "name" =>"回复 评论 实心",
            "code" =>"&#xe611;",
            "class" =>"layui-icon-reply-fill"
        ],
        [
            "name" =>"菜单 隐身 实心",
            "code" =>"&#xe60f;",
            "class" =>"layui-icon-menu-fill"
        ],
        [
            "name" =>"记录",
            "code" =>"&#xe60e;",
            "class" =>"layui-icon-log"
        ],
        [
            "name" =>"图片-细体",
            "code" =>"&#xe60d;",
            "class" =>"layui-icon-picture-fine"
        ],
        [
            "name" =>"表情-笑-细体",
            "code" =>"&#xe60c;",
            "class" =>"layui-icon-face-smile-fine"
        ],
        [
            "name" =>"列表",
            "code" =>"&#xe60a;",
            "class" =>"layui-icon-list"
        ],
        [
            "name" =>"发布 纸飞机",
            "code" =>"&#xe609;",
            "class" =>"layui-icon-release"
        ],
        [
            "name" =>"对 OK",
            "code" =>"&#xe605;",
            "class" =>"layui-icon-ok"
        ],
        [
            "name" =>"帮助",
            "code" =>"&#xe607;",
            "class" =>"layui-icon-help"
        ],
        [
            "name" =>"客服",
            "code" =>"&#xe606;",
            "class" =>"layui-icon-chat"
        ],
        [
            "name" =>"top 置顶",
            "code" =>"&#xe604;",
            "class" =>"layui-icon-top"
        ],
        [
            "name" =>"收藏-空心",
            "code" =>"&#xe600;",
            "class" =>"layui-icon-star"
        ],
        [
            "name" =>"收藏-实心",
            "code" =>"&#xe658;",
            "class" =>"layui-icon-star-fill"
        ],
        [
            "name" =>"关闭-实心",
            "code" =>"&#x1007;",
            "class" =>"layui-icon-close-fill"
        ],
        [
            "name" =>"关闭-空心",
            "code" =>"&#x1006;",
            "class" =>"layui-icon-close"
        ],
        [
            "name" =>"正确",
            "code" =>"&#x1005;",
            "class" =>"layui-icon-ok-circle"
        ],
        [
            "name" =>"添加-圆圈-细体",
            "code" =>"&#xe608;",
            "class" =>"layui-icon-add-circle-fine"
        ]
    ];

    /**
     * The default icon set registered on the controller.
     * 
     * @var array
     */
    public $iconSet = 'layui-icon';

    /**
     * The default icon registered on the controller.
     * 
     * @var array
     */
    public $icon = 'layui-icon-edit';

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'lay-search' => '',
    ];
    
    public function __construct(Form $form, string $name)
    {
        $this->form = $form;
        $this->attributes['name'] = $name;

        $this->attributes['lay-filter'] = 'icon-picker-' . $name;
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->attributes['lay-affix'] = $suffix;

        return $this;
    }

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function iconSet(string $iconSet): static
    {
        $this->iconSet = $iconSet;

        return $this;
    }

    public function option(): IconPickerOption
    {
        $field = new IconPickerOption($this->form, '');

        return tap($field, function ($field) {
            $this->options[] = $field;
        });
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        if(empty($this->options)) {

            $this->option()->value('')->label('');
    
            // the `value` field support "layui-icon layui-icon-moon" and "layui-icon-moon" value
            $value = array_reverse(explode(' ', $this->value));
            $valueIconSet = $value[1] ?? $this->iconSet;
            $valueIcon = $value[0];

            foreach($this->icons as $icon) {
                $isSelect = $this->iconSet == $valueIconSet && $icon['class'] == $valueIcon;
                $this->option()->value($this->iconSet . ' ' . $icon['class'])->label($icon['code'] . '&nbsp;&nbsp;' . $icon['name'])->select($isSelect);
            }
        }

        $options = collect($this->options)
            ->map(function($item) {
                return $item->render();
            })
            ->implode("\n");

        if($this->prefix === '') {
            return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block {$this->iconSet}" style="font-size: inherit; font-style: inherit;">
        <select $attributes>
            {$options}
        </select>
    </div>
</div>

HTML;
        }

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block {$this->iconSet}" style="font-size: inherit; font-style: inherit;">
        <div class="layui-input-wrap">
            <div class="layui-input-prefix">
                <i class="{$this->prefix}"></i>
            </div>
            <select $attributes>
                {$options}
            </select>
        </div>
    </div>
</div>

HTML;
    }

    public function run(): string
    {
        $name = $this->attributes['name'];

        return <<<JAVASCRIPT
layui.form.on('select(icon-picker-{$name})', function(data) {
    // data.elem.parentElement.firstElementChild.firstElementChild.classList = data.value ? 'layui-icon ' + data.value : '{$this->prefix}';
});
JAVASCRIPT;
    }
}
