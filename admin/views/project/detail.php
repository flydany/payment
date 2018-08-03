<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Project;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Project';
$this->setActiveNavigator('project/list');

\admin\assets\CheckerAsset::register($this);
\admin\assets\UploaderFileAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
        <p>1. the account will be disabled after the expiration date.</p>
    </div>
    <form id="info-detail" method="post" action="/project/<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>title</label>
            <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="project name.">
        </div>
        <div class="form-row">
            <div class="form-group col-xs-6 checker">
                <label>key</label>
                <input class="form-control" type="text" name="key" value="<?= Render::value($data, 'key') ?>" placeholder="key">
            </div>
            <div class="form-group col-xs-6 checker">
                <label>effect date</label>
                <input class="form-control" type="text" name="effect_date" value="<?= Render::value($data, 'effect_date') ?>" placeholder="effect date.">
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">public key</div>
            <div class="panel-body pb-zero">
                <div class="form-group" id="public-file"></div>
                <div class="form-group checker">
                    <label>key string</label>
                    <textarea class="form-control" name="public_key" placeholder="public key."><?= Render::value($data, 'public_key') ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <div class="form-group checker">
            <label>status</label>
            <?= Render::select('status', Project::$statusSelector, Render::value($data, 'status'), ['class' => 'select-picker']) ?>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Project::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
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

        // 初始化 公钥文件上传插件
        (new loaderFile()).init({
            conter: '#public-file',
            action: '/platform/file-encoder',
            onSuccess: function (file, response) {
                response = $.parseJSON(response);
                $('textarea[name=public_key]').val(response.reader);
            },
            onFailure: function (file, response) {
                response = $.parseJSON(response);
                BootstrapDialog.alert({ type: BootstrapDialog.TYPE_DANGER, message: 'public file load failure: ' + response.message });
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
</script>