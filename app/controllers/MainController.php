<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MainController {
    protected $f3;

    function beforeroute() {
//        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
//        $secret_key = "I am a key. Use me to unlock the door to this application.";
//        try {
//            $decoded = JWT::decode($auth, new Key($secret_key, 'HS256'));
//            $user_id = $decoded->id;
//            $user = new UserModel();
//            $valid = $user->isValidId($user_id);
//            if(!$valid) {
//                $data['status']['code'] = 0;
//                $data['status']['message'] = 'Protected Content.';
//                header('Content-Type: application/json');
//                echo json_encode($data);
//                die();
//            }
//        } catch(Exception $e) {
//            $data['status']['code'] = 0;
//            $data['status']['message'] = 'You are not authorized to access the contents.';
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