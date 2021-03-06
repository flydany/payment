<?php

/* @var $this admin\components\View */

use admin\helpers\Render;
use common\models\Project;
use common\models\Platform;
use common\models\Recharge;

$this->title = 'Recharge Record';
$this->addCrumbs('Recharge');

\admin\assets\TablerAsset::register($this);
\admin\assets\DatetimeAsset::register($this);
?>

<div class="contenter">
    <div class="form-inline search clearfix" id="info-search">
        <div class="input-group col-xs-3">
            <span class="input-group-addon">project</span>
            <?= Render::select('project_id', Project::selector(), null, ['prompt' => '--', 'placeholder' => 'project', 'class' => 'tabler select-picker', 'data-live-search' => 'true']) ?>
        </div>
        <div class="input-group col-xs-3">
            <span class="input-group-addon">project merchant</span>
            <input type="text" class="form-control tabler" name="project_merchant_id" placeholder="project merchant">
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">order</span>
            <input type="text" class="form-control tabler" name="order_number" placeholder="order number">
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">source order</span>
            <input type="text" class="form-control tabler" name="source_order_number" placeholder="source order number">
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">status</span>
            <?= Render::select('status', Recharge::$statusSelector, null, ['prompt' => '--', 'class' => 'tabler select-picker']) ?>
        </div>
        <div class="input-group col-xs-2">
            <span class="input-group-addon">bank</span>
            <?= Render::select('bank_id', Platform::$bankSelector, null, ['prompt' => '--', 'class' => 'tabler select-picker']) ?>
        </div>
        <div class="input-group col-xs-4">
            <span class="input-group-addon">time</span>
            <input type="text" class="form-control tabler datetime-picker" name="star" placeholder="start time">
            <span class="input-group-addon"><i class="fa fa-caret-right fa-fw"></i></span>
            <input type="text" class="form-control tabler datetime-picker" name="end" placeholder="end time">
        </div>
        <div class="input-group col-xs-1"><button class="btn btn-primary" id="search-button"><i class="fa fa-search fa-fw"></i>search</button></div>
    </div>

    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th>id</th>
            <th>project</th>
            <th>merchant</th>
            <th>order</th>
            <th>source</th>
            <th>bank</th>
            <th>amount</th>
            <th>status</th>
            <th>created at</th>
            <th>operation</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="10"><i class="fa fa-search fa-fw"></i>click on the search button to search data.</td>
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
</div>

<script>
    jQuery(document).ready(function() {
        // 初始化date样式
        $('input.datetime-picker').datetimepicker({ format: 'yyyy-mm-dd hh:ii:ss', autoclose: true });
        // 批量删除按钮事件
        tableHandler.requestMulti({ button: '.delete-mult', isKeep: false });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '/recharge/list',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.status'), select: 'status' });
                tableHandler.renderCategory({ category: $(param.tabler).find('.bank'), select: 'bank_id' });
                // 初始化 删除按钮事件
                tableHandler.requestSingle({ button: $(param.tabler).find('.delete-data'), url: $('.delete-mult:first').data('href'), isKeep: false });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{each infos as info key}}
    <tr id="tr-{{info.id}}" data-id="{{info.id}}">
        <td>{{info.id}}</td>
        <td><a href="/project/detail?id={{info.project_id}}">{{info.project.title}}</a></td>
        <td><a href="/project/merchant-detail?id={{info.project_merchant_id}}">{{info.projectMerchant.title}}</a></td>
        <td>{{info.order_number}}</td>
        <td>{{info.source_order_number}}</td>
        <td class="bank">{{info.bank_id}}</td>
        <td>{{info.amount | fmoney}}</td>
        <td class="status">{{info.status}}</td>
        <td>{{info.created_at | dateShow: 'minute'}}</td>
        <td>
            <a class="label label-primary" href="/recharge/detail?id={{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <span class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</span>
        </td>
    </tr>
    {{/each}}
</script>