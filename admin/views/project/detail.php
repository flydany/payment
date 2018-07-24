<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Project;
use common\models\ProjectContacts;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Project';
$this->setActiveNavigator('project/list');

\admin\assets\CheckerAsset::register($this);
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
            <div class="form-group col-md-6 checker">
                <label>effect date</label>
                <input class="form-control" type="text" name="effect_date" value="<?= Render::value($data, 'effect_date') ?>" placeholder="effect date.">
            </div>
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <?= Render::select('status', Project::$statusSelector, Render::value($data, 'status'), ['class' => 'picker']) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>public key</label>
            <textarea class="form-control" name="public_key" placeholder="public key."><?= Render::value($data, 'public_key') ?></textarea>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
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
    });
</script>