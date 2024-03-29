<?php

class UserTypeModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_role' => [
            'has-many' => ['\UserModel','role'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,50|||min_len,5'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'user_type');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createUserType($data): array {
        $this->name = $data['name'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'User Type Successfully Added.';
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
        $this->fields(['user_role'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All User Type successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No User Type found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getUserType($id): array {
        $this->fields(['user_role'], true);
        $this->load(['id=?', $id]);

        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'User Type Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateUserType($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'User Type Successfully Updated.';
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
            $status['message'] = 'Invalid User Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteUserType($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'User Type Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

}