<?php
class CategoryModel extends MasterModel{
    private $empCategory = "emp_category";

    public function getDTRows($data){
        $data['tableName'] = $this->empCategory;
        $data['searchCol'][] = "category";
		$columns =array('','','category');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCategoryList(){
        $data['tableName'] = $this->empCategory;
        return $this->rows($data);
    }

    public function getCategory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->empCategory;
        return $this->row($data);
    }

    public function save($data){
        $data['category'] = trim($data['category']);
        if($this->checkDuplicate($data['category'],$data['id']) > 0):
            $errorMessage['category'] = "Category Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->empCategory,$data,'Employee Category');
        endif;
    }

    public function checkDuplicate($category,$id=""){
        $data['tableName'] = $this->empCategory;
        $data['where']['category'] = $category;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->empCategory,['id'=>$id],'Employee Category');
    }
}
?>