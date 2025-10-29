<?php
class ContactDirectoryModel extends MasterModel{
    private $contactDirectory = "contact_directory";
    public function getDTRows($data){
        $data['tableName'] = $this->contactDirectory;
        $data['searchCol'][] = "comapny_name";
        $data['searchCol'][] = "contact_person";
        $data['searchCol'][] = "contact_number";
        $data['searchCol'][] = "email";
        $data['searchCol'][] = "service";
        $data['searchCol'][] = "Remark";

		$columns =array('','','comapny_name','contact_person','contact_number','email','service','Remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getContactDirectory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->contactDirectory;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->contactDirectory,$data,'Contact');
        
    }

    
    public function delete($id){
        return $this->trash($this->contactDirectory,['id'=>$id],'Contact');
    }
}
?>