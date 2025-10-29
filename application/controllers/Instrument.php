<?php
class Instrument extends MY_Controller
{
    private $indexPage = "instrument/index";
    private $formPage = "instrument/form";
    private $requestForm = "purchase_request/purchase_request";
    private $calibrationForm = "instrument/calibration_form";
    private $gstPercentage = array(["rate" => 0, "val" => 'NIL'], ["rate" => 0.25, "val" => '0.25%'], ["rate" => 3, "val" => '3%'], ["rate" => 5, "val" => '5%'], ["rate" => 12, "val" => '12%'], ["rate" => 18, "val" => '18%'], ["rate" => 28, "val" => '28%']);

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Instrument";
        $this->data['headData']->controller = "instrument";
        $this->data['headData']->pageUrl = "instrument";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['select'] = "id, item_name, item_type, item_code, make_brand, instrument_range, least_count, permissible_error, cal_required, cal_freq, cal_reminder, last_cal_date, next_cal_date, cal_agency, description";
        $data['where']['item_master.item_type'] = 6;
        if(!empty($status)){ $data['customWhere'][] = "(item_master.next_cal_date <= '".date('Y-m-d')."' OR item_master.next_cal_date IS NULL OR item_master.next_cal_date = '')"; }
        //$data['order_by']['item_master.item_code'] = 'DESC';
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "item_code";
        $data['searchCol'][] = "make_brand";
        $data['searchCol'][] = "instrument_range";
        $data['searchCol'][] = "least_count";
        $data['searchCol'][] = "permissible_error";
        $data['searchCol'][] = "cal_required";
        $data['searchCol'][] = "cal_freq";
        $data['searchCol'][] = "cal_agency";
        $data['searchCol'][] = "DATE_FORMAT(last_cal_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(next_cal_date,'%d-%m-%Y')";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "description";

        $columns = array('', '', 'item_name', 'item_code', 'make_brand', 'instrument_range', 'least_count', 'permissible_error', 'cal_required', 'cal_freq', 'cal_agency', 'description');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }

        $result = $this->instrument->getDTRows($data); //print_r($this->db->last_query()); exit;
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getInstrumentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInstrument()
    {
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->load->view($this->formPage, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_name']))
            $errorMessage['item_name'] = "Insrtument Name is required.";
        if (empty($data['cal_freq']))
            $errorMessage['cal_freq'] = "Cali. Frequency is required.";
        if (empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['item_type'] = 6;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function save00()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_name']))
            $errorMessage['item_name'] = "Insrtument Name is required.";
        if (empty($data['cal_freq']))
            $errorMessage['cal_freq'] = "Cali. Frequency is required.";
        if (empty($data['last_cal_date']))
            $errorMessage['last_cal_date'] = "Calibration Date is required.";

        if ($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['item_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['item_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['item_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['item_image']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
            $config = ['file_name' => time() . "_order_item_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path'    => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['item_image'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $data['item_image'] = $uploadData['file_name'];
            endif;
        else :
            unset($data['item_image']);
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['item_type'] = 6;
            $data['created_by'] = $this->session->userdata('loginId');
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['last_cal_date'] . "+" . $data['cal_freq'] . " months"));
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function edit()
    {
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['dataRow'] = $this->instrument->getItem($id);
        $this->load->view($this->formPage, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->instrument->delete($id));
        endif;
    }

    /* Purchase Request */
    public function addPurchaseRequest()
    {
        $this->data['itemData'] = $this->item->getItemLists(6, 7);
        $this->load->view($this->requestForm, $this->data);
    }

    public function savePurchaseRequest()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if (empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if (empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['item_data'] = "";
            $itemArray = array();
            if (isset($data['req_item_id']) && !empty($data['req_item_id'])) :
                foreach ($data['req_item_id'] as $key => $value) :
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

    /*Created By : Avruti @21-3-2022 */
    public function getCalibration(){
        $item_id = $this->input->post('id');
        $result = $this->item->getItem($item_id);  
        $this->data['item_id'] = $item_id;
        $this->data['dataRow'] = $result; 
        $this->data['calData'] = $this->item->getCalibrationList($item_id); 
        $this->load->view($this->calibrationForm,$this->data);
    }

    public function saveCalibration(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['cal_date']))
			$errorMessage['cal_date'] = "Date is required.";
		if(empty($data['cal_by']))
			$errorMessage['cal_by'] = "Calibration By is required.";
		if(empty($data['cal_certi_no']))
			$errorMessage['cal_certi_no'] = "Certificate No. is required.";
       
        if ($_FILES['certificate_file']['name'] != null || !empty($_FILES['certificate_file']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['certificate_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['certificate_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['certificate_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['certificate_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['certificate_file']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/gauges/');
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

            $itemData = $this->item->getItem($data['item_id']);  
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['cal_date'] . "+".$itemData->cal_freq." months") );
            
            $response = $this->item->saveCalibration($data);
            $result = $this->item->getCalibrationList($data['item_id']);
            $i=1;$tbodyData="";
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.','.$data['item_id'].",'Calibration'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->cal_date.'</td>
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
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "itemId"=>$data['item_id']]);
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

    public function printInstrumentData($id){
        
        $this->data['insData'] = $this->instrument->getItem($id); 
        $this->data['calData'] = $this->item->getCalibrationList($id); 
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('instrument/printInstrument',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Instrument History Card</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 48 <br> (00/01.03.2022)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		//print_r($pdfData);exit;
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
}
