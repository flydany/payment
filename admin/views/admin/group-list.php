<?php

/* @var $this \admin\components\View */

$this->title = 'Administrator Group List';
$this->addCrumbs('Manager');

\admin\assets\TablerAsset::register($this);

?>

<div class="contenter">
    <div class="form-inline search clearfix" id="info-search">
        <div class="input-group col-xs-3">
            <span class="input-group-addon">identity</span>
            <input type="text" class="form-control tabler" name="identity" placeholder="identity">
        </div>
        <div class="input-group col-xs-3">
            <span class="input-group-addon">title</span>
            <input type="text" class="form-control tabler" name="title" placeholder="title">
        </div>
        <button class="btn btn-primary" id="search-button"><i class="fa fa-search fa-fw"></i>search</button>
    </div>

    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th>identity</th>
            <th>title</th>
            <th>remark</th>
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
            <a type="button" class="btn btn-default" href="/admin/group-detail"><i class="fa fa-plus fa-fw"></i>insert</a>
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
            url: '/admin/group-list',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td>{{info.identity}}</td>
        <td>{{info.title}}</td>
        <td>{{info.remark}}</td>
        <td>
            <a class="label label-primary" href="/admin/group-detail?id={{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <a class="label label-success" href="/admin/group-permissions?id={{info.id}}"><i class="fa fa-superpowers fa-fw"></i>permission</a>
            <a class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
    {{/each}}
</script>
