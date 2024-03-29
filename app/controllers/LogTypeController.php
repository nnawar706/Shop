<?php

class LogTypeController extends MainController {

    public function index() {
        $type = new LogTypeModel();
        $data = $type->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function read($f3, $params) {
        $type = new LogTypeModel();
        $data = $type->getLogType($params['id']);
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
                    $type = new LogTypeModel();
                    $status = $type->updateLogType($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }

    public function delete($f3, $params) {
        $type = new LogTypeModel();
        $status = $type->deleteLogType($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}