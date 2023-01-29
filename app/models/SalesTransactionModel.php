<?php

class SalesTransactionModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_id' => [
            'belongs-to-one' => '\SalesOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'transaction_type_id' => [
            'belongs-to-one' => '\TransactionTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_transaction');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createTransaction($data): array {
        $this->sales_order_id = $data['sales_order_id'] ?? '';
        $this->transaction_type_id = $data['transaction_type_id'] ?? '';
        $this->amount_paid = $data['amount_paid'];
        $this->transaction_document_url = $data['transaction_document_url'] ?? '';
        $this->ref_comment = $data['ref_comment'];
        $this->transaction_at = date('y-m-d h:i:s');
        unset($data['submit']);

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Sales Transaction Successfully Added.';
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

    public function addFile($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->transaction_document_url = $fileName;
        $this->save();
    }

    public function deleteTransaction($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Sales Transaction Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid sales transaction Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}