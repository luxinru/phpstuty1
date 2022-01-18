<?php
namespace app\index\controller;
use library\Controller;
use think\Db;
use \think\Lang;
use think\Cookie;
use \think\Config;
/**
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Cutlangss extends controller
{
    /*语言切换*/
    public function cutlangs(){
        $lang = input('post.lang');
        
        // if($lang == ''){
        //      Cookie('lang','id-id');
        // }
        
        if($lang =='zh-cn'){
            Cookie('lang','zh-cn',time()+80000);

        }else if($lang =='th-th'){
            Cookie('lang','th-th',time()+80000);
        }else if($lang =='en-us'){
            Cookie('lang','en-us',time()+80000);
        }else if($lang =='jp-jp'){
            Cookie('lang','jp-jp',time()+80000);    
        }else{
            Cookie('lang','xh');
        }
        echo json_encode(['code'=>1]);
    }
}