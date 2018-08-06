<?php

/* @var $this \admin\components\View */

use common\models\Admin;
use common\models\AdminRole;
use common\models\AdminResource;

$this->title = 'Administrator Resources';
$this->addCrumbs('Manager');
$this->addCrumbs(ucfirst(Yii::$app->controller->action->id).' List', $target);
$this->setActiveNavigator($target);

\admin\assets\CheckerAsset::register($this);

?>

<div class="contenter">
    <div class="alert alert-info mt" role="alert">
        <p><strong>Heads up!</strong></p>
        <p>set administrator resource: <i class="fa fa-superpowers fa-fw"></i><?= AdminResource::$typeSelector[$resource->resourceType()] ?> data power <?= $resource->power ?>'s permission.</p>
        <p>1. when selecting a column, all permissions under the current column are included, including other rights that may be extended later.</p>
    </div>
    <form id="info-detail" action="/admin-resource/<?= Yii::$app->controller->action->id ?>?id=<?= $resource->id ?>" method="post">
        <div class="checkbox-group">
            <div class="checkbox w-p100">
                <input type="checkbox" autocomplete="off" checked>
                <label class="btn btn-primary w-p100">administrator role list</label>
            </div>
            <?php
            foreach(AdminRole::identitySelector() as $identity => $title) {
                ?>
                <div class="checkbox">
                    <input class="permission-group" id="gid-<?= $identity ?>" type="checkbox" name="identity[]" value="<?= $identity ?>"<?= in_array($identity, $resource->identities) ? ' checked' : '' ?> autocomplete="off">
                    <label class="btn btn-default" for="gid-<?= $identity ?>" style="text-align:left;"><i class="fa fa-superpowers fa-fw"></i><?= $title ?></label>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="checkbox-group">
            <div class="checkbox w-p100">
                <input type="checkbox" autocomplete="off" checked>
                <label class="btn btn-primary w-p100">administrator list</label>
            </div>
            <?php
            foreach(Admin::find()->select('id, username')->andWhere(['!=', 'id', '0'])->asArray()->all() as $admin) {
                ?>
                <div class="checkbox">
                    <input id="tid-<?= $admin['id'] ?>" type="checkbox" name="identity[]" value="<?= $admin['id'] ?>"<?= in_array($admin['id'], $resource->identities) ? ' checked' : '' ?> autocomplete="off">
                    <label class="btn btn-default" for="tid-<?= $admin['id'] ?>"><i class="fa fa-user fa-fw"></i><?= $admin['username'] ?></label>
                </div>
                <?php
            }
            ?>
        </div>
        <button class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
        <input type="hidden" name="type" value="<?= $resource->resourceType() ?>">
        <input type="hidden" name="power" value="<?= $resource->power ?>">
    </form>
</div>

<script>
    jQuery(document).ready(function() {
        // 点选按钮
        jQuery('.checkbox-group .checkbox input').bind(function() {
            if(jQuery(this).is(':checked')) {
            
            }
        });
    });
</script>