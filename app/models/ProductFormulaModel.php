<?php

class ProductFormulaModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'formula_ingredients_list' => [
            'has-many' => ['\ProductFormulaIngredientsModel','formula_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'category_id' => [
            'belongs-to-one' => '\CategoryModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,100|||min_len,5'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'product_formula');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createFormula($data): array {
        $this->db->begin();
        $this->name = $data['formula_name'] ?? '';
        $this->category_id = $data['category_id'] ?? '';
        if($this->validate()) {
            $this->save();
            $ingredient = new ProductFormulaIngredientsModel();
            $ingredient->createFormulaIngredient($data, $this->id);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Product Formula Successfully Added.';
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $this->db->commit();
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $this->fields(['formula_ingredients_list.formula_id.id','formula_ingredients_list.formula_id.name',
            'formula_ingredients_list.raw_mat_id.id','formula_ingredients_list.raw_mat_id.name']);
        $this->fields(['category_id.category_parent_id', 'category_id.product_category_id',
            'category_id.product_formula_category_id','category_id.description','category_id.featured','category_id.parent_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 2);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Product Formula successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Product Formula found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getFormula($id): array {
        $this->fields(['formula_ingredients_list.formula_id.id','formula_ingredients_list.formula_id.name',
            'formula_ingredients_list.raw_mat_id.id','formula_ingredients_list.raw_mat_id.name']);
        $this->fields(['category_id.category_parent_id', 'category_id.product_category_id',
            'category_id.product_formula_category_id','category_id.description','category_id.featured','category_id.parent_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 2);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Product Formula Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Formula Id.';
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
                    $info = $this->getFormula($id);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Product Formula Successfully Updated.';
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
            $status['message'] = 'Invalid Product Formula Id.';
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
                $status['message'] = 'Product Formula Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Formula Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getByCategory($id): array {
        $data = $this->afind(['category_id=?',$id], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Product Formula successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Product Formula found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;

    }

}