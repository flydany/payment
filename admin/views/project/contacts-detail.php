<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Project;
use common\models\ProjectContacts;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->addCrumbs('Project Contacts List', 'project/contacts-list');
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
    <form id="info-detail" method="post" action="/project/contacts-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>project number</label>
            <input class="form-control" type="text" name="project_id" value="<?= Render::value($data, 'project_id') ?>" placeholder="project number">
        </div>
        <div class="form-group checker">
            <label>identity</label>
            <?= Render::select('identity', ProjectContacts::$identitySelector, Render::value($data, 'identity'), ['class' => 'select-picker']) ?>
        </div>
        <div class="form-group checker">
            <label>name</label>
            <input class="form-control" type="text" name="name" value="<?= Render::value($data, 'name') ?>" placeholder="name">
        </div>
        <div class="form-group checker">
            <label>mobile</label>
            <input class="form-control" type="text" name="mobile" value="<?= Render::value($data, 'mobile') ?>" placeholder="mobile">
        </div>
        <div class="form-group checker">
            <label>email</label>
            <input class="form-control" type="text" name="email" value="<?= Render::value($data, 'email') ?>" placeholder="email">
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= ProjectContacts::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>