<?php

class ProductFormulaIngredientsModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'formula_id' => [
            'belongs-to-one' => '\ProductFormulaModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'raw_mat_id' => [
            'belongs-to-one' => '\ProductRawMaterialModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'percentage' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'no_of_unit' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
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
    public function createFormulaIngredient($data, $formula_id) {
        foreach ($data['formula_ingredients_list'] as $item) {
            $this->formula_id = $formula_id;
            $this->raw_mat_id = $item['raw_mat_id'] ?? '';
            $this->percentage = $item['percentage'] ?? '';
            $this->no_of_unit = $item['no_of_unit'] ?? '';
            if($this->validate()) {
                $this->save();
                $this->reset();
            } else {
                $this->db->rollback();
            }
        }
    }
//jfwlnvwnvbwnvwnvwnwkgfwkgkrgge4
    public function getAll(): array {
        $this->fields(['raw_mat_id.id','raw_mat_id.name']);
        $this->fields(['formula_id.category_id','formula_id.product_formula_ingredients_formula_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
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