<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Checker;
use common\models\Navigator;

class NavigatorController extends Controller {
    
    /**
     * show navigation list
     * @describe this action showing navigator
     * @return render / json
     */
    public function actionList()
    {
        // 获取所有菜单
        return $this->render('list');
    }
    /**
     * insert navigator
     * @method post
     * @return string json
     */
    public function actionInsert()
    {
        $navigator = new Navigator();
        if ( ! $navigator->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', $navigator->errors());
        }
        if ($navigator->save()) {
            return $this->json(SuccessCode, 'navigator update successful', ['id' => $navigator->id]);
        }
        return $this->json('System.Error', 'navigator insert failed');
    }
    /**
     * update navigator
     * @method post
     * @return string json
     */
    public function actionUpdate()
    {
        $navigator = Navigator::finder($this->request->post('id'));
        if( ! $navigator) {
            return $this->json('Invalid.Param', 'you are modifying an object that does not exist');
        }
        if ( ! $navigator->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', $navigator->errors());
        }
        if ($navigator->save()) {
            return $this->json(SuccessCode, 'navigator update successful');
        }
        return $this->json('System.Error', 'navigator update failed');
    }
    /**
     * delete navigator
     * @method post
     * @return string json
     */
    public function actionDelete()
    {
        if(! $navigatorIds = $this->request->post('id')) {
            return $this->json('Invalid.Param', 'please choose at least one navigation column');
        }
        // 判断是否存在子分类数据，如果存在则不能删除当前分类数据
        if(Navigator::find()->where(['parent_id' => $navigatorIds])->exists()) {
            return $this->json('Param.Error', 'please delete all the subnavigation first');
        }
        if(Navigator::deleteAll(['id' => $navigatorIds])) {
            return $this->json(SuccessCode, 'navigator delete successful');
        }
        return $this->json('System.Error', 'navigator delete failed');
    }
}
