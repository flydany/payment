<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\helpers\Render;
use common\models\Recharge;
use common\models\RechargeLog;

$this->addCrumbs('充值列表', 'recharge/recharge-list');
$this->title = '充值详情';
?>
<div class="box-content gap">
    <div class="flyer-form pane">
        <div class="form-item">
            <div class="input-title">订单号</div>
            <div class="input-block input-mid"><?= $data->order_number ?></div>
        </div>
        <div class="form-item">
            <div class="input-title">用户</div>
            <div class="input-block input-mid">
                <span class="flyer-status orange thin">uid: <?= $data->user_id ?></span>
                <span class="flyer-status blue thin"><i class="icon-user"></i> username: <?= $data->user->username ?></div></span>
        </div>
        <div class="form-item">
            <div class="input-title">充值方式</div>
            <div class="input-inline input-mid"><?= Recharge::$platformSelector[$data->platform]['title'] ?></div>
        </div>
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">充值金额</div>
                <div class="input-inline input-mid"><?= Render::amount($data->amount) ?> 元</div>
            </div>
            <div class="item-inline">
                <div class="input-title">手续费</div>
                <div class="input-inline input-mid"><?= Render::amount($data->fee) ?> 元</div>
            </div>
        </div>
        <div class="form-item">
            <div class="input-title">申请时间</div>
            <div class="input-block input-mid"><?= $data->success_at ? date('Y-m-d H:i:s', $data->created_at) : '---' ?></div>
        </div>
        <div class="form-item item-text">
            <div class="input-title">备注</div>
            <div class="input-block input-mid pd-10px"><?= Render::value($data, 'remark', '--') ?></div>
        </div>
        <div class="form-item">
            <div class="item-inline">
                <div class="input-title">充值状态</div>
                <div class="input-inline input-mid"><?= Recharge::$statusSelector[$data->status]['title'] ?></div>
            </div>
            <div class="item-inline">
                <div class="input-title">成功时间</div>
                <div class="input-inline input-mid"><?= ($data->status == Recharge::StatusSuccess) ? date('Y-m-d H:i:s', $data->success_at) : '--' ?></div>
            </div>
        </div>
    </div>
    <?php
    if( ! $data->complete()) {
        ?>
    <div class="data-title mt-20px mb-10px">修改充值状态</div>
    <form class="flyer-form pane" id="flyer-create" method="post">
        <div class="form-item item-text">
            <div class="input-title">备注</div>
            <div class="input-block"><textarea class="flyer-textarea" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea></div>
        </div>
        <div class="form-item">
            <div class="input-block tr bdn">
                <?php if($data->status == Recharge::StatusInit) { ?>
                    <button class="flyer-button normal border-round" id="success-button" type="button" data-href="<?= Url::to('@web/recharge/success?id='.$data->id) ?>"><i class="icon-ok"></i> 充值完成</button>
                <?php } ?>
                <button class="flyer-button danger border-round" id="refuse-button" type="button" data-href="<?= Url::to('@web/recharge/refuse?id='.$data->id) ?>"><i class="icon-remove"></i> 拒绝</button>
            </div>
        </div>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
    <?php
        }
        ?>
    <div class="data-title mb-10px">操作日志</div>
    <div class=" flyer-table">
        <table id="info-table" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th class="first"><i class="icon-list-ol"></i> 操作人</th>
                <th><i class="icon-desktop"></i> 事件</th>
                <th><i class="icon-location-arrow"></i> IP</th>
                <th><i class="icon-time"></i> 时间</th>
            </tr>
            </thead>
            <tbody><tr><td class="first" colspan="4"><i class="icon-spinner icon-spin"></i> 加载中.</td></tr></tbody>
        </table>
    </div>
    <div class="flyer-page mt right" id="info-page"><div class="html"></div></div>
    <div style="display:none;"><?= Render::select('handler', RechargeLog::$handlerSelector) ?></div>
</div>

<script src="<?= Render::static('flyer/flyer.class.js') ?>"></script>
<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tabler.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        // 表单美化
        (new flyer).init({ form: '.search' });
        // 提交按钮绑定事件
        $('#success-button, #refuse-button').bind('click', function() {
            var mthis = this;
            layer.confirm('真的要执行此次操作么？', {
                icon: 3,
                title: '操作确认',
                btn: ['确定', '取消']
            }, function(index) {
                $('#flyer-create').attr('action', $(mthis).attr('data-href')).submit();
            });
        });
        // 初始化表格异步加载事件
        (new tabler).init({
            // 请求地址
            url: '<?= Url::to('@web/recharge/recharge-log?id='.$data->id) ?>',
            // 数据渲染配置
            table: '#info-table', page: '#info-page', template: 'info-template', search: '#info-search',
            // 页面加载完毕自动loading
            readyCall: true,
            // param => tabler
            afterPost: function(param) {
                // 充值方式名称显示
                tableHandler.renderCategory({ category: $(param.tabler).find('.handler'), select: 'select[name=handler]' });
            }
        });
    });
</script>
<script id="info-template" type="text/html">
    {{if infos != undefined && infos.length}}
        {{each infos as info key}}
        <tr id="tr-{{info.id}}" data-id="{{info.id}}">
            <td class="first handler">{{info.handler}}</td>
            <td>{{info.remark}}</td>
            <td>{{info.ip}}</td>
            <td>{{info.created_at | dateShow}}</td>
        </tr>
        {{/each}}
    {{else}}
        <tr>
            <td class="first" colspan="4"><i class="icon-ban-circle"></i> 暂时没有任何操作日志!</td>
        </tr>
    {{/if}}
</script>