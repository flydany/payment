<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\project;

$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' project';
$this->setActiveNavigator('project/list');

$this->registerJavascript('@static/flyer/checker.class.js');
?>

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
        <div class="form-group col-md-6 checker">
            <label>effect date</label>
            <input class="form-control" type="text" name="effect_date" value="<?= Render::value($data, 'effect_date') ?>" placeholder="effect date.">
        </div>
        <div class="form-group col-md-6 checker">
            <label>status</label>
            <?= Render::select('status', Project::$statusSelector, Render::value($data, 'status')) ?>
        </div>
    </div>
    <div class="form-group checker">
        <label>remark</label>
        <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
    <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Project::checker() ?></textarea>
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
</form>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>