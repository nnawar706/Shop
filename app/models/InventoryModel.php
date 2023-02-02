<?php

class InventoryModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'notification_inventory_id' => [
            'has-many' => ['\NotificationModel','inventory_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'branch_id' => [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'stock_amount' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,11|||integer'
        ],
        'min_stock_alert' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,3|||integer'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'inventory');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createInventory($data): array {
        $this->product_id = $data['product_id'] ?? '';
        $this->branch_id = $data['to_branch_id'] ?? '';
        $this->stock_amount = $data['product_quantity'] ?? '';
        $this->min_stock_alert = $data['min_stock_alert'] ?? 2;
        if ($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Inventory Successfully Added.';
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
        $this->fields(['product_id.id','product_id.name','product_id.category_id.category_id','product_id.category_id.name']);
        $this->fields(['notification_inventory_id','branch_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 2);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All data from inventory successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No inventory data found.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateMinStockAlert($data): array {
        $product_id = $data['product_id'] ?? '';
        $branch_id = $data['branch_id'] ?? '';
        $this->load(['product_id=? AND branch_id=?', $product_id, $branch_id]);
        if($this->id) {
            $this->min_stock_alert = $data['min_stock_alert'];
            if($this->validate()) {
                $this->save();
                $data = $this->cast(NULL, 0);
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Minimum stock alert updated.';
            } else {
                $status['code'] = 0;
                $status['message'] = Base::instance()->get('error_msg');
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid product or branch Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function decrementStock($data): array {
        $product_id = $data['product_id'] ?? '';
        $branch_id = $data['from_branch_id'] ?? '';
        $quantity = $data['product_quantity'] ?? '';
        $this->load(['product_id=? AND branch_id=?', $product_id, $branch_id]);
        if($this->id) {
            $stock = $this->stock_amount;
            $stock_updated = $stock - $quantity;
            if($stock_updated >= 0) {
                $this->stock_amount = $stock_updated;
                $this->save();
                $info['message'] = "Stock updated";
                $info['status'] = 1;
            } else {
                $info['message'] = "Insufficient amount of product available";
                $info['status'] = 0;
            }
        } else {
            $info['message'] = "The product is not available in this branch.";
            $info['status'] = 0;
        }
        return $info;
    }

    /**
     * @throws Exception
     */
    public function incrementStock($data): array {
        $product_id = $data['product_id'] ?? '';
        $branch_id = $data['to_branch_id'] ?? '';
        $product_quantity = $data['product_quantity'] ?? '';
        $this->load(['product_id=? AND branch_id=?', $product_id, $branch_id]);
        if($this->id) {
            $stock = $this->stock_amount;
            $stock_updated = $stock + $product_quantity;
            $this->stock_amount = $stock_updated;
            $this->save();
            $this->reset();
            $info['message'] = "Stock updated";
            $info['status'] = 1;
        } else {
            $info = $this->createInventory($data);
        }
        return $info;
    }

    public function saleProduct($branch, $product, $amount) {
        $this->load(['product_id=? AND branch_id=?', $product, $branch]);
        $stock = $this->stock_amount;
        $stock_updated = $stock - $amount;
        $this->stock_amount = $stock_updated;
        $this->save();
    }

    public function checkEligibility($data): array {
        foreach ($data['product_name_list'] as $item) {
            $this->load(['product_id=? AND branch_id=?',$item['product_id'],$data['branch_id']]);
            if($this->id){
                if($this->stock_amount - $item['amount_unit'] >= 0) {
                    $status['code'][] = 1;
                    $status['message'][] = "available in stock";
                } else {
                    $status['code'][] = 0;
                    $status['message'][] = "insufficient amount of product";
                }
            } else {
                $status['code'][] = 0;
                $status['message'][] = "product is not available in this branch";
            }
        }
        return $status;
    }

    public function alertChecker(): ?array{
        return $this->afind(['stock_amount<=min_stock_alert AND flag_stock_alert=0'], [], 0, 0);
    }

    public function updateFlag($id, $flag) {
        $this->load(['id=?',$id]);
        if($this->id){
            $this->flag_stock_alert = $flag;
            $this->save();
        }
    }

}