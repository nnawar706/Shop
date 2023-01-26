<?php

class AttendanceController extends MainController {

    /**
     * @throws Exception
     */
    public function create() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $attendance = new AttendanceModel();
                    $status = $attendance->createAttendance($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }

    public function index($f3, $params) {
        $attendance = new AttendanceModel();
        $data = $attendance->getAll($params['page'], $params['limit']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function read($f3, $params) {
        $attendance = new AttendanceModel();
        $data = $attendance->getAttendance($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function search($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $attendance = new AttendanceModel();
                    $status = $attendance->getByDate($data, $params['page'], $params['limit']);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(200);
                }
            }
        } else {
            $this->f3->status(404);
        }
    }

    public function delete($f3, $params) {
        $attendance = new AttendanceModel();
        $status = $attendance->deleteAttendance($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
        $this->f3->status(200);
    }

}