<?php

/* @var $this \admin\components\View */

use admin\helpers\Render;
use common\models\Platform;
use common\models\Merchant;
use common\models\MerchantBank;


$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');
$this->addCrumbs('Merchant Bank List', 'platform/bank-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Platform';
$this->setActiveNavigator('platform/bank-list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/platform/bank-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-row">
            <div class="form-group col-xs-3 checker">
                <label>platform</label>
                <?= Render::select('platform_id', Platform::$platformSelector, Render::value($data, 'platform_id'), ['class' => 'select-picker']) ?>
            </div>
            <div class="form-group col-xs-6 checker">
                <label>merchant number</label>
                <input class="form-control" type="text" name="merchant_number" value="<?= Render::value($data, 'merchant_number') ?>" placeholder="all merchant number usable if empty">
            </div>
            <div class="form-group col-xs-3 checker">
                <label>payment type</label>
                <?= Render::select('paytype', Platform::$paytypeSelector, Render::value($data, 'paytype'), ['class' => 'select-picker']) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>bank</label>
            <?= Render::select('bank_id', Platform::$bankSelector, Render::value($data, 'bank_id'), ['prompt' => '--', 'class' => 'select-picker']) ?>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">bank priority</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-xs-4 checker">
                    <label>weekday priority</label>
                    <input class="form-control" type="text" name="priority" value="<?= Render::value($data, 'priority', 5) ?>" placeholder="weekday priority">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>weekend priority</label>
                    <input class="form-control" type="text" name="weekend_priority" value="<?= Render::value($data, 'weekend_priority', 5) ?>" placeholder="weekend priority">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>priority threshold(%)</label>
                    <input class="form-control" type="text" name="priority_threshold" value="<?= Render::value($data, 'priority_threshold', 100) ?>" placeholder="priority threshold">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">amount limit(yuan)</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-xs-4 checker">
                    <label>single amount limit</label>
                    <input class="form-control" type="text" name="single_amount" value="<?= bcdiv(Render::value($data, 'single_amount'), 100, 2) ?>" placeholder="single amount">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>per day amount limit</label>
                    <input class="form-control" type="text" name="day_amount" value="<?= bcdiv(Render::value($data, 'day_amount'), 100, 2) ?>" placeholder="day amount">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>per month amount limit</label>
                    <input class="form-control" type="text" name="month_amount" value="<?= bcdiv(Render::value($data, 'month_amount'), 100, 2) ?>" placeholder="month amount">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">count limit</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-xs-6 checker">
                    <label>per day count limit</label>
                    <input class="form-control" type="text" name="day_count" value="<?= Render::value($data, 'single_limit', 3) ?>" placeholder="single limit">
                </div>
                <div class="form-group col-xs-6 checker">
                    <label>per month count limit</label>
                    <input class="form-control" type="text" name="month_count" value="<?= Render::value($data, 'month_limit', 90) ?>" placeholder="month limit">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">weekday usable time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="weekday"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="weekday-timers-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">weekend usable time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="weekend"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="weekend-timers-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">holiday usable time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="holiday"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="holiday-timers-list"></div>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', MerchantBank::$statusSelector, Render::value($data, 'status'), ['class' => 'select-picker']) ?>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= MerchantBank::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<div id="timer-template" style="display:none;">
    <div class="form-group form-row timer">
        <div class="input-group col-xs-11">
            <label class="input-group-addon">time slot</label>
            <input class="form-control" type="text" placeholder="start time">
            <span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
            <span class="input-group-addon"><i class="fa fa-caret-right fa-fw"></i></span>
            <input class="form-control" type="text" placeholder="end time">
            <span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
        </div>
        <div class="col-xs-1"><a class="label label-danger" onclick="deleteTimer(this);" href="javascript:;"><i class="fa fa-close fa-fw"></i>delete</a></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });

        <?php
        foreach(['weekday', 'weekend', 'holiday'] as $type) {
        $key = $type.'_times';
        if( ! empty($data->{$key})) {
        foreach(json_decode($data->{$key}, true) as $time) {
        ?>
        insertTimer('<?= $type ?>', '<?= $time['start'] ?>', '<?= $time['end'] ?>');
        <?php
        } } }
        ?>

        $('.insert-timer').bind('click', function() {
            insertTimer($(this).data('type'));
        });
        $('.insert-timer').click();
    });
    // 添加时间段
    function insertTimer(type, start, end)
    {
        var timerWrap = $('#timer-template').html();
        $('#' + type + '-timers-list').append(timerWrap);
        $('#' + type + '-timers-list .timer:last-child input').eq(0).attr('name', type + '_start[]').val(start);
        $('#' + type + '-timers-list .timer:last-child input').eq(1).attr('name', type + '_end[]').val(end);
    }
    // 删除时间段
    function deleteTimer(mthis)
    {
        if ($(mthis).parents('.timer').siblings().length < 1) {
            return;
        }
        $(mthis).parents('.timer').remove();
    }
</script>