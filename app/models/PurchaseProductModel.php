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
        $total_amount = 0;
        foreach ($data['product_name_list'] as $item) {
            $this->purchase_order_id = $purchase_id;
            $prod = $product->getProduct($item['product_id']);
            if($prod['status']['code'] == 1) {
                $this->product_id = $item['product_id'];
                $total = $prod['data']['cost_price'] * $item['amount_unit'] - $item['discount_amount'];
                $this->product_price = $total;
                $this->amount_unit = $item['amount_unit'];
                $total_amount = $total_amount + $total;
                if($this->validate()) {
                    $this->save();
                    $status['product_product_list'][] = $this->cast(NULL, 0);
                    $this->reset();
//                    $status['code'][] = 1;
                }
                else {
                    $this->db->rollback();
//                    $status['code'][] = 0;
//                    $status['product_product_list'][] = Base::instance()->get('error_msg');
                }
            }
            else {
                $this->db->rollback();
//                $status['code'][] = 0;
//                $status['product_product_list'][] = "Product is not available";
            }
        }
        $order = new PurchaseOrderModel();
        $order->addTotalAmount($purchase_id, $total_amount);
        $status['total_amount'] = $total_amount;
        $status['purchase_order_id'] = $purchase_id;
        return $status;
    }
}
