<?php

namespace admin\controllers;

use Yii;
use common\helpers\Checker;
use common\helpers\Request;

class DocumentController extends Controller {

    public $layout = '../document/main.php';

    public $whiteList = ['index'];
    /**
     * Displays homepage.
     * 文档首页
     * @return mixed
     */
    public function actionIndex()
    {
        $idtypeSelector = [
            '1' => ['title' => '身份证', 'status' => 'purple'],
            '2' => ['title' => '其他', 'status' => 'green'],
        ];
        return $this->render('index', [
            'idtypeSelector' => $idtypeSelector,
        ]);
    }

    /**
     * Build Rule JSON
     * @return mixed
     */
    public function actionRule()
    {
        $rule = [
            'param' => [
                'password' => ['密码', ['password', 'required']],
                'repassword' => ['重复密码', ['password', 'eq' => ':password', 'required'], ['eq' => '两次密码输入不一致']],
            ],
            'relate' => [
                [[['password', 'required']], 'repassword']
            ],
        ];
        return $this->v(json_encode($rule));
    }
}
