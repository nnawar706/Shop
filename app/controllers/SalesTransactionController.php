<?php

class SalesTransactionController extends MainController {

    public $file_name;

    public function index() {
        $sales = new SalesTransactionModel();
        $data = $sales->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    protected function uploadDoc() {
        $this->f3->set('UPLOADS','ui/images/transactions/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->file_name = "sales_transaction_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->file_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $sales = new SalesTransactionModel();
            $result = $sales->createTransaction($this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadDoc();
                    if($this->file_name!=''){
                        $sales->addFile($result['data']['id'], 'https://nafisa.selopian.us/ui/images/transactions/'.$this->file_name);
                        $result['data']['transaction_document_url']= 'https://nafisa.selopian.us/ui/images/transactions/'.$this->file_name;
                    }
                } catch(PDOException $e) {
                    $sales->deleteTransaction($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add transaction info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function read($f3, $params) {
        $sales = new SalesTransactionModel();
        $data = $sales->getSales($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }
}