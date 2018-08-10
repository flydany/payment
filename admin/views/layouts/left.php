<?php

/* @var $this \admin\components\View */

use yii\helpers\ArrayHelper;
use common\helpers\Render;

$this->registerJs("
    jQuery(document).ready(function() {
        jQuery('#sidebar-menu .side-menu > li > a').bind('click', function() {
            var li = jQuery(this).parent('li');
            if(jQuery(li).hasClass('active')) {
                jQuery(li).removeClass('active');
            }
            else {
                jQuery('#sidebar-menu .side-menu > li.active').removeClass('active');
                jQuery(li).addClass('active');
            }
        });
    });
");
?>
<style>
    #website-name {
        height:57px;
        line-height:57px;
        padding-left:15px;
        font-size:24px;
        cursor:pointer;
    }
    #website-name a {
        color:#ECF0F1;
        text-decoration:none;
    }
    #website-name span {
        padding-left:15px;
    }
    #website-name i {
        border: 2px solid #EAEAEA;
        padding: 6px 7px;
        border-radius: 50%;
    }
    #sidebar-menu {
        margin-top:30px;
    }
    #sidebar-menu .fa {
        width: 26px;
        opacity: .99;
        display: inline-block;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
    }
    #sidebar-menu .menu-section {
        margin-bottom: 20px
    }
    #sidebar-menu .menu-section h3 {
        padding-left: 15px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        font-size: 11px;
        margin-bottom: 0;
        margin-top: 0;
        text-shadow: 1px 1px #000
    }
    #sidebar-menu .menu-section > ul {
        margin-top: 10px;
        padding-left:0;
    }
    #sidebar-menu .side-menu li {
        position: relative;
        display: block;
        cursor: pointer;
    }
    #sidebar-menu .side-menu > li.active {
        border-left:3px solid #1ABB9C;
    }
    #sidebar-menu .side-menu > li > a {
        display: block;
        margin-bottom: 6px;
        padding: 13px 15px 12px;
        color:#F2F5F7;
    }
    #sidebar-menu .side-menu > li.active > a {
        padding-left:12px;
        text-shadow: rgba(0,0,0,.25) 0 -1px 0;
        background: linear-gradient(#334556,#2C4257),#2A3F54;
        box-shadow: rgba(0,0,0,.25) 0 1px 0,inset rgba(255,255,255,.16) 0 1px 0
    }
    #sidebar-menu .side-menu li > a:hover {
        color: #F2F5F7 !important
    }
    #sidebar-menu li > a:hover, #sidebar-menu li > a:focus {
        text-decoration: none;
        background: 0 0;
    }
    #sidebar-menu .side-menu li a .toggle {
        float: right;
        text-align: center;
        margin-top: 5px;
        font-size: 10px;
        min-width: inherit;
        color: #C4CFDA;
    }
    #sidebar-menu .side-menu li.active a .toggle {
        text-align: right !important;
        margin-right: 7px;
    }
    #sidebar-menu .child-menu {
        display: none;
    }
    #sidebar-menu li.active > ul.child-menu {
        display:block;
        padding:0;
    }
    #sidebar-menu .child-menu li {
        padding-left: 30px;
    }
    #sidebar-menu .child-menu li:hover,
    #sidebar-menu .child-menu li.active {
        background-color:rgba(255,255,255,.06);
    }
    #sidebar-menu .child-menu li:before {
        height: 9px;
        width: 9px;
        bottom: auto;
        content: "";
        left: 23px;
        margin-top: 13px;
        position: absolute;
        right: auto;
        background:#425668;
        z-index: 1;
        border-radius: 50%;
    }
    #sidebar-menu .child-menu li:after {
        border-left: 1px solid #425668;
        bottom: 0;
        content: "";
        left: 27px;
        position: absolute;
        top: 0;
    }
    #sidebar-menu .child-menu > li > a {
        display: block;
        padding: 8px;
        color:rgba(255,255,255,.75);
    }
    @media (max-width: 1193px) {
        #left-panel,
        #website-name,
        #sidebar-menu {
            width:70px;
            text-align:center;
        }
        #left-panel {
            z-index:10;
        }
        #right-panel {
            margin-left:70px;
        }
        #website-name span,
        #sidebar-menu .menu-section > h3,
        #sidebar-menu .toggle,
        #sidebar-menu li.active > ul.child-menu {
            display:none;
        }
        #website-name {
            padding-left:0;
        }
        #sidebar-menu .fa {
            width:auto;
            font-size:28px;
        }
        #sidebar-menu .menu-section {
            margin-bottom:0;
        }
        #sidebar-menu .side-menu a {
            font-size:12px;
        }
        #sidebar-menu .side-menu a i:first-child {
            display:block;
            margin-bottom:5px;
        }
        #left-panel .side-menu {
            position:relative;
        }
        #sidebar-menu .child-menu {
            position:absolute;
            top:0;
            left:67px;
            width:230px;
            min-height:120px;
            background-color:#2A3F54;
            text-align:left;
            border-radius:0 5px 5px 0;
        }
        #sidebar-menu li:hover > ul.child-menu {
            display:block;
        }
        #sidebar-menu .side-menu > li:hover {
            border-left: 3px solid #1ABB9C;
        }
        #sidebar-menu .side-menu > li > a {
            padding:10px 0;
        }
        #sidebar-menu .side-menu > li.active > a {
            padding-left:0;
        }
    }
</style>

<div class="clearfix" style="width:100%;">
    <div  id="website-name">
        <a href="/index.php"><i class="fa fa-paw"></i><span><?= $this->context->module->name ?></span></a>
    </div>

    <!-- sidebar menu -->
    <div id="sidebar-menu">
        <?php
        $navigators = require(Yii::getAlias('@admin/config/navigator.php'));
        foreach($navigators[0] as $id => $part) {
            ?>
            <div class="menu-section">
                <h3><?= $part['title'] ?></h3>
                <ul class="nav side-menu">
                    <?php
                    foreach($navigators[$id] as $sid => $navigator) {
                        if( ! empty($navigators[$sid])) {
                            $active = in_array($this->activeNavigator(), ArrayHelper::getColumn($navigators[$sid], 'controller')) ? ' class="active"' : '';
                        }
                        else {
                            $active = ($navigator['controller'] == $this->activeNavigator()) ? ' class="active"' : '';
                        }
                        ?>
                        <li<?= $active ?> id="sid-<?= $sid ?>">
                            <a><i class="fa fa-<?= $navigator['icon_class'] ?> fa-fw"></i><?= $navigator['title'] ?><i class="fa fa-chevron-down toggle"></i></a>
                            <?php
                            if( ! empty($navigators[$sid])) {
                                ?>
                                <ul class="nav child-menu">
                                    <?php
                                    foreach($navigators[$sid] as $tid => $subNavigator) {
                                        $active = ($subNavigator['controller'] == $this->activeNavigator()) ? ' class="active"' : '';
                                        ?>
                                        <li<?= $active ?>><a href="/<?= $subNavigator['controller'] ?>"><?= $subNavigator['title'] ?></a></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
    <!-- /sidebar menu -->
</div>