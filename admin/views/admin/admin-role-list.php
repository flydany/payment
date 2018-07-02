<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;

$this->title = '权组管理';
?>

<div class="box-content gap">
    <div class="search bb flyer-form pane" id="info-search">
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">名称</div>
                <div class="input-inline w-120px"><input class="tabler flyer-input" name="title" placeholder="name."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">标识</div>
                <div class="input-inline w-120px"><input class="tabler flyer-input" name="identity" placeholder="identity."></div>
            </div>
            <div class="item-inline">
                <div class="input-inline bdn"><button class="flyer-button normal narrow border-round" id="search-button"><i class="icon-search"></i> <span>查 询</span></button></div>
            </div>
        </div>
    </div>
    <div class="flyer-table flyer-form mt-10px">
        <table id="info-table" cellspacing="0" cellpadding="0">
            <thead>
                <tr data-id="0">
                    <th class="first"><i class="icon-leaf"></i> 标识</th>
                    <th class="w-80px"><i class="icon-flag"></i> 名称</th>
                    <th><i class="icon-sitemap"></i> 描述</th>
                    <th><i class="icon-gear"></i> 操作</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="first" colspan="4"><i class="icon-search"></i> 点击查询按钮查找数据.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page">
        <ul class="handle">
            <li class="prev add-data"><a href="<?= Url::to('@web/admin/admin-role-detail') ?>"><i class="icon-plus icon-large"></i> 添加权组</a></li>
            <li class="select-all" data-table="#info-table"><a><i class="icon-check icon-large"></i> 全选</a></li>
            <li class="reverse-all" data-table="#info-table"><a><i class="icon-check-empty icon-large"></i> 反选</a></li>
            <li class="next delete-mult" data-table="#info-table" data-href="<?= Url::to('@web/admin/admin-role-delete') ?>"><a><i class="icon-trash icon-large"></i> 删除选中</a></li>
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
        (new flyer).init({ form: '#info-search' });
        // 批量删除按钮事件
        tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            url: '<?= Url::to('@web/admin/admin-role-list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('tbody .delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
                // 初始化 保存按钮事件
                tableHandler.saveLine({ button: $(param.tabler).find('tbody .save-data') });
                // 初始化 权限修改按钮事件
                tableHandler.alertDialog({
                    button: $(param.tabler).find('tbody .edit-permission'),
                    title: '<i class="icon-bell-alt"></i> 设置权组权限', area: ['90%', '90%'],
                    src: '<?= Url::to('@web/admin/admin-role-permission-edit') ?>'
                });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{if infos != undefined && infos.length}}
        {{each infos as info key}}
            <tr id="tr-{{info.id}}" data-id="{{info.id}}">
                <td class="first">
                    {{if info.id != 1}}
                    <input class="list" type="checkbox" name="info[]" value="{{info.id}}">
                    {{/if}}
                    {{info.identity}}
                </td>
                <td>{{info.title}}</td>
                <td>{{info.remark}}</td>
                <td>
                    {{if info.id != 1}}
                    <a class="cl-white flyer-status blue thin" href="<?= Url::to('@web/admin/admin-role-detail?id={{info.id}}') ?>"><i class="icon-edit icon-large" title="修改"></i>修改</a>
                    <a class="edit-permission cl-white flyer-status green thin" href="javascript:;"><i class="icon-bell-alt icon-large" title="设置权限"></i>设置权限</a>
                    <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                    {{else}}
                    <font class="cl-red"><i class="icon-ban-circle"></i> 系统预留</font>
                    {{/if}}
                </td>
            </tr>
        {{/each}}
    {{else}}
        <tr>
            <td class="first" colspan="4"><i class="icon-ban-circle"></i> 搜索程序未为您搜索到任何信息！</td>
        </tr>
    {{/if}}
</script>
