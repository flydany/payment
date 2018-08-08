<?php

/* @var $this admin\components\View */

use admin\helpers\Render;
use common\models\Project;
use common\models\ProjectApi;

$this->title = 'Project Api List';
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
            <span class="input-group-addon">api</span>
            <?= Render::select('api', ProjectApi::$apiSelector, null, ['prompt' => '--', 'class' => 'tabler select-picker']) ?>
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">status</span>
            <?= Render::select('status', ProjectApi::$statusSelector, null, ['prompt' => '--', 'class' => 'tabler select-picker']) ?>
        </div>
        <div class="input-group col-xs-1">
            <button class="btn btn-primary" id="search-button"><i class="fa fa-search fa-fw"></i>search</button>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th>title</th>
            <th>project</th>
            <th>api</th>
            <th>status</th>
            <th>updated at</th>
            <th>operation</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6"><i class="fa fa-search fa-fw"></i>click on the search button to search data.</td>
        </tr>
        </tbody>
    </table>
    <div class="btn-toolbar" id="info-page">
        <div class="btn-group" role="group">
            <a type="button" class="btn btn-default" href="/project/api-detail"><i class="fa fa-plus fa-fw"></i>insert</a>
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
            url: '/project/api-list',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.api'), select: 'api' });
                tableHandler.renderCategory({ category: $(param.tabler).find('.status'), select: 'status' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td>{{info.title}}</td>
        <td><a href="/project/detail?id={{info.project_id}}">{{info.project_id}}/{{info.project.title}}</a></td>
        <td class="api">{{info.api}}</td>
        <td class="status">{{info.status}}</td>
        <td>{{info.updated_at | dateShow: 'minute'}}</td>
        <td>
            <a class="label label-primary" href="/project/api-detail?id={{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <a class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
    {{/each}}
</script>