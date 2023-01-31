<?php

class PurchaseProductModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_order_id' => [
            'belongs-to-one' => '\PurchaseOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'product_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ],
        'amount_unit' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'purchase_product');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createPurchase($data, $purchase_id): array {
        $product = new ProductModel();
        $this->db->begin();
        $status['id'] = $purchase_id;
        foreach ($data['product_name_list'] as $item) {
            $this->purchase_order_id = $purchase_id;
            $prod = $product->getProduct($item['product_id']);
            if($prod) {
                $this->product_id = $item['product_id'];
                $this->product_price = $prod['data']['cost_price'];
                $this->amount_unit = $item['amount_unit'];
                if($this->validate()) {
                    try {
                        $this->save();
                        $status['product_list'][] = $this->cast(NULL, 0);
                        $this->reset();
                        $status['code'][] = 1;
                        $status['message'][] = 'Purchase Product Successfully Added.';
                    } catch(PDOException $e) {
                        $status['code'][] = 0;
                        $status['message'][] = $e->errorInfo[2];
                    }
                } else {
                    $status['code'][] = 0;
                    $status['message'][] = Base::instance()->get('error_msg');
                }
            } else {
                $status['code'][] = 0;
                $status['message'][] = Base::instance()->get('error_msg');
            }
        }
        $this->db->commit();
        return $status;
    }
}