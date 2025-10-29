<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Items extends MY_Controller
{
    private $indexPage = "item/index";
    private $itemForm = "item/form";
    private $rmForm = "item/rm_form";
    private $itemStockUpdateForm = "item/stock_update";
    private $itemOpeningStockForm = "item/opening_update";
    private $importData = "item/import";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Items";
		$this->data['headData']->controller = "items";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "items/pitems";
        $this->data['item_type'] = 3;
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function pitems(){
        $this->data['headData']->pageUrl = "items/pitems";
        $this->data['item_type'] = 3;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function consumable(){
        $this->data['headData']->pageUrl = "items/consumable";
        $this->data['item_type'] = 2;
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($item_type){  

        $result = $this->item->getDTRows($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            //$itmStock = $this->store->getItemStockRTD($row->id,$row->item_type);
            $row->qty = 0;
            if(!empty($row->stock_qty)){ $row->qty = $row->stock_qty;}
            $sendData[] = getItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* Updated at : 09-12-2021 [Milan chauhan] */
    public function addItem($item_type){
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        $this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->data['fgCodeList'] = $this->item->getItemList(1);
        
        if($item_type == 3):
            $this->load->view($this->rmForm,$this->data);
        else:        
			$this->data['subGroupList'] = $this->masterDetail->getMasterDetailsByCategory(['type'=>4]);
            $this->load->view($this->itemForm,$this->data);
        endif;
    }
    
	/* Updated at : 09-12-2021 [Milan chauhan] */
	/* Updated at : 25-12-2023 [Meghavi Faldu] */
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
		
		if($data['item_type'] == 3){
		    if(empty($data['material_grade_id']))
				$errorMessage['material_grade_id'] = "Grade is required.";
			if(empty($data['item_code']))
				$errorMessage['item_code'] = "Item Code is required.";
		}else{
			if(empty($data['item_name']))
				$errorMessage['item_name'] = "Item Name is required.";
            if(empty($data['sub_group']))
                $errorMessage['sub_group'] = "Sub Group is required.";
		}
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if($data['rm_type'] == 0):
				$data['item_name'] = $data['itmsize'].' ';
				$data['item_name'] .= $data['itmshape'].' ';
				$data['item_name'] .= $data['itmbartype'].' ';
				// $data['item_name'] .= $data['itmmaterialtype'];
    			$data['item_image'] =  $data['itmsize'] . '☻' . $data['itmshape'] . '☻' . $data['itmbartype'] . '☻' ; //. '☻' . $data['gname'];// . '☻' . $data['sbname'];
				$data['size'] = $data['itmsize'];
				
				
            else:
                $size = Array();
                if(!empty($data['diameter'])){$size[] = $data['diameter'];}
                if(!empty($data['length'])){$size[] = $data['length'];}
                if(!empty($data['flute_length'])){$size[] = $data['flute_length'];}
                $data['size'] = (!empty($size)) ? implode('X',$size) : NULL;
                
                unset($data['diameter'],$data['length'],$data['flute_length']);
			endif;
            unset($data['itmsize'],$data['itmshape'],$data['itmbartype']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data)); 
        endif;
    }
	
    /* Updated at : 09-12-2021 [Milan chauhan] */
	public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['dataRow'] = $dataRow = $this->item->getItem($id);
        $this->data['categoryList'] = $this->item->getCategoryList($this->data['dataRow']->item_type);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        $this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->data['fgCodeList'] = $this->item->getItemList(1);

        if($this->data['dataRow']->item_type == 3):
            $fgData = $this->item->getItemDataFromItemCode($dataRow->item_code); 
			$options="";
			if(!empty($fgData->id)):
				$result = $this->item->getDieListForSelect($fgData->id);
				
				if(!empty($result)): 
					foreach($result as $row):
						$selected = (!empty($dataRow->part_no) && $dataRow->part_no == $row->id)?'selected':'';
						$options .= "<option value='".$row->id."' ".$selected.">Die No. -".$row->item_code."</option>";
					endforeach;
				endif;
			endif;	
            $this->data['dieOptions'] = $options;
            $this->load->view($this->rmForm,$this->data);
        else:
		    $this->data['subGroupList'] = $this->masterDetail->getMasterDetailsByCategory(['type'=>4]);
            $this->load->view($this->itemForm,$this->data);
        endif;
    }
	
	// Created By Meghavi @29/09/2023 
    public function getDieListForSelect(){
		$data = $this->input->post(); 
		
		$result = $this->item->getDieListForSelect($data['item_id']);
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Die</option>';
			foreach($result as $row):
				$selected = (!empty($data['part_no']) && $data['part_no'] == $row->id)?'selected':'';
				$options .= "<option value='".$row->id."' ".$selected.">Die No. -".$row->item_code."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select Die</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
	
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addStockTrans(){
        $id = $this->input->post('id');
        $this->data['stockTransData'] = $this->item->getStockTrans($id);
        $this->load->view($this->itemStockUpdateForm,$this->data);
    }

    public function saveStockTrans(){
        $data = $this->input->post();
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Quantity is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		    $this->printJson(["status"=>1,"stockData"=>$this->item->saveStockTrans($data)]);
        endif;
	}

    public function deleteStockTrans(){
		$id = $this->input->post('id');
		$this->printJson($this->item->deleteStockTrans($id));
	}
	
	public function addOpeningStock(){
        $id = $this->input->post('id');
        $this->data['openingStockData'] = $this->item->getItemOpeningTrans($id);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->itemOpeningStockForm,$this->data);
    }

    public function saveOpeningStock(){
        $data = $this->input->post();
        $errorMessage = array();
        $itemData = $this->item->getItem($data['item_id']);
        if($itemData->item_type == 3 && empty($data['tc_no'])){
            $errorMessage['tc_no'] = "TC No. is required.";
        }
        
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['ref_date'] = $this->startYearDate;
            $data['created_by'] = $this->session->userdata('loginId');
            //print_r($data);exit;
            $this->printJson($this->item->saveOpeningStock($data));
        endif;
    }

    public function deleteOpeningStockTrans(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->deleteOpeningStockTrans($id));
        endif;
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
	
    public function importRM(){
        $this->load->view($this->importData,$this->data);
    }

    public function importRMExcel(){
        
		$postData = $this->input->post(); 
		$insp_excel = '';
		if(isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
			$_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
			$_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/items');
			$config = ['file_name' => "items_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['insp_excel'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$insp_excel = $uploadData['file_name'];
			endif;
			if(!empty($insp_excel))
			{
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$insp_excel); 
				$fileData = array($spreadsheet->getSheetByName('items')->toArray(null,true,true,true));
				$fieldArray = Array();
				
				if(!empty($fileData))
				{
					$fieldArray = $fileData[0][1];$row = 0;
					for($i=2;$i<=count($fileData[0]);$i++)
					{
						$rowData = Array();$c='A';
						foreach($fileData[0][$i] as $key=>$colData):
							$rowData[strtolower($fieldArray[$c++])] = $colData;
						endforeach;
						$this->item->saveImportRM($rowData);
                        $row++;
					}
					
				}
				$this->printJson(['status'=>1,'message'=>$row.' Record updated successfully.']);
			}
			else{$this->printJson(['status'=>0,'message'=>'Data not found...!']);}
		else:
			$this->printJson(['status'=>0,'message'=>'Please Select File!']);
		endif;
    }
}
?>