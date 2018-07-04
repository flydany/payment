<?php

/* @var $this admin\components\View */

use yii\helpers\Url;
use common\helpers\Render;
use common\models\AdminRole;

$this->title = 'Administrator List';
$this->addCrumbs('System');

$this->registerJavascript('@static/flyer/checker.class.js');
$this->registerJavascript('@static/flyer/tabler.class.js');
$this->registerJavascript('@static/flyer/tableHandler.class.js');
?>

<div class="form-inline search" id="info-search">
    <div class="input-group w-250px">
        <span class="input-group-addon"><i class="fa fa-user fa-fw"></i>username</span>
        <input type="text" class="form-control tabler" name="username" placeholder="username">
    </div>
    <div class="input-group w-250px">
        <span class="input-group-addon"><i class="fa fa-phone-square fa-fw"></i>mobile</span>
        <input type="text" class="form-control tabler" name="mobile" placeholder="mobile">
    </div>
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-superpowers fa-fw"></i>power group</span>
        <?= Render::select('role_id', AdminRole::identitySelector(), null, ['prompt' => '--', 'class' => 'tabler']) ?>
    </div>
    <button type="submit" class="btn btn-primary" id="search-button">Submit</button>
</div>

<table class="table table-bordered table-striped" id="info-table">
    <thead>
    <tr>
        <th><i class="fa fa-user fa-fw"></i>username</th>
        <th><i class="fa fa-id-card fa-fw"></i>realname</th>
        <th><i class="fa fa-superpowers fa-fw"></i>power group</th>
        <th><i class="fa fa-phone-square fa-fw"></i>mobile</th>
        <th><i class="fa fa-calendar-times-o fa-fw"></i>effect date</th>
        <th><i class="fa fa-clock-o fa-fw"></i>created at</th>
        <th><i class="fa fa-gear fa-fw"></i>operation</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="7"><i class="fa fa-search fa-fw"></i>click on the search button to search data.</td>
    </tr>
    </tbody>
</table>
<div class="btn-toolbar" id="info-page">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default"><i class="fa fa-check-square fa-fw"></i>check all</button>
        <button type="button" class="btn btn-default"><i class="fa fa-minus-square fa-fw"></i>inverse</button>
        <button type="button" class="btn btn-default"><i class="fa fa-trash fa-fw"></i>batch delete</button>
    </div>
    <div class="btn-group render" role="group"></div>
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
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td>{{info.username}}</td>
        <td>{{info.realname}}</td>
        <td class="role">{{info.role_id}}</td>
        <td>{{info.mobile}}</td>
        <td>{{info.end_date}}</td>
        <td>{{info.created_at | dateShow: 'minute'}}</td>
        <td>
            <a class="label label-primary" href="<?= Url::to('@web/admin/detail?id=') ?>{{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <a class="edit-permission label label-primary" href="javascript:;"><i class="fa fa-superpowers fa-fw"></i>permission</a>
            <a class="delete-data label label-primary" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
    {{/each}}
</script>