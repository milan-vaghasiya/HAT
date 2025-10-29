<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Grn extends MY_Controller
{
	private $indexPage = "grn/index";
	private $grnForm = "grn/form";
    private $inspection = "grn/material_inspection";
	private $inInspection = "grn/in_inspection";
	private $testReport = "grn/test_report";
	private $deviationReport = "grn/deviation_report";
    private $tc_inspection = "grn/tc_inspection";
    private $fg_opening_index = "grn/fg_opening_index";
    private $rm_opening_index = "grn/rm_opening_index";
    private $fg_opening_form = "grn/fg_opening_form";

	public function __construct(){ 
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Goods Receipt Note";
		$this->data['headData']->controller = "grn";
	}

	public function index(){
		$this->data['headData']->pageUrl = "grn";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

    public function getDTRows(){ 
		$result = $this->grnModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            
            $row->product_code = ''; $c=0;
			if(!empty($row->fgitem_id)):
				$fgData = $this->grnModel->getFinishGoods($row->fgitem_id);
				$item_code = array_column($fgData,'item_code');
				$row->product_code = implode(", ",$item_code);
			endif; 
            
            $row->controller = "grn";    
            $sendData[] = getGRNData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function addGRN(){
		$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
		$this->data['grn_prefix'] = 'GRN/'.$this->shortYear.'/';
		$this->data['itemData'] = '';//$this->item->getItemLists("2,3,4,5,6,7");
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['partyData'] = $this->party->getPartyList(0,'1,3');
        //$this->data['partyData'] = $this->party->getSupplierList();
		$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
		$this->data['fgItemList'] = $this->item->getItemList(1);		
		$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
		$this->data['soList'] = '';//$this->grnModel->getSoListForSelect();
		$this->load->view($this->grnForm,$this->data);
	}

	public function createGrn(){
		$data = $this->input->post();
		if($data['ref_id']):
			$orderItems = $this->purchaseOrder->getOrderItems($data['ref_id']); //print_r($this->db->last_query()); exit;
			$orderData = new stdClass();
			$orderData->party_id = $data['party_id'];
			$orderData->id = implode(",",$data['ref_id']); 
			$this->data['orderItems'] = $orderItems;
			$this->data['orderData'] = $orderData;
			$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
			$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
			$this->data['grn_prefix'] = 'GRN/'.$year.'/';
			$this->data['itemData'] = $this->item->getItemList();
			$this->data['itemTypeData'] = $this->item->getItemGroup();
			$this->data['unitData'] = $this->item->itemUnits();
			$this->data['partyData'] = $this->party->getPartyList(0,'1,3');
			//$this->data['partyData'] = $this->party->getSupplierList();
			$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
			$this->load->view($this->grnForm,$this->data);
		else:
			return redirect(base_url('purchaseOrder'));
		endif;
	}
	
	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
    }

	public function getItemListForSelect(){
		$data = $this->input->post();
		
		if(!empty($data['order_id'])):
			$result = $this->purchaseOrder->getItemListForGrn($data['item_type'],$data['order_id']);
		else:
        	$result = $this->item->getItemListForSelect($data['item_type']);
		endif;
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Item Name</option>';
			foreach($result as $row):
				$selected = (!empty($data['item_id']) && $data['item_id'] == $row->id)?'selected':'';
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select Item Name</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
    
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
		
		if(empty($data['grn_no']))
			$errorMessage['grn_no'] = "GRN No. is required.";
		if(empty($data['party_id']))
			$errorMessage['party_id'] = "Supplier Name is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['general_error'] = "Item is required.";
		if(empty($data['type']))
			$errorMessage['type'] = "Grn Type is required.";

		if(!empty($data['item_id'])):
			foreach($data['location_id'] as $key=>$value):
				if(empty($value)):
					$errorMessage['general_error'] = "Location is required.";
					break;
				endif;
			endforeach;
		endif;
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$masterData = [ 
				'id' => $data['grn_id'],
				'type' => $data['type'],
				'order_id' => $data['order_id'],
				'grn_prefix' => $data['grn_prefix'], 
				'grn_no' => $data['grn_no'], 
				'grn_date' => date('Y-m-d',strtotime($data['grn_date'])),
				'party_id' => $data['party_id'], 
				'transport' => $data['transport'], 
				//'lr_no' => $data['lr_no'], 
				//'lr_date' => (!empty($data['lr_date'])) ? $data['lr_date'] : NULL, 
				'vehicle_no' => $data['vehicle_no'], 
				'challan_no' => $data['challan_no'], 
				'challan_date' => $data['challan_date'], 
				'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'item_type' => $data['item_type'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'batch_no' => $data['batch_no'],
				'po_trans_id' => $data['po_trans_id'],
				'so_id' => $data['so_id'],
				'location_id' => $data['location_id'],
				'qty' => $data['qty'],				
				'qty_kg' => $data['qty_kg'],
				'price' => $data['price'],
				'color_code' => $data['color_code'],
				'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->grnModel->save($masterData,$itemData));
		endif;
	}
	
	public function edit($id){
		if(empty($id)):
			return redirect(base_url('grn'));
		else:
			$this->data['grnData'] = $this->grnModel->editInv($id);
			$this->data['itemData'] = $this->item->getItemLists("2,3,4,5,6,7");
			$this->data['itemTypeData'] = $this->item->getItemGroup();
            $this->data['unitData'] = $this->item->itemUnits();
            $this->data['partyData'] = $this->party->getPartyList(0,'1,3');
            //$this->data['partyData'] = $this->party->getSupplierList();
			$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
			$this->data['fgItemList'] = $this->item->getItemList(1);
			$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
			$this->data['soList'] = $this->grnModel->getSoListForSelect($this->data['grnData']->party_id); 
			$this->load->view($this->grnForm,$this->data);
		endif;
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->delete($id));
		endif;
	}

	public function materialInspection(){
		$this->data['headData']->pageUrl = "grn/materialInspection";
		$this->data['tableHeader'] = getQualityDtHeader('materialInspection');
		$this->data['fgItemList'] = $this->item->getItemList(1);
		$this->load->view($this->inspection,$this->data);
	}

    // radhika 15-9-21
    public function purchaseMaterialInspectionList($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->grnModel->purchaseMaterialInspectionList($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;    
            $row->controller = "purchaseInvoice";        
			$row->status_label=""; $row->product_code = "";

			if($row->inspected_qty == "0.000"):
				$row->status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			else:
				if($row->inspection_status == "Ok"):
					$tcData = $this->grnModel->getTcInspectionParam(['grade_id'=>$row->material_grade_id,'batch_no'=>$row->batch_no]);
					if(empty($tcData)):
						$row->status_label = '<span class="badge badge-pill badge-success m-1">Accepted</span>';
					else:
						$row->status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
					endif;
				else:
					$row->status_label ='<span class="badge badge-pill badge-danger m-1">Rejected</span>';
				endif;
				if($row->is_approve == 1):
					$row->status_label ='<span class="badge badge-pill badge-info m-1">Approve</span>';
				endif; 
			endif;
            
			if(!empty($row->fgitem_id)):
				$fgData = $this->grnModel->getFinishGoods($row->fgitem_id);
				$item_code = array_column($fgData,'item_code');
				$row->product_code = implode(", ",$item_code);
			endif;

			$row->paramCount = $this->grnModel->getInInspection($row->id);
			$sendData[] = getPurchaseMaterialInspectionData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getInspectedMaterial(){
		$id = $this->input->post('id');
		$this->printJson($this->grnModel->getInspectedMaterial($id));
	}
	
	public function inspectedMaterialSave(){
		$data = $this->input->post();
		$errorMessage = array();
		$i=1;$total_qty = 0;
		foreach($data['item_id'] as $key=>$value):
			$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
			$data['reject_qty'][$key] = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
			$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;

			$total_qty = $inspected_qty + $data['ud_qty'][$key] + $data['reject_qty'][$key] + $data['scrape_qty'][$key];			
			if($total_qty > $data['recived_qty'][$key]):
				$errorMessage['recived_qty'.$i] = "Received Qty. mismatched.";
			endif;
			if(empty($data['tc_no'][$key])):
				$errorMessage['tc_no'.$i] = "T.C. No. is required.";
			endif;
			$i++;
		endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->grnModel->inspectedMaterialSave($data));
		endif;
    }	

	public function getItemsForGRN(){
		$party_id = $this->input->post('party_id');
		$this->printJson(["status" => 1,"itemOptions" => $this->grnModel->getItemsForGRN($party_id)]);
	}
	
	public function itemColorCode(){
		$this->printJson($this->grnModel->itemColorCode());
	}
	
	public function setFGItems(){
		$fgitem_id = $this->input->post('fgitem_id');	
		$fgItemList = $this->item->getItemList(1);		
		$fgOpt = '';
		if(!empty($fgItemList) ):
			foreach($fgItemList as $row):
				$selected = '';
				if(!empty($fgitem_id)){if (in_array($row->id,explode(',',$fgitem_id))) {$selected = "selected";}}
				$fgOpt .= '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
			endforeach;
		endif;
		$this->printJson(['status'=>1,'fgOpt'=>$fgOpt]);
	}
	
	public function migrateGrnItems(){
		$grnItems = $this->db->select('id,item_id')->where('is_delete',0)->get('grn_transaction')->result();
		foreach($grnItems as $row):
			$itemData = $this->item->getItem($row->item_id);
			$this->db->where('id',$row->id)->update('grn_transaction',['item_type'=>$itemData->item_type]);
		endforeach;
		echo "Migrate Success.";exit;
	}

	// meghavi
	public function approveInspection(){
		$data = $this->input->post();	
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->approveInspection($data));
		endif;
	}

	public function getGrnOrders(){ 
		$party = $this->input->post('party_id'); 
		$this->printJson($this->grnModel->getGrnOrders($party));
	}

	public function getInspectionData(){
		$data=$this->input->post();
	
		$inInspectData = $this->grnModel->getInInspection($data['id']);
		$paramData =  $this->item->getPreInspectionParam($inInspectData->item_id);
		$html='<div class="col-12 col-md-12"><table class="table table-sm top-table" >
				<tr>
					<td style="width:60%;vertical-align:top;">
					
					<b>Part Name :</b>'.(!empty($inInspectData->item_name) ? $inInspectData->item_name:"").'<br><br>
					
					</td>
					<td style="width:40%;vertical-align:top;">
					
						<b>Lot Qty.:</b>'.(!empty($inInspectData->qty) ? $inInspectData->qty:"").' <br><br>
						
					</td>
				</tr>
	        </table></div>';
	
	
			$prms = '';$spcf = '';$tolerance = '';$instr = '';$i=1;$sample = Array();$param_sample='';
			foreach($paramData as $param):
				$prms .= '<td>'.$param->parameter.'</td>';
				$spcf .= '<td>'.$param->specification.'</td>';
				//$tolerance .= '<td>'.$param->lower_limit.' - '.$param->upper_limit.'</td>';
				$tolerance .= '<td>'.$param->lower_limit.'</td>';
				$instr .= '<td>'.$param->measure_tech.'</td>';
				
				if(!empty($inInspectData->parameter_ids)):
					$os = json_decode($inInspectData->observation_sample);
					$sample[$i-1] = $os->{$param->id};
				else:
					$sample[$i-1] = ['','','','','','','','','','',''];
				endif;
				$i++;
			endforeach;
			$i= $i-1;
			for($x=$i;$x<9;$x++){
				$prms .= '<td>&nbsp;</td>';
				$spcf .= '<td>&nbsp;</td>';
				$tolerance .= '<td>&nbsp;</td>';
				$instr .= '<td>&nbsp;</td>';
				$sample[$x] = ['','','','','','','','','','',''];
			}
			for($r=0;$r<11;$r++){
				if($r <= 9){$param_sample .= '<tr class="text-center"><th>'.($r+1).'</th>';}
				else{$param_sample .= '<tr class="text-center"><th>Result</th>';}
				for($c=0;$c<9;$c++){
					$pl = count($sample[$c]);$count=1;
					$param_sample .= '<td>'.$sample[$c][$r].'</td>';
				}
				$param_sample .= '</tr>';
			}
		$html.='<table class="table  table-sm table-bordered top-table" style="margin-top:10px;border:1px solid">
			<tr class="text-center bg-grey">
				<th style="width: 10%;"></th>
				<th style="width: 10%;">A</th>
				<th style="width: 10%;">B</th>
				<th style="width: 10%;">C</th>
				<th style="width: 10%;">D</th>
				<th style="width: 10%;">E</th>
				<th style="width: 10%;">F</th>
				<th style="width: 10%;">G</th>
				<th style="width: 10%;">H</th>
				<th style="width: 10%;">I</th>
			</tr>
			<tr class="text-center">
				<th>Parameter</th>
				'.$prms.'
			</tr>
			<tr class="text-center">
				<th>Specification</th>
				'.$spcf.'
			</tr>
			<tr class="text-center">
				<th>Tolerance</th>
				'.$tolerance.'
			</tr>
			<tr class="text-center">
				<th>Instruments Use</th>
				'.$instr.'
			</tr>
			<tr class="text-center bg-grey">
				<th colspan="10" style="font-size:14px;">Observation on Samples</th>
			</tr>';
			$html.=$param_sample.'
		
		</table>';
		$html.='<div class="col-md-12">
			<label for="approval_remarks">Remarks</label>
			<input type="text" id="approval_remarks" class="form-control" value="'.$inInspectData->approval_remarks.'" />
		</div>';

		$this->printJson($html);
	}

	public function getGrnList(){
        $this->printJson($this->grnModel->getGrnList($this->input->post('grn_id')));
    }

	//Create By : Avruti @15-04-2022
	public function getSoListForSelect(){
		$party_id = $this->input->post('party_id'); 
		$so_id = $this->input->post('so_id'); 
		$result = $this->grnModel->getSoListForSelect($party_id); //print_r($result);exit;
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select So No.</option>';
			foreach($result as $row):
				$selected = (!empty($so_id) && $so_id == $row->id)?'selected':'';
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".getPrefixNumber($row->trans_prefix,$row->trans_no)."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select So No.</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

	//Created By Karmi @15/04/2022
	public function getPartyOrders(){
		$this->printJson($this->grnModel->getPartyOrders($this->input->post('party_id')));
	}
	
	//Test Report * Created By Meghavi @10/08/2022
	public function getTestReport(){
        $grn_id = $this->input->post('id');
        $this->data['dataRow'] = $this->grnModel->getTestReport($grn_id);
        $this->data['grn_id'] = $grn_id;
        $this->load->view($this->testReport,$this->data);
    }

    public function updateTestReport(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['name_of_agency']))
            $errorMessage['name_of_agency'] = "Agency Name is required.";
        if(empty($data['test_description']))
            $errorMessage['test_description'] = "Description is required.";
		if(empty($data['sample_qty']))
            $errorMessage['sample_qty'] = "Sample Qty is required.";


        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveTestReport($data));
        endif;
    }
	
	function printGrn($id){
		$this->data['grnData'] = $this->grnModel->editInv($id);
		$this->data['partyData'] = $this->party->getParty($this->data['grnData']->party_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$grnData = $this->data['grnData']; 
		
        $prepared_by = (!empty($grnData->created_by))? $this->employee->getEmp($grnData->created_by)->emp_name : '';
        
		$pdfData = $this->load->view('grn/printGrn',$this->data,true);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		
		$htmlFooter ='<table class="table" style="border-top:1px solid #000000;">
						<tr>
							<td style="width:10%;"></td>
							<td style="width:40%;" class="text-left"><b>Prepared By</b></td>
							<br><br>
							<td style="width:50%;" class="text-center"><b>for AKSHAR ENGINEERS</b></td>
						</tr>
						<br><br>
						<tr>
							<td style="width:10%;"></td>
							<td style="width:40%;" class="text-left">'.$prepared_by.'</td>
							<td style="width:50%;" class="text-center"><b>Authorised Signatory</b></td>
						</tr>
					</table>';
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,45,35,5,10,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	//Created By Avruti @14/08/2022
	public function deviationReport($id){
        $this->data['dataRow'] = $this->grnModel->getInInspectionMaterial($id);
		$product_code = "";
		$c=0;
		if(!empty($this->data['dataRow']->fgitem_id)):
			$fgData = $this->grnModel->getFinishGoods($this->data['dataRow']->fgitem_id);
			$item_code = array_column($fgData,'item_code');
			$product_code = implode(", ",$item_code);
		endif;
		$this->data['product_code'] = $product_code;
		$this->data['inInspectData'] = $this->grnModel->getInInspection($id);
		$this->data['dData'] = $this->grnModel->getInInspectionDeviation($id); 
		$this->data['paramData'] =  $this->item->getPreInspectionParam($this->data['dataRow']->item_id);
		$this->load->view($this->deviationReport,$this->data);
	}

	//Created By Avruti @14/08/2022
	public function saveDeviationReport(){
		$data = $this->input->post(); 
        $errorMessage = Array();

        $insParamData = $this->item->getPreInspectionParam($data['item_id']);
		$inInspectData = $this->grnModel->getInInspection($data['grn_trans_id']);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array(); $data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
				$obj = New StdClass;
                $param = Array();
				if(!empty($inInspectData)):
					$obj = json_decode($inInspectData->observation_sample);
				endif;
				if(!empty($obj->{$row->id})):
					if($obj->{$row->id}[10] == 'Not Ok'):
					
						$param[] = $data['observation_'.$row->id];
						unset($data['observation_'.$row->id]);

						$param[] = $data['qty_'.$row->id];
						unset($data['qty_'.$row->id]);

						$param[] = $data['deviation_'.$row->id];
						unset($data['deviation_'.$row->id]);

						$pre_inspection[$row->id] = $param;
						$param_ids[] = $row->id;
					endif;
				endif;		
            endforeach;
        endif;

		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
           
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveInInspection($data));
        endif;
	}

	//Created By Avruti @14/08/2022
	function printDeviation($id){
		$this->data['deviationData'] = $this->grnModel->getInInspectionDeviation($id); 
        $this->data['paramData'] = $this->item->getPreInspectionParam($this->data['deviationData']->item_id);

		$deviationData = $this->data['deviationData'];
		$devArray=array();
		if(!empty($deviationData->observation_sample)):
			$devId = json_decode($deviationData->observation_sample); 
			foreach($devId as $key=>$value):
				$devData=new stdClass();
				$fgData = $this->item->getPreInspectionParamForDeviation($key);
				$devData->parameter=$fgData->parameter; 
				$devData->specification=$fgData->specification; 
				$devData->observation_=$value[0];
				$devData->qty_=$value[1];
				$devData->deviation_=$value[2];
				$devArray[]=$devData;
			endforeach;
		endif;
		$this->data['devArray']=$devArray;

		$deviationData->fgCode="";
		if(!empty($deviationData->fgitem_id)):  $i=1;
			$fgData = $this->grnModel->getFinishGoods($deviationData->fgitem_id);
			$item_code = array_column($fgData,'item_code');
			$deviationData->fgCode = implode(", ",$item_code); 
		endif;

		$prepare = $this->employee->getEmp($deviationData->created_by);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($deviationData->created_at).')'; 
		$approveBy = '';
		if(!empty($deviationData->is_approve)){
			$approve = $this->employee->getEmp($deviationData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($deviationData->approve_date).')'; 
		}

		$logo = base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('grn/printDeviation',$this->data,true);

		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.4rem;width:50%">DEVIATION APPROVAL REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 15 00(01/06/2020)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Approved By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
					
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	// UPDATED BY MEGHAVI @27/06/2023
	public function inInspection($id){
        $this->data['dataRow'] = $this->grnModel->getInInspectionMaterial($id);
		$this->data['inInspectData'] = $this->grnModel->getInInspection($id);
		$this->data['paramData'] =  $this->item->getPreInspectionParam($this->data['dataRow']->item_id);
		$this->load->view($this->inInspection,$this->data);
	}
	
	public function inInspectionData(){
		$data = $this->input->post();
		$paramData = $this->item->getInspParamByRoute($data['item_id'],$data['inspection_route']);
		$inInspectData = $this->grnModel->getInInspection($data['grn_trans_id']);
		$tbodyData="";$i=1; 
		if(!empty($paramData)):
			foreach($paramData as $row):
				$obj = New StdClass;
				if(!empty($inInspectData)):
					$obj = json_decode($inInspectData->observation_sample); 
				endif;
				$inspOption = '';
				$inspOption  = '<option value="Ok" '.((!empty($obj->{$row->id}[5]) && $obj->{$row->id}[5]=="OK")?'selected':"").' >Ok</option><option value="Not Ok" '.((!empty($obj->{$row->id}[5]) && $obj->{$row->id}[5]=="Not Ok")?'selected':"").'>Not Ok</option>';
				$tbodyData.= '<tr>
							<td style="text-align:center;">'.$i++.'</td>
							<td>'.$row->parameter.'</td>
							<td>'.$row->specification.'</td>
							<td>'.$row->lower_limit.'</td>';
				for($c=0;$c<5;$c++):
					if(!empty($obj->{$row->id})):
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center parameter_limit" data-specification="'.$row->specification.'" data-lower_limit="'.preg_replace("/[^0-9.]/", "", $row->lower_limit).'" value="'.$obj->{$row->id}[$c].'"></td>';
					else:
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center parameter_limit" data-specification="'.$row->specification.'" data-lower_limit="'.preg_replace("/[^0-9.]/", "", $row->lower_limit).'" value=""></td>';
					endif;
				endfor;
			
				if(!empty($obj->{$row->id})):
					$tbodyData .= '<td><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control" value="'.$obj->{$row->id}[5].'">'.$inspOption.'</select></td>';
				else:
					$tbodyData .= '<td><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control" value="">'.$inspOption.'</select></td>';
				endif;
			endforeach;
		endif;
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
	}

	public function saveInInspection(){
		$data = $this->input->post();
        $errorMessage = Array(); 

		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";

        $insParamData = $this->item->getPreInspectionParam($data['item_id'],0,$data['inspection_route']);
            
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= 10; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['inspection_route']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveInInspection($data));
        endif;
	}

	public function inInspection_pdf($id){
		$this->data['inInspectData'] = $inInspectData = $this->grnModel->getInInspection($id);
		$this->data['paramData'] =  $this->item->getPreInspectionParam($inInspectData->item_id); //print_r($this->data['paramData']);exit;
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		$inInspectData->fgCode="";
		if(!empty($inInspectData->fgitem_id)): $i=1; 
			$fgData = $this->grnModel->getFinishGoods($inInspectData->fgitem_id);
			$item_code = array_column($fgData,'item_code');
			$inInspectData->fgCode = implode(", ",$item_code);
		endif;

		$prepare = $this->employee->getEmp($inInspectData->created_by);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($inInspectData->created_at).')'; 
		$approveBy = '';
		if(!empty($inInspectData->is_approve)){
			$approve = $this->employee->getEmp($inInspectData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($inInspectData->approve_date).')'; 
		}
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('grn/printInInspection',$this->data,true);
		
		$paramData = $this->data['paramData'];
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">INCOMING INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F/QC/01 (00/01.01.16)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Approved By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
		$mpdf->showWatermarkImage = true;
		//if(!empty($inInspectData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; }
		//else{ $mpdf->SetWatermarkText('UNApproved Copy',0.1);$mpdf->showWatermarkText = true; }
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
	
	public function getTcInspectionParam(){
        $data = $this->input->post();   

		$gateReceipt = $this->grnModel->getGrnTrans($data['grn_trans_id']);
        $this->data['dataRow'] = $this->materialGrade->getMaterial($gateReceipt->material_grade_id);
		
		if($data['type'] == 1):
    	    $this->data['specificationData'] = $this->grnModel->getMaterialSpecification($gateReceipt->material_grade_id);
		else: 
			$tcData = $this->grnModel->getTcInspectionParam(['grade_id'=>$gateReceipt->material_grade_id,'batch_no'=>$gateReceipt->batch_no]);
			$this->data['specificationData'] = $tcData;
		endif;
		$this->data['type'] = $data['type'];
		$this->data['gateReceipt'] = $gateReceipt;
		$this->data['tclist'] = $this->grnModel->getTcNumber($gateReceipt->material_grade_id,$gateReceipt->batch_no);
        $this->load->view($this->tc_inspection,$this->data);
    }

	public function getTcParamByTcNo(){
		$data = $this->input->post();
		$tcData = $this->grnModel->getTcInspectionParam(['grade_id'=>$data['grade_id'],'tc_no'=>$data['tc_no']]);
		$chemBody = $mechBody = $hardBody = '<tr>'; $i=1;
		foreach($tcData as $row): unset($row->id);
			if($row->spec_type == 1):
				$chemBody .= '<td>';
				$chemBody .= '<div class="input-group">';
				$chemBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$chemBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$chemBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$chemBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$chemBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$chemBody .= '<input type="hidden" name="min_value[]" id="min_1'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$chemBody .= '<input type="hidden" name="max_value[]" id="max_1'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$chemBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="1'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result" readOnly />';
				$chemBody .= '</div><br>';
				$chemBody .= '<div class="error 1'.$i.'"></div>';
				$chemBody .= '</td>'; $i++;
			endif;
			if($row->spec_type == 2):
				$mechBody .= '<td>';
				$mechBody .= '<div class="input-group">';
				$mechBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$mechBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$mechBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$mechBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$mechBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$mechBody .= '<input type="hidden" name="min_value[]" id="min_2'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$mechBody .= '<input type="hidden" name="max_value[]" id="max_2'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$mechBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="2'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result" readOnly />';
				$mechBody .= '</div><br>';
				$mechBody .= '<div class="error 2'.$i.'"></div>';
				$mechBody .= '</td>'; $i++;
			endif;
			if($row->spec_type == 6):
				$hardBody .= '<td>';
				$hardBody .= '<div class="input-group">';
				$hardBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$hardBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$hardBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$hardBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$hardBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$hardBody .= '<input type="hidden" name="min_value[]" id="min_3'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$hardBody .= '<input type="hidden" name="max_value[]" id="max_3'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$hardBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="3'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result" readOnly />';
				$hardBody .= '</div><br>';
				$hardBody .= '<div class="error 3'.$i.'"></div>';
				$hardBody .= '</td>'; $i++;
			endif;
		endforeach;
		$chemBody .= '</tr>';
		$mechBody .= '</tr>';
		$hardBody .= '</tr>';
		$this->printJson(['status' => 1, 'chemBody' => $chemBody, 'mechBody' => $mechBody, 'hardBody' => $hardBody]);
	}

    public function saveTcInspectionParam(){
        $data = $this->input->post();
        $errorMessage = array(); 
		if(empty($data['ref_tc_no'])):
			$errorMessage['ref_tc_no'] = "TC. No. is required.";
		else:
			if(!empty($data['reftc']) && $data['reftc'] == '-1'):
				$checkDuplicate = $this->grnModel->checkDuplicateTC($data['ref_tc_no']);
				if($checkDuplicate > 0):
					//$errorMessage['ref_tc_no'] = "TC. No. is already exist.";
				endif;
			endif; unset($data['reftc']);
		endif;
        if(empty($data['min_value']))
			$errorMessage['generalError'] = "Material Specification is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveTcInspectionParam($data));
        endif;
    }
	
    public function printTc($trans_id){
        $this->data['gateReceipt'] = $gateReceipt = $this->grnModel->getGrnTrans($trans_id);
        $this->data['dataRow'] = $this->materialGrade->getMaterial($gateReceipt->material_grade_id);
		$tcData = $this->grnModel->getTcInspectionParam(['grade_id'=>$gateReceipt->material_grade_id,'batch_no'=>$gateReceipt->batch_no]);
		if(empty($tcData)):
    	    $this->data['specificationData'] = $this->grnModel->getMaterialSpecification($gateReceipt->material_grade_id);
		else: 
			$this->data['specificationData'] = $tcData;
		endif;
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('grn/tc_print',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
						    <td style="width:25%;"></td>
							<td style="width:25%;"></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
	    $mpdf->SetWatermarkImage($logo,0.05,array(120,60)); 
	    $mpdf->showWatermarkImage = true;
		//$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,39,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

	public function fgOpeningStock(){
		$this->data['item_type'] = 1;
		$this->data['headData']->pageUrl = "grn/fgOpeningStock";
        $this->data['tableHeader'] = getStoreDtHeader('fgOpeningStock');
		$this->load->view($this->fg_opening_index,$this->data);
	}

	public function rmOpeningStock(){
		$this->data['item_type'] = 3;
		$this->data['headData']->pageUrl = "grn/rmOpeningStock";
        $this->data['tableHeader'] = getStoreDtHeader('fgOpeningStock');
		$this->load->view($this->fg_opening_index,$this->data);
	}

    public function getFgOpeningDTRows($item_type = 1){ 
		$data=$this->input->post();$data['item_type'] = $item_type;
		$result = $this->grnModel->getFgOpeningDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = "grn";    
            $sendData[] = getFgOpeningData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addFgStock($item_type = 1){
		if($item_type == 1){
			$this->data['fgList'] = $this->item->getItemList(1);
		}
		$this->data['item_type'] = $item_type;
		$this->data['itemList'] = $this->item->getItemList(3);
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
		$this->data['type']="";
		$this->load->view($this->fg_opening_form,$this->data);
	}

	public function getTcParamByMaterialGrade(){
		$data = $this->input->post();
		$tcData = $this->grnModel->getMaterialSpecification($data['grade_id']);
							
                           
		$chemBody = $mechBody = $hardBody = '<tr>'; $i=1;$chemThead="";$mechThead="";$hardThead=""; 
		$chemThead .='<tr class="text-center">';
						if(!empty($tcData)):
							foreach($tcData as $row):
								if($row->spec_type == 1):
									$chemThead .= '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
								endif;
							endforeach;
						endif;
		$chemThead .='</tr>';

		$mechThead .='<tr class="text-center">';
						if(!empty($tcData)):
							foreach($tcData as $row):
								if($row->spec_type == 2):
									$mechThead .= '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
								endif;
							endforeach;
						endif;
		$mechThead .='</tr>';

		$hardThead .='<tr class="text-center">';
						if(!empty($tcData)):
							foreach($tcData as $row):
								if($row->spec_type == 6):
									$hardThead .= '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
								endif;
							endforeach;
						endif;
		$hardThead .='</tr>';
		foreach($tcData as $row): unset($row->id);
			if($row->spec_type == 1):
				$chemBody .= '<td>';
				$chemBody .= '<div class="input-group">';
				$chemBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$chemBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$chemBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$chemBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$chemBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$chemBody .= '<input type="hidden" name="min_value[]" id="min_1'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$chemBody .= '<input type="hidden" name="max_value[]" id="max_1'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$chemBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="1'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result"  />';
				$chemBody .= '</div><br>';
				$chemBody .= '<div class="error 1'.$i.'"></div>';
				$chemBody .= '</td>'; $i++;
			endif;
			if($row->spec_type == 2):
				$mechBody .= '<td>';
				$mechBody .= '<div class="input-group">';
				$mechBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$mechBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$mechBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$mechBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$mechBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$mechBody .= '<input type="hidden" name="min_value[]" id="min_2'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$mechBody .= '<input type="hidden" name="max_value[]" id="max_2'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$mechBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="2'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result"  />';
				$mechBody .= '</div><br>';
				$mechBody .= '<div class="error 2'.$i.'"></div>';
				$mechBody .= '</td>'; $i++;
			endif;
			if($row->spec_type == 6):
				$hardBody .= '<td>';
				$hardBody .= '<div class="input-group">';
				$hardBody .= '<input type="hidden" name="id[]" value="'.((!empty($row->id))?$row->id:"").'" />';
				$hardBody .= '<input type="hidden" name="grade_id[]" value="'.((!empty($row->grade_id))?$row->grade_id:"").'" />';
				$hardBody .= '<input type="hidden" name="spec_type[]" value="'.((!empty($row->spec_type))?$row->spec_type:"") .'" />';
				$hardBody .= '<input type="hidden" name="param_name[]" value="'.((!empty($row->param_name))?$row->param_name:"") .'" />';
				$hardBody .= '<input type="hidden" name="sub_param[]" value="'.((!empty($row->sub_param))?$row->sub_param:"") .'" />';
				$hardBody .= '<input type="hidden" name="min_value[]" id="min_3'. $i .'" value="'.((!empty($row->min_value))?$row->min_value:"").'" />';
				$hardBody .= '<input type="hidden" name="max_value[]" id="max_3'. $i .'" value="'.((!empty($row->max_value))?$row->max_value:"").'" />';
				$hardBody .= '<input type="text" name="result[]" class="form-control floatOnly" data-rowid="3'.$i.'" value="'.((!empty($row->result))?$row->result:"").'" placeholder="Result"  />';
				$hardBody .= '</div><br>';
				$hardBody .= '<div class="error 3'.$i.'"></div>';
				$hardBody .= '</td>'; $i++;
			endif;
		endforeach;
		$chemBody .= '</tr>';
		$mechBody .= '</tr>';
		$hardBody .= '</tr>';
		$this->printJson(['status' => 1, 'chemBody' => $chemBody, 'mechBody' => $mechBody, 'hardBody' => $hardBody, 'chemThead' => $chemThead, 'mechThead' => $mechThead, 'hardThead' => $hardThead]);
	}

	public function saveFgStock(){
        $data = $this->input->post();
        $errorMessage = array(); 
		if(empty($data['ref_tc_no'])):
			$errorMessage['ref_tc_no'] = "TC. No. is required.";
		endif;
		if(empty($data['item_id'])):
			$errorMessage['item_id'] = "Item is required.";
		endif;
		if(empty($data['fg_item_id']) && $data['item_type'] == 1):
			$errorMessage['fg_item_id'] = "Finish Good is required.";
		endif;
		if(empty($data['material_grade'])):
			$errorMessage['material_grade'] = "Material Grade is required.";
		endif;
		if(empty($data['batch_no'])  && $data['item_type'] == 1 ):
			$errorMessage['batch_no'] = "Batch No is required.";
		endif;
		if(empty($data['qty'])):
			$errorMessage['qty'] = "Qty is required.";
		endif;
		if(empty($data['heat_no'])):
			$errorMessage['heat_no'] = "Heat No is required.";
		endif;
        if(empty($data['min_value']))
			$errorMessage['generalError'] = "Material Specification is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveFgStock($data));
        endif;
    }

	public function deleteFgStock(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->deleteFgStock($id));
		endif;
	}
}
?>