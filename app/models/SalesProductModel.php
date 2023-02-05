<?php

class SalesProductModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_id' => [
            'belongs-to-one' => '\SalesOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'buying_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'selling_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'discount_amount' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'amount_unit' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_product');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createSales($data, $sales_id): array {
        $product = new ProductModel();
        $inv = new InventoryModel();
        $status['sales_order_id'] = $sales_id;
        $total_amount = 0;
        foreach ($data['product_name_list'] as $item) {
            $this->sales_order_id = $sales_id;
            $prod = $product->getProduct($item['product_id']);
            if($prod['status']['code'] == 1) {
                if($data['sales_type_id'] == 1) {
                    $this->selling_price = $prod['data']['wholesale_price'];
                    $total = $prod['data']['wholesale_price'] * $item['amount_unit'] - $item['discount_amount'];
                } else {
                    $this->selling_price = $prod['data']['retail_price'];
                    $total = $prod['data']['retail_price'] * $item['amount_unit'] - $item['discount_amount'];
                }
                $total_amount = $total_amount + $total;
                $this->product_id = $item['product_id'];
                $this->buying_price = $prod['data']['cost_price'];
                $this->amount_unit = $item['amount_unit'] ?? 1;
                $this->discount_amount = $item['discount_amount'] ?? 0;
                if($this->validate()) {
                    $this->save();
                    $status['sales_product_list'][] = $this->cast(NULL, 0);
                    $inv->saleProduct($data['branch_id'], $item['product_id'], $item['amount_unit']);
                    $this->reset();
                } else {
                    $this->db->rollback();
                }
            } else {
                $this->db->rollback();
            }
        }
        $order = new SalesOrderModel();
        $order->addTotalAmount($sales_id, $total_amount);
        $status['total_amount'] = $total_amount;
        return $status;
    }

    public function getAll(): array {
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All sales product successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No sales product found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getSales($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'sales product Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid sales product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteSales($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Sales Product Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Product Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}