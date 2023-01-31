<?php

class CustomerController extends MainController {

    public $image_name;

    public function index($f3, $params) {
        $customer = new CustomerModel();
        $data = $customer->getAll($params['page'], $params['limit']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function all() {
        $customer = new CustomerModel();
        $data = $customer->getAllCustomers();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getAllByName($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $customer = new CustomerModel();
                    $status = $customer->getByName($data, $params['page'], $params['limit']);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    protected function uploadImage() {
        $this->f3->set('UPLOADS','ui/images/customers/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->image_name = "customer_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->image_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $customer = new CustomerModel();
            $result = $customer->createCustomer($this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $customer->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/customers/'.$this->image_name);
                        $result['data']['profile_photo_url']= 'https://nafisa.selopian.us/ui/images/customers/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $customer->deleteCustomer($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add customers info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function read($f3, $params) {
        $customer = new CustomerModel();
        $data = $customer->getCustomer($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if($this->f3->exists('POST.create')) {
            $customer = new CustomerModel();
            $result = $customer->updateCustomer($params['id'], $this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $customer->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/customers/'.$this->image_name);
                        $result['data']['profile_photo_url']= 'https://nafisa.selopian.us/ui/images/customers/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add customers info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function delete($f3, $params) {
        $customer = new CustomerModel();
        $status = $customer->deleteCustomer($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}