<?php

class SalesKpiModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_id' => [
            'belongs-to-one' => '\UserModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required',
        ],
        'target_sales_volume' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_kpi');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createSalesKpi($data): array {
        $this->user_id = $data['user_id'] ?? '';
        $this->target_sales_volume = $data['target_sales_volume'] ?? '';
        $this->last_modified_at = date('y-m-d h:i:s');
        if($this->validate()) {
            try {
                $this->save();
                $result = $this->getSalesKpi($this->id);
                $log = new LogModel();
                $stat = "Sales KPI ID: " . $this->id . " has been created.";
                $log->add($stat, 12);
                $status['code'] = 1;
                $status['message'] = 'Sales KPI Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1452) ? "This user does not exist." : (($e->errorInfo[1] == 1062) ? "Sales kpi for this user already exists." : $e->errorInfo[2]);
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $this->fields(['user_id.id','user_id.profile_user_id']);
        $this->fields(['user_id.profile_user_id.nid','user_id.profile_user_id.nid_photo_url','user_id.profile_user_id.profile_photo_url',
            'user_id.profile_user_id.designation_id','user_id.profile_user_id.salary','user_id.profile_user_id.department_id',
            'user_id.profile_user_id.shop_id','user_id.profile_user_id.nid_no','user_id.profile_user_id.ref_comment'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 2);
        if ($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All Salesman Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Salesman Found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getSalesKpi($id): array {
        $this->fields(['user_id.id','user_id.profile_user_id']);
        $this->fields(['user_id.profile_user_id.nid','user_id.profile_user_id.nid_photo_url','user_id.profile_user_id.profile_photo_url',
            'user_id.profile_user_id.designation_id','user_id.profile_user_id.salary','user_id.profile_user_id.department_id',
            'user_id.profile_user_id.shop_id','user_id.profile_user_id.nid_no','user_id.profile_user_id.ref_comment'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $result['data'] = $this->cast(NULL, 2);
            $status['code'] = 1;
            $status['message'] = 'Sales KPI Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales KPI Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getUserKpi($id): array {
        $this->fields(['user_id.id','user_id.profile_user_id']);
        $this->fields(['user_id.profile_user_id.nid','user_id.profile_user_id.nid_photo_url','user_id.profile_user_id.profile_photo_url',
            'user_id.profile_user_id.designation_id','user_id.profile_user_id.salary','user_id.profile_user_id.department_id',
            'user_id.profile_user_id.shop_id','user_id.profile_user_id.nid_no','user_id.profile_user_id.ref_comment'], true);
        $this->load(['user_id=?', $id]);
        if($this->id) {
            $result['data'] = $this->cast(NULL, 2);
            $status['code'] = 1;
            $status['message'] = 'User Sales KPI Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid user Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateSalesKpi($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->target_sales_volume = $data['target_sales_volume'] ?? '';
            $this->last_modified_at = date('y-m-d h:i:s');
            if($this->validate()) {
                try {
                    $this->save();
                    $result = $this->getSalesKpi($this->id);
                    $log = new LogModel();
                    $stat = "Sales KPI ID: " . $this->id . " has been updated.";
                    $log->add($stat, 12);
                    $status['code'] = 1;
                    $status['message'] = 'Sales KPI Successfully Updated.';
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
            $status['message'] = 'Invalid Sales KPI Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteSalesKpi($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
                $log = new LogModel();
                $stat = "Sales KPI ID: " . $this->id . " has been deleted.";
                $log->add($stat, 12);
                $status['code'] = 1;
                $status['message'] = 'Sales KPI Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales KPI Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}