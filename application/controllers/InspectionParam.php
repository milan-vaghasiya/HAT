<?php 
class InspectionParam extends MY_Controller{
    private $indexPage = "inspection_param/index";
    private $inspectionForm = "inspection_param/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Inspection Parameter";
		$this->data['headData']->controller = "inspectionParam";
		$this->data['headData']->pageUrl = "inspectionParam";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
        $result = $this->item->getProdOptDTRows($this->input->post(),3);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInspectionParamData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

 public function getPreInspection(){
        $item_id=$this->input->post('id');
        $this->data['paramData']=$this->item->getPreInspectionParam($item_id);
		$this->data['param'] = explode(',',$this->grnModel->getMasterOptions()->ins_param); 
		// $this->data['instruments'] = explode(',',$this->grnModel->getMasterOptions()->ins_instruments); 
        $this->data['categoryData'] = $this->itemCategory->getCategoryList(6,7);
        $this->data['item_id']=$item_id;
        $this->load->view($this->inspectionForm,$this->data); 
    }

    public function savePreInspectionParam(){
        $data = $this->input->post(); ///print_r($data);exit;
		$errorMessage = array();
        if(empty($data['parameter']))
            $errorMessage['parameter'] = "Parameter is required.";
        if(empty($data['lower_limit']))
			$errorMessage['lower_limit'] = "Tolerance is required.";

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->item->savePreInspectionParam($data);
            $paramData = $this->item->getPreInspectionParam($data['item_id']);  //print_r($paramData);exit;
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                   
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>                                
                                <td>'.$row->specification.'</td>         
                                <td>'.$row->inspection_route.'</td>
                                <td>'.$row->lower_limit.'</td>
                                <td>'.$row->category_name.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deletePreInspection(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deletePreInspection($data['id']);
            $paramData = $this->item->getPreInspectionParam($data['item_id']);
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                    echo '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->parameter.'</td>
                        <td>'.$row->specification.'</td>
                        <td>'.$row->inspection_route.'</td>
                        <td>'.$row->lower_limit.'</td>
                        <td>'.$row->category_name.'</td>
                        <td class="text-center">
                            <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                        </td>
                    </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }
}
?>