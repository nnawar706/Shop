<?php

class MainController {
    protected $f3;

    function beforeroute() {
//        if(!$this->f3->get('SESSION.isLoggedIn')) {
//            $data['status'] = 0;
//            $data['msg'] = 'Not authorized to access the content.';
//            header('Content-Type: application/json');
//            echo json_encode($data);
//            die();
//        }
    }

    function afterroute() {
    }

    function __construct() {
        date_default_timezone_set('Asia/Dhaka');
        $f3 = Base::instance();
        $db = new DB\SQL(
            $f3->get('db_dns') . $f3->get('db_name'),
            $f3->get('db_user'),
            $f3->get('db_pass')
        );
        $f3->set('DB',$db);
        $this->f3=$f3;
    }
}