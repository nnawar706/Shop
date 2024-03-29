<?php

class ProductController extends MainController {

    public $image_name;

    public function index() {
        $product = new ProductModel();
        $data = $product->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function read($f3, $params) {
        $product = new ProductModel();
        $data = $product->getProduct($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    protected function uploadImage() {
        $this->f3->set('UPLOADS','ui/images/products/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->image_name = "product_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->image_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $product = new ProductModel();
            $result = $product->createProduct($this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $product->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/products/'.$this->image_name);
                        $result['data']['product_image_url']= 'https://nafisa.selopian.us/ui/images/products/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $product->deleteProduct($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add products info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if($this->f3->exists('POST.create')) {
            $product = new ProductModel();
            $result = $product->updateProduct($params['id'], $this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $product->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/products/'.$this->image_name);
                        $result['data']['product_image_url']= 'https://nafisa.selopian.us/ui/images/products/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $product->deleteProduct($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add products info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function delete($f3, $params) {
        $product = new ProductModel();
        $status = $product->deleteProduct($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}