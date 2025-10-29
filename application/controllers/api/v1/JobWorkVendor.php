<?php
class JobWorkVendor extends MY_Apicontroller{
    
    public function __construct(){
        parent::__construct();
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $status = (isset($_REQUEST['status']) && !empty($_REQUEST['status']))?$_REQUEST['status']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'status'=>$status];
        $this->data['jobCardList'] = $this->jobWorkVendor_v2->getJobWorkVendorList($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function jobWorkReturnTransList($id){
        $transList = $this->jobWorkVendor_v2->getReturnTransaction($id);
        $this->data['transactionList'] = $transList['result'];
        $this->data['pending_qty'] = $transList['pending_qty'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function jobWorkReturnSave(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['qty'])):
            $errorMessage['qty'] = "Qty is required.";
        else:
            $pendingQty = $this->jobWorkVendor_v2->getJobWorkVendorRow($data['id'])->pending_qty;
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Qty not available for returned.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->jobWorkVendor_v2->saveJobWorkReturn($data);
            $result['transactionList'] = $this->jobWorkVendor_v2->getReturnTransaction($data['id'])['result'];
            unset($result['transHtml']);
            $this->printJson($result);
        endif;
    }

    public function deleteReturnTrans(){
        $data = $this->input->post();
        if(empty($data['id']) || $data['key'] == ""):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->jobWorkVendor_v2->deleteReturnTrans($data);
            $result['transactionList'] = $this->jobWorkVendor_v2->getReturnTransaction($data['id'])['result'];
            unset($result['transHtml']);
            $this->printJson($result);
        endif;
    }
}
?>