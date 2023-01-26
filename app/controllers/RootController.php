<?php

class RootController extends DemoController {

    public function index() {
        echo "HOMEPAGE HERE";
    }

    public function signin() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $user = new UserModel();
                    if($user->isAvailable($data)) {
                        $id = $user->getUserID($data['phone_username']);
                        $user->updateUser($id);
//                        $role = $user->getRole($id);
                        $this->f3->set('SESSION.isLoggedIn', TRUE);
                        $this->f3->set('SESSION.username', $this->f3->get('POST.phone_username'));
                        $this->f3->set('SESSION.userid', $id);
//                        $this->f3->set('SESSION.role', $role);
                        $info['status'] = 1;
                        $info['message'] = 'Successfully signed in!';
                    } else {
                        $info['status'] = 0;
                        $info['message'] = 'User not found.';
                    }
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function signout() {
        $this->f3->clear('SESSION.isLoggedIn');
        $this->f3->clear('SESSION.email');
        $this->f3->clear('SESSION.userid');
        $data['status'] = 1;
        $data['message'] = 'Successfully signed out.';
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}