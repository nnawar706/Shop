<?php

class PurchaseTransactionModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_id' => [
            'belongs-to-one' => '\PurchaseOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'transaction_type_id' => [
            'belongs-to-one' => '\TransactionTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'payment_status_id' => [
            'belongs-to-one' => '\PurchaseStatusModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'amount_paid' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,7'
        ],
        'ref_comment' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR512,
            'validate' => 'max_len,500'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'purchase_transaction');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createPurchase($data): array {
        $this->purchase_id = $data['purchase_id'] ?? '';
        $this->transaction_type_id = $data['transaction_type_id'] ?? '';
        $this->amount_paid = $data['amount_paid'] ?? '';
        $this->transaction_document_url = $data['transaction_document_url'] ?? '';
        $this->ref_comment = $data['ref_comment'] ?? '';
        $this->payment_status_id = $data['payment_status_id'];
        $this->transaction_at = date('y-m-d h:i:s');
        if($this->validate()) {
            try {
                $this->save();
                $amount = new PurchaseOrderModel();
                $amount->updatePaidAmount($data['amount_paid'], $data['purchase_id']);
                $result['data'] = $this->cast(NULL, 0);
                $status['code'] = 1;
                $status['message'] = 'Purchase Transaction Successfully Added.';
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

    public function getAll(): array {
        $this->fields(['purchase_id.id']);
        $this->fields(['transaction_type_id.sales_transaction_transaction_type_id','transaction_type_id.purchase_transaction_transaction_type','payment_status_id.purchase_transaction_payment_status_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Purchase transaction successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Purchase transaction found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function addDoc($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->transaction_document_url = $fileName;
        $this->save();
    }

    public function getPurchase($id): array {
        $this->fields(['purchase_id.id']);
        $this->fields(['transaction_type_id.sales_transaction_transaction_type_id','transaction_type_id.purchase_transaction_transaction_type','payment_status_id.purchase_transaction_payment_status_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Purchase transaction Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Purchase transaction Id.';
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
                $status['message'] = 'Purchase transaction Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Purchase transaction Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}