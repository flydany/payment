<?php

/* @var $this \admin\components\View */

use common\helpers\Render;
use common\models\Project;
use common\models\ProjectContacts;


$this->addCrumbs('Project');
$this->addCrumbs('Project List', 'project/list');
$this->title = (isset($data['id']) ? 'Update' : 'Insert'). ' project';
$this->setActiveNavigator('project/list');

$this->registerJavascript('@static/flyer/checker.class.js');
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
            <input class="form-control" type="text" name="Project[title]" value="<?= Render::value($data, 'title') ?>" placeholder="project name.">
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 checker">
                <label>effect date</label>
                <input class="form-control" type="text" name="Project[effect_date]" value="<?= Render::value($data, 'effect_date') ?>" placeholder="effect date.">
            </div>
            <div class="form-group col-md-6 checker">
                <label>status</label>
                <?= Render::select('Project[status]', Project::$statusSelector, Render::value($data, 'status')) ?>
            </div>
        </div>
        <div class="form-group checker">
            <label>public key</label>
            <textarea class="form-control" name="Project[public_key]" placeholder="public key."><?= Render::value($data, 'public_key') ?></textarea>
        </div>
        <div class="form-group checker">
            <label>remark</label>
            <textarea class="form-control" name="Project[remark]" placeholder="remark."><?= Render::value($data, 'remark') ?></textarea>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">project contacts<a class="fr label label-primary" id="insert-contact"><i class="fa fa-plus fa-fw"></i>insert</a></div>
            <div class="panel-body pb-zero" id="contacts-list"></div>
        </div>
        <button type="submit" class="btn btn-primary" id="save-button"><i class="fa fa-save fa-fw"></i>save</button>
        <textarea id="info-detail-json" data-form="#info-detail" style="display:none;"><?= Project::checker() ?></textarea>
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
    </form>
</div>

<div id="contact-template" style="display:none;">
    <div class="form-group form-row contact">
        <div class="input-group col-md-3 checker">
            <label class="input-group-addon">identity</label>
            <?= Render::select('Contact[identity][]', ProjectContacts::$identitySelector, Render::value($data, 'identity')) ?>
        </div>
        <div class="input-group col-md-2 checker">
            <label class="input-group-addon">name</label>
            <input class="form-control" type="text" name="Contact[name][]" value="" placeholder="name">
        </div>
        <div class="input-group col-md-3 checker">
            <span class="input-group-addon">mobile</span>
            <input class="form-control" type="text" name="Contact[mobile][]" value="" placeholder="mobile">
        </div>
        <div class="input-group col-md-3 checker">
            <label class="input-group-addon">email</label>
            <input class="form-control" type="text" name="Contact[email][]" value="" placeholder="email">
        </div>
        <div class="col-md-1"><a class="label label-danger" onclick="deleteContact(this);" href="javascript:;"><i class="fa fa-close fa-fw"></i>delete</a></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 表单数据验证
        (new checker).init({ ruleDom: '#info-detail-json' });
        <?php
            if( ! empty($data->contacts)) {
                foreach($data->contacts as $contact) {
                    ?>
        insertContact(<?= json_encode($contact) ?>);
        <?php
                }
            }
            else {
                ?>
        insertContact(null);
        <?php
            }
        ?>
        $('#insert-contact').bind('click', function() {
            insertContact(null);
        });
    });

    // 删除联系人
    function deleteContact(mthis)
    {
        if ($(mthis).parents('.contact').siblings().length < 1) {
            return;
        }
        $(mthis).parents('.contact').remove();
    }
    // 添加规格值
    function insertContact(contact = null)
    {
        if(contact === null) {
            contact = { identity: '', name: '', mobile: '', email: '' };
        }
        var contactWrap = $('#contact-template .contact').clone(true);
        $(contactWrap).show();
        var keys = ['identity', 'name', 'mobile', 'email'];
        for(var i = 0; i < keys.length; ++i) {
            $(contactWrap).find(["name=" + keys[i] + "[]"]).val(contact[keys[i]]);
        }
        $('#contacts-list').append(contactWrap);
    }
</script>