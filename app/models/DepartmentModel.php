<?php

class DepartmentModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_profile_department_id' => [
            'has-many' => ['\UserProfileModel','department_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha_space|||max_len,100'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'department');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createDepartment($data): array {
        $this->name = $data['name'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Department Successfully Added.';
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

    public function getAll(): array {
        $this->fields(['user_profile_department_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All department successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No department found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getDepartment($id): array {
        $this->fields(['user_profile_department_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'department Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid department Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateDepartment($id, $data): array {
        $info = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $status['code'] = 1;
                    $status['message'] = 'department Successfully Updated.';
                } catch(PDOException $e) {
                    $status['code'] = 0;
                    $status['message'] = $e->errorInfo[2];
                }
            } else {
                $status['code'] = 0;
                $status['message'] = Base::instance()->get('error_msg');
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid department Id.';
        }
        $result['data'] = $info;
        $result['status'] = $status;
        return $result;
    }

    public function deleteDepartment($id): array {
        $data = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'department Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid department Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }
}