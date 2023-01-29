<?php

class SalesProductModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_id' => [
            'belongs-to-one' => '\SalesOrderModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
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
        foreach ($data as $item) {
            $this->sales_order_id = $sales_id;
            $this->product_id = $item['product_id'];
            $this->buying_price = $item['buying_price'];
            $this->selling_price = $item['selling_price'];
            $this->discount_amount = $item['discount_amount'];
            $this->amount_unit = $item['amount_unit'];
            if($this->validate()) {
                try {
                    $this->save();
                    $product_list[] = $this->cast(NULL, 0);
                    $this->reset();
                    $status['code'] = 1;
                    $status['message'] = 'Sales Product Successfully Added.';
                } catch(PDOException $e) {
                    $status['code'] = 0;
                    $status['message'] = $e->errorInfo[2];
                }
            } else {
                $status['code'] = 0;
                $status['message'] = Base::instance()->get('error_msg');
            }
        }
        return $product_list;
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

    /**
     * @throws Exception
     */
    public function updateSales($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->copyfrom($data);
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Sales Product Successfully Updated.';
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
            $status['message'] = 'Invalid Sales Product Id.';
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