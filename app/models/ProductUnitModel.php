<?php

class ProductUnitModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'product_unit_type' => [
            'has-many' => ['\ProductModel','unit_type'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'product_raw_material_product_unit_id' => [
            'has-many' => ['\ProductRawMaterialModel','product_unit_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha|||max_len,50',
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'product_unit');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createProductUnit($data): array {
        $this->name = $data['name'] ?? '';

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Product unit Successfully Added.';
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
        $this->fields(['product_unit_type'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All product unit successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No product unit found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getProductUnit($id): array {
        $this->fields(['product_unit_type'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Product Unit Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid product unit Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateProductUnit($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Product Unit Successfully Updated.';
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
            $status['message'] = 'Invalid Product Unit Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteProductUnit($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Product Unit Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Unit Id.';
        }
        $result['status'] = $status;
        return $result;
    }

}