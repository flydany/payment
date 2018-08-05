<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;
use common\models\MerchantBankMaintain;


$this->addCrumbs('Platform');
$this->addCrumbs('Merchant List', 'platform/merchant-list');
$this->addCrumbs('Merchant Bank Maintain List', 'platform/maintain-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Platform';
$this->setActiveNavigator('platform/maintain-list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/platform/maintain-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-row">
            <div class="form-group col-xs-3 checker">
                <label>platform</label>
                <?= Render::select('platform_id', Platform::$platformSelector, Render::value($data, 'platform_id'), ['class' => 'select-picker']) ?>
            </div>
            <div class="form-group col-xs-6 checker">
                <label>merchant number</label>
                <input class="form-control" type="text" name="merchant_number" value="<?= Render::value($data, 'merchant_number') ?>" placeholder="all merchant number disable if empty">
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
            <div class="panel-heading">amount limit(yuan)</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-xs-4 checker">
                    <label>maintain single amount limit</label>
                    <input class="form-control" type="text" name="single_amount" value="<?= bcdiv(Render::value($data, 'single_amount'), 100, 2) ?>" placeholder="single amount">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>maintain per day amount limit</label>
                    <input class="form-control" type="text" name="day_amount" value="<?= bcdiv(Render::value($data, 'day_amount'), 100, 2) ?>" placeholder="day amount">
                </div>
                <div class="form-group col-xs-4 checker">
                    <label>maintain per month amount limit</label>
                    <input class="form-control" type="text" name="month_amount" value="<?= bcdiv(Render::value($data, 'month_amount'), 100, 2) ?>" placeholder="month amount">
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">maintain time</div>
            <div class="panel-body pb-zero form-row">
                <div class="form-group col-xs-6 checker">
                    <label>maintain begin at</label>
                    <div class="input-group">
                        <input class="form-control datetime-picker" data-date-format="yyyy-mm-dd hh:ii:ss" type="text" name="begin_at" value="<?= date('Y-m-d H:i:s', Render::value($data, 'begin_at', time())) ?>" placeholder="begin at">
                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    </div>
                </div>
                <div class="form-group col-xs-6 checker">
                    <label>maintain finish at</label>
                    <div class="input-group">
                        <input class="form-control datetime-picker" data-date-format="yyyy-mm-dd hh:ii:ss" type="text" name="finish_at" value="<?= date('Y-m-d H:i:s', Render::value($data, 'begin_at', strtotime('+1 days'))) ?>" placeholder="finish at">
                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                    </div>
                </div>
            </div>
            <div class="panel-heading default">disable time slot<a class="btn btn-default btn-sm ml-15px" id="insert-timer"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="timers-list"></div>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', MerchantBankMaintain::$statusSelector, Render::value($data, 'status'), ['class' => 'select-picker']) ?>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= MerchantBankMaintain::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<div id="timer-template" style="display:none;">
    <div class="form-group form-row timer">
        <div class="input-group col-xs-11">
            <label class="input-group-addon">start time</label>
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
        if( ! empty($data->times)) {
        foreach(json_decode($data->times, true) as $time) {
        ?>
        insertTimer('<?= $time['start'] ?>', '<?= $time['end'] ?>');
        <?php
        } }
        ?>

        $('#insert-timer').bind('click', function() {
            insertTimer();
        });
        $('#insert-timer').click();
    });
    // 添加时间段
    function insertTimer(start, end)
    {
        var timerWrap = $('#timer-template').html();
        $('#timers-list').append(timerWrap);
        $('#timers-list .timer:last-child input').eq(0).attr('name', 'start[]').val(start);
        $('#timers-list .timer:last-child input').eq(1).attr('name', 'end[]').val(end);
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