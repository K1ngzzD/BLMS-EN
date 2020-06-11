<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\UserModel;
use Home\Model\BorrowModel;

final class User extends BaseController{

    public function index(){
        $this->accessPage();

        //用户信息
        $userModel = new UserModel;
        $userInfo  = $userModel->fetchOne("id={$_SESSION['userId']}");

        //借阅书籍详情
        $borrowModel = new BorrowModel;
        $borrowInfo  = $borrowModel->getBorrowInfo();

        $this->smarty->assign("borrowInfo",$borrowInfo);
        $this->smarty->assign("userInfo",$userInfo);
        $this->smarty->display("User/index.html");
    }
	//Json修改用户接口
    public function changeInfo(){
        $this->accessJson();

        $id             =  $_POST['userId'];
        $data['name']   =  $_POST['name'];
		$data['class']   =  $_POST['class'];
        $data['mobile']  =  $_POST['mobile'];
		$data['qq']   =  $_POST['qq'];
		$data['email']   =  $_POST['email'];
		$data['postcode']   =  $_POST['postcode'];
		$data['address']   =  $_POST['address'];



        if(in_array("",$data)){
            $this->sendJsonMessage("Please enter the complete information",1);
        }

        $userModel = new UserModel;

        if($userModel->update($data,"id={$id}")){
            $this->sendJsonMessage("Modify success",0);
        }else{
            $this->sendJsonMessage("Modify failed",1);
        }
    }
    //Json挂失接口
    public function lost(){
        $this->accessJson();

        $id = $_SESSION['userId'];

        $userModel = new UserModel;
        if($userModel->update(array("status"=>0),"id={$id}")){
            //挂失成功后销毁session，使登陆失效
            $_SESSION = array();
            session_destroy();
            $this->sendJsonMessage("Report the loss success",0);
        }else{
            $this->sendJsonMessage("Report the loss failed",1);
        }
    }

    //Json修改密码接口
    public function changePwd(){
        $this->accessJson();

        $originPwd  =   md5($_POST['originPwd']);
        $newPwd     =   md5($_POST['newPwd']);
        $confrimPwd =   md5($_POST['confirmPwd']);

        //确认密码二次验证，防止非法提交
        if($newPwd != $confrimPwd){
            $this->sendJsonMessage("The two passwords are inconsistent",1);
        }
        
        $userModel = new UserModel;
        if($userModel->rowCount("id={$_SESSION['userId']} and pwd='{$originPwd}'")){
            if($userModel->update(array("pwd"=>$newPwd),"id={$_SESSION['userId']} and pwd='{$originPwd}'")){
                //更改密码后销毁当前session
                $_SESSION = array();
                session_destroy();
                $this->sendJsonMessage("Password modified success",0);
            }else{
                $this->sendJsonMessage("Password modified failed",1);
            }
        }else{
            $this->sendJsonMessage("Original password error",1);
        }
    }

}