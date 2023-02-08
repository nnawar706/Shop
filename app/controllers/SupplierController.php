<?php

class SupplierController extends MainController {

    public $image_name;

    public function index($f3, $params) {
        $supplier = new SupplierModel();
        $data = $supplier->getAll($params['page'], $params['limit']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function all() {
        $supplier = new SupplierModel();
        $data = $supplier->getAllSuppliers();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    protected function uploadImage() {
        $this->f3->set('UPLOADS','ui/images/suppliers/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->image_name = "supplier_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->image_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $supplier = new SupplierModel();
            $result = $supplier->createSupplier($this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $supplier->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->image_name);
                        $result['data']['profile_photo_url']= 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $supplier->deleteSupplier($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add suppliers info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function read($f3, $params) {
        $supplier = new SupplierModel();
        $data = $supplier->getSupplier($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if($this->f3->exists('POST.create')) {
            $supplier = new SupplierModel();
            $result = $supplier->updateSupplier($params['id'], $this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $supplier->addImage($params['id'], 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->image_name);
                        $result['data']['image_url']= 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $supplier->deleteSupplier($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't update suppliers info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function getAllByName($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $supplier = new SupplierModel();
                    $status = $supplier->getByName($data, $params['page'], $params['limit']);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(200);
                }
            }
        }
    }

    public function delete($f3, $params) {
        $supplier = new SupplierModel();
        $status = $supplier->deleteSupplier($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}