<?php
class ProcessModel extends MasterModel{
    private $processMaster = "process_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->processMaster;
        $data['select'] = "process_master.*,GROUP_CONCAT(department_master.name SEPARATOR ', ') as dept_name";
		$data['leftJoin']['department_master'] = "FIND_IN_SET(department_master.id, process_master.dept_id ) > 0";
		
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "department_master.name";
        $data['serachCol'][] = "process_master.remark";
        
        $data['group_by'][] = "process_master.id";
		
		$columns =array('','','process_master.process_name','','process_master.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getProcessList($data = array()){
        $data['tableName'] = $this->processMaster;
        if(!empty($data['process_ids'])):
            $data['where_in']['id'] = $data['process_ids'];
        endif;
        return $this->rows($data);
    }

    public function getDepartmentWiseProcess($id){
        $data['tableName'] = $this->processMaster;
        $data['customWhere'][] = 'find_in_set("'.$id.'", dept_id)';
        return $this->rows($data);
    }

    public function getProductProcess($postData){
        $data['tableName'] = 'product_process';
        $data['select'] = "TIME_TO_SEC(product_process.cycle_time) as master_ct,product_process.finished_weight";
        $data['where']['product_process.process_id'] = $postData['process_id'];
        $data['where']['product_process.item_id'] = $postData['item_id'];
        $result = $this->row($data);
        //print_r($this->printQuery());exit;
        return $result;
    }

    public function getProcess($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->processMaster;
        return $this->row($data);
    }

    public function getProcessDetail($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->processMaster;
        return $this->customRow($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['process_name'],$data['id']) > 0):
            $errorMessage['process_name'] = "Process name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->processMaster,$data,'Process');

            /** Process added in store */
            /*$process_id = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
            $strQuery['where']['ref_id'] = $process_id;
            $strQuery['tableName'] = 'location_master';
            $strResult = $this->row($strQuery);
            if (empty($strResult)) {
                $query['tableName'] = 'location_master';
                $query['select'] = "MAX(store_type) as store_type";
                $queryResult = $this->row($query);
                $store_type = ($queryResult->store_type < 10) ? 10 : $queryResult->store_type + 1;

                $storeData = [
                    'id' => '',
                    'store_name' => "Process",
                    'location' => $data['process_name'],
                    'store_type' => $store_type,
                    'ref_id' => $process_id
                ];
                $this->store->save($storeData);
            }*/

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

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->processMaster;
        $data['where']['process_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->processMaster,['id'=>$id],'Process');
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
        $data['tableName'] = $this->processMaster;
		
        return $this->numRows($data);
    }

    public function getProcessList_api($limit, $start){
        $data['tableName'] = $this->processMaster;
        $data['select'] = "process_master.*,department_master.name as dept_name";
		$data['join']['department_master'] = "process_master.dept_id = department_master.id";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//



    public function processMigration()
    {
        $processList = $this->process->getProcessList();
        if (!empty($processList)) {
            foreach ($processList as $row) {
                /** Process added in store */

                $strQuery['where']['ref_id'] = $row->id;
                $strQuery['tableName'] = 'location_master';
                $strResult = $this->row($strQuery);
                if (empty($strResult)) {
                    $query['tableName'] = 'location_master';
                    $query['select'] = "MAX(store_type) as store_type";
                    $queryResult = $this->row($query);
                    $store_type = ($queryResult->store_type < 10) ? 10 : $queryResult->store_type + 1;
                    $storeData = [
                        'id' => '',
                        'store_name' => "Process",
                        'location' => $row->process_name,
                        'store_type' => $store_type,
                        'ref_id' => $row->id
                    ];
                    $result=$this->store->save($storeData);
                }
            }
        }
        return true;
    }
}
?>