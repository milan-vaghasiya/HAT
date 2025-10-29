<?php
class TaxMasterModel extends MasterModel{
    private $taxMaster = "tax_master";
    private $partyMaster = "party_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->taxMaster;
        $data['select'] = 'tax_master.*, (CASE WHEN tax_master.tax_type =1 THEN "Purchase" WHEN tax_master.tax_type =2 THEN "Sales" ELSE "" END) as tax_type_name,
        (CASE WHEN tax_master.calculation_type = 1 THEN "Amount" ELSE "Qty" END) as calc_type_name,
        (CASE WHEN tax_master.is_active =1 THEN "Active" WHEN tax_master.is_active =0 THEN "Inactive" ELSE "" END) as is_active_name,
        (CASE WHEN tax_master.add_or_deduct =1 THEN "Add" WHEN tax_master.add_or_deduct =-1 THEN "Deduct" ELSE "" END) as add_or_deduct_name';

        $data['leftJoin']['party_master'] = 'party_master.id = tax_master.acc_id';

        $data['searchCol'][] = "name";
        $data['searchCol'][] = "acc_name";
       
		$columns =array('','','name','tax_type','','','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getTaxMaster($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->taxMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data['name'],$data['tax_type'],$data['id']) > 0):
                $errorMessage['name'] = "Tax Name is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->taxMaster,$data,'Tax Master');
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

    public function checkDuplicate($name,$entryType,$id=""){
        $data['tableName'] = $this->taxMaster;
        $data['where']['name'] = $name;        
        $data['where']['tax_type'] = $entryType;        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->taxMaster,['id'=>$id],'Tax Master');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function getActiveTaxList($type = ""){
        $queryData = array();
        $queryData['tableName'] = $this->taxMaster;
        $queryData['where']['is_active'] = 1;
        if(!empty($type))
            $queryData['where']['tax_type'] = $type;
        
        $result = $this->rows($queryData);
        return $result;
    }
}
?>