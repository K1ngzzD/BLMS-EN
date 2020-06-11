<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\BorrowModel;

final class Borrow extends BaseController{

    protected $table = "borrow_list";

    //Json续借接口
    public function prolong(){
        $this->accessJson();

        //未传参中断
        if(!isset($_POST['bookId'])){
            $this->sendJsonMessage("Missing bookid parameter",1);
        }

        $bookId = $_POST['bookId'];

        $borrowModel = new BorrowModel;
        $result = $borrowModel->fetchOne("book_id={$bookId} AND user_id={$_SESSION['userId']}");

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
        if($borrowModel->update($data,"book_id={$bookId} AND user_id={$_SESSION['userId']}")){
            $this->sendJsonMessage("Renew success",0);
        }else{
            $this->sendJsonMessage("Renew failed",1);
        }
    }
}