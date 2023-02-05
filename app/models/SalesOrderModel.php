<?php

class SalesOrderModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_product_sales_order_id' => [
            'has-many' => ['\SalesProductModel','sales_order_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'sales_transaction_sales_order_id' => [
            'has-one' => ['\SalesTransactionModel','sales_order_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'customer_id' => [
            'belongs-to-one' => '\CustomerModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'branch_id' => [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'user_id' => [
            'belongs-to-one' => '\UserModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'sales_type_id' => [
            'belongs-to-one' => '\SalesTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_order');
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
        $this->customer_id = $data['customer_id'] ?? '';
        $this->branch_id = $data['branch_id'] ?? '';
        $this->user_id = $data['user_id'] ?? '';
        $this->sales_type_id = $data['sales_type_id'] ?? '';
        $this->sold_at = date('y-m-d h:i:s') ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $products = new SalesProductModel();
                $info['data'] = $products->createSales($data, $this->id);
            } catch(PDOException $e) {
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

    public function getAll(): array {
        $this->fields(['customer_id.id','customer_id.name','branch_id.id','branch_id.name','user_id.id',
            'user_id.profile_user_id.user_id','user_id.profile_user_id.name','sales_type_id.id','sales_type_id.type']);
        $this->fields(['sales_transaction_sales_order_id','sales_product_sales_order_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 2);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Sales Order successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Sales Order found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getSales($id): array {
        $this->fields(['customer_id.id','customer_id.name','branch_id.id','branch_id.name','user_id.id',
            'user_id.profile_user_id.user_id','user_id.profile_user_id.name','sales_type_id.id','sales_type_id.type']);
        $this->fields(['sales_transaction_sales_order_id','sales_product_sales_order_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 2);
            $status['code'] = 1;
            $status['message'] = 'Sales Order Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Order Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateOrder($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->copyfrom($data);
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Sales Order Successfully Updated.';
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
            $status['message'] = 'Invalid Sales Order Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteOrder($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Sales Order Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Order Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function addTotalAmount($sales_id, $total_amount) {
        $this->load(['id=?',$sales_id]);
        $this->total_amount = $total_amount;
        $this->save();
    }

    public function getTotalAmount($sales_id): int {
        $this->load(['id=?',$sales_id]);
        if($this->id) {
            return $this->total_amount;
        } else {
            return 0;
        }
    }

    public function updatePaidAmount($sales_id, $amount_paid) {
        $this->load(['id=?',$sales_id]);
        $this->paid_amount = $amount_paid;
        $this->save();
    }

}