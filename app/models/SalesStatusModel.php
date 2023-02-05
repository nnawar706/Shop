<?php

class SalesStatusModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_transaction_sales_status_id' => [
            'has-many' => ['\SalesTransactionModel','sales_status_id'],
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'status' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||max_len,50|||min_len,5'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_status');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll(): array {
        $this->fields(['sales_transaction_sales_status_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All sales status successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No sales status found.';
        }
        $result['status'] = $status;
        return $result;
    }

}