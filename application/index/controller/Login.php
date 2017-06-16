<?php
namespace app\index\controller;

use app\index\model\User;
use think\Session;

class Login extends Common
{
    public function _initialize(){
        
    }   
    
    
    public function index()
    {
        return $this->fetch('index');
    }

    public function checkLogin()
    {
        $user_tel = input('user_tel');
        $user_passwd = input('user_passwd');
        if(empty($user_tel) || empty($user_passwd)){
            return json(array('errorCode' => 1,'msg' => '参数异常!'));
        }

        if(!preg_match("/^1[34578]{1}\d{9}/",$user_tel)){
            return json(array('errorCode' => 1,'msg' => '手机号码填写有误!'));
        }

//        if(request()->isAjax() == false){
//            return json(array('errorCode' => 1,'msg' => '请求异常!'));
//        }

        $user = User::get([
            'user_tel' => $user_tel,
            'user_passwd' => md5($user_passwd),
        ]);

        if($user){
            Session::set('userInfo', $user);
            return json(array('errorCode' => 0,'msg' => 'success','data' => Session::get('userInfo')));
        }else{
            return json(array('errorCode' => 1,'msg' => '登录失败'));
        }
    }

    public function register(){
        return $this->fetch('register');
    }

    public function checkRegister()
    {
        $user_tel = input('user_tel');
        $user_passwd = input('user_passwd');
        $user_passwd_confirm = input('user_passwd_confirm');

        if(!$user_tel || !$user_passwd || !$user_passwd_confirm){
            return json(array('errorCode' => 1,'msg' => '请求参数异常!'));
        }

        if($user_passwd != $user_passwd_confirm){
            return json(array('errorCode' => 1,'msg' => '两次密码输入不一致!'));
        }

        $user = User::get([
            'user_tel' => $user_tel,
        ]);

        if($user){
            return json(array('errorCode' => 1,'msg' => '手机账号已注册!'));
        }

        $userModel = new User();
        $userModel->data([
            'user_tel' => $user_tel,
            'user_passwd' => md5($user_passwd),
            'create_time' => time(),
        ]);
        $userModel->save();
        return json(array('errorCode' => 0,'msg' => '注册成功!'));
    }

    public function loginOut(){
        Session::delete('userInfo');
        return json(array('errorCode' => 0,'msg' => '注销成功!'));
    }
}
