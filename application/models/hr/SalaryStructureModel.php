<?php
class SalaryStructureModel extends MasterModel{
    private $salaryHeads = "salary_heads";
    private $ctcFormat = "ctc_format";


    public function getDTRows($data){
        $data['tableName'] = $this->ctcFormat;
        $data['select'] = "ctc_format.*,(CASE WHEN ctc_format.salary_duration = 'M' THEN 'Monthly' WHEN ctc_format.salary_duration = 'H' THEN 'Hourly' ELSE '' END) as salary_duration_text";
        $data['searchCol'][] = "format_name";
        $data['searchCol'][] = "format_no";
        $data['searchCol'][] = "(CASE WHEN ctc_format.salary_duration = 'M' THEN 'Monthly' WHEN ctc_format.salary_duration = 'H' THEN 'Hourly' ELSE '' END)";
        $data['searchCol'][] = "gratuity_days";
        $data['searchCol'][] = "gratuity_per";
        $data['searchCol'][] = "effect_from";
        
		$columns =array('','','format_name','format_no','salary_duration','gratuity_days','gratuity_per','effect_from');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getSalaryHeadDTRows($data){
        $data['tableName'] = $this->salaryHeads;
        $data['select'] = "salary_heads.*,(CASE WHEN salary_heads.type = 1 THEN 'Earning' WHEN salary_heads.type = -1 THEN 'Deduction' ELSE '' END) as type_text";
        $data['searchCol'][] = "head_name";
        $data['searchCol'][] = "(CASE WHEN salary_heads.type = 1 THEN 'Earning' WHEN salary_heads.type = -1 THEN 'Deduction' ELSE '' END)";
        
		$columns =array('','','salary_heads','type');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function nextFormatNo(){
        $data['select'] = "MAX(format_no) as format_no";
        $data['tableName'] = $this->ctcFormat;
		$format_no = $this->specificRow($data)->format_no;
		$formatNo = (!empty($format_no))?($format_no + 1):1;
		return $formatNo;
    }
    
    public function getCtcFormat($id){
        $data['tableName'] = $this->ctcFormat;
		$data['where']['id'] = $id;
        return $this->row($data);
    }
    
    public function save($data){
        try {
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['format_no'] = $this->nextFormatNo();
            endif;
            $result = $this->store($this->ctcFormat,$data,'CTC Structure');
            
            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function checkDuplicateCtcFormat($format_name,$id){
        $data['tableName'] = $this->ctcFormat;
        $data['where']['format_name'] = trim($format_name);
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }
    
    public function delete($id){
        try {
            $this->db->trans_begin();

            $result = $this->trash($this->ctcFormat,['id'=>$id],'CTC Structure');

            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }    

    public function getSalaryHead($id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getSalaryHeadList($data = array()){
        $queryData = array();
        $queryData['tableName'] = $this->salaryHeads;
        $queryData['select'] = "*";
        if(!empty($data['type'])):
            $queryData['where']['type'] = $data['type'];
        endif;
        if(isset($data['is_system'])):
            $queryData['where']['is_system'] = $data['is_system'];
        endif;
        if(!empty($data['ids'])):
            $queryData['where_in']['id'] = $data['ids'];
        endif;
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function saveSalaryHead($data){    
        try {
            $this->db->trans_begin();
            
            $result = $this->store($this->salaryHeads,$data,'Salary Head');
            
            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function checkDuplicateSalaryHead($head_name,$id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['head_name'] = trim($head_name);
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }
    
    public function deleteSalaryHead($id){
        try {
            $this->db->trans_begin();

            $queryData = array();
            $queryData['tableName'] = $this->ctcFormat;
            $queryData['customWhere'][] = "(find_in_set(".$id.",eh_ids) OR find_in_set(".$id.",dh_ids))";
            $checkResult = $this->rows($queryData);

            if(empty($checkResult)):
                $result = $this->trash($this->salaryHeads,['id'=>$id],'Salary Head');
            else:
                $result = ['status'=>0,'message'=>"Salary Head in use. You can not delete it."];
            endif;

            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getCtc($postData = []){
        $data['tableName'] = $this->ctcFormat;
        $data['select'] = 'ctc_format.*,salary_heads.head_name,salary_heads.parent_head,salary_heads.type,salary_heads.cal_type';
		if(!empty($postData['format_name'])){$data['where']['format_name'] = $postData['format_name'];}
        $data['leftJoin']['salary_heads'] = 'ctc_format.id = salary_heads.ctc_id';
        return $this->rows($data);
    }
    

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getCtcFormats($postData=array()){
        $data['tableName'] = $this->ctcFormat;
        if(!empty($postData['salary_duration'])):
            $data['where']['salary_duration'] = $postData['salary_duration'];
        endif;
        return $this->rows($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getCtcFromat($id){
        $data['tableName'] = $this->ctcFormat;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getSalaryHeadsOnCtcFormat($formate_id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['ctc_id'] = $formate_id;
        return $this->rows($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getProfessionTaxBaseOnGrossSalary($grossSalary){
        $queryData['tableName'] = "professional_tax";
        $queryData['where']['min_val <='] = $grossSalary;
        return $this->row($queryData);
    }

}
?>