<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Platform;
use common\models\Project;
use common\models\Merchant;
use common\models\ProjectMerchant;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->addCrumbs('Project Merchant List', 'project/merchant-list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' Project Merchant';
$this->setActiveNavigator('project/merchant-list');

\admin\assets\CheckerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p><?= $this->modifyNotice(Render::value($data, 'id')) ?></p>
    </div>
    <form id="info-detail" method="post" action="/project/merchant-<?= isset($data['id']) ? 'update?id='.$data['id'] : 'insert' ?>">
        <div class="form-group checker">
            <label>title</label>
            <input class="form-control" type="text" name="title" value="<?= Render::value($data, 'title') ?>" placeholder="project name.">
        </div>
        <div class="form-row">
            <div class="form-group col-xs-6 checker">
                <label>project</label>
                <?= Render::select('project_id', Project::selector(), Render::value($data, 'project_id'), ['class' => 'select-picker', 'data-live-search' => 'true']) ?>
            </div>
            <div class="form-group col-xs-6 checker">
                <label>merchant</label>
                <?= Render::select('merchant_id', Merchant::selector(), Render::value($data, 'merchant_id'), ['class' => 'select-picker', 'data-live-search' => 'true']) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-xs-6 checker">
                <label>merchant</label>
                <?= Render::select('paytype', Platform::$paytypeSelector, Render::value($data, 'paytype'), ['class' => 'select-picker']) ?>
            </div>
            <div class="form-group col-xs-6 checker">
                <label>status</label>
                <?= Render::select('status', ProjectMerchant::$statusSelector, Render::value($data, 'status'), ['class' => 'select-picker']) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="remark" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= ProjectMerchant::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<script>
    $(document).ready(function() {
        jQuery('select[name=project_id], select[name=merchant_id]').selectpicker();

        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
    });
</script>