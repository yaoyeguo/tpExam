<?php
namespace app\index\controller;

class Login extends Common
{
    public function _initialize(){
        
    }   
    
    
    public function index()
    {
        return $this->fetch('index');
    }
}
