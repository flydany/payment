<?php

/* @var $this yii\web\View */

use yii\helpers\ArrayHelper;
use common\models\PermissionGroup;

$this->title = 'Administrator Group Permissions';
$this->addCrumbs('System');
$this->setActiveNavigator('admin/role-list');

$this->registerJavascript('@static/flyer/checker.class.js');
$this->registerJavascript('@static/flyer/tableHandler.class.js');
?>

<div class="alert alert-info mt" role="alert">
    <p><strong>Heads up!</strong></p>
    <p>set administrator：<i class="icon-group"></i> <?= $admin->username ?> 's permission.</p>
    <p>1. when selecting a column, all permissions under the current column are included, including other rights that may be extended later.</p>
    <p>2. after set user's permission group, you also can make individual permission for this user individually.</p>
</div>
<form id="info-detail" action="/admin/permissions?id=<?= $admin->id ?>" method="post">
    <div class="form-group alert alert-danger" role="alert">
        <h3><i class="fa fa-superpowers fa-fw"></i>set permission group</h3>
        <div class="checkbox-group">
            <?php
            foreach(PermissionGroup::identitySelector() as $identity => $title) {
                ?>
                <div class="checkbox">
                    <input id="gid-<?= $identity ?>" type="checkbox" name="permissionGroups[]" value="<?= $identity ?>"<?= in_array($identity, $admin->identities) ? ' checked' : '' ?> autocomplete="off">
                    <label class="btn btn-default" for="gid-<?= $identity ?>" style="text-align:left;"><i class="fa fa-superpowers fa-fw"></i><?= $title ?></label>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    $already = $admin->permissionSelector();
    $navigators = require(Yii::getAlias('@admin/config/navigator.php'));
    foreach($navigators[0] as $id => $part) {
        ?>
        <div class="form-group alert alert-info" role="alert">
            <h3><i class="fa fa-<?= $part['icon_class'] ?> fa-fw"></i><?= $part['title'] ?></h3>
            <?php
            foreach($navigators[$id] as $sid => $navigator) {
                ?>
                <div class="checkbox-group">
                    <div class="checkbox w-p100">
                        <input id="sid-<?= $sid ?>" type="checkbox" name="permissions[]" value="<?= $navigator['controller'] ?>" autocomplete="off">
                        <label class="btn btn-primary w-p100" for="sid-<?= $sid ?>" style="text-align:left;">
                            <i class="fa fa-<?= $navigator['icon_class'] ?> fa-fw"></i><?= $navigator['title'] ?>
                        </label>
                    </div>
                    <?php
                    if( ! empty($navigators[$sid])) {
                        ?>
                        <?php
                        foreach($navigators[$sid] as $tid => $subNavigator) {
                            ?>
                            <div class="checkbox">
                                <input id="tid-<?= $tid ?>" type="checkbox" name="permissions[]" value="<?= $subNavigator['controller'] ?>"<?= in_array($subNavigator['controller'], $already) ? ' checked' : '' ?> autocomplete="off">
                                <label class="btn btn-default" for="tid-<?= $tid ?>">
                                    <i class="fa fa-<?= $subNavigator['icon_class'] ?> fa-fw"></i><?= $subNavigator['title'] ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
    ?>
    <button class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
</form>

<script>
    jQuery(document).ready(function() {
        // 点选按钮
        jQuery('.checkbox-group .checkbox input').bind(function() {
            if(jQuery(this).is(':checked')) {

            }
        });
    });
</script>