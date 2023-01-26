<?php

class BrandController extends MainController {

    public $image_name;

    public function index() {
        $brand = new BrandModel();
        $data = $brand->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function uploadImage() {
        $this->f3->set('UPLOADS','ui/images/brands/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->image_name = "brand_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->image_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $brand = new BrandModel();
            $result = $brand->createBrand($this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $brand->addImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/brands/'.$this->image_name);
                        $result['data']['logo_url']= 'https://nafisa.selopian.us/ui/images/brands/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $brand->deleteBrand($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add brands info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function read($f3, $params) {
        $brand = new BrandModel();
        $data = $brand->getBrand($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if($this->f3->exists('POST.create')) {
            $brand = new BrandModel();
            $result = $brand->updateBrand($params['id'], $this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadImage();
                    if($this->image_name!=''){
                        $brand->addImage($params['id'], 'https://nafisa.selopian.us/ui/images/brands/'.$this->image_name);
                        $result['data']['logo_url'] = 'https://nafisa.selopian.us/ui/images/brands/'.$this->image_name;
                    }
                } catch(PDOException $e) {
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't update brands info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function delete($f3, $params) {
        $brand = new BrandModel();
        $status = $brand->deleteBrand($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }
}