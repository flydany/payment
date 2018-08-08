<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use admin\helpers\Render;

$this->title = 'Login In';
?>

<style>
    body {background: url(<?= Url::to('@web/static/image/login-background.jpg') ?>);background-attachment:fixed;font-family:Courier, '微软雅黑', monospace;height:100%;font-size: 100%;}
    #login-contain {width:520px;margin:0 auto;padding-top:120px;font-size:14px;}
    #logo {position:relative;width:118px;height:118px;margin:0 auto;padding:5px;background:#fff;border-radius:128px;z-index:90;}
    #logo #logo-icon {width:118px;height:118px;background:url(<?= Url::to('@web/static/image/login-tennis.jpg') ?>);background-size:100%;border-radius:118px;}

    #pg-login {padding:100px 50px 30px 50px;margin-top:-84px;background-color:rgba(255,255,255,.5);-webkit-background-clip:padding-box;background-clip:padding-box;border:8px solid rgba(0,0,0,0.2);border-radius:20px;box-shadow:0 5px 15px rgba(0,0,0,0.5);}
    #login-title {height:25px;line-height:25px;margin:15px 20px;}
    #captcha-code {height:30px;cursor:pointer;}
    #login-title .login-error {color:#cf6666;}

    .form-item button,
    .form-item input {width:100%;height:40px;margin-bottom:20px;padding:0 10px;font-size:14px;border:none;border-radius:5px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;box-shadow:0 0 5px rgba(0,0,0,.4);background-color:rgba(255,255,255,.5);}

    .has-error .flyer-input {box-shadow:0 0 5px #cf6666;}
    .has-pass .flyer-input {box-shadow:0 0 5px #3c763d;}
    #image-captcha {width:100%;height:40px;border:none;cursor:pointer;}

    .input-inline {float:left;}
</style>
<div id="login-contain">
    <div id="logo"><div id="logo-icon"></div></div>
    <div id="pg-login">
        <form id="flyer-login" action="<?= Url::to('@web/welcome/login') ?>" method="post">
            <div class="form-item checker">
                <div class="input-block"><input class="flyer-input" name="username" type="text" value="<?= Render::value($param, 'username') ?>" placeholder="用户名"></div>
            </div>
            <div class="form-item checker">
                <div class="input-block"><input class="flyer-input" name="password" type="password" value="<?= Render::value($param, 'password') ?>" placeholder="密码"></div>
            </div>
            <div class="form-item">
                <div class="input-inline checker" style="width:253px;"><input class="flyer-input" name="captcha" type="text" placeholder="图形验证码"></div>
                <div class="input-inline" style="width:140px;margin-left:10px;"><img id="image-captcha" src="<?= Url::to('@web/welcome/captcha?name=admin_login') ?>"></div>
            </div>
            <div class="form-item mt-10px">
                <div class="inline-block w-p100">
                    <button class="flyer-button normal" id="login-button" type="submit">Sign In</button>
                    <input name="skip" type="hidden" value="<?= isset($skip_url) ? $skip_url : '' ?>">
                    <textarea id="flyer-login-json" data-form="#flyer-login" style="display:none;"><?= $relate ?></textarea>
                </div>
            </div>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken() ?>">
        </form>
    </div>
</div>

<script src="<?= Render::static('flyer/checker.class.js') ?>"></script>
<script src="<?= Render::static('flyer/tableHandler.class.js') ?>"></script>
<script>
    var second = 60, timer;
    $(document).ready(function() {
        $('#image-captcha').bind('click', function() {
            $(this).attr('src', "<?= Url::to('@web/welcome/captcha?name=admin_login&rd=') ?>" + Math.random());
        });
        
        // 表单数据验证
        (new checker).init({ ruleDom: '#flyer-login-json' });

        // 异常提示
        if('<?= Yii::$app->controller->status['code'] ?>' !== '<?= SuccessCode ?>') {
            layer.msg('<?= Yii::$app->controller->status['message'] ?>', {shift: 6});
        }
    });
</script>