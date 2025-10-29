<?php
class MaterialGrade extends MY_Controller
{
    private $indexPage = "material_grade/index";
    private $materialForm = "material_grade/form";
    private $inspection_param = "material_grade/inspection_param";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Grade";
		$this->data['headData']->controller = "materialGrade";
        $this->data['headData']->pageUrl = "materialGrade";
	}
	 
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->materialGrade->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMaterialGrade(){
        $this->data['scrapData'] = $this->materialGrade->getScrapList();
        $this->data['colorList'] = explode(',',$this->materialGrade->getMasterOptions()->color_code);
        $this->data['standard'] = $this->materialGrade->getStandardName();
        $this->load->view($this->materialForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['material_grade']))
			$errorMessage['material_grade'] = "Material Grade is required.";
        if(empty($data['scrap_group']))
			$errorMessage['scrap_group'] = "Scrap Group is required.";
        //if(empty($data['metal_code']))
			//$errorMessage['metal_code'] = "Code is required.";
        
		if(empty($data['standard'])):
            if(empty($data['standardName'])):
                $errorMessage['standard'] = "Standard is required.";
            else:
                $data['standard'] = $data['standardName'];
            endif;
        endif;
        unset($data['standardName']);
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['metal_code'] = lpad($data['metal_code'],2,'0');
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->materialGrade->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->materialGrade->getMaterial($id);
        $this->data['scrapData'] = $this->materialGrade->getScrapList();
        $this->data['colorList'] = explode(',',$this->materialGrade->getMasterOptions()->color_code);
        $this->data['standard'] = $this->materialGrade->getStandardName();
        $this->load->view($this->materialForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->materialGrade->delete($id));
        endif;
    }

    public function getInspectionParam(){
        $grade_id = $this->input->post('id');        
        $this->data['dataRow'] = $this->materialGrade->getMaterial($grade_id);
        $this->data['specificationData'] = $this->materialGrade->getMaterialSpecification($grade_id);
        $this->load->view($this->inspection_param,$this->data);
    }

    public function saveInspectionParam(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['min_value']))
			$errorMessage['generalError'] = "Material Specification is required.";
			
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['grade_id'],
                // 'macro_structure' => $data['macro_structure'],
                // 'micro_structure' => $data['micro_structure'],
                // 'grain_size' => $data['grain_size'],
                // 'step_down_test' => $data['step_down_test'],
                // 'mpi' => $data['mpi'],
                // 'ultrasonic_test' => $data['ultrasonic_test'],
                'remark' => $data['remark']
            ];

            $specification = [
                'id' => $data['id'],
                'min_value' => $data['min_value'],
                'max_value' => $data['max_value']
            ];

            $this->printJson($this->materialGrade->saveInspectionParam($masterData,$specification));
        endif;
    }
}
?>