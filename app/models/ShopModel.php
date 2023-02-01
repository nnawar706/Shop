<?php

class ShopModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_profile_shop_id' => [
            'has-many' => ['\UserProfileModel','shop_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'branch_shop_id' => [
            'has-many' => ['\BranchModel','shop_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'filter' => 'trim',
            'validate' => 'required|||unique|||min_len,5|||max_len,50|||alpha_space'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'shop');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createShop($data): array {
        $this->name = $data['name'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $result['data']['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'Shop Successfully Added.';
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
        $this->fields(['user_profile_shop_id','branch_shop_id.location','branch_shop_id.geolocation',
            'branch_shop_id.inventory_branch_id','branch_shop_id.sales_order_branch_id',
            'branch_shop_id.inventory_trace_from_branch_id','branch_shop_id.inventory_trace_to_branch_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All shop successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No shop found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getShop($id): array {
        $this->fields(['user_profile_shop_id','branch_shop_id.location','branch_shop_id.geolocation',
            'branch_shop_id.inventory_branch_id','branch_shop_id.sales_order_branch_id',
            'branch_shop_id.inventory_trace_from_branch_id','branch_shop_id.inventory_trace_to_branch_id'], true);
        $data = [];
        $this->fields(['user_profile_shop_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $status['code'] = 1;
            $status['message'] = 'Shop Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Shop Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateShop($id, $data): array {
        $info = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $info['name'] = $this->name;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Shop Successfully Updated.';
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
            $status['message'] = 'Invalid Shop Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteShop($id): array {
        $data = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Shop Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1451) ? "Deletion of this shop is restricted." : $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Shop Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}