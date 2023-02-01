<?php

class UserProfileModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_id' => [
            'belongs-to-one' => '\UserModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'filter' => 'trim',
            'validate' => 'required|||min_len,5|||max_len,128|||alpha_space'
        ],
        'nid_no' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||unique|||max_len,20'
        ],
        'designation_id' => [
            'belongs-to-one' => '\UserDesignationModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'department_id' => [
            'belongs-to-one' => '\DepartmentModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'salary' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||numeric|||max_len,7'
        ],
        'shop_id' => [
            'belongs-to-one' => '\ShopModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'ref_comment' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR256,
            'validate' => 'required'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'user_profile');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createProfile($data): array {
        $this->user_id = $data['user_id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->nid_no = $data['nid_no'] ?? '';
        $this->nid_photo_url = $data['nid_photo_url'] ?? '';
        $this->profile_photo_url = $data['profile_photo_url'] ?? 'https://nafisa.selopian.us/ui/images/users/user0.png';
        $this->designation_id = $data['designation_id'] ?? '';
        $this->salary= $data['salary'] ?? '';
        $this->department_id = $data['department_id'] ?? '';
        $this->shop_id = $data['shop_id'] ?? '';
        $this->ref_comment = $data['ref_comment'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $result = $this->getProfile($this->id);
                $log = new LogModel();
                $stat = "User profile ID: " . $this->id . " has been created for user ID: ." . $data['user_id'];
                $log->add($stat, 13);
                $status['code'] = 1;
                $status['message'] = 'User profile Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1452) ? "Invalid input data." : (($e->errorInfo[1] == 1062) ? "User profile for this user already exists." : $e->errorInfo[1]);
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateProfile($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id){
            $this->user_id = $data['user_id'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->nid_no = $data['nid_no'] ?? '';
            $this->designation_id = $data['designation_id'] ?? '';
            $this->salary= $data['salary'] ?? '';
            $this->department_id = $data['department_id'] ?? '';
            $this->shop_id = $data['shop_id'] ?? '';
            $this->ref_comment = $data['ref_comment'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $result = $this->getProfile($this->id);
                    $log = new LogModel();
                    $stat = "User profile ID: " . $this->id . " has been updated";
                    $log->add($stat, 13);
                    $status['code'] = 1;
                    $status['message'] = 'User profile Successfully Updated.';
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
            $status['message'] = 'Invalid user profile id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $this->fields(['user_id.id','user_id.phone_username']);
        $this->fields(['designation_id.user_profile_designation_id','department_id.user_profile_department_id','shop_id.user_profile_shop_id','shop_id.branch_shop_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All user profile successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No user profile found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function addProfileImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->profile_photo_url = $fileName;
        $this->save();
    }

    public function addNidPhotoImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->nid_photo_url = $fileName;
        $this->save();
    }

    public function getProfile($id): array {
        $this->fields(['user_id.id','user_id.phone_username']);
        $this->fields(['designation_id.user_profile_designation_id','department_id.user_profile_department_id','shop_id.user_profile_shop_id','shop_id.branch_shop_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'User profile successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid user profile Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteProfile($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
                $log = new LogModel();
                $stat = "User profile ID: " . $this->id . " has been deleted";
                $log->add($stat, 13);
                $status['code'] = 1;
                $status['message'] = 'User Profile Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid user profile Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getSalesmanProfile(): array {
        $data = $this->afind(['designation_id=?', 2]);
        $i = 0;
        if ($data) {
            foreach ($data as $item) {
                $info[$i]['user_id'] = $item['user_id']['id'];
                $info[$i]['name'] = $item['name'];
                $i = $i + 1;
            }
            $result['data'] = $info;
            $status['code'] = 1;
            $status['message'] = 'All Salesman Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Salesman Found.';
        }
        $result['status'] = $status;
        return $result;
    }
}