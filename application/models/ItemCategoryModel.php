<?php
class ItemCategoryModel extends MasterModel{
    private $itemCategory = "item_category";
    
	public function getDTRows($data){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*, item_group.group_name';
        $data['join']['item_group'] = 'item_group.id = item_category.category_type';
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_group.group_name";
        $data['searchCol'][] = "item_category.remark";
		$columns =array('','','category_name','group_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCategoryList($category_type=""){
        $data['tableName'] = $this->itemCategory;
        if(!empty($category_type)){$data['where']['category_type'] = $category_type;}
        return $this->rows($data);
    }

    public function getCategory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->itemCategory;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
			if($this->checkDuplicate($data['category_name'],'category_name',$data['id']) > 0):
				$errorMessage['category_name'] = "Category Name is duplicate.";
				$result = ['status'=>0,'message'=>$errorMessage];
			//elseif($this->checkDuplicate($data['category_code'],'category_code',$data['id']) > 0):
			//	$errorMessage['category_code'] = "Code is duplicate.";
			//	$result = ['status'=>0,'message'=>$errorMessage];
			else:
				$result = $this->store($this->itemCategory,$data,'Item Category');
			endif;
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function checkDuplicate($name,$field='category_name',$id=""){
        $data['tableName'] = $this->itemCategory;
        $data['where'][$field] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->itemCategory,['id'=>$id],'Item Category');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }	
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
    update by : 
    note : 
*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->itemCategory;
        return $this->numRows($data);
    }

    public function getItemCategoryList_api($limit, $start){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*, item_group.group_name';
        $data['join']['item_group'] = 'item_group.id = item_category.category_type';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>