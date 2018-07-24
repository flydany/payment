<?php

/* @var $this admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;

$this->title = 'Merchant Bank List';
$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');

\admin\assets\TablerAsset::register($this);

?>

<div class="contenter">
    <div class="form-inline search clearfix" id="info-search">
        <div class="input-group col-md-2">
            <span class="input-group-addon"><i class="fa fa-thumb-tack fa-fw"></i></span>
            <?= Render::select('platform_id', Platform::$platformSelector, null, ['prompt' => '--', 'class' => 'tabler picker']) ?>
        </div>
        <div class="input-group col-md-2">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
            <input type="text" class="form-control tabler" name="merchant_number" placeholder="merchant number">
        </div>
        <div class="input-group col-md-2">
            <span class="input-group-addon"><i class="fa fa-recycle fa-fw"></i></span>
            <?= Render::select('paytype', Platform::$paytypeSelector, null, ['prompt' => '--', 'class' => 'tabler picker']) ?>
        </div>
        <div class="input-group col-md-2">
            <span class="input-group-addon"><i class="fa fa-book fa-fw"></i></span>
            <?= Render::select('bank_id', Platform::$bankSelector, null, ['prompt' => '--', 'class' => 'tabler picker']) ?>
        </div>
        <div class="input-group col-md-2">
            <span class="input-group-addon"><i class="fa fa-check fa-fw"></i></span>
            <?= Render::select('status', Merchant::$statusSelector, null, ['prompt' => '--', 'class' => 'tabler picker']) ?>
        </div>
        <div class="input-group col-md-1"><button class="btn btn-primary" id="search-button"><i class="fa fa-search fa-fw"></i>search</button></div>
    </div>

    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th><i class="fa fa-list fa-fw"></i></th>
            <th><i class="fa fa-book fa-fw"></i>title</th>
            <th><i class="fa fa-thumb-tack fa-fw"></i>platform</th>
            <th><i class="fa fa-shopping-bag fa-fw"></i>merchant</th>
            <th><i class="fa fa-recycle fa-fw"></i>payment</th>
            <th><i class="fa fa-check fa-fw"></i>status</th>
            <th><i class="fa fa-clock-o fa-fw"></i>updated at</th>
            <th><i class="fa fa-gear fa-fw"></i>operation</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="8"><i class="fa fa-search fa-fw"></i>click on the search button to search data.</td>
        </tr>
        </tbody>
    </table>
    <div class="btn-toolbar" id="info-page">
        <div class="btn-group" role="group">
            <a type="button" class="btn btn-default" href="/platform/bank-detail"><i class="fa fa-plus fa-fw"></i>insert</a>
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
            url: '/platform/bank-list',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // 全选、反选按钮、页面加载完毕自动loading
            selectButton: '.select-all', reverseButton: '.reverse-all', readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.platform'), select: 'platform_id' });
                tableHandler.renderCategory({ category: $(param.tabler).find('.paytype'), select: 'paytype' });
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
        <td>{{info.id}}</td>
        <td>{{info.title}}</td>
        <td class="platform">{{info.platform_id}}</td>
        <td>{{info.merchant_number}}</td>
        <td class="paytype">{{info.paytype}}</td>
        <td class="status">{{info.status}}</td>
        <td>{{info.updated_at | dateShow: 'minute'}}</td>
        <td>
            <a class="label label-primary" href="/platform/bank-detail?id={{info.id}}"><i class="fa fa-edit fa-fw"></i>edit</a>
            <a class="delete-data label label-danger" href="javascript:;"><i class="fa fa-trash fa-fw"></i>delete</a>
        </td>
    </tr>
    {{/each}}
</script>