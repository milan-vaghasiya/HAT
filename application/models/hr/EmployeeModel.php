<?php
class EmployeeModel extends MasterModel{
    private $empMaster = "employee_master";
    private $designation = "emp_designation";
    private $empSalary = "emp_salary_detail";
    private $empDocs = "emp_docs_detail";
    private $empNom = "emp_nomination_detail";
    private $empEdu = "emp_education_detail";
    private $leaveAuthority = "leave_authority";
    private $empDocuments = "emp_docs";
    private $empCtc = "emp_salary";
    private $staffSkill = "staff_skill";
    private $facilityMaster = "facility_master";
    private $empFacility = "emp_facility";
    private $attendanceLog = "attendance_log";
    
    public function getDTRows($data){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name as dept_name,emp_designation.title as emp_designation,shift_master.shift_name,emp_category.category as emp_category";
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
        $data['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $data['where']['employee_master.emp_role !='] = "-1";
		if($data['status']==0){$data['where']['employee_master.is_active']=1;}
        else{$data['where']['employee_master.is_active']=0;}
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "shift_master.shift_name";
        $data['searchCol'][] = "employee_master.emp_contact";
        
		$columns =array('','','employee_master.emp_name','employee_master.emp_code','department_master.name','emp_designation.title','shift_master.shift_name','employee_master.emp_contact');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }


    public function getEmpList($postData = array()){
        $queryData = array();
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name,department_master.name";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        
        if(isset($postData['emp_role'])):
            $queryData['customWhere'][] = "employee_master.emp_role NOT IN (".$postData['emp_role'].")";
        else:
            $queryData['customWhere'][] = "employee_master.emp_role NOT IN (-1,1)";
        endif;
        if(isset($postData['is_active'])):
            $queryData['where_in']['employee_master.is_active'] = $postData['is_active'];
        else:
            $queryData['where']['employee_master.is_active'] = 1;
        endif;
        if(!empty($postData['biomatric_id'])){$queryData['where']['employee_master.biomatric_id'] = $postData['biomatric_id'];}
		//else{$queryData['where']['employee_master.biomatric_id > '] = 0;}
        if(!empty($postData['emp_grade'])){$queryData['where_in']['employee_master.emp_grade'] = $postData['emp_grade'];}
		return $this->rows($queryData);
    }

    public function getEmp($id){
        $data['where']['id'] = $id;
        //$data['where']['is_active'] = 1;
        $data['tableName'] = $this->empMaster;
        return $this->row($data);
    }
    
    public function getEmpByCode($emp_code,$is_active='1'){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.id, employee_master.emp_code, department_master.name as dept_name, emp_designation.title as emp_designation";
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_code'] = $emp_code;
        $data['where_in']['is_active'] = $is_active;
        return $this->row($data);
    }
    
    // Get Birthday List
	public function getEmpTodayBirthdayList($postdata=[]){
	    $queryData = array();
	    $queryData['tableName'] = $this->empMaster;
	    $queryData['select'] = "employee_master.id, employee_master.emp_code,employee_master.emp_name,employee_master.emp_contact, department_master.name as dept_name, emp_designation.title as emp_dsg, emp_category.category";
	    $queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $queryData['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $queryData['where']['employee_master.emp_role !='] = "-1";
        $queryData['where']['employee_master.is_active'] = 1;
        if(!empty($postdata['dob'])){$queryData['where']['DATE_FORMAT(employee_master.emp_birthdate,"%d-%m")'] = date("d-m",strtotime($postdata['dob']));}
        else{$queryData['where']['DATE_FORMAT(employee_master.emp_birthdate,"%d-%m")'] = date("d-m");}
        return $this->rows($queryData);
	}
    
    public function getsalesExecutives(){
        $data['where']['emp_designation'] = 8;
        $data['where']['is_active'] = 1;
        $data['where']['employee_master.id !='] = 1;
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }
	
    public function getEmployeeListByRole(){
        $data['tableName'] = $this->empMaster;
        $data['where']['is_active'] = 1;
        if(!in_array($this->userRole,[1,-1,7])){$data['where']['id'] = $this->loginID;}
        $data['where']['employee_master.id !='] = 1;
        return $this->rows($data);
    }
	
    public function getEmpListV1($postData=[]){
        $data['tableName'] = $this->empMaster;
        $data['where_in']['is_active'] = '0,1';
        $data['where']['attendance_status'] = 1;
        $data['where']['employee_master.id !='] = 1;
        if(!empty($postData['emp_id'])){
            $data['where']['employee_master.id'] = $postData['emp_id'];
            $data['where_in']['is_active'] = '1';
        }
        if(!empty($postData['is_active'])){$data['where_in']['is_active'] = $postData['is_active'];}
        return $this->rows($data);
    }
	
    public function getEmployeeList($deleted = ''){
        $data['tableName'] = $this->empMaster;
        $data['where_in']['is_active'] = '0,1';
        $data['where']['employee_master.id !='] = 1;
        if(!empty($deleted)){$data['all']['employee_master.is_delete'] = '0,1';}
        return $this->rows($data);
    }

	/*** Created By JP @09-12-2022 ***/
	public function getEmpListForReport($postData = [])
	{
		$empQuery['select'] = "employee_master.id, employee_master.emp_code,employee_master.emp_name, employee_master.shift_id, department_master.name as dept_name, emp_designation.title as emp_dsg, emp_category.category";
        $empQuery['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $empQuery['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $empQuery['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        
        if(!empty($postData['currentDate']))
        {
            $day = date('d',strtotime($postData['currentDate']));$month = date('m',strtotime($postData['currentDate']));$year = date('Y',strtotime($postData['currentDate']));
            $empQuery['select'] .= ",emp_shiftlog.d".intval($day)." as currentShift,ifnull(shift_master.shift_name,'') as lastShift";
            $empQuery['leftJoin']['emp_shiftlog'] = "employee_master.id = emp_shiftlog.emp_id";
            $empQuery['leftJoin']['shift_master'] = "emp_shiftlog.d".intval($day)." = shift_master.id";
            $empQuery['where']['MONTH(emp_shiftlog.month)'] = $month;
            $empQuery['where']['YEAR(emp_shiftlog.month)'] = $year;
        }
		//$empQuery['where']['employee_master.id!='] = 1;
		if(!empty($postData['biomatric_id'])){$empQuery['where']['employee_master.biomatric_id'] = $postData['biomatric_id'];}
		else{$empQuery['where']['employee_master.biomatric_id > '] = 0;}
		
		if($this->loginId != -1 && $this->loginId != 1){ $empQuery['where']['employee_master.id'] = $this->loginId; }
		
		$empQuery['where']['employee_master.shift_id > '] = 0;
		$empQuery['where']['employee_master.is_active'] = 1;
        if(!empty($postData['deleted'])){$data['all']['employee_master.is_delete'] = [0,1];}
		$empQuery['order_by']['employee_master.emp_code'] = 'ASC';
        $empQuery['tableName'] = $this->empMaster;
		$empData = $this->rows($empQuery);
		//$this->printQuery();
		return $empData;
	}
	
    //Updated By Meghavi
    public function getSetterList(){
        $data['tableName'] = $this->empMaster;
        // $data['where']['emp_sys_desc_id'] = 4;
        $data['customWhere'][] = 'find_in_set("4", emp_sys_desc_id)';
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }

    public function getSetterInspectorList(){
        $data['tableName'] = $this->empMaster;
        // $data['where']['emp_sys_desc_id'] = 3;
        $data['customWhere'][] = 'find_in_set("3", emp_sys_desc_id)';
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }

    public function getLineInspectorList(){
        $data['tableName'] = $this->empMaster;
        // $data['where']['emp_sys_desc_id'] = 2;
        $data['customWhere'][] = 'find_in_set("2", emp_sys_desc_id)';
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }

    public function getFQCInspectorList(){
        $data['tableName'] = $this->empMaster;
        $data['customWhere'][] = 'find_in_set("5", emp_sys_desc_id)';
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }
    public function getMachineOperatorList(){
        $data['tableName'] = $this->empMaster;
        // $data['where']['emp_sys_desc_id'] = 1;
        $data['customWhere'][] = 'find_in_set("1", emp_sys_desc_id)';
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }

    // public function getEmpSalary($emp_id){
    //     $data['where']['emp_id'] = $emp_id;
    //     $data['tableName'] = $this->empSalary;
    //     return $this->row($data);
    // }

    public function getEmpDocs($emp_id){
        $data['where']['emp_id'] = $emp_id;
        $data['tableName'] = $this->empDocs;
        return $this->row($data);
    }

    public function save($data){
        // if($this->checkDuplicate($data['emp_contact'],$data['id']) > 0):
        //     $errorMessage['emp_contact'] = "This Number is already exist.";
        //     return ['status'=>0,'message'=>$errorMessage];
        // else:
            if($this->checkDuplicateEmpCode($data['emp_code'],$data['id']) > 0):
                $errorMessage['emp_code'] = "Emp Code is already exist.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
    			if(empty($data['id'])):
                    //$result = $this->addEmployeeInDevice($data);
                    //if($result['status'] == 2):
                        //return $result;
                    //endif;
                    $data['emp_psc'] = $data['emp_password'];
                    $data['emp_password'] = md5($data['emp_password']); 
                endif;
                unset($data['designationTitle']);
                $empData = $this->getEmp($data['id']);
                $result = $this->store($this->empMaster,$data,'Employee');
                
                $relieveData = array();
                $relieveData['emp_id'] = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
                $relieveData['emp_joining_date'] = $data['emp_joining_date'];
                $relieveData['emp_relieve_date'] = '';
                $relieveData['reason'] = '';
                $relieveData['is_delete'] = 0;
                if (empty($data['id'])) {
                    $this->saveReliveDetailJson($relieveData);
                } else {
                    if (!empty($empData->relieve_detail)) {
                        $jsonData = json_decode($empData->relieve_detail);
                        $relieveArr = array();
    
                        $joiningDate = array();
                        foreach ($jsonData as $row) {
                            $joiningDate[] = $row->emp_relieve_date;
                            if ($empData->emp_joining_date == $row->emp_joining_date) {
                                $relieveArr[] = [
                                    'emp_joining_date' => $data['emp_joining_date'],
                                    'emp_relieve_date' => '',
                                    'reason' => ''
                                ];
                            } else {
                                $relieveArr[] = $row;
                            }
                        }
                        $max = max(array_map('strtotime', $joiningDate));
                        if ($data['emp_joining_date'] > date('Y-m-d', $max)) {
                            $this->edit($this->empMaster, ['id' => $data['id']], ['relieve_detail' => json_encode($relieveArr), 'emp_joining_date' => $data['emp_joining_date']]);
                        } else {
                            $errorMessage['emp_joining_date'] = "Sorry you can not edit joining date beacuse joining date is less then last relieve date";
                            return ['status' => 0, 'message' => $errorMessage];
                        }
                    } else {
                        $this->saveReliveDetailJson($relieveData);
                    }
                }
                
                return $result;
            endif;
        // endif;
    }

    public function updateProfilePic($postData){
        try{
            $this->db->trans_begin();
            
            if(!empty($postData['emp_id']) AND !empty($postData['emp_profile'])):
                $this->edit($this->empMaster, ['id' => $postData['emp_id']], ['emp_profile' => $postData['emp_profile']]);
            endif;
        
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Profile image updated successfully'];
            endif;
            
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    
    public function checkDuplicateEmpCode($emp_code,$id=""){
        $numRows = 0;
        if(!empty($emp_code)):
            $data['tableName'] = $this->empMaster;
            $data['where']['emp_code'] = $emp_code;
			$data['where']['is_active'] = 1;
            
            if(!empty($id))
                $data['where']['id !='] = $id;
            $numRows = $this->numRows($data);
        else:
            $numRows = 0;
        endif;
        return $numRows;
    }
    
    public function checkDuplicate($emp_contact,$id=""){
        $numRows = 0;
        if(!empty($emp_contact)):
            $data['tableName'] = $this->empMaster;
            $data['where']['emp_contact'] = $emp_contact;
			$data['where']['is_active'] = 1;
            
            if(!empty($id))
                $data['where']['id !='] = $id;
            $numRows = $this->numRows($data);
        else:
            $numRows = 0;
        endif;
        return $numRows;
    }
    
    public function editProfile($data){
        $form_type = $data['form_type']; unset($data['form_type'], $data['designationTitle']);
        $empData = $this->getEmp($data['id']);
        $result = $this->store($this->empMaster,$data,'Employee');
        
        if($form_type == 'workProfile'):
            $relieveData = array();
            $relieveData['emp_id'] = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
            $relieveData['emp_joining_date'] = $data['emp_joining_date'];
            $relieveData['emp_relieve_date'] = '';
            $relieveData['reason'] = '';
            $relieveData['is_delete'] = 0;
            if (empty($data['id'])) {
                $this->saveReliveDetailJson($relieveData);
            } else {
                if (!empty($empData->relieve_detail)) {
                    $jsonData = json_decode($empData->relieve_detail);
                    $relieveArr = array();
    
                    $joiningDate = array();
                    foreach ($jsonData as $row) {
                        $joiningDate[] = $row->emp_relieve_date;
                        if ($empData->emp_joining_date == $row->emp_joining_date) {
                            $relieveArr[] = [
                                'emp_joining_date' => $data['emp_joining_date'],
                                'emp_relieve_date' => '',
                                'reason' => ''
                            ];
                        } else {
                            $relieveArr[] = $row;
                        }
                    }
                    $max = max(array_map('strtotime', $joiningDate));
                    if ($data['emp_joining_date'] > date('Y-m-d', $max)) {
                        $this->edit($this->empMaster, ['id' => $data['id']], ['relieve_detail' => json_encode($relieveArr), 'emp_joining_date' => $data['emp_joining_date']]);
                    } else {
                        $errorMessage['emp_joining_date'] = "Sorry you can not edit joining date beacuse joining date is less then last relieve date";
                        return ['status' => 0, 'message' => $errorMessage];
                    }
                } else {
                    $this->saveReliveDetailJson($relieveData);
                }
            }
        endif;
        return $result;
    }

    public function saveEmpSalary($data){
        //return $this->store($this->empSalary,$data,'Employee');
        return $this->store($this->empMaster,$data,'Employee');
    }

    public function saveEmpDocs($data){
        return $this->store($this->empDocs,$data,'Employee');
    }

    public function getNominationData($id){
		$data['where']['emp_id'] = $id;
		$data['tableName'] = $this->empNom;
		return $this->rows($data);
	}

    public function saveEmpNom($data){
        $nomData = $this->getNominationData($data['emp_id']);

        foreach($data['nom_name'] as $key=>$value):
			$empNomData = [
                            'id'=>$data['trans_id'][$key],
                            'emp_id'=>$data['emp_id'],
                            'nom_name'=>$value,
                            'nom_gender'=>$data['nom_gender'][$key],
                            'nom_relation'=>$data['nom_relation'][$key],
                            'nom_dob'=>$data['nom_dob'][$key],
                            'nom_proportion'=>$data['nom_proportion'][$key],
                            'created_by'=>$data['created_by']
                        ];
            $this->store($this->empNom,$empNomData);
		endforeach;
        if(!empty($nomData)):
			foreach($nomData as $key=>$value):
				if(!in_array($value->id,$data['trans_id'])){
					$this->trash($this->empNom,['id'=>$value->id],'');
				}
			endforeach;
		endif;
		return ['status'=>1,'message'=>'Employee Nomination Details saved successfully.'];
    }

    public function getEducationData($id){
		$data['where']['emp_id'] = $id;
		$data['tableName'] = $this->empEdu;
		return $this->rows($data);
	}

    public function saveEmpEdu($data){
        $eduData = $this->getEducationData($data['emp_id']);

        foreach($data['course'] as $key=>$value):
			$empEduData = [
                            'id'=>$data['trans_id'][$key],
                            'emp_id'=>$data['emp_id'],
                            'course'=>$value,
                            'university'=>$data['university'][$key],
                            'passing_year'=>$data['passing_year'][$key],
                            'grade'=>$data['grade'][$key],
                            'created_by'=>$data['created_by']
                        ];
            $this->store($this->empEdu,$empEduData);
		endforeach;
        if(!empty($eduData)):
			foreach($eduData as $key=>$value):
				if(!in_array($value->id,$data['trans_id'])){
					$this->trash($this->empEdu,['id'=>$value->id],'');
				}
			endforeach;
		endif;
		return ['status'=>1,'message'=>'Employee Education Details saved successfully.'];
    }

    public function delete($id){
        $this->trash($this->empSalary,['emp_id'=>$id],'Employee');
        $this->trash($this->empDocs,['emp_id'=>$id],'Employee');
        $this->trash($this->empNom,['emp_id'=>$id],'Employee');
        $this->trash($this->empEdu,['emp_id'=>$id],'Employee');
        return $this->trash($this->empMaster,['id'=>$id],'Employee');
    }

    public function activeInactive($id,$value){ 
        $this->edit($this->empMaster,['id'=>$id],['is_active'=>$value],'');
        $msg = ($value == 1)?"actived":"in-active";
        return ['status'=>1,'message'=> "Employee ".$msg." successfully."];
    }

    public function changePassword($id,$data){
        if(empty($id)):
            return ['status'=>2,'message'=>'Somthing went wrong...Please try again.'];
        endif;

        $empData = $this->getEmp($id);
        if(md5($data['old_password']) == $empData->emp_password):
            $postData = ['emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
            $this->edit($this->empMaster,['id'=>$id],$postData);
            return ['status'=>1,'message'=>"Password changed successfully."];
        else:
            return ['status'=>0,'message'=>['old_password'=>"Old password not match."]];
        endif;
    }

    public function getDesignation()
    {
        $data['tableName'] = $this->designation;
        return  $this->rows($data);
    }

    public function saveDesignation($designation,$emp_dept_id){
        $created_by = $this->session->userdata('loginId');
        $queryData = ['id'=>'','title'=>$designation,'dept_id'=>$emp_dept_id,'created_by'=>$created_by];
        $designationData = $this->store("emp_designation",$queryData,'Employee');
        return $designationData['insert_id'];
    }

    public function getEmployee($emp_id){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*, department_master.name, emp_designation.title";
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.id'] = $emp_id;
        $data['where']['is_active'] = 1;
        return $this->row($data);
    }

    public function saveLeaveAuthority($data){
        $queryData['select'] = "MAX(priority) as priority";
		$queryData['where']['emp_id'] = $data['emp_id'];
		$queryData['tableName'] = $this->leaveAuthority;
		$priorityData =  $this->row($queryData);
        $data['priority'] = $priorityData->priority + 1;

        if($this->checkDuplicateleaveAuthority($data['emp_id'],$data['dept_id'],$data['desi_id'],$data['id']) > 0):
            $errorMessage['emp_dept_id'] = "This Authority is already exist.";
            return ['status'=>0,'message'=>$errorMessage];
        else:

            return $this->store($this->leaveAuthority,$data,'Leave Authority');
        endif;
    }

    public function checkDuplicateleaveAuthority($emp_id,$dept_id,$desi_id,$id=""){
        $data['tableName'] = $this->leaveAuthority;
        $data['where']['emp_id'] = $emp_id;
        $data['where']['dept_id'] = $dept_id;
        $data['where']['desi_id'] = $desi_id;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function getLeaveHierarchy($emp_id){
		$queryData['tableName'] = $this->leaveAuthority;
        $queryData['select'] = "leave_authority.*,department_master.name,emp_designation.title";
        $queryData['join']['department_master'] = "leave_authority.dept_id = department_master.id";
        $queryData['join']['emp_designation'] = "leave_authority.desi_id = emp_designation.id";
		$queryData['where']['leave_authority.emp_id'] = $emp_id;
		return $this->rows($queryData);
    }

	public function setLeaveApprovalPriority($data){
		$ids = explode(',', $data['id']);
		$i=1;
		foreach($ids as $pp_id):
			$seqData=Array("priority"=>$i++);
			$this->edit($this->leaveAuthority,['id'=>$pp_id],$seqData);
		endforeach;
		
		return ['status'=>1,'message'=>'Process Priority updated successfully.'];
	}
	
	public function deleteLeaveAuthority($data)
	{
		$queryData=Array();
		$queryData['select'] = "priority";
		$queryData['where']['id'] = $data['id'];
		$queryData['tableName'] = $this->leaveAuthority;
		$oldPriority =  $this->row($queryData)->priority;
		
		$upProcess=Array();
		$upProcess['tableName'] = $this->leaveAuthority;
		$upProcess['where']['emp_id'] = $data['emp_id'];
		$upProcess['where']['priority > '] = $oldPriority;
		$upProcess['where']['is_delete']=0;
		$upProcess['set']['priority']='priority, - 1';
		$q = $this->setValue($upProcess);
	
		$this->remove($this->leaveAuthority,['id'=>$data['id']],'');
		
		return ['status'=>1,'message'=>'Authority Removed successfully.'];
	}

    public function getEmpReport(){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,emp_designation.title";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_role !='] = "-1";
        $data['where']['is_active'] = 1;
        return $this->rows($data);
    }

    public function getEmpEdu($id){
        $data['tableName'] = $this->empEdu;
        $data['where']['emp_id'] =  $id;
        return $this->rows($data);
    }
    
    // meghavi
    public function checkEmployeeStatus($id)
	{
		$result = new StdClass;$result->salary=0;$result->document=0;$result->nomination=0;$result->education=0;$result->leave=0;
		$queryData = Array();
		$queryData['tableName'] = $this->empCtc;
		$queryData['where']['emp_id'] = $id;
		$salaryData = $this->rows($queryData);
		$result->salary=count($salaryData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->empDocs;
		$queryData['where']['emp_id'] = $id;
		$documentData = $this->rows($queryData);
		$result->document=count($documentData);

        $queryData = Array();
		$queryData['tableName'] = $this->empNom;
		$queryData['where']['emp_id'] = $id;
		$nominationData = $this->rows($queryData);
		$result->nomination=count($nominationData);
		
		$queryData = Array();
		$queryData['tableName'] =$this->empEdu;
		$queryData['where']['emp_id'] = $id;
		$educationData = $this->rows($queryData);
		$result->education=count($educationData);

        $queryData = Array();
		$queryData['tableName'] = $this->leaveAuthority;
		$queryData['where']['emp_id'] = $id;
		$leaveData = $this->rows($queryData);
		$result->leave=count($leaveData);
		
		return $result;
	}


    public function getDeviceForEmployee()
    {
        $data['tableName'] ="device_master";
        $data['select'] = "device_master.*";
        // $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        // $data['where']['employee_master.emp_role !='] = "-1";
        // $data['where']['is_active'] = 1;
        return $this->rows($data);
    }
    
     // Created By Karmi @08/01/2022
    public function saveEmpDocumentsParam($data){
        return $this->store($this->empDocuments,$data,'Employee Document');
    }

    // Created By Karmi @08/01/2022
    public function getEmpDocuments($emp_id){
        $data['tableName'] = $this->empDocuments;
        $data['select'] = 'emp_docs.*,(CASE WHEN doc_type =1 THEN "Extra Documents" WHEN doc_type =2 THEN "Aadhar Card"  WHEN doc_type =3 THEN "Basic Rules" ELSE "" END) as doc_type_name';
		$data['where']['emp_id']=$emp_id;		
		return $this->rows($data);

	}

    // Created By Karmi @08/01/2022
    public function deleteEmpDocuments($id){
        return $this->trash($this->empDocuments,['id'=>$id],"Record");
	}
    
    // Created By Karmi @08/01/2022
    public function getEmpforExpCertificate($id){ 
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,emp_designation.title";
        $data['join']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.id'] = $id;
        return $this->row($data);
    }
    
    public function getEmpSalary($emp_id){
        $data['tableName'] = $this->empCtc;
        $data['where']['emp_id'] = $emp_id;
        $data['where']['is_active'] = 1;
        return $this->row($data);
    }

    public function saveEmpCtc($data){
        if(empty($data['id'])):
            $ctcData = $this->getEmpSalary($data['emp_id']);
            $this->edit($this->empCtc,['emp_id'=>$data['emp_id'], 'is_active'=>'1'],['is_active'=>'0','effect_end'=>$data['effect_start']],'Employee Ctc');
        endif;
        return $this->store($this->empCtc,$data,'Employee Ctc');
    }
    
    public function saveReliveDetailJson($data){
        $empQuery['tableName'] = $this->empMaster;
        $empQuery['where']['id'] = $data['emp_id'];
        $empQuery['where']['employee_master.is_delete'] = $data['is_delete'];
        $empData = $this->row($empQuery);

        if (!empty($empData->emp_relieve_date) && !empty($data['emp_joining_date']) && $empData->emp_relieve_date > $data['emp_joining_date']) {
            $errorMessage['emp_joining_date'] = "Your joining date is less then last relieve date";
            return ['status' => 0, 'message' => $errorMessage];
        }
        $relieveArr = array();
        if (!empty($empData->relieve_detail)) {
            $relieveArr = json_decode($empData->relieve_detail);
        }

        $relieveArr[] = [
            'emp_joining_date' => $data['emp_joining_date'],
            'emp_relieve_date' => $data['emp_relieve_date'],
            'reason' => $data['reason']
        ];
        $joining_date = '';
        if (!empty($data['emp_joining_date'])) {
            $joining_date = $data['emp_joining_date'];
        } else {
            $joining_date = $empData->emp_joining_date;
        }
        return $this->edit($this->empMaster, ['id' => $data['emp_id']], ['relieve_detail' => json_encode($relieveArr), 'emp_joining_date' => $joining_date]);
    }

    public function saveEmpReliveJoinData($data){
        if (!empty($data['emp_joining_date'])) {
            $empQuery['tableName'] = $this->empMaster;
            $empQuery['where']['id'] = $data['id'];
            $empQuery['where']['is_delete'] = 2;
            
            $empData = $this->row($empQuery);

            if (!empty($empData->emp_relieve_date) && $empData->emp_relieve_date > $data['emp_joining_date']) {
                $errorMessage['emp_joining_date'] = "Your joining date is less then last relieve date";
                return ['status' => 0, 'message' => $errorMessage];
            }
        }
        $this->edit($this->empMaster, ['id' => $data['id']], ['is_delete' => $data['is_delete'], 'emp_relieve_date' => $data['emp_relieve_date']]);
        $relieveData = array();
        $relieveData['emp_id'] = $data['id'];
        $relieveData['emp_joining_date'] = $data['emp_joining_date'];
        $relieveData['emp_relieve_date'] = $data['emp_relieve_date'];
        $relieveData['reason'] = $data['reason'];
        $relieveData['is_delete'] = $data['is_delete'];
        $result = $this->saveReliveDetailJson($relieveData);

        return $result;
    }

    public function getRelievedEmpDTRows($data){
        $data['tableName'] = $this->empMaster;
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_contact";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "employee_master.emp_code";

        $data['select'] = "employee_master.*,department_master.name,emp_designation.title";
        $data['where']['employee_master.emp_name!='] = "Admin";
        $data['where']['employee_master.is_delete'] = 2;
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['order_by']['employee_master.emp_code'] = "ASC";
        $columns = array('', '', 'employee_master.emp_name', 'employee_master.emp_code', 'employee_master.emp_contact', 'department_master.name', 'emp_designation.title', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }
    
    public function addEmployeeInDevice($id,$empId){
        $empData = $this->getEmp($empId);
        $data['tableName'] ="device_master";
        $data['select'] = "device_master.*";
        $data['where']['id'] = $id;
        $deviceData=$this->row($data);
        $empDevice="";
        if(!empty($empData->device_id))
        {
            $empDevice=$empData->device_id.','.$id;
        }
        else
        {
            $empDevice=$id;
        }
        if(empty($empData->emp_code)):
            return ['status'=>0,'message'=>'Employee code not found.'];
        endif;
        $empCode = $deviceData->Empcode = trim($empData->emp_code);
        $empName = $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
        
        $deviceResponse = $this->biometric->addEmpDevice($deviceData);
        
        if($deviceResponse['status'] == 0):
            $result = ['status'=>0,'message'=>'cURL Error #: ' . $deviceResponse['result']]; 
        else:
            $responseData = json_decode($deviceResponse['result']);
           
            if(!empty($responseData)):
                if($responseData->Error == false):
                    $this->store($this->empMaster,['id'=>$empData->id,'biomatric_id'=>$empData->emp_code,'device_id'=>$empDevice]);
                    $result = ['status'=>1,'message'=>'Employee added scucessfully.','CURLResponse'=>$responseData]; 
                else:
                    $result = ['status'=>0,'message'=>'Somthing is wrong or Device is offline. Employee can not added.','CURLResponse'=>$responseData];
                endif;
            else:
                $result = ['status'=>0,'message'=>'cURL Error #: Device is offline or somthing else. Employee can not added.','CURLResponse'=>$responseData];
            endif;
        endif;
        return $result;
    }
    
    public function removeEmployeeInDevice($id,$empId){
        $empData = $this->getEmp($empId);
        $data['tableName'] ="device_master";
        $data['select'] = "device_master.*";
        $data['where']['id'] = $id;
        $deviceData=$this->row($data);
        $empDeviceArr=Array();$empDevice="";
        if(!empty($empData->device_id))
        {
            $ed = explode(',',$empData->device_id);
            foreach($ed as $e){if($e != $id){$empDeviceArr[]=$e;}}
        }
        else
        {
            $empDeviceArr[]=$id;
        }
        $empDevice=implode(',',$empDeviceArr);
        if(empty($empData->emp_code)):
            return ['status'=>0,'message'=>'Employee code not found.'];
        endif;
        $empCode = $deviceData->Empcode = trim($empData->emp_code);
        $empName = $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
        
        $deviceResponse = $this->biometric->removeEmpDevice($deviceData);
        
        if($deviceResponse['status'] == 0):
            $result = ['status'=>0,'message'=>'cURL Error #: ' . $deviceResponse['result']]; 
        else:
            $responseData = json_decode($deviceResponse['result']);
           
            if(!empty($responseData)):
                if($responseData->Error == false):
                    $this->store($this->empMaster,['id'=>$empData->id,'device_id'=>$empDevice]);
                    $result = ['status'=>1,'message'=>'Employee deleted scucessfully.','CURLResponse'=>$responseData]; 
                else:
                    $result = ['status'=>0,'message'=>'Somthing is wrong or Device is offline. Employee can not added.','CURLResponse'=>$responseData];
                endif;
            else:
                $result = ['status'=>0,'message'=>'cURL Error #: Device is offline or somthing else. Employee can not added.','CURLResponse'=>$responseData];
            endif;
        endif;
        return $result;
    }
    public function transferEmpCode1($data){
        $transferOldShiftLog = $this->transferOldShiftLog($data);
        $transferOldPunches = $this->transferOldPunches($data);
        $empData = $this->getEmp($data['emp_id']);
        if(!empty($empData))
        {
            $deviceIds = Array();
            if(!empty($empData->device_id)){$deviceIds = explode(',',$empData->device_id);}
            if(!empty($deviceIds))
            {
                foreach($deviceIds as $did)
                {
                    $data['tableName'] ="device_master";
                    $data['where']['id'] = $did;
                    $deviceData=$this->row($data);
                    if(!empty($deviceData))
                    {
                        if(empty($data['new_emp_code'])):
                            return ['status'=>0,'message'=>'Employee code not found.'];
                        endif;
                        $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
                        $deviceData->Empcode = trim($data['old_emp_code']);
                        //print_r($deviceData);exit;
                        // Remove Emp with Old Code
                        $remResponse = $this->biometric->removeEmpDevice($deviceData);
                        
                        // Add Emp with New Code
                        $deviceData->Empcode = trim($data['new_emp_code']);
                        $addResponse = $this->biometric->addEmpDevice($deviceData);
                        
                        if($addResponse['status'] == 0):
                            $result = ['status'=>0,'message'=>'cURL Error #: ' . $addResponse['result']]; 
                        else:
                            $responseData = json_decode($addResponse['result']);
                           
                            if(!empty($responseData)):
                                if($responseData->Error == false):
                                    $this->store($this->empMaster,['id'=>$empData->id,'temp_biometric_id'=>$data['old_emp_code'],'emp_code'=>$data['new_emp_code'],'biomatric_id'=>$data['new_emp_code']]);
                                    $result = ['status'=>1,'message'=>'Employee transfered scucessfully.','CURLResponse'=>$responseData]; 
                                else:
                                    $result = ['status'=>0,'message'=>'Something is wrong or Device is offline. Employee can not transfered .','CURLResponse'=>$responseData];
                                endif;
                            else:
                                $result = ['status'=>0,'message'=>'cURL Error #: Device is offline or somthing else. Employee can not transfered .','CURLResponse'=>$responseData];
                            endif;
                        endif;
                        return $result;
                    }
                }
            }
        }
    }
    
    public function transferEmpCode($data){
        try{
            $this->db->trans_begin();
            $result = [];
            $empData = $this->getEmp($data['emp_id']);
            if(!empty($empData))
            {
    		    $deviceIds = Array();
                if(!empty($empData->device_id)){$deviceIds = explode(',',$empData->device_id);}
                if(!empty($deviceIds))
                {
                    foreach($deviceIds as $did)
                    {
                        $data['tableName'] ="device_master";
                        $data['where']['id'] = $did;
                        $deviceData=$this->row($data);
                        if(!empty($deviceData))
                        {
                            if(empty($data['new_emp_code'])):
                                return ['status'=>0,'message'=>'Employee code not found.'];
                            endif;
                            $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
                            $deviceData->Empcode = trim($data['old_emp_code']);
                            //print_r($deviceData);exit;
                            // Remove Emp with Old Code
                            $remResponse = $this->biometric->removeEmpDevice($deviceData);
                            
                            // Add Emp with New Code
                            $deviceData->Empcode = trim($data['new_emp_code']);
                            $addResponse = $this->biometric->addEmpDevice($deviceData);
                            
                            if($addResponse['status'] == 0):
                                $result = ['status'=>0,'message'=>'cURL Error #: ' . $addResponse['result']]; 
                            else:
                                $responseData = json_decode($addResponse['result']);
                               
                                if(!empty($responseData)):
                                    if($responseData->Error == false):
                                        // Update New Code in Employee Master
                                        $this->store($this->empMaster,['id'=>$empData->id,'temp_biometric_id'=>$data['old_emp_code'],'emp_code'=>$data['new_emp_code'],'biomatric_id'=>$data['new_emp_code']]);
                                        
                                        // Update Old Attendance Logs in Attendance Table
                                        $transferOldPunches = $this->transferOldAttendanceLog($data);
                                        
                                        $result = ['status'=>1,'message'=>'Employee transfered scucessfully.','CURLResponse'=>$responseData]; 
                                    else:
                                        $result = ['status'=>0,'message'=>'Something is wrong or Device is offline. Employee can not transfered .','CURLResponse'=>$responseData];
                                    endif;
                                else:
                                    $result = ['status'=>0,'message'=>'cURL Error #: Device is offline or somthing else. Employee can not transfered .','CURLResponse'=>$responseData];
                                endif;
                            endif;
                        }
                    }
        		    if($this->db->trans_status() !== FALSE):
                        $this->db->trans_commit();
                        return $result;
                    endif;
                }
                $this->db->trans_rollback();
                echo $e->getMessage();exit;
            }
            else{
                $this->db->trans_rollback();
                echo $e->getMessage();exit;
            }
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function transferOldAttendanceLog($data){
		$where['punch_date >= '] = date('Y-m-d 00:00:00',strtotime($data['transfer_from']));
		$where['emp_code'] = $data['old_emp_code'];
        $this->edit('attendance_log',$where,['temp_emp_code'=>$data['old_emp_code'],'emp_code'=>$data['new_emp_code']]);
        return true;
    }
    
    public function transferEmpCodeOld($data){
        //$transferOldShiftLog = $this->transferOldShiftLog($data);
        $transferOldPunches = $this->transferOldAttendanceLog($data);
        $empData = $this->getEmp($data['emp_id']);
        if(!empty($empData))
        {
			$this->store($this->empMaster,['id'=>$empData->id,'temp_biometric_id'=>$data['old_emp_code'],'emp_code'=>$data['new_emp_code'],'biomatric_id'=>$data['new_emp_code']]);
			$result = ['status'=>1,'message'=>'Employee transfered scucessfully.'];
			
            /*$deviceIds = Array();
            if(!empty($empData->device_id)){$deviceIds = explode(',',$empData->device_id);}
            if(!empty($deviceIds))
            {
                foreach($deviceIds as $did)
                {
                    $data['tableName'] ="device_master";
                    $data['where']['id'] = $did;
                    $deviceData=$this->row($data);
                    if(!empty($deviceData))
                    {
                        if(empty($data['new_emp_code'])):
                            return ['status'=>0,'message'=>'Employee code not found.'];
                        endif;
                        $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
                        $deviceData->Empcode = trim($data['old_emp_code']);
                        //print_r($deviceData);exit;
                        // Remove Emp with Old Code
                        $remResponse = $this->biometric->removeEmpDevice($deviceData);
                        
                        // Add Emp with New Code
                        $deviceData->Empcode = trim($data['new_emp_code']);
                        $addResponse = $this->biometric->addEmpDevice($deviceData);
                        
                        if($addResponse['status'] == 0):
                            $result = ['status'=>0,'message'=>'cURL Error #: ' . $addResponse['result']]; 
                        else:
                            $responseData = json_decode($addResponse['result']);
                           
                            if(!empty($responseData)):
                                if($responseData->Error == false):
                                    $this->store($this->empMaster,['id'=>$empData->id,'temp_biometric_id'=>$data['old_emp_code'],'emp_code'=>$data['new_emp_code'],'biomatric_id'=>$data['new_emp_code']]);
                                    $result = ['status'=>1,'message'=>'Employee transfered scucessfully.','CURLResponse'=>$responseData]; 
                                else:
                                    $result = ['status'=>0,'message'=>'Something is wrong or Device is offline. Employee can not transfered .','CURLResponse'=>$responseData];
                                endif;
                            else:
                                $result = ['status'=>0,'message'=>'cURL Error #: Device is offline or somthing else. Employee can not transfered .','CURLResponse'=>$responseData];
                            endif;
                        endif;
                        return $result;
                    }
                }
            }*/
        }
    }
    
    public function transferOldPunches($data){
        $queryData['tableName'] = 'device_punches';
		$queryData['where']['punch_date >= '] = date('Y-m-d',strtotime($data['transfer_from']));
        $deviceData=$this->rows($queryData);
        $punchData = Array();
		foreach($deviceData as $pnc)
		{
			$newPunches = str_replace($data['old_emp_code'],$data['new_emp_code'],$pnc->punch_data);
			$punchData = ['id'=>$pnc->id,'punch_date'=>$pnc->punch_date,'punch_data'=>$newPunches];
			$this->store('device_punches',$punchData);
		}
        return $punchData;
    }
    
    public function transferOldShiftLog($data){
		/*$queryData = Array();
		$queryData['tableName'] = 'attendance_shiftlog';
		$queryData['where']['attendance_date >= '] = date('Y-m-d',strtotime($data['transfer_from']));
        $shiftLogData=$this->rows($queryData);
        $punchData = Array();
		foreach($shiftLogData as $row)
		{
		    $newPunches = Array();
            
			$newPunches = str_replace($data['old_emp_code'],$data['new_emp_code'],$row->punchdata);
			$shiftLog = ['id'=>$row->id,'attendance_date'=>$row->attendance_date,'punchdata'=>json_encode($newPunches)];
			$this->store('attendance_shiftlog',$shiftLog);
		}
        return $punchData;*/
    }
    
    public function updateStaffSkill($data){ 
		foreach($data['skill_id'] as $key=>$value):
			if(!empty($data['skill_status'][$key])):	
				$staffData = [
					'id'=>$data['id'][$key],
					'skill_id'=>$value,
					'emp_id'=>$data['emp_id'],
					'skill_status'=>$data['skill_status'][$key]
				];
				$this->store($this->staffSkill,$staffData,'');
			endif;
		endforeach;

		return ['status'=>1,'message'=>'Staff Skill Save Successfully.'];
	}

    public function getStaffSkill($id){
        $data['tableName'] = $this->staffSkill;
        $data['select']="staff_skill.*,skill_master.skill";
        $data['leftJoin']['skill_master'] = 'staff_skill.skill_id = skill_master.id';
        $data['where']['emp_id'] = $id;
        return $this->rows($data);
    }
    public function getEmployees($id){
        $data['where_in']['id'] = $id;
        //$data['where']['is_active'] = 1;
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }

    // Emp Facility *Created By Meghavi @22/12/22*
    public function getFacilityData($id){
		$data['tableName'] = $this->empFacility;
        $data['select']="emp_facility.*,facility_master.ficility_type,facility_master.remark";
        $data['leftJoin']['facility_master'] = 'emp_facility.type = facility_master.id';
        $data['where']['emp_id'] = $id;
		return $this->rows($data);
	}

    public function saveEmpFacility($data){
        return $this->store($this->empFacility,$data,'Employee Facility');
    }

    public function deleteEmpFacility($id){
        return $this->trash($this->empFacility,['id'=>$id],"Record");
	}
	
	public function updateEmpChargeableFacility($data){
        return  $this->store($this->empMaster,$data,'Facility');
    }

    public function getTypeList(){
        $data['tableName'] = $this->facilityMaster;
        return $this->rows($data);
    }
    
    	//Created By Karmi @13/01/2022
    public function changeEmpPsw($id){
        $data['id'] = $id;
        $data['emp_psc'] = '123456';
        $data['emp_password'] = md5($data['emp_psc']); 
        $this->store($this->empMaster,['id'=>$data['id'], 'emp_password'=>  $data['emp_password'], 'emp_psc'=> $data['emp_psc']]);
        return ['status'=>1,'message'=>'Password Reset successfully.'];
	}
	    
    public function getActiveSalaryStructure($emp_id){
        $queryData = array();
        $queryData['tableName'] = "salary_structure";
        $queryData['where']['emp_id'] = $emp_id;
        $queryData['where']['is_active'] = 1;
        $queryData['order_by']['id'] = "DESC";
        $result = $this->row($queryData);
        return $result;
    }

    public function saveEmpSalaryStructure($data){
        try{
            $this->db->trans_begin();
            
            $activeStructure = $this->getActiveSalaryStructure($data['ctc_emp_id']);

            /* $data['effect_start'] = NULL;
            if($data['salary_duration'] == "D"):
                $data['ctc_amount'] = 0;
                $data['effect_start'] = date("Y-m-d");
            endif; */

            $empSalaryData = [
                'emp_id' => $data['ctc_emp_id'],
                'emp_type' => $data['ctc_emp_type'],
                'format_id' => $data['ctc_format'],
                'salary_duration' => $data['salary_duration'],
                'ctc_amount' => $data['ctc_amount'],
                'org_ctc_amount' => $data['org_ctc_amount'],
                'salary_head_json' => json_encode($data['salary_head_json']),
                'is_active' => 1,
                'effect_start' => date("Y-m-d"),
                'created_by' => $this->loginId
            ];

            if(!empty($activeStructure)):
                $empSalaryData['id'] = $activeStructure->id;
                /* if($data['ctc_format'] == $activeStructure->format_id && $data['ctc_amount'] == $activeStructure->ctc_amount && $data['salary_duration'] == $activeStructure->salary_duration):
                    $empSalaryData['id'] = $activeStructure->id;
                else:
                    $empSalaryData['id'] = "";
                    $this->edit("salary_structure",['emp_id'=>$data['ctc_emp_id'],'is_active' => 1],['is_active' => 0,'effect_end'=>$data['effect_start']]);
                endif; */
            else:
                $empSalaryData['id'] = "";
            endif;
            
            $this->store('salary_structure',$empSalaryData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Salary Structure updated successfully'];
            endif;
            
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    
    public function saveSalary($data){
        try{
            $this->db->trans_begin();
            
            $this->edit('employee_master',['id'=>$data['ctc_emp_id']],['salary_actual'=>$data['salary_actual'],'salary_on_paper'=>$data['salary_on_paper']]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Salary updated successfully'];
            endif;
            
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
}
?>