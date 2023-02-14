<?php

class PurchaseOrderModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_transaction_purchase_id' => [
            'has-one' => ['\PurchaseTransactionModel','purchase_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'product_list' => [
            'has-many' => ['\PurchaseProductModel','purchase_order_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'inventory_trace_purchase_id' => [
            'has-many' => ['\InventoryTraceModel','purchase_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'default_branch_id' => [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'supplier_id' => [
            'belongs-to-one' => '\SupplierModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'supply_schedule' => [
            'type' => \DB\SQL\Schema::DT_TIMESTAMP,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'purchase_order');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createOrder($data): array {
        $this->db->begin();
        $this->default_branch_id = $data['branch_id'] ?? 1;
        $this->supplier_id = $data['supplier_id'] ?? '';
        $this->purchased_at = date('y-m-d h:i:s');
        $this->supply_schedule = $data['supply_schedule'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $products = new PurchaseProductModel();
                $info['data'] = $products->createPurchase($data, $this->id);
                $trace = new InventoryTraceModel();
                $status = $trace->createTrace($data, $this->id);
                $info['status']['code'] = 1;
                $info['status']['message'] = "Request successful";
            } catch (PDOException $e) {
                $info['status']['code'] = 0;
                $info['status']['message'] = 'Invalid data';
            }
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = 'Invalid data';
        }
        $this->db->commit();
        return $info;
    }

    public function getPurchase($id): array {
        $this->fields(['default_branch_id.id','default_branch_id.name','supplier_id.id','supplier_id.name']);
        $this->fields(['purchase_transaction_purchase_id','inventory_trace_purchase_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Purchase Order Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Purchase Order Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $this->fields(['default_branch_id.id','default_branch_id.name','supplier_id.id','supplier_id.name']);
        $this->fields(['purchase_transaction_purchase_id','inventory_trace_purchase_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All Purchase Order successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Purchase Order found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deletePurchase($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Purchase Product Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Purchase Product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function addTotalAmount($purchase_id, $total_amount) {
        $this->load(['id=?',$purchase_id]);
        $this->total_amount = $total_amount;
        $this->save();
    }

    public function updatePaidAmount($amount_paid, $purchase_id) {
        $this->load(['id=?',$purchase_id]);
        $this->paid_amount = $amount_paid;
        $this->save();
    }

    public function getOrders($data, $sid): ?array {
        $this->fields(['supplier_id','purchase_transaction_purchase_id','total_amount','paid_amount','inventory_trace_purchase_id'], true);
        $this->fields(['default_branch_id.id','default_branch_id.name']);
        $rows = $this->afind(['date(purchased_at)>=? AND date(purchased_at)<=? AND supplier_id=?',$data['from'], $data['to'],$sid],['order'=>'id DESC']);
        if($rows) {
            return $rows;
        } else {
            return null;
        }
    }

    public function getTotalDueAndPaid($data, $sid): array {
        $due = 0;
        $paid = 0;
        $total = 0;
        $rows = $this->afind(['date(purchased_at)>=? AND date(purchased_at)<=? AND supplier_id=?',$data['from'], $data['to'],$sid]);
        if($rows) {
            foreach ($rows as $item) {
                $due = $due + ($item['total_amount'] - $item['paid_amount']);
                $paid = $paid + $item['paid_amount'];
                $total = $total + $item['total_amount'];
            }
        }
        $data['due'] = $due;
        $data['paid'] = $paid;
        $data['total'] = $total;
        return $data;
    }

    public function getTotalOrders($data, $sid): int {
        $this->load(['date(purchased_at)>=? AND date(purchased_at)<=? AND supplier_id=?',$data['from'], $data['to'],$sid]);
        return $this->loaded();
    }

    public function completedOrders($data, $sid): int {
        $this->load(['total_amount=paid_amount AND date(purchased_at)>=? AND date(purchased_at)<=? AND supplier_id=?',$data['from'],$data['to'],$sid]);
        return $this->loaded();
    }

    public function getTotalCost($data, $sid): int {
        $from = $data['from'];
        $to = $data['to'];
        $result = $this->db->exec("SELECT SUM(total_amount) AS total FROM purchase_order WHERE supplier_id='" . $sid . "' AND DATE(purchased_at)>='" . $from . "' AND date(purchased_at)<='" . $to . "'");
        return intval($result[0]['total']);
    }

    public function getProductList($data, $sid)
    {
        $from = $data['from'];
        $to = $data['to'];
        return $this->db->exec("select distinct(name) from product join purchase_product on product.id=purchase_product.product_id
    join purchase_order on purchase_product.purchase_order_id=purchase_order.id where purchase_order.supplier_id='" . $sid . "' AND DATE(purchased_at)>='" . $from . "' AND date(purchased_at)<='" . $to . "'");
    }
}
