<?php

namespace admin\controllers;

use yii\helpers\ArrayHelper;
use common\helpers\Pager;
use common\models\Message;

class MessageController extends Controller {
    
    // 设置所属一级导航 不设置表示 就是当前类名
    public $parent = 'member';
    
    /**
     * @name showing Message list
     * @return string
     */
    public function actionMessageList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('message-list');
        }
        $param = $this->request->post();
        $param['deleted_at'] = 0;
        $query = Message::filterConditions(Message::initCondition([['title', 'like'], 'receiver_id', 'type', 'status', 'deleted_at'], $param));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->with('receiver')->orderBy('id desc')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }

    /**
     * @name show message information
     * @param id int by get request
     * @return string
     */
    public function actionMessageDetail()
    {
        $message = null;
        $messageId = $this->request->get('id');
        if($messageId && ( ! $message = Message::finder($messageId))) {
            return $this->error('无效的站内信（Invalid Message）', 'message/message-list');
        }
        return $this->render('message-detail', ['data' => $message]);
    }
    /**
     * @name insert Message
     * @method post
     * @return string html
     */
    public function actionMessageInsert()
    {
        $message = new Message();
        $param = $this->request->post();
        $message->setAttributesByKey($param);
        if ( ! $message->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($message->errors(), 'message/message-detail');
        }
        if ($message->save()) {
            // 保存成功
            return $this->success('站内信（'.$message->title.'）添加成功（Insert Success）', [
                ['title' => '前往站内信列表页', 'url' => 'message/message-list'],
                ['title' => '继续修改站内信', 'url' => 'message/message-detail?id='.$message->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('站内信（'.$message->title.'）添加失败，请重试（System Fail）', 'message/message-detail');
    }
    /**
     * @name update message information
     * @describe use get(id) to find Message
     * @return string html
     */
    public function actionMessageUpdate()
    {
        /* @var $message Message */
        // id 为必填项，判断管理员存在状态
        $message = Message::finder($this->request->get('id'));
        // 未得到，渲染错误页面
        if( ! $message) {
            return $this->error('无效的站内信（Invalid Message）', 'message/message-list');
        }
        $message->setPostRequest();
        if ( ! $message->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($message->errors(), 'message/message-detail?id='.$message->id);
        }
        if ($message->save()) {
            // 保存成功
            return $this->success('站内信（'.$message->title.'）更新成功（Update Success）', [
                ['title' => '前往站内信列表页', 'url' => 'message/message-list'],
                ['title' => '继续修改站内信', 'url' => 'message/message-detail?id='.$message->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('站内信（'.$message->title.'）更新失败，请重试（System Error）', 'message/message-detail?id='.$message->id);
    }
    /**
     * @name delete Message
     * @param ids array post request
     * @return string json
     */
    public function actionMessageDelete()
    {
        if( ! (($ids = $this->request->post('id')) || ($ids = $this->request->post('ids')))) {
            return $this->json('Invalid.Param', '请求异常（Invalid Param）');
        }
        else if(Message::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, '站内信删除成功（Delete Success）');
        }
        return $this->json('System.Error', '站内信删除失败（System Error）');
    }
}
