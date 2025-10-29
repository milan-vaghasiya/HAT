<?php

class RmProcess extends MY_Controller
{
    private $indexPage = "rm_process/index";
    private $formPage = "rm_process/form";
    private $return_rm = "rm_process/return_rm";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "RM Process";
		$this->data['headData']->controller = "rmProcess";
		$this->data['headData']->pageUrl = "rmProcess";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->rmProcessModel->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            
            $recivedItem = $this->rmProcessModel->getRmProcessList($row->id);
            $row->return_itm = ''; $j=1;
            foreach($recivedItem as $itm):
                if(!empty($itm->item_id)){
                    if($j==1){
                        $row->return_itm .= $itm->item_name.' (Qty: '.$itm->qty.' '.$itm->unit_name.')'; 
                    } else { 
                        $row->return_itm .= '<br>'.$itm->item_name.' (Qty: '.$itm->qty.' '.$itm->unit_name.')'; 
                    } $j++;
                } 
            endforeach;
            
            $sendData[] = getRmProcessData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRmProcess(){
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['vendorList']=$this->party->getVendorList();
        $this->load->view($this->formPage,$this->data);
    }

	/**
	 * Updated By Mansee
	 * Date : 12-02-2022
	*/
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        // if(empty($data['log_date']))
        // $errorMessage['log_date'] = "Date is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');  
            $this->printJson($this->rmProcessModel->save($data));
        endif;
    }
	
    public function edit(){     
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['vendorList']=$this->party->getVendorList();
        $dataRow = $this->rmProcessModel->getRmProcess($this->input->post('id'));
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rmProcessModel->delete($id));
        endif;
    }

    public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->rmProcessModel->batchWiseItemStock($data);
        $this->printJson($result);
	}

    /*Created By : Avruti @21-3-2022 */
    public function returnRmProcess(){
        $id = $this->input->post('id'); 
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['dataRow'] = $this->rmProcessModel->getRmProcess($id);  
        $this->data['calData'] = $this->rmProcessModel->getRmProcessList($id); //print_r($this->data['calData']);exit;
        $this->load->view($this->return_rm,$this->data);
    }

    public function saveReturnRm(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        if(empty($data['return_item_id']))
            $errorMessage['return_item_id'] = "Item is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required."; 

        if(!empty($data['return_item_id'])):
            $pendingQty = $this->rmProcessModel->getReturnPending($data['trans_ref_id'],$data['ref_batch']);
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Qty not available for return.";
            endif;
        endif;
                      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $response = $this->rmProcessModel->saveReturnRm($data);
            $result = $this->rmProcessModel->getRmProcessList($data['ref_id']);
            $i=1;$tbodyData="";
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'RmProcess'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->location.'</td>
                            <td>'.$row->batch_no.'</td>    
                            <td class="text-center">
                            <a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashReturnRm('.$row->id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                            </td>
                            </tr>'; 
                            $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1, "tbodyData"=>$tbodyData]);
        endif;
    }

    public function deleteReturnRm(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $ref_id=$this->rmProcessModel->deleteReturnRm($id);
            $result = $this->rmProcessModel->getRmProcessList($ref_id);
            $i=1;$tbodyData=""; 
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'RmProcess'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->location.'</td>
                            <td>'.$row->batch_no.'</td>    
                            <td class="text-center">
                            <a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashReturnRm('.$row->id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                            </td>
                            </tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1, "tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function getJobOrderList(){
		$vendor_id = $this->input->post('vendor_id');
		$product_id = $this->input->post('item_id');
		$job_order_id = $this->input->post('job_order_id'); 
        $result = $this->rmProcessModel->getJobOrderList($vendor_id,$product_id); 
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Job Work Order</option>';
			foreach($result as $row):
				$selected = (!empty($job_order_id) && $job_order_id == $row->id)?'selected':'';
			
					$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected." >".getPrefixNumber($row->jwo_prefix,$row->jwo_no)."</option>";
		
				endforeach;
		else:
			$options .= '<option value="">Select Job Work Order</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
}
?>