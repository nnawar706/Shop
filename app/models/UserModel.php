<?php

class UserModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'profile_user_id' => [
            'has-one' => ['\UserProfileModel','user_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'sales_kpi_user_id' => [
            'has-one' => ['\SalesKpiModel','user_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'sales_order_user_id' => [
            'has-many' => ['\SalesOrderModel','user_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'role' =>[
            'belongs-to-one' => '\UserTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'attendance_user_id' => [
            'has-many' => ['\AttendanceModel','user_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'phone_username' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||regex,/^(?:\+88|88)?(01[3-9]\d{8})$/'
        ],
        'password' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||min_len,5|||max_len,20'
        ],
        'account_status' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||exact_len,1|||numeric'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'user');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createUser($data): array {
        $this->phone_username = $data['phone_username'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->password_changed_at = date('y-m-d h:i:s');
        $this->account_status = 1;
        if($this->validate()) {
            try {
                $this->password = md5($data['password']);
                $this->save();
                $result = $this->getUser($this->id);
                $log = new LogModel();
                $stat = "User ID: " . $this->id . " has been created";
                $log->add($stat, 15);
                $status['code'] = 1;
                $status['message'] = 'User Successfully Added.';
            } catch (PDOException $e) {
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
        $this->fields(['sales_order_user_id', 'password', 'salt', 'last_password', 'sales_kpi_user_id', 'profile_user_id', 'attendance_user_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All User Successfully Fetched.';
            $result['data'] = $data;
        } else {
            $status['code'] = 0;
            $status['message'] = 'No User Found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getUser($id): array {
        $this->fields(['profile_user_id','sales_order_user_id', 'password', 'salt', 'last_password', 'sales_kpi_user_id', 'attendance_user_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'User Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getUserID($phone) {
        $this->load(['phone_username = ?',$phone]);
        if($this->id) {
            return $this->id;
        } else {
            return 0;
        }
    }

    /**
     * @throws Exception
     */
    public function userUpdate($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->phone_username = $data['phone_username'] ?? '';
            $this->role = $data['role'] ?? '';
            $this->last_password = $this->password;
            $this->password = $data['password'] ?? '';
            $this->account_status = $data['account_status'] ?? 1;
            if($this->validate()) {
                try {
                    $this->password = md5($data['password']);
                    $this->password_changed_at = date('y-m-d h:i:s');
                    $this->save();
                    $result = $this->getUser($this->id);
                    $log = new LogModel();
                    $stat = "User ID: " . $this->id . " has been updated";
                    $log->add($stat, 15);
                    $status['code'] = 1;
                    $status['message'] = 'User Successfully Updated.';
                } catch (PDOException $e) {
                    $status['code'] = 0;
                    $status['message'] = $e->errorInfo[2];
                }
            } else {
                $status['code'] = 0;
                $status['message'] = Base::instance()->get('error_msg');
            }
        } else {
            $status['code'] = 0;
            $status['message'] = "Invalid user id";
        }
        $result['status'] = $status;
        return $result;
    }

    public function updateUser($id) {
        $this->load(['id=?', $id]);
        $this->last_login_at = date('y-m-d h:i:s');
        $this->last_login_ip = $_SERVER['REMOTE_ADDR'];
        $this->save();
    }

    public function deleteUser($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
                $log = new LogModel();
                $stat = "User ID: " . $this->id . " has been deleted";
                $log->add($stat, 15);
                $status['code'] = 1;
                $status['message'] = 'User Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1451) ? "Deletion of this user is restricted." : $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid User Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function isAvailable($data): bool {
        $auth = new Auth($this, array('id'=>'phone_username','pw'=>'password'));
        return $auth->login($data['phone_username'], md5($data['password']));
    }

    public function findSalesmen(): array {
        $this->fields(['password', 'salt', 'last_password', 'sales_kpi_user_id', 'profile_user_id', 'attendance_user_id', 'password_changed_at'], true);
        $data = $this->afind(['role=?', 2], ['order'=>'id DESC'], 0, 3);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Salesman Successfully Fetched.';
            for($i=0;$i<count($data);$i++) {
                $info[$i] = $data[$i]['role']['user_role'][$i]['profile_user_id'];
                if($info[$i]!==null){
                    $info1['user_id'] = $info[$i]['user_id'];
                    $info1['name'] = $info[$i]['name'];
                    $salesman_info[] = $info1;
                }
            }
            $result['data'] = $salesman_info;
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Salesman Found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getSalesmanInfo($sid): ?array {
        $this->fields(['attendance_user_id','role','phone_username','password','salt','last_password','password_changed_at',
            'last_login_at','last_login_ip','account_status','sales_kpi_user_id.last_modified_at','sales_order_user_id'], true);
        $this->fields(['profile_user_id.id','profile_user_id.name','profile_user_id.user_id']);
        return $this->afind(['id=?',$sid],[],0,1);
    }

    public function getBranch($user_id) {
        $this->load(['id=?',$user_id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            return $data['profile_user_id']['branch_id'];
        } else {
            return 0;
        }
    }

    public function isValidId($user_id): bool {
        $this->load(['id=?',$user_id]);
        if($this->id) {
            return true;
        }
        return false;
    }

    public function getRole($id) {
        $this->load(['id=?',$id]);
        return $this->role;
    }

}