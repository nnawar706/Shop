<?php

class SupplierModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'inventory_trace_from_supplier_id' => [
            'has-many' => ['\InventoryTraceModel','from_supplier_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'inventory_trace_to_supplier_id' => [
            'has-many' => ['\InventoryTraceModel','to_supplier_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha|||min_len,5|||max_len,100'
        ],
        'email' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'unique|||valid_email'
        ],
        'phone_no' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||regex,/^(?:\+88|88)?(01[3-9]\d{8})$/'
        ],
        'address' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||max_len,100'
        ],
        'company_name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'alpha_dash|||max_len,50'
        ],
        'company_phone_no' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'regex,/^(?:\+88|88)?(01[3-9]\d{8})$/'
        ],
        'company_address' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'max_len,100'
        ],
        'bank_account_info' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR256,
            'validate' => 'max_len,200'
        ],
        'ref_comment' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR512,
            'validate' => 'max_len,500'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'supplier');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createSupplier($data): array {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_no = $data['phone_no'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->company_name = $data['company_name'] ?? '';
        $this->company_phone_no = $data['company_phone_no'] ?? '';
        $this->company_address = $data['company_address'] ?? '';
        $this->bank_account_info = $data['bank_account_info'] ?? '';
        $this->ref_comment = $data['ref_comment'] ?? '';
        $this->profile_photo_url = $data['profile_photo_url'] ?? 'https://nafisa.selopian.us/ui/images/suppliers/supplier_img.png';
        unset($data['submit']);

        if($this->validate()) {
            try {
                $this->save();
                $result = $this->getSupplier($this->id);
                $log = new LogModel();
                $stat = "Supplier ID: " . $this->id . " has been created";
                $log->add($stat, 14);
                $status['code'] = 1;
                $status['message'] = 'Supplier Successfully Added.';
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

    /**
     * @throws Exception
     */
    public function updateSupplier($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->phone_no = $data['phone_no'] ?? '';
            $this->address = $data['address'] ?? '';
            $this->company_name = $data['company_name'] ?? '';
            $this->company_phone_no = $data['company_phone_no'] ?? '';
            $this->company_address = $data['company_address'] ?? '';
            $this->bank_account_info = $data['bank_account_info'] ?? '';
            $this->ref_comment = $data['ref_comment'] ?? '';
            $this->blacklist = $data['blacklist'] ?? 0;
            unset($data['submit']);

            if($this->validate()) {
                try {
                    $this->save();
                    $result = $this->getSupplier($this->id);
                    $log = new LogModel();
                    $stat = "Supplier ID: " . $this->id . " has been updated";
                    $log->add($stat, 14);
                    $status['code'] = 1;
                    $status['message'] = 'Supplier Successfully Updated.';
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
            $status['message'] = 'Invalid Supplier ID.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAll($pageno, $perPage): array {
        $offset = ($pageno - 1) * $perPage;
        $totalRecords = $this->db->exec("SELECT COUNT(*) FROM suppliers")[0]['COUNT(*)'];
        $totalPages = ceil($totalRecords/$perPage);
        $status['total pages'] = $totalPages;
        $status['current page'] = (int)$pageno;
        $result['status'] = $status;
        $result['data'] = $this->afind([], ['limit'=>$perPage, 'offset'=>$offset], 0, 0);
        return $result;
    }

    public function getSupplier($id): array {
        $this->fields(['inventory_trace_from_supplier_id','inventory_trace_to_supplier_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Supplier Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Supplier Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteSupplier($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
                $log = new LogModel();
                $stat = "Supplier ID: " . $this->id . " has been deleted";
                $log->add($stat, 14);
                $status['code'] = 1;
                $status['message'] = 'Supplier Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1451) ? "Deletion of this supplier is restricted." : $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Supplier Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAllSuppliers(): array {
        $this->fields(['inventory_trace_from_supplier_id','inventory_trace_to_supplier_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All suppliers successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No suppliers found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function addImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->profile_photo_url = $fileName;
        $this->save();
    }

    public function getByName($data, $pageno, $perPage): array {
        $name = $data['name'];
        $offset = ($pageno - 1) * $perPage;
        $result['data'] = $this->afind(['name=?',$name], ['limit'=>$perPage, 'offset'=>$offset], 0, 0);
        return $result;
    }

    public function getAllIds()
    {
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        foreach ($data as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

}