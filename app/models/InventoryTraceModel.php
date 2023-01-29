<?php

class InventoryTraceModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'transfer_type_id' => [
            'belongs-to-one' => '\TransferTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'purchase_id' => [
            'belongs-to-one' => '\PurchaseOrderModel',
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
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
            'validate' => 'required|||integer|||max_len,5'
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
        $this->fields(['purchase_id.id','product_id.id','product_id.name','from_branch_id.id','from_branch_id.name','to_branch_id.id','to_branch_id.name','from_supplier_id.id','from_supplier_id.name','to_supplier.id','to_supplier_id.name']);
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
    public function createTrace($data): array {
        $this->copyfrom($data);
        $this->event_time = date('y-m-d h:i:s');

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Inventory trace Successfully Added.';
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
}