<?php
class QcInstrumentModel extends MasterModel{
    private $itemMaster = "qc_instruments";
    private $stockTrans = "stock_transaction";
    private $qc_indent = "qc_indent";
    private $calibration = 'calibration';
    private $qcChallan = "qc_challan";
    private $qcChallanTrans = "qc_challan_trans";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "qc_instruments.*, CONCAT('[',item_category.category_name,'] ',item_category.category_name) as category_name";
        $data['leftJoin']['item_category'] = "item_category.id = qc_instruments.category_id";
        $data['where']['qc_instruments.item_type'] = $data['item_type'];
         
        if(empty($data['status'])){$data['status'] = 0;}
        
        if($data['status'] != 5){ $data['where']['qc_instruments.status'] = $data['status']; }
        else // Due For Calibration
        {
            $data['where_in']['qc_instruments.status'] = "1,2";
            $data['customWhere'][] = "DATE_SUB(qc_instruments.next_cal_date, INTERVAL (qc_instruments.cal_reminder + 1) DAY) <= '".date('Y-m-d')."'";
        }
        
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "qc_instruments.item_code";
        $data['searchCol'][] = "qc_instruments.mfg_sr";
        $data['searchCol'][] = "qc_instruments.item_name";
        $data['searchCol'][] = "qc_instruments.make_brand";
        $data['searchCol'][] = "qc_instruments.cal_required";
        $data['searchCol'][] = "qc_instruments.cal_freq";
        $data['searchCol'][] = "qc_instruments.location_name";
        
		$columns =array('','','qc_instruments.item_code','qc_instruments.mfg_sr','qc_instruments.item_name','qc_instruments.make_brand','qc_instruments.cal_required','qc_instruments.cal_freq','qc_instruments.location_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        //$this->printQuery();
        return $result;
    }

    public function getSerialWiseDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "qc_instruments.*,calibration.cal_by,calibration.cal_date,calibration.next_cal_date";
        $data['leftJoin']['calibration'] = "qc_instruments.item_id = calibration.item_id AND qc_instruments.item_code = calibration.batch_no AND calibration.is_active = 1 AND calibration.is_delete = 0";
        $data['where']['qc_instruments.item_type'] = 1;
        $data['where']['qc_instruments.is_delete'] = 0;
        if(!empty($data['status'])){ $data['customWhere'][] = "(calibration.next_cal_date <= '".date('Y-m-d')."' OR calibration.next_cal_date IS NULL OR calibration.next_cal_date = '')"; }
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "qc_instruments.item_code";
        $data['searchCol'][] = "qc_instruments.item_name";
        $data['serachCol'][] = "qc_instruments.make_brand";
        $data['serachCol'][] = "qc_instruments.mfg_sr";

		$columns =array('','','qc_instruments.item_code','qc_instruments.item_name','qc_instruments.make_brand','qc_instruments.mfg_sr');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getItem($id){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'qc_instruments.*,item_category.category_name,item_category.category_code as cat_code';
        $data['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        $data['where']['qc_instruments.id'] = $id;
        return $this->row($data);
    }
    
    public function save($data){
        if(empty($data['id'])):
            if($data['item_type'] == 1){
                $postData = [
                    'category_id'=>$data['category_id']
                ];
                $generateCode = $this->getDataForGenerateCode($postData);
            }else{
                $postData = [
                    'category_id'=>$data['category_id']
                ];
                $generateCode = $this->getDataForGenerateCode($postData); 
            }
            $catData = $this->itemCategory->getCategory($data['category_id']);

            $serial_no = (!empty($generateCode->serial_no)?$generateCode->serial_no+1:1);
            $code = $catData->category_code.sprintf("%03d",$serial_no);
            $name = $code.' '.$catData->category_name.' '.$data['size'];
    
            $data['item_code']=$code;
            $data['serial_no']=$serial_no;
            $data['item_name']=$name;
            $data['cat_name']=$catData->category_name;
            $data['cat_code']=$catData->category_code;
            $data['status']=1;
			
			if(containsWord($data['cat_name'], 'thread')){
			}else{
			    $data['thread_type']=NULL;
			}
        endif;

        $chkDuplicate = array();
        if($data['item_type'] == 2){
             $msg = 'Instrument';
             $chkDuplicate=[
                'id'=>$data['id'],
                'instrument_range'=>$data['instrument_range'],
                'least_count'=>$data['least_count'],
                'category_id'=>$data['category_id'],
                'item_type'=>$data['item_type'],
            ];
        }else{
            $msg = 'Gauge';
            $chkDuplicate=[
                'id'=>$data['id'],
                'size'=>$data['size'],
                'category_id'=>$data['category_id'],
                'item_type'=>$data['item_type'],
            ];
        }
		$data['next_cal_date'] = (!empty($data['last_cal_date']) && !empty($data['cal_freq'])) ? date('Y-m-d', strtotime($data['last_cal_date'] . "+".$data['cal_freq']." months")): NULL;
        $data['item_name'] = $data['item_code'].' '.$data['cat_name'].' '.(($data['item_type']==2)?$data['instrument_range'] : $data['size']);
        unset($data['cat_name'],$data['cat_code']);
        return $this->store($this->itemMaster,$data);
	}

    public function checkDuplicate($postData){
        $data['tableName'] = $this->itemMaster;
        if(!empty($postData['item_type'])){$data['where']['item_type'] = $postData['item_type'];}
        if(!empty($postData['category_id'])){$data['where']['category_id'] = $postData['category_id'];}
        if(!empty($postData['size'])){$data['where']['size'] = $postData['size'];}
        if(!empty($postData['instrument_range'])){$data['where']['instrument_range'] = $postData['instrument_range'];}
        if(!empty($postData['least_count'])){$data['where']['least_count'] = $postData['least_count'];}
        if(!empty($postData['id']))
            $data['where']['id !='] = $postData['id'];

        return $this->numRows($data);
    }

    public function delete($id){
		$itemData = $this->getItem($id);
        return $this->trash($this->itemMaster,['id'=>$id]);
    }

    public function saveRejectGauge($data){
        return $this->edit($this->itemMaster,['id'=>$data['id']],['status'=>4,'reject_reason'=>$data['reject_reason'],'rejected_at'=>date('Y-m-d H:i:s'),'rejected_by'=>$this->loginId]);
    }
    
    public function getActiveInstruments(){
        $data['tableName'] = $this->itemMaster;
        $data['where']['status'] = 1;
        return $this->rows($data);
    }

    public function saveCalibration($data){
        try{
            $this->db->trans_begin();
            $this->edit($this->calibration,['item_id'=>$data['item_id'],'batch_no'=>$data['batch_no']],['is_active'=>0],'Instruments');
            $data['is_active'] = 1;
            $result = $this->store($this->calibration,$data,'Calibration');

            $this->edit('qc_instruments',['id'=>$data['item_id']],['last_cal_date'=>$data['cal_date'],'cal_agency'=>$data['cal_agency'],'next_cal_date'=>$data['next_cal_date']],'Instruments');
		
            //$this->edit('qc_instruments', ['id'=>$data['item_id']], ['status'=>1]);

            $update = [
                'receive_by'=>$data['created_by'],
                'receive_at'=>$data['cal_date'],
                'to_location'=>$data['to_location'],
                'in_ch_no'=>$data['cal_certi_no']
                //'trans_status'=>1
            ];
            $this->edit($this->qcChallanTrans, ['id'=>$data['challan_trans_id']], $update);
            
            // $setData = Array();
            // $setData['tableName'] = $this->qcChallan;
            // $setData['where']['id'] = $data['challan_id'];
            // $setData['set']['receive_qty'] = 'receive_qty, + 1';
            // $this->setValue($setData);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	 
   	}
    
    //----------------------------------------//
    public function getDataForGenerateCode($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'qc_instruments.item_code,MAX(serial_no) as serial_no,qc_instruments.least_count,qc_instruments.instrument_range,item_category.category_name,qc_instruments.item_type,qc_instruments.instrument_range,qc_instruments.size,item_category.category_code';
        $data['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        if(!empty($postData['category_id'])){ $data['where']['qc_instruments.category_id'] =$postData['category_id']; }
        if(!empty($postData['instrument_range'])){ $data['where']['qc_instruments.instrument_range'] =$postData['instrument_range']; }
        if(!empty($postData['least_count'])){ $data['where']['qc_instruments.least_count'] =$postData['least_count']; }
        if(!empty($postData['item_type'])){ $data['where']['qc_instruments.item_type'] =$postData['item_type']; }
        if(!empty($postData['size'])){ $data['where']['qc_instruments.size'] =$postData['size']; }
        if(!empty($postData['category_code'])){ $data['where']['item_category.category_code'] =$postData['category_code']; }
        if(!empty($postData['item_code'])){ $data['where']['qc_instruments.item_code'] =$postData['item_code']; }
        return $this->row($data);
    }
    
    public function getNiextItemNo($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "MAX(store_id) as item_code";
        if(!empty($postData['item_type'])){ $data['where']['qc_instruments.item_type'] =$postData['item_type']; }
        if(!empty($postData['category_id'])){ $data['where']['qc_instruments.category_id'] =$postData['category_id']; }
        $maxNo = $this->specificRow($data)->item_code;
        $nextItemCode = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextItemCode;
    }

    public function getInstrumentByCode($item_code){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'qc_instruments.*,item_category.category_name,item_category.category_code as cat_code';
        $data['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        $data['where_in']['qc_instruments.part_no'] = $item_code;
        return $this->rows($data);
    }

    public function getInstrumentCodeWiseList(){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'qc_instruments.item_code,MAX(store_id) as serial_no,qc_instruments.least_count,qc_instruments.instrument_range,item_category.category_name,qc_instruments.item_type,qc_instruments.instrument_range,qc_instruments.size,item_category.category_code';
        $data['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        $data['where_in']['item_type'] = '1,2';
        $data['group_by'][]='category_id';
        $data['group_by'][] = 'item_code';
        return $this->rows($data);
    }


    /* Created By :- Sweta @08-08-2023 */
    public function getGaugeInstList($type="0"){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "qc_instruments.id,qc_instruments.item_code,qc_instruments.item_name";
		$data['where']['status'] = 1; 
		if(!empty($type))
			$data['where_in']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
}