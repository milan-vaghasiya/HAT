<?php
class SalaryStructure extends MY_Controller{
    private $indexPage = "hr/salary_structure/index";
    private $formPage = "hr/salary_structure/form";
    private $salaryHeadFrom = "hr/salary_structure/salary_heads_form";
    private $salaryHeadIndex = "hr/salary_structure/salary_heads_index";
    
    private $typeArray = ["1"=>'Earnings',"-1"=>"Deduction"];
    private $caltypeArray = ["1"=>'Basic',"2"=>"HRA","3"=>'PF',"4"=>"Speacial"];
    private $parentheadArray = ["1"=>'Gross Earning',"2"=>"General Earning","3"=>'Gross Deduction',"4"=>"General Deduction"];
    private $calMethodArray = ["1"=>'Percentage (%)',"2"=>"Amount","3"=>"Auto"];
    private $calOnArray = ["1"=>"CTC","2"=>'Basic+DA',"3"=>"Gross Salary"];
    
	public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Salary Structure";
		$this->data['headData']->controller = "hr/salaryStructure";
        $this->data['headData']->pageUrl = "hr/salaryStructure";
	}
	
    /* CTC Format Start */
	public function index(){
        $this->data['headData']->pageUrl = "hr/salaryStructure";
        $this->data['tableHeader'] = getHrDtHeader('ctcFormat');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->salaryStructure->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCtcFormatData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCtcFormat(){
        $systemEarningHead = $this->salaryStructure->getSalaryHeadList(['type'=>1,'is_system'=>1]);
        $systemDeductionHead = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'is_system'=>1]);
        $systemEarningHeadIds = (!empty($systemEarningHead))?implode(",",array_column($systemEarningHead,'id')):"";
        $systemDeductionHeadIds = (!empty($systemDeductionHead))?implode(",",array_column($systemDeductionHead,'id')):"";
        $this->data['systemEarningHeadIds'] = $systemEarningHeadIds;
        $this->data['systemDeductionHeadIds'] = $systemDeductionHeadIds;
        $this->data['earningHead'] = $this->salaryStructure->getSalaryHeadList(['type'=>1,'is_system'=>0]);
        $this->data['deductionHead'] = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'is_system'=>0]);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['format_name']))
            $errorMessage['format_name'] = "Format Name is required.";
        if(empty($data['effect_from']))
            $errorMessage['effect_from'] = "Effect From is required.";
            
        if($this->salaryStructure->checkDuplicateCtcFormat($data['format_name'],$data['id']) > 0)
            $errorMessage['head_name'] = "Format Name is Duplicate.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(!empty($data['eh_ids'])):
                $data['eh_ids'] = $data['system_eh_ids'].",".$data['eh_ids'];
            else:
                $data['eh_ids'] = $data['system_eh_ids'];
            endif;

            if(!empty($data['dh_ids'])):
                $data['dh_ids'] = $data['system_dh_ids'].",".$data['dh_ids'];
            else:
                $data['dh_ids'] = $data['system_dh_ids'];
            endif;
            unset($data['system_eh_ids'],$data['system_dh_ids']);

            if(empty($data['id'])): 
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $data['created_by'] = $this->session->userdata('loginId');
            else:
                $data['updated_at'] = date('Y-m-d H:i:s'); 
                $data['updated_by'] = $this->session->userdata('loginId');
            endif;
            
            $result = $this->salaryStructure->save($data);
            $this->printJson($result);
        endif;
    }
    
    public function edit(){
        $id = $this->input->post('id');
        $systemEarningHead = $this->salaryStructure->getSalaryHeadList(['type'=>1,'is_system'=>1]);
        $systemDeductionHead = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'is_system'=>1]);
        $systemEarningHeadIds = (!empty($systemEarningHead))?implode(",",array_column($systemEarningHead,'id')):"";
        $systemDeductionHeadIds = (!empty($systemDeductionHead))?implode(",",array_column($systemDeductionHead,'id')):"";
        $this->data['systemEarningHeadIds'] = $systemEarningHeadIds;
        $this->data['systemDeductionHeadIds'] = $systemDeductionHeadIds;
        $this->data['earningHead'] = $this->salaryStructure->getSalaryHeadList(['type'=>1,'is_system'=>0]);
        $this->data['deductionHead'] = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'is_system'=>0]);
        $this->data['dataRow'] = $this->salaryStructure->getCtcFormat($id);
        $this->load->view($this->formPage,$this->data);
    }
    
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->salaryStructure->delete($id);
            $this->printJson($result);
        endif;
    }

    /* CTC Format End */

    /* Salary Head Start */

    public function heads(){
        $this->data['headData']->pageUrl = "hr/salaryStructure";
        $this->data['tableHeader'] = getHrDtHeader('salaryHead');
        $this->load->view($this->salaryHeadIndex,$this->data);
    }

    public function getSalaryHeadDTRows(){
        $result = $this->salaryStructure->getSalaryHeadDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalaryHeadData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSalaryHead(){
        $this->data['typeArray'] = $this->typeArray;
        $this->load->view($this->salaryHeadFrom,$this->data);
    }
    
    public function saveSalaryHead(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['head_name']))
            $errorMessage['head_name'] = "Head Name is required.";
        if(empty($data['type']))
            $errorMessage['type'] = "Type is required.";
        if(empty($data['effect_in']))
            $errorMessage['effect_in'] = "Effect In is required.";
            
        if($this->salaryStructure->checkDuplicateSalaryHead($data['head_name'],$data['id']) > 0)
            $errorMessage['head_name'] = "Head Name is Duplicate.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])){ 
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $data['created_by'] = $this->session->userdata('loginId');
            }else{
                $data['updated_at'] = date('Y-m-d H:i:s'); 
                $data['updated_by'] = $this->session->userdata('loginId');
            }
            $result = $this->salaryStructure->saveSalaryHead($data);
            $this->printJson($result);
        endif;
    }

    public function editSalaryHead(){
        $id = $this->input->post('id');
        $this->data['typeArray'] = $this->typeArray;
        $this->data['dataRow'] = $this->salaryStructure->getSalaryHead($id);
        $this->load->view($this->salaryHeadFrom,$this->data);
    }
    
    public function deleteSalaryHead(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->salaryStructure->deleteSalaryHead($id);
            $this->printJson($result);
        endif;
    }

    /* Salary Head End */
}
?>