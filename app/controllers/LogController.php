<?php

class LogController extends MainController {

    public function getAll($f3, $params) {
        $log = new LogModel();
        $data = $log->getAll($params['page'], $params['limit']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function readAllByType($f3, $params) {
        $log = new LogModel();
        $data = $log->getByType($params['typeid'], $params['page'], $params['limit']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function getByDateRange($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $log = new LogModel();
                    $status = $log->getByDate($data, $params['page'], $params['limit']);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(200);
                }
            }
        }
    }

}