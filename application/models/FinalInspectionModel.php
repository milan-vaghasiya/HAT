<?php
class FinalInspectionModel extends MasterModel{
    private $inspectionTrans = "final_inspection_trans";
    private $jobRejection = "job_rejection";
    private $scrapeTrans = "job_return_material";
    private $jobCard = "job_card";

    public function getDTRows($data){
        if(!empty($data['job_id'])):
            $data['tableName'] = $this->jobRejection;
            $data['select'] = "job_rejection.*,item_master.item_name,item_master.item_code,process_master.process_name,job_inward.product_id,job_rejection.rejection_type_id";        
            $data['leftJoin']['job_card'] = "job_card.id = job_rejection.job_card_id";
            $data['leftJoin']['job_inward'] = "job_inward.id = job_rejection.job_inward_id";
            $data['leftJoin']['item_master'] = "item_master.id = job_inward.product_id";
            $data['leftJoin']['process_master'] = "process_master.id = job_rejection.rejection_type_id";
            $data['where']['job_rejection.pending_qty != '] = "0.000";
            $data['where']['job_rejection.job_card_id'] = $data['job_id'];
            $data['where']['job_rejection.type'] = 0;

			$data['searchCol'][] = "item_master.item_name";
            $data['searchCol'][] = "job_rejection.qty";
            $data['searchCol'][] = "job_rejection.pending_qty";
            $data['searchCol'][] = "process_master.process_name";

            return $this->pagingRows($data);
        else:
            $response = [
                "draw" => intval($data['draw']),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array()
            ];
            return $response;
        endif;
    }

    public function getInspectionTrans($ref_id){
        $queryData['tableName'] = $this->inspectionTrans;
        $queryData['where']['ref_id'] = $ref_id;
        $transData = $this->rows($queryData);

        $html = '';$i=1;
        foreach($transData as $row):
            $totalQty = $row->ok_qty + $row->ud_qty + $row->scrape_qty;
            $html .= '<tr>
                        <td class="text-center" style="width:5%;">'.$i++.'</td>
                        <td>'.$row->ok_qty.'</td>
                        <td>'.$row->ud_qty.'</td>
                        <td>'.$row->scrape_qty.'</td>
                        <td>'.$row->remark.'</td>
                        <td class="text-center" style="width:10%;">
                            <button type="button" onclick="trashInspection('.$row->id.','.$totalQty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                        </td>
                    </tr>';
        endforeach;

        return ['status'=>1,'htmlData'=>$html,'resultData'=>$transData];
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        //print_r($data);exit;
        $ok_qty = (!empty($data['ok_qty']))?$data['ok_qty']:0;
        $ud_qty = (!empty($data['ud_qty']))?$data['ud_qty']:0;
        $scrape_qty = (!empty($data['scrape_qty']))?$data['scrape_qty']:0;
        $totalOkQty = $ok_qty + $ud_qty;

        $setData = Array();
        $setData['tableName'] = $this->jobCard;
        $setData['where']['id'] = $data['job_card_id'];
        $setData['set']['unstored_qty'] = 'unstored_qty, + '.$totalOkQty;
        $setData['set']['total_reject_qty'] = 'total_reject_qty, - '.$totalOkQty;
        $this->setValue($setData);
        
        $setData = Array();
        $setData['tableName'] = $this->jobRejection;
        $setData['where']['id'] = $data['rejection_id'];
        $setData['set']['pending_qty'] = 'pending_qty, - '.($totalOkQty + $scrape_qty);
        $setData['set']['qty'] = 'qty, - '.$totalOkQty;
        $this->setValue($setData);

        $transData = [
            'id' => '',
            'ref_id' => $data['rejection_id'],
            'job_card_id' => $data['job_card_id'],
            'job_inward_id' => $data['job_inward_id'],
            'rejection_type_id' => $data['rejection_type_id'],
            'product_id' => $data['product_id'],
            'ok_qty' => $data['ok_qty'],
            'ud_qty' => $data['ud_qty'],
            'scrape_qty' => $data['scrape_qty'],
            'remark' => $data['remark'],
            'created_by' => $data['created_by']
        ];
        $insepctionSave = $this->store($this->inspectionTrans,$transData);

        if(!empty($data['scrape_qty'])):
            $scrapeTransData = [
                'id' => '',
                'type' => 3,
                'job_card_id' => $data['job_card_id'],
                'job_inward_id' => $data['job_inward_id'],
                'ref_id' => $insepctionSave['insert_id'],
                'item_id' => $data['product_id'],
                'qty' => $data['scrape_qty'],
                'operator_id' => $data['operator_id'],
                'machine_id' => $data['machine_id'],
                'created_by' => $data['created_by']
            ];
            $this->store($this->scrapeTrans,$scrapeTransData);
        endif;

        $result = ['status'=>1,'message'=>'Final Inspection saved successfully.','htmlData'=>$this->getInspectionTrans($data['rejection_id'])['htmlData']];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }

    public function delete($data){
        try{
            $this->db->trans_begin();
        $queryData['tableName'] = $this->inspectionTrans;
        $queryData['where']['id'] = $data['id'];
        $transData = $this->row($queryData);

        $totalOkQty = $transData->ok_qty + $transData->ud_qty;

        $setData = Array();
        $setData['tableName'] = $this->jobCard;
        $setData['where']['id'] = $transData->job_card_id;
        $setData['set']['unstored_qty'] = 'unstored_qty, - '.$totalOkQty;
        $setData['set']['total_reject_qty'] = 'total_reject_qty, + '.$totalOkQty;
        $this->setValue($setData);
        
        $setData = Array();
        $setData['tableName'] = $this->jobRejection;
        $setData['where']['id'] = $transData->ref_id;
        $setData['set']['pending_qty'] = 'pending_qty, + '.($totalOkQty + $transData->scrape_qty);
        $this->setValue($setData);

        $this->trash($this->inspectionTrans,['id'=>$data['id']]);

        if($transData->scrape_qty > 0):
            $this->trash($this->scrapeTrans,['ref_id'=>$data['id'],'type'=>3]);
        endif;

        $result = ['status'=>1,'message'=>'Record deleted successfully.','htmlData'=>$this->getInspectionTrans($data['rejection_id'])['htmlData']];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }
}
?>