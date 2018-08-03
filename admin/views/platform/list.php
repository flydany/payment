<?php

/* @var $this admin\components\View */

use common\helpers\Render;
use common\models\Platform;

$this->title = 'Platform List';
$this->addCrumbs('Platform');

\admin\assets\TablerAsset::register($this);
?>

<div class="contenter">
    <div class="alert alert-info" role="alert">
        <p><strong>Heads up!</strong></p>
        <p>1. set platform\'s permission.</p>
    </div>
    <table class="table table-bordered table-striped" id="info-table">
        <thead>
        <tr>
            <th><i class="fa fa-list fa-fw"></i>platform name</th>
            <th><i class="fa fa-gear fa-fw"></i>operation</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if($powers) {
            foreach($powers as $power) {
                ?>
                <tr>
                    <td><?= $power . '/' . Platform::$platformSelector[$power] ?></td>
                    <td>
                        <a class="label label-warning" href="/admin-resource/platform?id=<?= $power ?>"><i class="fa fa-superpowers fa-fw"></i>permission</a>
                    </td>
                </tr>
                <?php
            }
        }
        else {
            ?>
            <tr>
                <td colspan="2"><i class="fa fa-ban fa-fw"></i>no permission for any platform.</td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>