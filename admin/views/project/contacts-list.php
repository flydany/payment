<?php

/* @var $this admin\components\View */

use admin\helpers\Render;
use common\models\Project;
use common\models\ProjectContacts;

$this->title = 'Project Contacts List';
$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');

\admin\assets\TablerAsset::register($this);
?>

<div class="contenter">
    <div class="form-inline search clearfix" id="info-search">
        <div class="input-group col-xs-3">
            <span class="input-group-addon">project</span>
            <?= Render::select('project_id', Project::selector(), $this->request->get('id'), ['prompt' => '--', 'class' => 'tabler select-picker', 'data-live-search' => 'true']) ?>
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">identity</span>
            <?= Render::select('identity', ProjectContacts::$identitySelector, null, ['prompt' => '--', 'class' => 'tabler select-picker']) ?>
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">name</span>
            <input type="text" class="form-control tabler" name="name" placeholder="name">
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">mobile</span>
            <input type="text" class="form-control tabler" name="mobile" placeholder="mobile">
        </div>
        <div class="input-group col-xs-1">
            <button class="btn btn-primary" id="search-button"><i class="fa fa-search fa-fw"></i>search</button>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th>project</th>
            <th>identity</th>
            <th>name</th>
            <th>mobile</th>
            <th>email</th>
            <th>updated at</th>
            <th>operation</th>
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
            <a type="button" class="btn btn-default" href="/project/contacts-detail"><i class="fa fa-plus fa-fw"></i>insert</a>
            <button type="button" class="btn btn-default"><i class="fa fa-check-square fa-fw"></i>check all</button>
            <button type="button" class="btn btn-default"><i class="fa fa-minus-square fa-fw"></i>inverse</button>
            <button type="button" class="btn btn-default"><i class="fa fa-trash fa-fw"></i>batch delete</button>
        </div>
        <div class="btn-group render" role="group"></div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        // 批量删除按钮事件
        tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '/project/contacts-list',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 所属权组名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.identity'), select: 'identity' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td><a href="/project/detail?id={{info.project_id}}">{{info.project_id}}/{{info.project.title}}</a></td>
        <td class="identity">{{info.identity}}</td>
        <td>{{info.name}}</td>
        <td>{{info.mobile}}</td>
        <td>{{info.email}}</td>
        <td>{{info.updated_at | dateShow: 'minute'}}</td>
        <td>
            <a class="label label-primary" href="/project/contacts-detail?id={{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <a class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
    {{/each}}
</script>