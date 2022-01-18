<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 源码来自九牛网(www.9nw.cc) 会员投稿
// +----------------------------------------------------------------------
// |  注：在使用本系统禁止用于一切非法行为。使用用途仅限于测试、实验、研究为目的，禁止用于一切商业运营，本团队不承担使用者在使用过程中的任何违法行为负责。
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Console;
use think\Db;
use think\exception\HttpResponseException;

/**
 * 系统公共操作
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{

    /**
     * 显示后台首页
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $this->title = '系统管理后台';
        NodeService::applyUserAuth(true);
        $this->menus = NodeService::getMenuNodeTree();
        if (empty($this->menus) && !NodeService::islogin()) {
            $this->redirect('@admin/login');
        } else {
            $this->fetch();
        }
    }

    /**
     * 后台环境信息
     * @auth true
     * @menu true
     */
    public function main()
    {
        $this->think_ver = \think\App::VERSION;
        $this->mysql_ver = Db::query('select version() as ver')[0]['ver'];

        //昨天
        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );

        $this->goods_num = db('xy_goods_list')->count('id');
        $this->today_goods_num = db('xy_goods_list')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        $this->yes_goods_num = db('xy_goods_list')->where('addtime','between',[$yes1,$yes2])->count('id');

        //用户
        $this->users_num = db('xy_users')->count('id');
        $this->today_users_num = db('xy_users')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        $this->yes_users_num = db('xy_users')->where('addtime','between',[$yes1,$yes2])->count('id');

        //订单数量
        $this->order_num = db('xy_convey')->count('id');
        $this->today_order_num = db('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        $this->yes_order_num = db('xy_convey')->where('addtime','between',[$yes1,$yes2])->count('id');

        //订单总额
        $this->order_sum = db('xy_convey')->sum('num');
        $this->today_order_sum = db('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_order_sum = db('xy_convey')->where('addtime','between',[$yes1,$yes2])->sum('num');

        //充值
        $this->user_recharge = db('xy_recharge')->where('status',2)->sum('num');
        $this->today_user_recharge = db('xy_recharge')->where('status',2)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_user_recharge = db('xy_recharge')->where('status',2)->where('addtime','between',[$yes1,$yes2])->sum('num');

        //提现
        $this->user_deposit = db('xy_deposit')->where('status',2)->sum('num');
        $this->today_user_deposit = db('xy_deposit')->where('status',2)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_user_deposit = db('xy_deposit')->where('status',2)->where('addtime','between',[$yes1,$yes2])->sum('num');

        //抢单佣金
        $this->user_yongjin = db('xy_convey')->where('status',1)->sum('commission');
        $this->today_user_yongjin = db('xy_convey')->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('commission');
        $this->yes_user_yongjin = db('xy_convey')->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');

         //利息宝
        $this->user_lixibao = db('xy_lixibao')->where('type',1)->where('is_sy',0)->sum('num');
        $this->today_user_lixibao = db('xy_lixibao')->where('type',1)->where('is_sy',0)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_user_lixibao = db('xy_lixibao')->where('type',1)->where('is_sy',0)->where('addtime','between',[$yes1,$yes2])->sum('num');

        //下级返佣
        $this->user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->sum('num');
        $this->today_user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('num');

        //下级返佣
        $this->user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->sum('num');
        $this->today_user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_user_fanyong = db('xy_balance_log')->where('type',6)->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('num');

        //用户余额
        $this->user_yue = db('xy_users')->sum('balance');
        $this->user_djyue = db('xy_users')->sum('freeze_balance');
        $this->today_lxbsy = db('xy_balance_log')->where('type',23)->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->today_lxbzc = db('xy_balance_log')->where('type',22)->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');



        $isVersion = '';
        if (!session('check_update_version')){
            $isVersion = $this->Update(1);
        }

        $this->assign('has_version', $isVersion);
        $this->fetch();
    }

    /**
     * 修改密码
     * @param integer $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pass($id)
    {
        $this->applyCsrfToken();
        if (intval($id) !== intval(session('admin_user.id'))) {
            $this->error('只能修改当前用户的密码！');
        }
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        if ($this->request->isGet()) {
            $this->verify = true;
            $this->_form('SystemUser', 'admin@user/pass', 'id', [], ['id' => $id]);
        } else {
            $data = $this->_input([
                'password'    => $this->request->post('password'),
                'repassword'  => $this->request->post('repassword'),
                'oldpassword' => $this->request->post('oldpassword'),
            ], [
                'oldpassword' => 'require',
                'password'    => 'require|min:4',
                'repassword'  => 'require|confirm:password',
            ], [
                'oldpassword.require' => '旧密码不能为空！',
                'password.require'    => '登录密码不能为空！',
                'password.min'        => '登录密码长度不能少于4位有效字符！',
                'repassword.require'  => '重复密码不能为空！',
                'repassword.confirm'  => '重复密码与登录密码不匹配，请重新输入！',
            ]);
            $user = Db::name('SystemUser')->where(['id' => $id])->find();
            if (md5($data['oldpassword']) !== $user['password']) {
                $this->error('旧密码验证失败，请重新输入！');
            }
            $result = NodeService::checkpwd($data['password']);
            if (empty($result['code'])) $this->error($result['msg']);
            if (Data::save('SystemUser', ['id' => $user['id'], 'password' => md5($data['password'])])) {
                $this->success('密码修改成功，下次请使用新密码登录！', '');
            } else {
                $this->error('密码修改失败，请稍候再试！');
            }
        }
    }

    /**
     * 修改用户资料
     * @param integer $id 会员ID
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function info($id = 0)
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        $this->applyCsrfToken();
        if (intval($id) === intval(session('admin_user.id'))) {
            $this->_form('SystemUser', 'admin@user/form', 'id', [], ['id' => $id]);
        } else {
            $this->error('只能修改登录用户的资料！');
        }
    }

    /**
     * 清理运行缓存
     * @auth true
     */
    public function clearRuntime()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        try {
            Console::call('clear');
            Console::call('xclean:session');
            $this->success('清理运行缓存成功！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $e) {
            $this->error("清理运行缓存失败，{$e->getMessage()}");
        }
    }

    /**
     * 压缩发布系统
     * @auth true
     */
    public function buildOptimize()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        try {
            Console::call('optimize:route');
            Console::call('optimize:schema');
            Console::call('optimize:autoload');
            Console::call('optimize:config');
            $this->success('压缩发布成功！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $e) {
            $this->error("压缩发布失败，{$e->getMessage()}");
        }
    }


    public function Update($isreturn)
    {
        $version = config("version");
        $isHtml = $isreturn?0:1;
        $con = '已经是最新版!';
        session('check_update_version',1);
        if($isreturn ) return $con;

        echo $con;die;
    }


    public function order_info()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }

        $deposit = db('xy_deposit')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')->where('xd.status',1)->where('u.is_jia',0)->count('xd.id');
        $recharge = db('xy_recharge')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')->where('xd.status',1)->where('u.is_jia',0)->count('xd.id');
        $deposit_jia = db('xy_deposit')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')->where('xd.status',1)->where('u.is_jia',1)->count('xd.id');
        $recharge_jia = db('xy_recharge')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')->where('xd.status',1)->where('u.is_jia',1)->count('xd.id');
        echo json_encode(['deposit'=>$deposit,'recharge'=>$recharge,'deposit_jia'=>$deposit_jia,'recharge_jia'=>$recharge_jia]);

    }

    public function clear()
    {
        $isVersion = $this->Update(0);
    }

}
