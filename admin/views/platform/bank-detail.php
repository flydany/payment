<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;
use common\models\MerchantBank;


$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');
$this->addCrumbs('Merchant List', 'platform/bank-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Platform';
$this->setActiveNavigator('platform/bank-list');

\admin\assets\CheckerAsset::register($this);

$this->registerCss('
    #timers-list .input-group {
        margin-bottom:15px;
    }
');
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/platform/bank-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-row">
            <div class="form-group col-md-3 checker">
                <label>platform</label>
                <?= Render::select('platform_id', Platform::$platformSelector, Render::value($data, 'platform_id'), ['class' => 'picker']) ?>
            </div>
            <div class="form-group col-md-6 checker">
                <label>merchant number</label>
                <input class="form-control" type="text" name="merchant_number" value="<?= Render::value($data, 'merchant_number') ?>" placeholder="merchant number">
            </div>
            <div class="form-group col-md-3 checker">
                <label>payment type</label>
                <?= Render::select('paytype', Platform::$paytypeSelector, Render::value($data, 'paytype'), ['class' => 'picker']) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>bank</label>
            <?= Render::select('bank_id', Platform::$bankSelector, Render::value($data, 'bank_id'), ['prompt' => '--', 'class' => 'picker']) ?>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">amount limit</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-md-4 checker">
                    <label>single amount limit</label>
                    <input class="form-control" type="text" name="single_limit" value="<?= Render::value($data, 'single_limit') ?>" placeholder="single limit">
                </div>
                <div class="form-group col-md-4 checker">
                    <label>per day amount limit</label>
                    <input class="form-control" type="text" name="day_limit" value="<?= Render::value($data, 'day_limit') ?>" placeholder="day limit">
                </div>
                <div class="form-group col-md-4 checker">
                    <label>per month amount limit</label>
                    <input class="form-control" type="text" name="month_limit" value="<?= Render::value($data, 'month_limit') ?>" placeholder="month limit">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">count limit</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-md-4 checker">
                    <label>per day count limit</label>
                    <input class="form-control" type="text" name="single_count_limit" value="<?= Render::value($data, 'single_limit') ?>" placeholder="single limit">
                </div>
                <div class="form-group col-md-4 checker">
                    <label>per month count limit</label>
                    <input class="form-control" type="text" name="month_count_limit" value="<?= Render::value($data, 'month_limit') ?>" placeholder="month limit">
                </div>
                <div class="form-group col-md-4 checker">
                    <label>threshold percent limit(%)</label>
                    <input class="form-control" type="text" name="threshold_limit" value="<?= Render::value($data, 'threshold_limit') ?>" placeholder="threshold limit">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">weekday time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="weekday"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="weekday-timers-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">weekend time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="weekend"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="weekend-timers-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">holiday time limit<a class="btn btn-default btn-sm ml-15px insert-timer" data-type="holiday"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="holiday-timers-list"></div>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', MerchantBank::$statusSelector, Render::value($data, 'status'), ['class' => 'picker']) ?>
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
        <div class="input-group col-md-11">
            <label class="input-group-addon">time slot</label>
            <input class="form-control" type="text" value="" placeholder="start time">
            <span class="input-group-addon"><i class="fa fa-caret-right fa-fw"></i></span>
            <input class="form-control" type="text" value="" placeholder="end time">
        </div>
        <div class="col-md-1"><a class="label label-danger" onclick="deleteTimer(this);" href="javascript:;"><i class="fa fa-close fa-fw"></i>delete</a></div>
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
        if(name) {
            $(timerWrap).find('input').eq(0).attr('name', type + '_start[]').val(start);
            $(timerWrap).find('input').eq(1).attr('name', type + '_end[]').val(end);
        }
        $('#' + type + '-timers-list').append(timerWrap);
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