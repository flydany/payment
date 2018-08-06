<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Project;
use common\models\ProjectApi;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->addCrumbs('Project Api List', 'project/api-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Project Api';
$this->setActiveNavigator('project/api-list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/project/api-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>title</label>
            <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="project api title">
        </div>
        <div class="form-group checker">
            <label>project</label>
            <?= Render::select('project_id', Project::selector(), $this->request->get('id'), ['class' => 'tabler select-picker', 'data-live-search' => 'true']) ?>
        </div>
        <div class="form-group checker">
            <label>api</label>
            <?= Render::select('api', ProjectApi::$apiSelector, Render::value($data, 'api'), ['class' => 'select-picker', 'id' => 'api']) ?>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">api parameters<a class="btn btn-default btn-sm ml-15px" id="insert-parameter"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new parameter</span></div>
            <div class="panel-body pb-zero" id="parameters-list"></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading default">usable time slot<a class="btn btn-default btn-sm ml-15px" id="insert-timer"><i class="fa fa-plus fa-fw"></i>insert</a><span class="text-danger"><i class="fa fa-long-arrow-left fa-fw ml-15px"></i>click this button to add a new timer</span></div>
            <div class="panel-body pb-zero" id="timers-list"></div>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', ProjectApi::$statusSelector, Render::value($data, 'status'), ['class' => 'select-picker']) ?>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark"><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= ProjectApi::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<div id="parameter-template" style="display:none;">
    <div class="form-group form-row parameter">
        <div class="input-group col-xs-3 checker">
            <label class="input-group-addon">name</label>
            <input class="form-control" type="text" name="parameter_name[]" value="" placeholder="parameter name">
        </div>
        <div class="input-group col-xs-8 checker">
            <span class="input-group-addon">value</span>
            <input class="form-control" type="text" name="parameter_value[]" value="" placeholder="parameter value">
        </div>
        <div class="col-xs-1"><a class="label label-danger" onclick="deleteParameter(this);" href="javascript:;"><i class="fa fa-close fa-fw"></i>delete</a></div>
    </div>
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

        <?php
        if( ! empty($data->parameters)) {
        foreach(json_decode($data->parameters, true) as $name => $value) {
        ?>
        insertParameter('<?= $name ?>', '<?= $value ?>');
        <?php
        } }
        ?>
        $('#insert-parameter').bind('click', function() {
            insertParameter();
        });
        $('#insert-parameter').click();

        $('#api').bind('change', function() {
            var sparameters = {
                '<?= ProjectApi::ApiRecharge ?>': [],
                '<?= ProjectApi::ApiWithdraw ?>': [['single_amount', 1000], ['day_amount', 1000000], ['day_count', 1]],
                '<?= ProjectApi::ApiAgreement ?>': []
            };

            var api = $(this).val();
            for(var i = 0; i < sparameters[api].length; ++i) {
                var has = false;
                $.each($('#parameters-list').find("input[name='parameter_name[]']"), function() {
                    if($(this).val() == '' && $(this).parents('.parameter').find('input').eq(1).val() == '') {
                        $(this).val(sparameters[api][i][0]);
                        $(this).parents('.parameter').find('input').eq(1).val(sparameters[api][i][1]);
                        has = true;
                        return false;
                    }
                    else if($(this).val() == sparameters[api][i][0]) {
                        has = true;
                        return false;
                    }
                });
                if(has == false) {
                    insertParameter(sparameters[api][i][0], sparameters[api][i][1]);
                }
            }
        });
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
    
    // 添加参数对
    function insertParameter(name, value)
    {
        var parameterWrap = $('#parameter-template .parameter').clone(true);
        if(name) {
            $(parameterWrap).find('input').eq(0).val(name);
            $(parameterWrap).find('input').eq(1).val(value);
        }
        $('#parameters-list').append(parameterWrap);
    }
    // 删除参数对
    function deleteParameter(mthis)
    {
        if ($(mthis).parents('.parameter').siblings().length < 1) {
            return;
        }
        $(mthis).parents('.parameter').remove();
    }
</script>