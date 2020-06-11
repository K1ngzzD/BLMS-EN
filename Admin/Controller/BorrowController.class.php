<?php
namespace Admin\Controller;
use Base\BaseController;
use Admin\Model\BorrowModel;

final class Borrow extends BaseController{

    public function index(){
        $this->accessPage();

        $this->smarty->display("Borrow/index.html");
    }

    //Json借书和还书接口
    public function manage(){
        $this->accessJson();

        $bookId  =  $_POST['bookId'];
        $userId  =  $_POST['userId'];
        $action  =  $_POST['action'];

        if($userId == "" || $bookId == ""){
            $this->sendJsonMessage("Please fill in the complete information",1);
        }

        $borrowModel = new BorrowModel;
        if($action == "borrow"){
            //借书
            if($borrowModel->canBorrow($bookId,$userId)){
                $data = array(
                    "book_id"     =>  $bookId,
                    "user_id"     =>  $userId,
                    "borrow_date" =>  date("Y-m-d"),
                    "back_date"   =>  date("Y-m-d",strtotime("+2 month"))
                );
                if($borrowModel->insert($data)){
                    $this->sendJsonMessage("Lend books success",0);
                }else{
                    $this->sendJsonMessage("Lend books failed",1);
                }
            }else{
                $this->sendJsonMessage("The information is wrong or the book has been lent",1);
            }
        }else if($action == "return"){
            //还书
            if($borrowModel->canReturn($bookId,$userId)){
                if($borrowModel->delete("book_id={$bookId} AND user_id={$userId}")){
                    $this->sendJsonMessage("Return books success.",0);
                }else{
                    $this->sendJsonMessage("Return books failed",1);
                }
            }else{
                $this->sendJsonMessage("The information is wrong or the user does not take this book",1);
            }
        }else{
            $this->sendJsonMessage("Parameter error",1);
        }
    }

    //Json续借接口
    public function prolong(){
        $this->accessJson();

        //未传参中断
        if(!isset($_POST['bookId']) || !isset($_POST['userId'])){
            $this->sendJsonMessage("Missing parameters",1);
        }

        $bookId = $_POST['bookId'];
        $userId = $_POST['userId'];

        $borrowModel = new BorrowModel;
        $result = $borrowModel->fetchOne("book_id={$bookId} AND user_id={$userId}");

        //没有借书就不能续借
        if(empty($result)){
            $this->sendJsonMessage("The user did not borrow the book",1);
        }
        //超期不能续借
        if(strtotime($result['back_date']) < time()){
            $this->sendJsonMessage("Overdue books cannot be renewed",1);
        }

        //计算应还时间
        $backTime = date("Y-m-d",strtotime("+1 month",strtotime($result['back_date'])));
        $data = array("back_date"=>$backTime);
        if($borrowModel->update($data,"book_id={$bookId} AND user_id={$userId}")){
            $this->sendJsonMessage("Renew success",0);
        }else{
            $this->sendJsonMessage("Renew failed",1);
        }
    }

    //Json还书接口
    public function returnBook(){
        $this->accessJson();

        $bookId = $_POST['bookId'];
        $userId = $_POST['userId'];

        $borrowModel = new BorrowModel;
        if($borrowModel->canReturn($bookId,$userId)){
            if($borrowModel->delete("book_id={$bookId} AND user_id={$userId}")){
                $this->sendJsonMessage("Return books success",0);
            }else{
                $this->sendJsonMessage("Return books failed",1);
            }
        }else{
            $this->sendJsonMessage("The information is wrong or the user does not take this book",1);
        }
    }
}