<?php

class PurchaseTransactionController extends MainController {

    public $doc_name;

    public function index() {
        $purchase = new PurchaseTransactionModel();
        $data = $purchase->getAll();
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
            $this->doc_name = "transaction_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->doc_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $purchase = new PurchaseTransactionModel();
            $result = $purchase->createPurchase($this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadDoc();
                    if($this->doc_name!=''){
                        $purchase->addDoc($result['data']['id'], 'https://nafisa.selopian.us/ui/images/transactions/'.$this->doc_name);
                        $result['data']['transaction_document_url']= 'https://nafisa.selopian.us/ui/images/transactions/'.$this->doc_name;
                    }
                } catch(PDOException $e) {
                    $purchase->deletePurchase($result['data']['id']);
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
        $purchase = new PurchaseTransactionModel();
        $data = $purchase->getPurchase($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

}