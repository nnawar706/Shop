<?php

class SalesTypeModel extends \DB\Cortex
{
    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_sales_type_id' => [
            'has-many' => ['\SalesOrderModel','sales_type_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'type' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha_space|||max_len,50'
        ]
    ];


    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_type');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createType($data): array {
        $this->type = $data['type'] ?? '';
        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Sales Type Successfully Added.';
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
        $this->fields(['sales_order_sales_type_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Sales Type successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Sales Type found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getType($id): array {
        $this->fields(['sales_order_sales_type_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Sales Type Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateType($id, $data): array {
        $this->fields(['sales_order_sales_type_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->type = $data['type'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Sales Type Successfully Updated.';
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
            $status['message'] = 'Invalid Sales Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteType($id): array {
        $data = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'Sales Type Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Type Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

}