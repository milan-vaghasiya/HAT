<?php
class ProductionOperationModel extends MasterModel{
    private $productionOperation = "production_operation";
	private $productProcess = "product_process";

    public function getDTRows($data){
        $data['tableName'] = $this->productionOperation;
        $data['searchCol'][] = "operation_name";
		$columns =array('','','operation_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getOperationList(){
        $data['tableName'] = $this->productionOperation;
        return $this->rows($data);
    }

    public function getOperation($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->productionOperation;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $data['operation_name'] = trim($data['operation_name']);
        if($this->checkDuplicate($data['operation_name'],$data['id']) > 0):
            $errorMessage['operation_name'] = "Operation Name is duplicate.";
            $result= ['status'=>0,'message'=>$errorMessage];
        else:
            $result= $this->store($this->productionOperation,$data,'Production Operation');
        endif;
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function checkDuplicate($operation,$id=""){
        $data['tableName'] = $this->productionOperation;
        $data['where']['operation_name'] = $operation;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->productionOperation,['id'=>$id],'Production Operation');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @27-11-2021 3:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->productionOperation;
        return $this->numRows($data);
    }

    public function getProductionOperationList_api($limit, $start){
        $data['tableName'] = $this->productionOperation;

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>