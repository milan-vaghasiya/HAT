<?php 
class DispatchPlanModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $dispatch_plan = "dispatch_plan";
   
    public function getDispatchPlan($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.id as so_id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.doc_date,trans_main.trans_date, trans_main.remark, trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date, party_master.party_code, party_master.currency, item_master.packing_qty as packingQty, item_master.size as item_size';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_child.trans_status'] = 0;
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
	public function getWIPQtyForDispatchPlan($data){
        $queryData['tableName'] = "job_card";
        $queryData['select'] = "SUM(job_card.qty) as job_qty ,SUM(total_out_qty) as total_out_qty";
        $queryData['where']['job_card.product_id'] = $data['item_id'];
        $queryData['customWhere'][] = "(job_card.sales_order_id = ".$data['sales_order_id']." OR job_card.sales_order_id = 0)";
        $queryData['where']['job_card.order_status !=']= 4;
		return $this->row($queryData);
    }

    public function nextTransNo(){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] = date("Y");
        $data['where']['MONTH(trans_date)'] = date("m");
        $data['tableName'] = $this->dispatch_plan;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function savePlan($data){
        try{
            $this->db->trans_begin();
            $trans_no = $this->nextTransNo();
            $trans_prefix = 'P'.n2y(date("Y")).n2m(date("m"));
            $trans_number = $trans_prefix.sprintf("%04d",$trans_no);
            foreach($data['so_trans_id'] as $key=>$value){

                if($data['plan_qty'][$key] > 0){
                    
                    $soData = $this->salesOrder->getSalesOrderData($value);
                    $planData=[
                        'id'=>'',
                        'trans_date'=>date("Y-m-d"),
                        'trans_no'=>$trans_no,
                        'trans_prefix'=>$trans_prefix,
                        'trans_number'=>$trans_number,
                        'so_trans_id'=>$value,
                        'so_id'=>$soData->trans_main_id,
                        'item_id'=>$soData->item_id,
                        'qty'=>$data['plan_qty'][$key],
                        'created_by'=>$this->loginId,
                    ];
                    $result = $this->store($this->dispatch_plan,$planData);
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $value;
                    $setData['set']['plan_qty'] = 'plan_qty, + '.$data['plan_qty'][$key];
                    $this->setValue($setData);
                }
                
            }
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function getPlanDTRows($data){
        $data['tableName'] = $this->dispatch_plan;
        $data['select'] = 'dispatch_plan.*,trans_main.trans_no as so_no,trans_main.trans_prefix as so_prefix,party_master.party_code,item_master.item_name';
        $data['leftJoin']['trans_main'] = "trans_main.id = dispatch_plan.so_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = dispatch_plan.so_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = dispatch_plan.item_id";
        
        $data['order_by']['dispatch_plan.trans_date'] = "ASC";
       

        $data['searchCol'][] = "DATE_FORMAT(dispatch_plan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "dispatch_plan.trans_number";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "party_master.party_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "dispatch_plan.qty";
       
		$columns =array('','','dispatch_plan.trans_date','dispatch_plan.trans_number','trans_main.trans_no','party_master.party_code','titem_master.item_name','tdispatch_plan.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPlanData($trans_number){
        $data['tableName'] = $this->dispatch_plan;
        $data['select'] = 'dispatch_plan.*,trans_main.trans_no as so_no,trans_main.trans_prefix as so_prefix,party_master.party_code,item_master.item_name,trans_child.cod_date,trans_main.doc_no';
        $data['leftJoin']['trans_main'] = "trans_main.id = dispatch_plan.so_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = dispatch_plan.so_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = dispatch_plan.item_id";
        $data['where']['dispatch_plan.trans_number'] = $trans_number;
        $data['where']['trans_child.trans_status'] = 0;
        $data['order_by']['dispatch_plan.trans_date'] = "ASC";

        return $this->rows($data);
    }
}
?>