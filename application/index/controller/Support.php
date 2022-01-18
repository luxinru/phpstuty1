<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Support extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        $this->info = db('xy_cs')->where('status',1)->select();
        $this->assign('list',$this->info);

        $this->msg = db('xy_index_msg')->where('status',1)->select();
        return $this->fetch();
    }


    public function index2()
    {
        $this->url = isset($_REQUEST['url']) ? $_REQUEST['url'] :'';
        return $this->fetch();
    }

    /**
     * 首页
     */
    public function detail()
    {
        $id = input('get.id/d',1);
        $this->info = db('xy_index_msg')->where('id',$id)->find();
        if($id==13){
            if(cookie('think_var')=="zh-cn"){
                 $gundong = db('xy_index_msg')->where('id',13)->value('content');;;
            }elseif(cookie('think_var')=="en-ww"){
                 $gundong = db('xy_index_msg')->where('id',356)->value('content');;;
            }elseif(cookie('think_var')=='en-in'){
                $gundong = db('xy_index_msg')->where('id',357)->value('content');;;
            }else{
                 $gundong = db('xy_index_msg')->where('id',13)->value('content');;;
            }
        }elseif($id==14){
            if(cookie('think_var')=="zh-cn"){
                 $gundong = db('xy_index_msg')->where('id',14)->value('content');;;
            }elseif(cookie('think_var')=="en-ww"){
                 $gundong = db('xy_index_msg')->where('id',358)->value('content');;;
            }elseif(cookie('think_var')=='en-in'){
                $gundong = db('xy_index_msg')->where('id',359)->value('content');;;
            }else{
                 $gundong = db('xy_index_msg')->where('id',14)->value('content');;;
            }
        }elseif($id==333){
            if(cookie('think_var')=="zh-cn"){
                 $gundong = db('xy_index_msg')->where('id',333)->value('content');;;
            }elseif(cookie('think_var')=="en-ww"){
                 $gundong = db('xy_index_msg')->where('id',360)->value('content');;;
            }elseif(cookie('think_var')=='en-in'){
                $gundong = db('xy_index_msg')->where('id',361)->value('content');;;
            }else{
                 $gundong = db('xy_index_msg')->where('id',333)->value('content');;;
            }
        }elseif($id==334){
            if(cookie('think_var')=="zh-cn"){
                 $gundong = db('xy_index_msg')->where('id',334)->value('content');;;
            }elseif(cookie('think_var')=="en-ww"){
                 $gundong = db('xy_index_msg')->where('id',362)->value('content');;;
            }elseif(cookie('think_var')=='en-in'){
                $gundong = db('xy_index_msg')->where('id',363)->value('content');;;
            }else{
                 $gundong = db('xy_index_msg')->where('id',334)->value('content');;;
            }
        }elseif($id==335){
            if(cookie('think_var')=="zh-cn"){
                 $gundong = db('xy_index_msg')->where('id',335)->value('content');;;
            }elseif(cookie('think_var')=="en-ww"){
                 $gundong = db('xy_index_msg')->where('id',364)->value('content');;;
            }elseif(cookie('think_var')=='en-in'){
                $gundong = db('xy_index_msg')->where('id',365)->value('content');;;
            }else{
                 $gundong = db('xy_index_msg')->where('id',335)->value('content');;;
            }
        }else{
            $gundong=db('xy_index_msg')->where('id',$id)->value('content');
        }
        $this->info['content']=$gundong;
        return $this->fetch();
    }




    /**
     * 换一个客服
     */
    public function other_cs()
    {
        $data = db('xy_cs')->where('status',1)->where('id','<>',$id)->find();
        if($data) return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$data]);
        return json(['code'=>1,'info'=>lang('暂无数据')]);
    }
}