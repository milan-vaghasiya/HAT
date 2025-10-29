<?php
class Store extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function locationList(){
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }
}
?>