<?php 
class ProductReporModel extends MasterModel
{
    private $itemMaster = "item_master";
    private $partyMaster = "party_master";  
	private $stockTransaction = "stock_transaction"; 
	private $refTypeList = Array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production');

    public function getItemWiseStock($data)
	{	
		$itmData = $this->item->getItem($data['item_id']);
				
		$thead = '<tr><th colspan="5">Product : ('.$itmData->item_code.') '.$itmData->item_name.'</th></tr>
					<tr>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
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
					$queryData['select'] = "SUM(qty) as qty,batch_no,location_id";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result))
					{
						foreach($result as $row)
						{
							if($row->qty > 0):
								$tbody .= '<tr>';
									$tbody .= '<td class="text-center">'.$i++.'</td>';
									$tbody .= '<td>'.$lData['store_name'].'</td>';
									$tbody .= '<td>'.$batch->location.'</td>';
									$tbody .= '<td>'.$row->batch_no.'</td>';
									$tbody .= '<td>'.floatVal($row->qty).'</td>';
							$tbody .= '</tr>';
							endif;
						}
					}
				endforeach;
			}
		}
        return ['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody];
    }
}
?>