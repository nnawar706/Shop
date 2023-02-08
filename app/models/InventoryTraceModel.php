<?php

class InventoryTraceModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'transfer_type_id' => [
            'belongs-to-one' => '\TransferTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'purchase_id' => [
            'belongs-to-one' => '\PurchaseOrderModel',
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'from_branch_id' => [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'to_branch_id'=> [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'from_supplier_id' => [
            'belongs-to-one' => '\SupplierModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'to_supplier_id' => [
            'belongs-to-one' => '\SupplierModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'product_quantity' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'inventory_trace');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll(): array {
        $this->fields(['purchase_id.id','product_id.id','product_id.name','from_branch_id.id','from_branch_id.name','to_branch_id.id','to_branch_id.name',
            'from_supplier_id.id','from_supplier_id.name','to_supplier.id','to_supplier_id.name']);
        $this->fields(['transfer_type_id.inventory_trace_transfer_type_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All data from inventory trace successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No inventory trace data found.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function createTrace($data, $id): array {
        foreach ($data['product_name_list'] as $item) {
            $this->transfer_type_id = 1;
            $this->purchase_id = $id;
            $this->product_id = $item['product_id'] ?? '';
            $this->product_quantity = $item['amount_unit'] ?? '';
            $this->from_supplier_id = $data['supplier_id'];
            $this->to_branch_id = $data['default_branch_id'] ?? 1;
            $this->event_time = date('y-m-d h:i:s');
            if($this->validate()) {
                $this->save();
                $status = $this->cast(NULL, 0);
                $this->reset();
                $log = new LogModel();
                $stat = "Product ID: " . $item['product_id'] . " has been purchased from supplier ID: " . $data['supplier_id'] . " at branch ID: " . $data['default_branch_id'];
                $log->add($stat, 9);
                $inventory = new InventoryModel();
                $status = $inventory->incrementStock($status);
            }
        }
        return $status;
    }

    /**
     * @throws Exception
     */
    public function transferStock($data): array {
        $this->transfer_type_id = 2;
        $this->product_id = $data['product_id'] ?? '';
        $this->product_quantity = $data['product_quantity'] ?? '';
        $this->from_branch_id = $data['from_branch_id'] ?? '';
        $this->to_branch_id = $data['to_branch_id'] ?? '';
        $this->event_time = date('y-m-d h:i:s');
        if($this->validate()) {
            try {
                $this->save();
                $status['data'] = $this->cast(NULL, 0);
                $log = new LogModel();
                $stat = "Product ID: " . $data['product_id'] . " has been transferred from branch ID: " . $data['from_branch_id'] . " to branch ID: " . $data['to_branch_id'];
                $log->add($stat, 10);
                $status['code'] = 1;
                $status['message'] = 'Stock Successfully transferred.';
            } catch (PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid data.';
        }
        return $status;
    }

    public function supplierChecker($data): bool{
        $this->load(['from_supplier_id=? AND to_branch_id=? AND product_id=?',$data['to_supplier_id'],$data['from_branch_id'],$data['product_id']]);
        if($this->id) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function returnStock($data): array {
        $this->transfer_type_id = 3;
        $this->purchase_id = $data['purchase_id'] ?? '';
        $this->product_id = $data['product_id'] ?? '';
        $this->product_quantity = $data['product_quantity'] ?? '';
        $this->from_branch_id = $data['from_branch_id'] ?? '';
        $this->to_supplier_id = $data['to_supplier_id'] ?? '';
        $this->event_time = date('y-m-d h:i:s');
        if($this->validate()) {
            try {
                $inventory = new InventoryModel();
                $result = $inventory->decrementStock($data);
                if($result['status'] == 1) {
                    $this->save();
                    $status['data'] = $this->cast(NULL, 0);
                    $log = new LogModel();
                    $stat = "Product ID: " . $data['product_id'] . " has been returned from branch ID: " . $data['from_branch_id'] . " to supplier ID: " . $data['to_supplier_id'];
                    $log->add($stat, 2);
                    $status['code'] = 1;
                    $status['message'] = 'Stock Successfully returned.';
                } else {
                    $status['code'] = $result['status'];
                    $status['message'] = $result['message'];
                }
            } catch (PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid data.';
        }
        return $status;
    }
}