<?php
class MaterialRequestModel extends MasterModel{
    private $jobMaterialDispatch = "job_material_dispatch";
    private $purchaseRequest = "purchase_request";

    public function nextReqNo(){
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "MAX(req_no) as req_no";
		$data['where']['req_date >= '] = $this->startYearDate;
		$data['where']['req_date <= '] = $this->endYearDate;
		$req_no = $this->specificRow($data)->req_no;
		$nextReqNo = (!empty($req_no))?($req_no + 1):1;
		return $nextReqNo;
    }

    public function getDTRows($data){
       
        $data['tableName'] = $this->purchaseRequest;
        $data['select'] = "purchase_request.*,item_master.item_code as fg_name";
        $data['leftJoin']['item_master'] = "purchase_request.fg_item_id = item_master.id";
        $data['where']['purchase_request.fg_item_id !='] = 0;
        $data['where']['purchase_request.req_date >= '] = $this->startYearDate;
        $data['where']['purchase_request.req_date <= '] = $this->endYearDate;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(purchase_request.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "";

        $columns =array('','','','purchase_request.req_date','item_master.item_name','job_material_dispatch.req_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    //Changed By Avruti @15/08/2022
    public function save($data,$itemData){
        try{
            $this->db->trans_begin();
         if(!empty($data['id'])):
            $reqData=$this->getRequestEditData($data['req_no']);
            foreach($reqData as $key=>$value):
                if(!in_array($value->id,$itemData['id'])):		
                    $this->trash($this->purchaseRequest,['id'=>$value->id]);
                endif;
            endforeach;
        endif;    
            foreach($itemData['req_item_id'] as $key=>$value):
                $transData = [
                                'id' => $itemData['id'][$key],
                                'material_type' => $data['material_type'],
                                'req_date' => $data['req_date'],
                                'dispatch_date' => $data['dispatch_date'],
                                'fg_item_id' => $data['fg_item_id'],
                                'req_no' => $data['req_no'],
                                'req_item_id' => $value,
                                'req_item_name' => $itemData['req_item_name'][$key],
                                'req_qty' => $itemData['req_qty'][$key],
                                'remark' => $data['remark'],
                                'created_by'  => $this->session->userdata('loginId')
                            ];
                $this->store($this->purchaseRequest,$transData);
            endforeach;
            $result = ['status'=>1,'message'=>'Material Request send successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getRequestData($id){
        $data['tableName'] = $this->purchaseRequest;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getRequestEditData($req_no){
        $data['tableName'] = $this->purchaseRequest;
        $data['where']['req_no'] = $req_no;
        return $this->rows($data);
    }

    // //Changed By Karmi @10/08/2022
    //   public function getRequestData($id){
    //     $data['tableName'] = $this->jobMaterialDispatch;
    //     $data['select'] = "job_material_dispatch.*,job_card.product_id,item_master.item_name";
    //     $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
    //     $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
    //     $data['where']['job_material_dispatch.id'] = $id;
    //     return $this->row($data);
    // }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->purchaseRequest,['id'=>$id],'Request');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	/*  Create By : Avruti @27-11-2021 3:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->jobMaterialDispatch;	
        $data['where']['job_material_dispatch.req_type'] = 1;
        return $this->numRows($data);
    }

    public function getMaterialRequestList_api($limit, $start){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['where']['job_material_dispatch.req_type'] = 1;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>