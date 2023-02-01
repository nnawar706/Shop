<?php

class BranchModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'inventory_branch_id' => [
            'has-many' => ['\InventoryModel','branch_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'sales_order_branch_id' => [
            'has-many' => ['\SalesOrderModel','branch_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'inventory_trace_from_branch_id' => [
            'has-many' => ['\InventoryTraceModel','from_branch_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'inventory_trace_to_branch_id' => [
            'has-many' => ['\InventoryTraceModel','to_branch_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'shop_id' => [
            'belongs-to-one' => '\ShopModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required',
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha_space|||min_len,5|||max_len,30',
        ],
        'location' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,100',
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'branch');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createBranch($data): array {
        $this->name = $data['name'] ?? '';
        $this->shop_id = $data['shop_id'] ?? '';
        $this->location = $data['location'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $result = $this->getBranch($this->id);
                $status['code'] = 1;
                $status['message'] = 'Branch Successfully Added.';
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
        $this->fields(['inventory_branch_id', 'sales_order_branch_id','inventory_trace_from_branch_id',
            'inventory_trace_to_branch_id','shop_id.user_profile_shop_id','shop_id.branch_shop_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All branch successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No branch found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAllShop($id): int|array {
        $this->fields(['inventory_branch_id', 'sales_order_branch_id','inventory_trace_from_branch_id',
            'inventory_trace_to_branch_id','shop_id.user_profile_shop_id','shop_id.branch_shop_id'], true);
        $data = $this->afind(['shop_id=?', $id], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'branches under one shop are successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No branch found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getBranch($id): array {
        $this->fields(['inventory_branch_id', 'sales_order_branch_id','inventory_trace_from_branch_id',
            'inventory_trace_to_branch_id','shop_id.user_profile_shop_id','shop_id.branch_shop_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Branch Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Branch Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateBranch($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            $this->shop_id = $data['shop_id'] ?? '';
            $this->location = $data['location'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $result = $this->getBranch($this->id);
                    $status['code'] = 1;
                    $status['message'] = 'Branch Successfully Updated.';
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
            $status['message'] = 'Invalid Branch Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteBranch($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Branch Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1451) ? "Cannot delete this branch since it has inventories." : $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Branch Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}