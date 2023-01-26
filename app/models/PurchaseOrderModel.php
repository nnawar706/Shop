<?php

class PurchaseOrderModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_transaction_purchase_id' => [
            'has-one' => ['\PurchaseTransactionModel','purchase_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'status' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||max_len,50'
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

}