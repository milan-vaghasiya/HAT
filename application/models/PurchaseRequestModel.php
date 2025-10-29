<?php
class PurchaseRequestModel extends MasterModel{
    private $purchaseRequest = "purchase_request";
    
    public function getDTRows($data){ 
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.*,item_master.item_name,item_master.item_type,job_card.job_no,job_card.job_prefix,fg_item.item_code as fg_name";
        $data['leftJoin']['job_card'] = "purchase_request.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_request.req_item_id";
        $data['leftJoin']['item_master as fg_item'] = "purchase_request.fg_item_id = fg_item.id";
        if($data['status'] == 2){ 
            $data['where']['purchase_request.order_status'] = 3; 
            $data['where']['purchase_request.req_date >= '] = $this->startYearDate;
		    $data['where']['purchase_request.req_date <= '] = $this->endYearDate;
        }
        if($data['status'] == 1){ 
            $data['where']['purchase_request.order_status'] = 1; 
            $data['where']['purchase_request.req_date >= '] = $this->startYearDate;
		    $data['where']['purchase_request.req_date <= '] = $this->endYearDate;
        }
        if($data['status'] == 0){ $data['where_in']['purchase_request.order_status'] = '0,2'; }

        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";

        $columns =array('','','CONCAT(job_card.job_prefix,job_card.job_no)','job_material_dispatch.req_date','job_material_dispatch.req_item_id','job_material_dispatch.req_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPurchaseRequest($id){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.id,purchase_request.req_item_id,purchase_request.req_qty,item_master.item_name,item_master.item_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code, unit_master.unit_name,job_card.product_id as fgitem_id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_request.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['job_card'] = "purchase_request.job_card_id = job_card.id";
        $data['where']['purchase_request.id'] = $id;
        $result = $this->row($data);
        
        $result->fgitem_name = (!empty($result->fgitem_id))?$this->item->getItem($result->fgitem_id)->item_name:"";
        $result->igst = $result->gst_per;
        $result->sgst = $result->cgst = round(($result->gst_per/2),2); 
        $result->igst_amt = $result->sgst_amt = $result->cgst_amt = $result->amount = $result->net_amount = 0;
		$result->disc_per = $result->disc_amt = 0;
		$result->delivery_date = date('Y-m-d');
		$result->amount = round(($result->req_qty * $result->price),2); 
        if($result->gst_per > 0):
            $result->igst_amt = round((($result->amount * $result->gst_per)/100),2); 
			$result->sgst_amt = $result->cgst_amt = round(($result->igst_amt / 2));
        endif;
		$result->item_id=$result->req_item_id;
		$result->qty=$result->req_qty;
		unset($result->req_item_id,$result->req_qty);

		return $result;
    }

    public function getPurchaseRequestForOrder($id){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.*,fg_item.item_code as fgitem_code,item_master.item_name,item_master.item_code,item_master.item_type,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code, unit_master.unit_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_request.req_item_id";
        $data['leftJoin']['item_master as fg_item'] = "fg_item.id = purchase_request.fg_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        //$data['leftJoin']['job_card'] = "purchase_request.job_card_id = job_card.id";
        $data['where']['purchase_request.id'] = $id;
        $itemData = $this->rows($data);
        
        $result = array(); $senddata = array();
        //$itemData = json_decode($prdata->item_data);
        if(!empty($itemData)):
            foreach($itemData as $item):
                
                // $data = array();
                // $data['tableName'] = 'item_master';
                // $data['select'] = "";
                // $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
                // $data['where']['item_master.id'] = $item->req_item_id;
                // $result = $this->row($data);
    
                $item->fgitem_name = (!empty($item->fgitem_code))? $item->fgitem_code : "";
                $item->igst = $item->gst_per;
                $item->sgst = $item->cgst = round(($item->gst_per/2),2); 
                $item->igst_amt = $item->sgst_amt = $item->cgst_amt = $item->amount = $item->net_amount = 0;
                $item->disc_per = $item->disc_amt = 0;
                $item->delivery_date = date('Y-m-d');
                $item->amount = round(($item->req_qty * $item->price),2); 
                if($item->gst_per > 0):
                    $item->igst_amt = round((($item->amount * $item->gst_per)/100),2); 
                    $item->sgst_amt = $item->cgst_amt = round(($item->igst_amt / 2),2);
                endif;
                $item->net_amount = round(($item->igst_amt + $item->amount),2);
                $item->item_id=$item->req_item_id;
                $item->qty=$item->req_qty;
                $item->fgitem_id=0;
                $item->fgitem_name='';
                unset($item->req_item_id,$item->req_qty);
    
                $senddata[] = $item;
            endforeach;
        endif;
		return $senddata;
    }

    public function getPurchaseReqForEnq($id){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.id,purchase_request.req_item_id,purchase_request.req_qty,item_master.item_name,item_master.item_code,item_master.item_type,item_master.price,item_master.unit_id, unit_master.unit_name,job_card.product_id as fgitem_id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_request.req_item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['job_card'] = "purchase_request.job_card_id = job_card.id";
        $data['where']['purchase_request.id'] = $id;
        $result = $this->row($data);
        
		$result->fgitem_name = (!empty($result->fgitem_id))?$this->item->getItem($result->fgitem_id)->item_name:"";
		$result->qty=$result->req_qty;
		unset($result->req_item_id,$result->req_qty);
		return $result;
    }
    
    public function approvePreq($data) {
        $this->store($this->purchaseRequest, ['id'=> $data['id'], 'order_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }
    
    public function closePreq($data) {
        $this->store($this->purchaseRequest, ['id'=> $data['id'], 'order_status' => $data['val']]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }
    
    /*  Change By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : Sales Enquiry No
    */  
    public function getPurchaseOrder(){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.*";
        $data['where_in']['purchase_request.order_status'] = '2';
        $resultData = $this->rows($data);

        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<tr>
                    <td class="text-center">
                        <input type="checkbox" id="md_checkbox_'.$i.'" name="pr_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                    </td>
                    <td class="text-center">'.$row->req_item_name.'</td>
                    <td class="text-center">'.$row->req_qty.'</td>
                </tr>'; $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function createPurchaseOrder($data){ 
        if(!empty($data)): //print_r($data['pr_id']);exit;
            $senddata = array();
            foreach($data['pr_id'] as $key => $value):
                $data['tableName'] = $this->purchaseRequest;
                $data['select'] = "purchase_request.*,item_master.item_name,item_master.item_code,item_master.item_type,item_master.gst_per,item_master.price,item_master.unit_id,item_master.hsn_code, unit_master.unit_name";
                $data['where']['purchase_request.id'] = $value;
                $data['leftJoin']['item_master'] = "item_master.id = purchase_request.req_item_id";
                $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
                $itemData = $this->rows($data);

                //$itemData = json_decode($prdata->item_data);
                if(!empty($itemData)):
                    foreach($itemData as $item):
                        $item->fgitem_name = (!empty($item->fgitem_id))?$this->item->getItem($item->fgitem_id)->item_name:"";
                        $item->igst = $item->gst_per;
                        $item->sgst = $item->cgst = round(($item->gst_per/2),2); 
                        $item->igst_amt = $item->sgst_amt = $item->cgst_amt = $item->amount = $item->net_amount = 0;
                        $item->disc_per = $item->disc_amt = 0;
                        $item->delivery_date = date('Y-m-d');
                        $item->amount = round(($item->req_qty * $item->price),2); 
                        if($item->gst_per > 0):
                            $item->igst_amt = round((($item->amount * $item->gst_per)/100),2); 
                            $item->sgst_amt = $item->cgst_amt = round(($item->igst_amt / 2));
                        endif;
                        $item->item_id=$item->req_item_id;
                        $item->qty=$item->req_qty;
                        $item->fgitem_id=0;
                        $item->fgitem_name='';
                        unset($item->req_item_id,$item->req_qty);
            
                        $senddata[] = $item;
                    endforeach;
                endif;
            endforeach;
            return $senddata;
        endif;
    }
	
	/*  Create By : Avruti @27-11-2021 1:00 PM
    update by : 
    note : 
    */
    //----------------------------- API Function Start -------------------------------------------//

    public function getCount($status = 0, $type = 0){
        $data['tableName'] = $this->purchaseRequest;
        if($status == 2){ $data['where']['purchase_request.order_status'] = 3; }
        if($status == 1){ $data['where']['purchase_request.order_status'] = 1; }
        if($status == 0){ $data['where_in']['purchase_request.order_status'] = '0,2'; }
        return $this->numRows($data);
    }

    public function getPurchaseRequestList_api($limit, $start, $status = 0, $type = 0){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.*,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "purchase_request.job_card_id = job_card.id";
        if($status == 2){ $data['where']['purchase_request.order_status'] = 3; }
        if($status == 1){ $data['where']['purchase_request.order_status'] = 1; }
        if($status == 0){ $data['where_in']['purchase_request.order_status'] = '0,2'; }
        
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

//------------------------------ API Function End --------------------------------------------//
}
?>