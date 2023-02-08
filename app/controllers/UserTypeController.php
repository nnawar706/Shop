<?php

class UserTypeController extends MainController {

    public function index() {
        $type = new UserTypeModel();
        $data = $type->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    /**
     * @throws Exception
     */
    public function create() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $type = new UserTypeModel();
                    $status = $type->createUserType($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }

    public function read($f3, $params) {
        $type = new UserTypeModel();
        $data = $type->getUserType($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
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
                    $type = new UserTypeModel();
                    $status = $type->updateUserType($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }

    public function delete($f3, $params) {
        $type = new UserTypeModel();
        $status = $type->deleteUserType($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}