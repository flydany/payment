<?php

/* @var $this yii\web\View */

use yii\helpers\ArrayHelper;
use common\models\Navigator;

$this->title = 'Administrator Group Permissions';
$this->addCrumbs('System');

$this->registerJavascript('@static/flyer/checker.class.js');
$this->registerJavascript('@static/flyer/tableHandler.class.js');

$this->registerCss('
    .checkbox-margin label {
        margin:5px 0;
    }
');
?>

<div class="container-fluid">
    <div class="alert alert-info mt" role="alert">
        <p><strong>Heads up!</strong></p>
        <p>set adminstrator group：<i class="icon-group"></i> <?= $role->title ?> 's permission.</p>
        <p>1. when selecting a column, all permissions under the current column are included, including other rights that may be extended later.</p>
    </div>
    <form id="info-detail">
        <?php
        $navigators = require(Yii::getAlias('@admin/config/navigator.php'));
        foreach($navigators[0] as $id => $part) {
            ?>
            <div class="form-group alert alert-info" role="alert">
                <h3><i class="fa fa-<?= $part['icon_class'] ?> fa-fw"></i><?= $part['title'] ?></h3>
                <?php
                foreach($navigators[$id] as $sid => $navigator) {
                    ?>
                    <div class="checkbox-margin" id="sid-<?= $sid ?>" data-toggle="buttons">
                        <label class="btn btn-primary col-lg-12 text-left">
                            <i class="fa fa-<?= $navigator['icon_class'] ?> fa-fw"></i><input type="checkbox" autocomplete="off"> <?= $navigator['title'] ?>
                        </label>
                        <?php
                        if( ! empty($navigators[$sid])) {
                            ?>
                            <?php
                            foreach($navigators[$sid] as $tid => $subNavigator) {
                                ?>
                                <label class="btn btn-default active">
                                    <i class="fa fa-<?= $subNavigator['icon_class'] ?> fa-fw"></i><input type="checkbox" checked autocomplete="off"><?= $subNavigator['title'] ?>
                                </label>
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
    </form>
</div>

<script>
    $(document).ready(function() {
        // init form
        (new flyer).init({ form: '#flyer-create' });

        // 点选按钮
        $('.flyer-choise.parent input[type=checkbox]').bind('click', function() {
            if ($(this).is(':checked')) {
                $('.permission-role-' + $(this).val() + ' input[type=checkbox]:not(:checked)').click();
            }
            else {
                $('.permission-role-' + $(this).val() + ' input[type=checkbox]:checked').click();
            }
        });

        // 初始用户已存在权限
        tableHandler.requestSingle({
            isConfirm: false, isAlert: false,
            url: '/admin/permission-detail',
            beforeRequest: function() {
                $('#save-button').attr('data-layer-index', layer.load(0, {shade: [0.3, '#000']}));
            },
            beforePost: function(param) {
                param.data = {id: '<?= $role->identity ?>'};
            },
            requestSuccess: function (param) {
                layer.close($('#save-button').attr('data-layer-index'));
                $('#permission-detail input[type=checkbox]:disabled').attr('disabled', false);
                if ('super' in param.response.infos) {
                    fill_checkbox('#permission-detail input[type=checkbox]');
                }
                else {
                    for (var permission in param.response.infos) {
                        if ('super' in param.response.infos[permission]) {
                            fill_checkbox('#permission-detail-' + permission);
                            fill_checkbox('.permission-role-' + permission + ' input[type=checkbox]');
                        }
                        else {
                            for (var detail in param.response.infos[permission]) {
                                fill_checkbox('#permission-detail-' + detail);
                            }
                        }
                    }
                }
            }
        });

        // 保存操作
        tableHandler.requestSingle({
            button: '#save-button', isConfirm: false,
            url: '/admin/role-permission-edit?id='.<?= $role->id ?>,
            beforeRequest: function() {
                $('#save-button').attr('data-layer-index', layer.load(0, {shade: [0.3, '#000']}));
                $('#save-button').attr('disabled', true).find('span').text('数据提交中.');
            },
            beforePost: function(param) {
                param.data.permission_detail = [];
                var checkbox = $("input[name='permission_detail[]']:checked");
                for(var i = 0; i < checkbox.length; ++i) {
                    param.data.permission_detail.push($(checkbox).eq(i).val());
                }
            },
            requestSuccess: function (param) {
                var check = $('#flyer-create input[name=role]:checked');
                var text;
                if ($(check).val() == 0) {
                    text = '';
                }
                else {
                    text = '<span class="flyer-status blue statue-thin">' + $(check).siblings('label').text() + '</span>';
                }
                parent.$('#info-table tbody #tr-<?= $role->id ?> .role').html(text);
                parent.layer.alert('设置成功', {icon: 6}, function () {
                    parent.layer.closeAll();
                });
            }
        });
    });

    function fill_checkbox(checkbox, is_user)
    {
        $.each($(checkbox), function() {
            if( ! $(this).is(':checked')) {
                $(this).click();
            }
        });
        if (is_user) {
            $(checkbox).attr('disabled', true);
        }
    }
</script>