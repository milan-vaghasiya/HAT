<?php
class SubMenuConfModel extends MasterModel{
    private $subMenuMaster = "sub_menu_master";

    public function getDTRows($data){
        $data['tableName'] = $this->subMenuMaster;
       // $data['order_by']['sub_menu_master.menu_seq'] = "ASC";
        $data['select']="sub_menu_master.*,menu_master.menu_name";
        $data['join']['menu_master']="menu_master.id=sub_menu_master.menu_id";

        $data['searchCol'][] = "sub_menu_seq";
        $data['searchCol'][] = "sub_menu_icon";
        $data['searchCol'][] = "sub_menu_name";
        $data['searchCol'][] = "sub_controller_name";
        $data['searchCol'][] = "menu_id";
        $data['searchCol'][] = "is_report";
		$columns =array('','','sub_menu_seq','sub_menu_icon','sub_menu_name','sub_controller_name','menu_id','is_report');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSubMenuConfList(){
        $data['tableName'] = $this->subMenuMaster;
        return $this->rows($data);
    }

    public function getSubMenuConf($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->subMenuMaster;
        return $this->row($data);
    }

    public function getSubMenus($menu_id){
        $data['tableName'] = $this->subMenuMaster;
        $data['where']['menu_id']=$menu_id;
        return $this->rows($data);
    }

    public function save($data){
        $data['sub_menu_name'] = trim($data['sub_menu_name']);
        if($this->checkDuplicate($data['sub_menu_name'],$data['id']) > 0):
            $errorMessage['sub_menu_name'] ="Sub Menu Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
           // return $this->store($this->subMenuMaster,$data,'Menu Master');
             $mainmenus=$this->getSubMenus($data['menu_id']); 

            if(!empty($data['id'])):
                $oldData=$this->getSubMenuConf($data['id']);
                foreach($mainmenus as $row):
                    if(($row->sub_menu_seq > $oldData->sub_menu_seq) AND ($row->sub_menu_seq <= $data['sub_menu_seq'])):
                        $this->store($this->subMenuMaster,['id'=>$row->id,'sub_menu_seq'=>($row->sub_menu_seq - 1)],'Sub Menu Master');
                    endif;
                endforeach;
            endif;
            $result= $this->store($this->subMenuMaster,$data,'Menu Master');
            if(empty($data['id'])):
                foreach($mainmenus as $row):
                    if($row->sub_menu_seq >= $data['sub_menu_seq']):
                        $this->store($this->subMenuMaster,['id'=>$row->id,'sub_menu_seq'=>($row->sub_menu_seq +1)],'Sub Menu Master');
                    endif;
                endforeach;
            endif;
            return $result;
        endif;
     }

    public function checkDuplicate($sub_menu_name,$id=""){
        $data['tableName'] = $this->subMenuMaster;
        $data['where']['sub_menu_name'] = $sub_menu_name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){

        if(!empty($id)):
            $oldData=$this->getSubMenuConf($id);
            $mainmenus=$this->getSubMenus($oldData->menu_id); 

            foreach($mainmenus as $row):
                if($row->sub_menu_seq > $oldData->sub_menu_seq):
                    $this->store($this->subMenuMaster,['id'=>$row->id,'sub_menu_seq'=>($row->sub_menu_seq - 1)],'Sub Menu Master');
                endif;
            endforeach;
        endif;
        return $this->trash($this->subMenuMaster,['id'=>$id],'Sub Menu Master');
    }
}
?>