<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use admin\helpers\Render;
use common\models\Message;

$this->title = '站内信列表';
?>

<div class="box-content gap">
    <div class="search flyer-form pane" id="info-search">
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">收件人</div>
                <div class="input-inline w-80px"><input class="tabler flyer-input" name="receiver_id" placeholder="receiver id."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">站内信标题</div>
                <div class="input-inline w-120px"><input class="tabler flyer-input" name="title" placeholder="title."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">类型</div>
                <div class="input-inline w-100px"><?= Render::select('type', Message::$typeSelector, null, ['flyer' => 'select', 'class' => 'tabler', 'prompt' => '---']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-title">状态</div>
                <div class="input-inline w-80px"><?= Render::select('status', Message::$statusSelector, null, ['flyer' => 'select', 'class' => 'tabler', 'prompt' => '---']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-inline bdn"><button class="flyer-button normal narrow border-round" id="search-button"><i class="icon-search"></i> <span> 查询</span></button></div>
            </div>
        </div>
    </div>
    <div class="flyer-table flyer-form mt-10px">
        <table id="info-table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="first"><i class="icon-list-ol"></i> 标题</th>
                    <th><i class="icon-bookmark"></i> 类型</th>
                    <th><i class="icon-user"></i> 收件人</th>
                    <th><i class="icon-bell-alt"></i> 状态</th>
                    <th><i class="icon-time"></i> 添加时间</th>
                    <th><i class="icon-gear"></i> 操作</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="6"><i class="icon-search"></i> 点击查询按钮查找数据.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page">
        <ul class="handle">
            <li class="prev"><a href="<?= Url::to('@web/message/message-detail') ?>"><i class="icon-plus icon-large"></i> 添加站内信</a></li>
            <li class="select-all" data-table="#info-table"><a><i class="icon-check icon-large"></i> 全选</a></li>
            <li class="reverse-all" data-table="#info-table"><a><i class="icon-check-empty icon-large"></i> 反选</a></li>
            <li class="next delete-mult" data-table="#info-table" data-href="<?= Url::to('@web/message/message-delete') ?>"><a><i class="icon-trash icon-large"></i> 删除选中</a></li>
        </ul>
        <div class="html"></div>
    </div>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tabler.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // 美化FORM
        (new flyer).init({ form: '#info-search' });
        // 批量删除按钮事件
        tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/message/message-list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.type'), select: 'type' });
                tableHandler.renderCategory({ category: $(param.tabler).find('.status'), select: 'status' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{if infos != undefined && infos.length}}
        {{each infos as info key}}
            <tr id="tr-{{info.id}}" data-id="{{info.id}}">
                <td class="first">
                    <input class="list" type="checkbox" name="info[]" value="{{info.id}}">
                    <font class="flyer-status blue thin">{{info.id}}</font> {{info.title}}
                </td>
                <td class="type">{{info.type}}</td>
                <td>{{info.receiver_id}}:{{info.receiver.username}}</td>
                <td class="status">{{info.status}}</td>
                <td>{{info.created_at | dateShow: 'minute'}}</td>
                <td>
                    <a class="flyer-status blue thin" href="<?= Url::to('@web/message/message-detail?id=') ?>{{info.id}}"><i class="icon-edit icon-large" title="修改"></i>修改</a>
                    <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                </td>
            </tr>
        {{/each}}
    {{else}}
        <tr>
            <td class="first" colspan="6"><i class="icon-ban-circle"></i> 搜索程序未为您搜索到任何信息！</td>
        </tr>
    {{/if}}
</script>