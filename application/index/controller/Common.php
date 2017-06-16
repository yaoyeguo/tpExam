<?php

namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Session;


class Common extends Controller
{
    public function _initialize() {
        header('Content-type:text/html;charset=utf-8');
        //检查登录状态
        $this->checkLogin();
    }
    
    
    public function checkLogin(){
        if(!Session::get('userInfo')){
            return $this->error('请登录', Url('Login/index'));
        }
    }
}

