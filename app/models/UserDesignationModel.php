<?php

class UserDesignationModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_profile_designation_id' => [
            'has-many' => ['\UserProfileModel','designation_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,50|||min_len,5'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'user_designation');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll(): array {
        $this->fields(['user_profile_designation_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All user designation successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No user designation found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAllSalesman(): array {
        $this->fields(['user_profile_designation_id'], true);
        $data = $this->afind(['name=?','Salesman'], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All user designation successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No user designation found.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function createUserDesignation($data): array {
        $this->name = $data['name'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'User Designation Successfully Added.';
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

    public function getUserDesignation($id): array {
        $this->fields(['user_profile_designation_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'User Designation Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Designation Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateUserDesignation($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'User Designation Successfully Updated.';
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
            $status['message'] = 'Invalid User Designation Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteUserDesignation($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'User Designation Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Designation Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}