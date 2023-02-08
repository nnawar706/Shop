<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RootController extends DemoController {

    public function index() {
        echo "HOMEPAGE HERE";
    }

    public function signIn() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $user = new UserModel();
                    if($user->isAvailable($data)) {
                        $id = $user->getUserID($data['phone_username']);
                        $role = $user->getRole($id);
                        $user->updateUser($id);
                        $info['data'] = $this->generateToken($id, $role);
                        $this->generateSecuredCookie($id, $role);
                        $info['status'] = 1;
                        $info['message'] = 'Successfully signed in!';
                    } else {
                        $info['status']['code'] = 0;
                        $info['status']['message'] = 'Invalid data.';
                        $this->f3->status(401);
                    }
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    private function generateToken($id, $role): array {
        $user = new UserModel();
        $secret_key = "I am a key. Use me to unlock the door to this application.";
        $j_iat = time();
        $payload = [
            "id" => $id,
            "role" => $role,
            "iat" => $j_iat,
            "exp" => time() + (60 * 15)
        ];
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        $data['type'] = "Bearer";
        $data['token'] = $jwt;
        $user->setUser_j_iat($id, $j_iat);
        return $data;
    }

    private function generateSecuredCookie($id, $role) {
        $user = new UserModel();
        $secret_key = "I am a key. Use me to unlock the door to this application.";
        $r_iat = time();
        $payload = [
            "id" => $id,
            "role" => $role,
            "iat" => $r_iat,
            "exp" => time() + (60 * 60 * 24 * 90)
        ];
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        setcookie("secured_jwt", $jwt, time() + 3600, "", "", true, true);
        $user->setUser_r_iat($id, $r_iat);
    }

    private function deleteCookie() {
        setcookie('jwt', '', time() - (60 * 60));
        unset($_COOKIE['jwt']);
    }

    public function signOut() {
        $this->deleteCookie();
        $data['status'] = 1;
        $data['message'] = 'Successfully signed out.';
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function refreshToken() {
        $jwt = $_COOKIE['jwt'];
        try {
            $secret_key = "I am a key. Use me to unlock the door to this application.";
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $user_id = $decoded->id;
            $role = $decoded->role;
            $user = new UserModel();
            $valid = $user->isValidId($user_id);
            if($valid) {
                $this->deleteCookie();
                $this->generateCookie($user_id, $role);
            }
        } catch(Exception $e) {
            echo "Invalid JWT token";
        }
    }

}