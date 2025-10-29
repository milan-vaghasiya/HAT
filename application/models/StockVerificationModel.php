<?php 
class StockVerificationModel extends MasterModel
{
    private $itemMaster = "item_master";
	private $stockVerification = "stock_verification";
	private $stockTrans = "stock_transaction";

    public function getItemData($data,$item_type="")
    {
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*";
        if(!empty($item_type)){$data['where']['item_master.item_type'] = $item_type;}
		$data['searchCol'][] = "item_name";
        $data['searchCol'][] = "item_code";
		$columns =array('','item_name','item_code','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

	public function getVarifyData($item_id){
		$data['tableName'] = "stock_verification";
		$data['select'] = "SUM(stock_verification.physical_qty) as physicalQty, stock_verification.entry_date";
		$data['where']['item_id'] = $item_id;
		$data['order_by']['entry_date'] = 'DESC';
		$data['order_by']['id'] = 'DESC';
		$data['group_by'][] = 'stock_verification.entry_date';
		$data['limit'] = 1;
		return $this->row($data);
	}

    public function getItemWiseStock($data)
	{
		$itmData = $this->item->getItem($data['item_id']);
		$thead = '<tr>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
						<th>Physical Qty.</th>
						<th>Reason</th>
					</tr>';
		$tbody = '';
        $i=1;
		$locationData = $this->store->getStoreLocationList();
		if(!empty($locationData))
		{
			foreach($locationData as $lData)
			{
				foreach($lData['location'] as $batch):
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty, batch_no";
					$queryData['where']['item_id'] = $itmData->id;
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result))
					{
						foreach($result as $row)
						{
							if(floatVal($row->qty) > 0):
								$tbody .= '<tr>';
									$tbody .= '<td class="text-center">'.$i.'</td>';
									$tbody .= '<td>'.$lData['store_name'].'</td>';
									$tbody .= '<td>'.$batch->location.'</td>';
									$tbody .= '<td>'.$row->batch_no.'</td>';
									$tbody .= '<td>'.floatVal($row->qty).'</td>';
									$tbody .= '<td>
													<input class="form-control floatOnly" type="text" name="physical_qty[]" />
													<input type="hidden" name="stock_qty[]" value="'.floatVal($row->qty).'" />
													<input type="hidden" name="location_id[]" value="'.$batch->id.'" />
													<input type="hidden" name="batch_no[]" value="'.$row->batch_no.'" /></td>';
									$tbody .= '<td>
													<input class="form-control" type="text" name="reason[]" />
													<div class="error reason'.$i.'"></div>
												</td>';
								$tbody .= '</tr>'; 
								$i++;
							endif;
						}
					}
				endforeach;
			}
		}
        return ['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody];
    }

	public function save($data){ 
		foreach($data['physical_qty'] as $key=>$value):
			if($value != ''):
				$varQty = $value - $data['stock_qty'][$key];
				$verificationData = [
					'id' => '',
					'entry_date' => formatDate($data['entry_date'],'Y-m-d'),
					'item_id' => $data['item_id'],
					'location_id'=> $data['location_id'][$key],
					'batch_no'=> $data['batch_no'][$key],  
					'stock_qty' => $data['stock_qty'][$key],
					'physical_qty' => $value,
					'variation_qty' => $varQty,
					'reason' => $data['reason'][$key],
					'created_by' => $this->loginID
				];
				$verifyData = $this->store($this->stockVerification,$verificationData);

				/*** UPDATE STOCK TRANSACTION DATA ***/
				if($varQty != 0){
					if($varQty > 0){ $transType = 1; }else{ $transType = 2; }
					$stockQueryData['id']="";
					$stockQueryData['location_id'] = $data['location_id'][$key];
					if(!empty($data['batch_no'][$key])){$stockQueryData['batch_no'] = $data['batch_no'][$key];}
					$stockQueryData['trans_type'] = $transType;
					$stockQueryData['item_id'] = $data['item_id'];
					$stockQueryData['qty'] = $varQty;
					$stockQueryData['ref_type'] = 6;
					$stockQueryData['ref_id'] = $verifyData['insert_id'];
					$stockQueryData['ref_date']= formatDate($data['entry_date'],'Y-m-d');
					$stockQueryData['created_by'] = $this->loginID;
					$this->store($this->stockTrans,$stockQueryData);
				}
			endif;
		endforeach;
		return ['status'=>1,'message'=>"Stock Verified successfully."];
	}
}
?>