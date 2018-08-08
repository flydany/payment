<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use admin\helpers\Render;
use common\models\UserAccountChange;

$this->title = '用户资金流水';
?>

<div class="box-content gap">
    <div class="search flyer-form pane" id="info-search">
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">用户编号</div>
                <div class="input-inline w-80px"><input class="tabler flyer-input" name="user_id" placeholder="user id."></div>
            </div>
            <div class="item-inline">
                <div class="input-title">类型</div>
                <div class="input-inline w-100px"><?= Render::select('type', UserAccountChange::$changeSelector, null, ['prompt' => '全部--', 'flyer' => 'select', 'class' => 'tabler']) ?></div>
            </div>
            <div class="item-inline">
                <div class="input-title">日期</div>
                <div class="date-limit input-inline">
                    <div class="date-start"><input class="tabler flyer-input" name="start" value="" placeholder="2017-01-01."></div>
                    <div class="date-end"><input class="tabler flyer-input" name="end" value="" placeholder="2017-01-02."></div>
                </div>
            </div>
            <div class="item-inline">
                <div class="input-inline bdn"><button class="flyer-button normal narrow border-round" id="search-button"><i class="icon-search"></i> <span>查 询</span></button></div>
            </div>
        </div>
    </div>
    <div class="flyer-table mt-10px">
        <table id="info-table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="first"><i class="icon-user"></i> 用户</th>
                    <th><i class="icon-sitemap"></i> 类型</th>
                    <th><i class="icon-cny"></i> 金额</th>
                    <th><i class="icon-cny"></i> 余额</th>
                    <th><i class="icon-time"></i> 时间</th>
                </tr>
            </thead>
            <tbody class="flyer-form">
                <tr><td colspan="5"><i class="icon-search"></i> 点击查询按钮查找数据.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page"><div class="html"></div></div>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tabler.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        (new flyer).init({ form: '.search' });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/finance/account-change-list') ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search', button: '#search-button',
            // param => tabler
            afterPost: function(param) {
                // 名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.type'), select: 'type' });
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
                    {{info.user_id}}:{{info.user.username}}
                </td>
                <td class="type">{{info.type}}</td>
                <td>{{fmoney(info.amount / 100, 2)}}</td>
                <td>{{fmoney(info.after_change / 100, 2)}}</td>
                <td>{{info.created_at | dateShow}}</td>
            </tr>
        {{/each}}
    {{else}}
        <tr>
            <td class="first" colspan="5"><i class="icon-ban-circle"></i> 搜索程序未为您搜索到任何信息！</td>
        </tr>
    {{/if}}
</script>