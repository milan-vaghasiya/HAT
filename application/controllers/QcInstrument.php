<?php
class QcInstrument extends MY_Controller
{
    private $indexPage = "qc_instrument/index";
    private $formPage = "qc_instrument/form";
    private $requestForm = "purchase_request/purchase_request";
    private $indexUsed = "qc_instrument/index_used";
    private $calibrationForm = "qc_gauges/calibration_form";

   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Instrument";
		$this->data['headData']->controller = "qcInstrument";
		$this->data['headData']->pageUrl = "qcInstrument"; 
	}
	
	public function index($status=1){
        $this->data['status']=$status;
        $controller = (in_array($status,[1,5])) ? 'qcInstrumentChk' : 'qcInstrument' ;
        $this->data['tableHeader'] = getQualityDtHeader($controller);
        $this->load->view($this->indexPage,$this->data);
    }

	public function indexUsed($status=2){
		$this->data['status']=$status;
		$this->data['headData']->pageUrl = "qcInstrument";
        $this->data['tableHeader'] = getQualityDtHeader('qcChallan');
        $this->load->view($this->indexUsed,$this->data);
    }
    
    public function getDTRows($status=1){ 
		$data=$this->input->post();
		$data['status']=$status; $data['item_type']=2;
		$result = $this->qcInstrument->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getQcInstrumentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function getChallanDTRows($status=2){ 
		$data=$this->input->post();
		$data['status']=$status; $data['item_type'] = 2;
		$result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = 'qcChallan';
            $sendData[] = getQcChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInstrument(){
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['status'] = 1;
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['gauge_type'] == 3){
            if ($data['min_range'] == "" || $data['min_range'] < 0)
                $errorMessage['min_range'] = "Min Range is required.";
    
            if ($data['max_range'] == "" || $data['max_range'] < 0)
                $errorMessage['max_range'] = "Max Range is required.";
        }else{
            if (empty($data['instrument_range']))
                $errorMessage['instrument_range'] = "Instrument Range is required.";
        }

        if (empty($data['least_count']))
            $errorMessage['least_count'] = "Least Count is required.";
        if (empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['item_type'] = 2;
            $data['created_by'] = $this->session->userdata('loginId');
            if(isset($data['min_range']) && $data['max_range']):
                $data['instrument_range']=$data['min_range'].'-'.$data['max_range']; 
            endif;
            unset($data['min_range'],$data['max_range']);
            $this->printJson($this->qcInstrument->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['dataRow'] = $this->qcInstrument->getItem($id);
        $this->data['empData'] = $this->employee->getEmpList();
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->qcInstrument->delete($id));
        endif;
    }
    
    public function inwardGauge(){
        $data = $this->input->post();
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['dataRow'] = $this->qcInstrument->getItem($data['id']);
        $this->data['status'] = $data['status'];
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->formPage,$this->data);
    }
	
	public function saveRejectGauge(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['reject_reason'])):
            $errorMessage['reject_reason'] = "Reject Reason is required.";
        endif;
        
        $data['id'] = $data['gauge_id'];
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->qcInstrument->saveRejectGauge($data));
        endif;
    }

    /*Created By : NYN @14-02-2023 */
    public function getCalibration(){ 
        $data = $this->input->post();
        $this->data['dataRow'] = $result = $this->qcChallan->getQcChallanTransRow($data['id']);  
        $this->data['calData'] = $this->item->getCalibrationList($result->item_id); 
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->calibrationForm,$this->data);
    }

    public function saveCalibration(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['cal_date']))
			$errorMessage['cal_date'] = "Date is required.";
		if(empty($data['cal_certi_no']))
			$errorMessage['cal_certi_no'] = "Certificate No. is required.";
        if(empty($data['to_location']))
            $errorMessage['to_location'] = "Receive Location is required.";

       
        if ($_FILES['certificate_file']['name'] != null || !empty($_FILES['certificate_file']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['certificate_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['certificate_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['certificate_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['certificate_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['certificate_file']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
            $config = ['file_name' => time() . "_certificate_file_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path'    => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['certificate_file'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $data['certificate_file'] = $uploadData['file_name'];
            endif;
        else :
            unset($data['certificate_file']);
        endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $itemData = $this->qcInstrument->getItem($data['item_id']);  
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['cal_date'] . "+".$itemData->cal_freq." months") );
            $data['created_by'] = $this->session->userdata('loginId');
            $response = $this->qcInstrument->saveCalibration($data);
			$this->printJson($response);
        endif;
    }

    public function deleteCalibration(){
        $id = $this->input->post('id');
        $item_id = $this->input->post('item_id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deleteCalibration($id,$item_id);

            $result = $this->item->getCalibrationList($item_id);
            $i=1;$tbodyData=""; 
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'Calibration'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.formatDate($row->cal_date).'</td>
                            <td>'.$row->cal_by.'</td>
                            <td>'.$row->cal_agency.'</td>
                            <td>'.$row->cal_certi_no.'</td>                                        
                            <td>'.((!empty($row->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$row->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                            <td class="text-center">';
                                $tbodyData.= '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashCalibration('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>';
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "itemId"=>$item_id]);
        endif;
    }
	
    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(6);
        $this->load->view($this->requestForm,$this->data);
    }

    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_data'] = "";$itemArray = array();
			if(isset($data['req_item_id']) && !empty($data['req_item_id'])):
				foreach($data['req_item_id'] as $key=>$value):
					$itemArray[] = [
						'req_item_id' => $value,
						'req_qty' => $data['req_qty'][$key],
						'req_item_name' => $data['req_item_name'][$key]
					];
				endforeach;
				$data['item_data'] = json_encode($itemArray);
			endif;
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data));
        endif;
    }

    /*******/
    public function getInstrumentCode(){
        $data = $this->input->post();$data['item_type'] = 2;
        $data['instrument_range']=$data['min_range'].'-'.$data['max_range'];
        $result = $this->qcInstrument->getMaxItemCode($data);
        $part_no = sprintf("%02d",($result->item_code+1));
        $item_code = sprintf("%03d",$data['cat_code']).'-'.$part_no;
        $this->printJson(['item_code'=>$item_code,'part_no'=>$part_no]);
    }
}
?>