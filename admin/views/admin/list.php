<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\helpers\Render;
use common\models\AdminRole;

$this->title = 'Manager';
$this->addCrumbs('System');

$this->registerJs('flyer/checker.class.js');
$this->registerJs('flyer/tabler.class.js');
$this->registerJs('flyer/tableHandler.class.js');
?>

<div class="form-inline search" id="info-search">
    <div class="input-group w-180px">
        <span class="input-group-addon">用户名</span>
        <input type="text" class="form-control" placeholder="Username">
    </div>
    <div class="input-group w-180px">
        <span class="input-group-addon">手机号</span>
        <input type="text" class="form-control" placeholder="Mobile">
    </div>
    <div class="input-group w-200px">
        <span class="input-group-addon">权组</span>
        <?= Render::select('role_id', AdminRole::identitySelector(), null, ['prompt' => '--', 'class' => 'tabler']) ?>
    </div>
    <button type="submit" class="btn btn-primary" id="search-button">Submit</button>
</div>


<table class="table table-bordered table-striped" id="info-table">
    <thead>
    <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row">1</th>
        <td>Mark</td>
        <td>Otto</td>
        <td>@mdo</td>
    </tr>
    <tr>
        <th scope="row">2</th>
        <td>Jacob</td>
        <td>Thornton</td>
        <td>@fat</td>
    </tr>
    <tr>
        <th scope="row">3</th>
        <td>Larry</td>
        <td>the Bird</td>
        <td>@twitter</td>
    </tr>
    </tbody>
</table>
<div class="page right" id="info-page">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default"><i class="fa fa-check-square fa-fw"></i>Check All</button>
        <button type="button" class="btn btn-default"><i class="fa fa-minus-square fa-fw"></i>Inverse</button>
        <button type="button" class="btn btn-default"><i class="fa fa-trash fa-fw"></i>Batch Delete</button>
    </div>
    <div class="html"></div>
</div>

<script>
    jQuery(document).ready(function() {
        // 批量删除按钮事件
         tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/admin/list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 所属权组名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.role'), select: 'role_id' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
                // 初始化 权限修改按钮事件
                tableHandler.alertDialog({
                    button: $(param.tabler).find('.edit-permission'),
                    title: '<i class="icon-bell-alt"></i> 设置用户权限', area: ['90%', '90%'],
                    src: '<?= Url::to('@web/admin/admin-permission-edit') ?>'
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
                    {{if info.id != 1}}<input class="list" type="checkbox" name="info[]" value="{{info.id}}">{{/if}}
                    {{info.username}}
                </td>
                <td class="role">{{info.role_id}}</td>
                <td>{{info.realname}}</td>
                <td>{{info.mobile}}</td>
                <td>{{info.end_date}}</td>
                <td>{{info.created_at | dateShow: 'minute'}}</td>
                <td>
                    <a class="flyer-status blue thin" href="<?= Url::to('@web/admin/admin-detail?id=') ?>{{info.id}}"><i class="icon-edit icon-large" title="修改"></i>修改</a>
                    <a class="edit-permission flyer-status green thin" href="javascript:;"><i class="icon-bell-alt icon-large" title="设置权限"></i>设置权限</a>
                    <a class="delete-data flyer-status red thin" href="javascript:;"><i class="icon-trash icon-large" title="删除"></i>删除</a>
                </td>
            </tr>
        {{/each}}
    {{/if}}
</script>