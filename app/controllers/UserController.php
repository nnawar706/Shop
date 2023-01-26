<?php

class UserController extends MainController {

    public function index() {
        $user = new UserModel();
        $data = $user->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function create() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $user = new UserModel();
                    $status = $user->createUser($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function read($f3, $params) {
        $user = new UserModel();
        $data = $user->getUser($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if ($this->f3->VERB == 'PUT' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $user = new UserModel();
                    $status = $user->userUpdate($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function getSalesmen() {
        $user = new UserModel();
        $status = $user->findSalesmen();
        header('Content-Type: application/json');
        echo json_encode($status);
    }

    public function delete($f3, $params) {
        $user = new UserModel();
        $status = $user->deleteUser($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}