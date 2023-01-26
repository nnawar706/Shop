<?php

class AttendanceModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_id' => [
            'belongs-to-one' => '\UserModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'attendance');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll($pageno, $perPage): array {
        $offset = ($pageno - 1) * $perPage;
        $totalRecords = $this->db->exec("SELECT COUNT(*) FROM attendance")[0]['COUNT(*)'];
        $totalPages = ceil($totalRecords/$perPage);
        $status['total pages'] = $totalPages;
        $status['current page'] = (int)$pageno;
        $result['status'] = $status;
        $data = $this->afind([], ['limit'=>$perPage, 'offset'=>$offset], 0, 2);
        $i = 0;
        if ($data) {
            foreach ($data as $item) {
                $info[$i]['user_id'] = $item['user_id']['id'];
                $info[$i]['name'] = $item['user_id']['profile_user_id']['name'];
                $info[$i]['date'] = $item['date'];
                $info[$i]['check_in'] = $item['check_in'];
                $info[$i]['check_out'] = $item['check_out'];
                $i = $i + 1;
            }
            $result['data'] = $info;
        }
        return $result;
    }

    public function getAttendance($id): array {
        $this->load(['user_id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 2);
            $info['user_id'] = $data['user_id']['id'];
            $info['name'] = $data['user_id']['profile_user_id']['name'];
            $info['date'] = $data['date'];
            $info['check_in'] = $data['check_in'];
            $info['check_out'] = $data['check_out'];
            $result['data'] = $info;
            $status['code'] = 1;
            $status['message'] = 'Attendance Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Attendance Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getByDate($data, $pageno, $perPage) {
        if($data['start'] !== "" && $data['end'] !== "") {
            $offset = ($pageno - 1) * $perPage;
            $date_start = $data['start'];
            $date_end = $data['end'];
            return $this->afind(['date>=? AND date<=?', $date_start, $date_end], ['limit'=>$perPage, 'offset'=>$offset], 0, 0);
        }
    }

    public function deleteAttendance($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Attendance Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Attendance Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function createAttendance($data): array {
        $this->copyfrom($data);
        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Attendance Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['status'] = $status;
        return $result;
    }

}