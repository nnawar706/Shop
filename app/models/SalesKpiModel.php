<?php

class SalesKpiModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_id' => [
            'belongs-to-one' => '\UserModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required',
        ],
        'sales_volume' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||numeric'
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
        $info = [];
        $this->user_id = $data['user_id'] ?? '';
        $this->sales_volume = $data['sales_volume'] ?? '';
        $this->last_modified = date('y-m-d h:i:s');

        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'Sales KPI Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['data'] = $info;
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $data = $this->find([], []);
        $i = 0;
        if ($data) {
            foreach ($data as $item) {
                $info[$i]['user_id'] = $item->user_id->id;
                $info[$i]['name'] = $item->user_id->profile_user_id->name;
                $info[$i]['sales_volume'] = $item->sales_volume;
                $info[$i]['last_modified'] = $item->last_modified;
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

    public function getSalesKpi($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $data['id'] = $this->id;
            $data['user_id'] = $this->user_id->id;
            $data['name'] = $this->user_id->profile_user_id->name;
            $data['sales_volume'] = $this->sales_volume;
            $data['last_modified'] = $this->last_modified;
            $result['data'] = $data;
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
        $this->load(['user_id=?', $id]);
        if($this->id) {
            $data['id'] = $this->id;
            $data['user_id'] = $this->user_id->id;
            $data['name'] = $this->user_id->profile_user_id->name;
            $data['sales_volume'] = $this->sales_volume;
            $data['last_modified'] = $this->last_modified;
            $result['data'] = $data;
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
        $info = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->sales_volume = $data['sales_volume'] ?? '';
            $this->last_modified = date('y-m-d h:i:s');
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
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
        $result['data'] = $info;
        $result['status'] = $status;
        return $result;
    }

    public function deleteSalesKpi($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
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