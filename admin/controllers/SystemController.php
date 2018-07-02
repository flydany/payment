<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Checker;
use common\models\District;
use common\models\Navigator;
use common\models\Category;

class SystemController extends Controller {
    
    /**
     * -----------------------------------------
     *  分类相关操作 开始
     * -----------------------------------------
     * @name show category list for category's CURD
     * @describe this action showing category
     * -----------------------------------------
     */
    public function actionCategoryList()
    {
        if( ! $this->request->isAjax) {
            // 渲染页面
            return $this->render('category-list');
        }
        // 获取所有菜单
        $categories = Category::find()->where(['flag' => 1])->orderBy('sort ASC')->asArray()->all();
        return $this->json(['infos' => ArrayHelper::index($categories, null, 'parent_id'), 'start_index' => 0]);
    }
    /**
     * @name insert category
     * @method post
     * @return string json
     */
    public function actionCategoryInsert()
    {
        $category = new Category();
        $category->setAttributes($this->request->post());
        if ( ! $category->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', implode('。', ArrayHelper::getColumn($category->getErrors(), '0')).'（Invalid Param）');
        }
        if ($category->save()) {
            return $this->json(['id' => $category->id]);
        }
        return $this->json('System.Error', '导航添加失败（System Error）');
    }
    /**
     * @name update category
     * @method post
     * @return string json
     */
    public function actionCategoryUpdate()
    {
        $category = Category::finder($this->request->post('id'));
        if( ! $category) {
            return $this->json('Invalid.Param', '数据异常：你正在修改一个不存在的对象（Invalid Param）');
        }
        $category->setPostRequest();
        if ( ! $category->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', implode('。', ArrayHelper::getColumn($category->getErrors(), '0')).'（Invalid Param）');
        }
        if ($category->save()) {
            return $this->json();
        }
        return $this->json('System.Error', '分类更新失败（System Error）');
    }
    /**
     * @name delete category
     * @method post
     * @return string json
     */
    public function actionCategoryDelete()
    {
        if( ! (($category_ids = $this->request->post('id')) || ($category_ids = $this->request->post('ids')))) {
            return $this->json('Invalid.Param', '请选择至少一个分类（Invalid Param）');
        }
        // 判断是否存在子分类数据，如果存在则不能删除当前分类数据
        if(Category::find()->where(['parent_id' => $category_ids])->exists()) {
            return $this->json('Param.Error', '请先删除所有的子分类（Children Not Empty）');
        }
        if(Category::deleteAll(['id' => $category_ids])) {
            return $this->json(SuccessCode, '分类删除成功（Delete Success）');
        }
        return $this->json('System.Error', '分类删除失败（System Error）');
    }

    
    /**
     * --------------------------------------------
     *  导航相关操作 开始
     * --------------------------------------------
     * @name for navigator's CURD
     * @describe this action showing navigator
     * @return render / json
     * --------------------------------------------
     */
    public function actionNavigatorList()
    {
        if( ! $this->request->isAjax) {
            // 渲染页面
            return $this->render('navigator-list');
        }
        // 获取所有菜单
        $navigators = Navigator::find()->where(['flag' => 1])->orderBy('sort ASC')->asArray()->all();
        return $this->json(['infos' => ArrayHelper::index($navigators, null, 'parent_id'), 'start_index' => 0]);
    }
    /**
     * @name insert navigator
     * @method post
     * @return string json
     */
    public function actionNavigatorInsert()
    {
        $navigator = new Navigator();
        $navigator->setPostRequest();
        if ( ! $navigator->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', implode('。', ArrayHelper::getColumn($navigator->getErrors(), '0')).'（Invalid Param）');
        }
        if ($navigator->save()) {
            return $this->json(['id' => $navigator->id]);
        }
        return $this->json('System.Error', '导航添加失败（System Error）');
    }
    /**
     * @name update navigator
     * @method post
     * @return string json
     */
    public function actionNavigatorUpdate()
    {
        $navigator = Navigator::finder($this->request->post('id'));
        if( ! $navigator) {
            return $this->json('Invalid.Param', '数据异常：你正在修改一个不存在的对象（Invalid Param）');
        }
        $navigator->setPostRequest();
        if ( ! $navigator->validate()) {
            // 参数异常，渲染错误页面
            return $this->json('Invalid.Param', implode('。', ArrayHelper::getColumn($navigator->getErrors(), '0')).'（Invalid Param）');
        }
        if ($navigator->save()) {
            return $this->json();
        }
        return $this->json('System.Error', '导航更新失败（System Error）');
    }
    /**
     * @name delete navigator
     * @method post
     * @return string json
     */
    public function actionNavigatorDelete()
    {
        if( ! (($navigator_ids = $this->request->post('id')) || ($navigator_ids = $this->request->post('ids')))) {
            return $this->json('Invalid.Param', '请选择至少一个导航栏目（Invalid Param）');
        }
        // 判断是否存在子分类数据，如果存在则不能删除当前分类数据
        if(Navigator::find()->where(['parent_id' => $navigator_ids])->exists()) {
            return $this->json('Param.Error', '请先删除所有的子导航（Children Not Empty）');
        }
        if(Navigator::deleteAll(['id' => $navigator_ids])) {
            return $this->json(SuccessCode, '导航删除成功（Delete Success）');
        }
        return $this->json('System.Error', '导航删除失败（System Error）');
    }

    /**
     * ----------------------------------------------------------------------------------
     * 系统分类，地区等子父结构数据 JS 化 开始
     * ----------------------------------------------------------------------------------
     * @name 文件参数初始化页面渲染
     * @return string render html page
     */
    public function actionSystemInit()
    {
        return $this->render('system-init');
    }
    
    function actionDistrictInit()
    {
        $js_path = Yii::getAlias('@static/system/district.data.js');
        $districts = District::find()->select('id, areaname, parentid')->where(['level' => [1, 2, 3]])->orderBy('sort asc')->asArray()->all();
        if($fw = fopen($js_path, 'w')) {
            fwrite($fw, 'var Districts = '.json_encode(ArrayHelper::map($districts, 'id', 'areaname', 'parentid')).';');
            fwrite($fw, 'var DistrictsRelation = '. json_encode(ArrayHelper::map($districts, 'id', 'parentid')).';');
            fclose($fw);
            return $this->json(['code' => SuccessCode, 'message' => '城市关系数据写入JS文件成功（Write Success）']);
        }
        else {
            return $this->json(['code' => 'System.Error', 'message' => '城市关系数据写入JS文件失败（System Error）']);
        }
    }
    /**
     * @name 填充文章分类数据关系作品到 category-article.data.js文件中
     * @method post
     * @return string json
     */
    public function actionArticleCategoryInit()
    {
        $js_path = Yii::getAlias('@static/system/category-article.data.js');
        if($this->initCategoryJs($this->params['articleCategories'], $js_path, 'ArticleCategories', 'ArticleCategoriesRelation')) {
            return $this->json(['code' => SuccessCode, 'message' => '分类关系数据写入JS文件成功（Write Success）']);
        }
        return $this->json(['code' => 'System.Error', '分类关系数据写入JS文件失败（System Error）']);
    }
    /**
     * @name 填充作品分类数据关系模型到 category-design.data.js文件中
     * @method post
     * @return string json
     */
    public function actionDesignCategoryInit()
    {
        $js_path = Yii::getAlias('@static/system/category-design.data.js');
        if($this->initCategoryJs($this->params['designCategories'], $js_path, 'DesignCategories', 'DesignCategoriesRelation')) {
            return $this->json(['code' => SuccessCode, 'message' => '分类关系数据写入JS文件成功（Write Success）']);
        }
        return $this->json(['code' => 'System.Error', '分类关系数据写入JS文件失败（System Error）']);
    }
    /**
     * @name 填充作品类型分类数据关系模型到 type-design.data.js文件中
     * @method post
     * @return string json
     */
    public function actionDesignModelInit()
    {
        $js_path = Yii::getAlias('@static/system/category-design-model.data.js');
        if($this->initCategoryJs($this->params['designModelCategories'], $js_path, 'DesignModelCategories', 'DesignModelCategoriesRelation')) {
            return $this->json(['code' => SuccessCode, 'message' => '分类关系数据写入JS文件成功（Write Success）']);
        }
        return $this->json(['code' => 'System.Error', '分类关系数据写入JS文件失败（System Error）']);
    }
    
    /**
     * @name 填充数据关系作品到js文件中
     * @param $top_id int 所属顶级元素编号
     * @param $js_path string JS 文件路径
     * @param $name string 数据变量名称
     * @param $relation string 关系变量名称
     * @return bool
     */
    public static function initCategoryJs($top_id, $js_path, $name, $relation)
    {
        $categories = Category::find()
            ->select('id, parent_id, title')
            ->where(['top_id' => $top_id, 'flag' => 1])
            ->andWhere(['!=', 'parent_id', 0])
            ->asArray()->all();
        if($fw = fopen($js_path, 'w')) {
            fwrite($fw, 'var '.$name.' = '. json_encode(ArrayHelper::map($categories, 'id', 'title', 'parent_id')).';');
            fwrite($fw, 'var '.$relation.' = '. json_encode(ArrayHelper::map($categories, 'id', 'parent_id')).';');
            fclose($fw);
            return true;
        }
        else {
            return false;
        }
    }
}
