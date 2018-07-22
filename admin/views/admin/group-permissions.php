<?php

/* @var $this \admin\components\View */

use yii\helpers\ArrayHelper;
use common\models\Navigator;

$this->title = 'Administrator Group Permissions';
$this->addCrumbs('Manager');
$this->addCrumbs('Administrator Group List', 'admin/group-list');
$this->addCrumbs('Update Administrator Group', 'admin/group-detail?id='.$role->id);
$this->setActiveNavigator('admin/group-list');

$this->registerJavascript('@static/flyer/checker.class.js');
$this->registerJavascript('@static/flyer/tableHandler.class.js');
?>

<div class="contenter">
    <div class="alert alert-info mt" role="alert">
        <p><strong>Heads up!</strong></p>
        <p>set administrator group: <i class="fa fa-superpowers fa-fw"></i><?= $role->title ?> 's permission.</p>
        <p>1. when selecting a column, all permissions under the current column are included, including other rights that may be extended later.</p>
    </div>
    <form id="info-detail" action="/admin/group-permissions?id=<?= $role->id ?>" method="post">
        <?php
        $already = $role->permissionSelector;
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