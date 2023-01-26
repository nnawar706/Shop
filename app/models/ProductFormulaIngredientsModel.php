<?php

class ProductFormulaIngredientsModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'formula_id' => [
            'belongs-to-one' => '\ProductFormulaModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,4'
        ],
        'percentage' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,3'
        ],
        'unit_size' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,7'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'product_formula_ingredients');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createFormula($data): array {
        $this->copyfrom($data);
        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Product Formula Ingredient Successfully Added.';
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
        $this->fields(['formula_id.category_id.category_parent_id', 'formula_id.category_id.product_category_id','formula_id.category_id.product_formula_category_id','formula_id.category_id.featured','formula_id.category_id.parent_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 2);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Product Formula Ingredient successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Product Formula Ingredient found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getFormula($id): array {
        $this->fields(['formula_id.category_id.category_parent_id', 'formula_id.category_id.product_category_id','formula_id.category_id.product_formula_category_id','formula_id.category_id.featured','formula_id.category_id.parent_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 2);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Product Formula Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Formula Ingredient Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateFormula($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->copyfrom($data);
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Product Formula Ingredient Successfully Updated.';
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
            $status['message'] = 'Invalid Product Formula Ingredient Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteFormula($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Product Formula Ingredient Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Formula Ingredient Id.';
        }
        $result['status'] = $status;
        return $result;
    }

}