<?php

class UserProfileController extends MainController {

    public $profile_photo_url, $nid_photo_url;

    public function index() {
        $user = new UserProfileModel();
        $data = $user->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    protected function uploadImage() {
        $this->f3->set('UPLOADS','ui/images/users/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            if($formFieldName === 'profile_photo_url'){
                $file_data = pathinfo($fileBaseName);
                $this->profile_photo_url = "user_" . time() . rand(100,999) . "." . $file_data['extension'];
                return $this->profile_photo_url;
            }
        });
        $this->f3->set('UPLOADS','ui/images/nids/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            if($formFieldName === 'nid_photo_url'){
                $file_data = pathinfo($fileBaseName);
                $this->nid_photo_url = "nid_" . time() . rand(100,999) . "." . $file_data['extension'];
                return $this->nid_photo_url;
            }
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $user = new UserProfileModel();
            $result = $user->createProfile($this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadImage();
                    if($this->profile_photo_url!=''){
                        $user->addProfileImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/users/'.$this->profile_photo_url);
                        $result['data']['profile_photo_url']= 'https://nafisa.selopian.us/ui/images/users/'.$this->profile_photo_url;
                    }
                    if($this->nid_photo_url!=''){
                        $user->addNidPhotoImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/nids/'.$this->nid_photo_url);
                        $result['data']['nid_photo_url']= 'https://nafisa.selopian.us/ui/images/nids/'.$this->nid_photo_url;
                    }
                } catch(PDOException $e) {
                    $user->deleteProfile($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add user profile info.";
                }
            }
        header('Content-Type: application/json');
        echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function read($f3, $params) {
        $user = new UserProfileModel();
        $data = $user->getProfile($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function getSalesman() {
        $user = new UserProfileModel();
        $data = $user->getSalesmanProfile();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if($this->f3->exists('POST.create')) {
            $user = new UserProfileModel();
            $result = $user->updateProfile($params['id'], $this->f3->get('POST'));
            if($result['status']['code'] === 1){
                try {
                    $this->uploadImage();
                    if($this->profile_photo_url!=''){
                        $user->addProfileImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/users/'.$this->profile_photo_url);
                        $result['data']['profile_image_url']= 'https://nafisa.selopian.us/ui/images/users/'.$this->profile_photo_url;
                    }
                    if($this->nid_photo_url!=''){
                        $user->addNidPhotoImage($result['data']['id'], 'https://nafisa.selopian.us/ui/images/users/'.$this->nid_photo_url);
                        $result['data']['nid_image_url']= 'https://nafisa.selopian.us/ui/images/nids/'.$this->nid_photo_url;
                    }
                } catch(PDOException $e) {
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't update user profile info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            $this->f3->status(201);
        }
    }

    public function delete($f3, $params) {
        $user = new UserProfileModel();
        $status = $user->deleteProfile($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }

}