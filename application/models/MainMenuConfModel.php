<?php
class MainMenuConfModel extends MasterModel{
    private $menuMaster = "menu_master";

    public function getDTRows($data){
        $data['tableName'] = $this->menuMaster;
        $data['order_by']['menu_master.menu_seq'] = "ASC";

        $data['searchCol'][] = "menu_name";
		$columns =array('','','menu_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMainMenuConfList(){
        $data['tableName'] = $this->menuMaster;
        return $this->rows($data);
    }

    public function getMainMenuConf($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->menuMaster;
        return $this->row($data);
    }

    public function getMainMenus(){
        $data['tableName'] = $this->menuMaster;
        return $this->rows($data);
    }
    public function getMainMenuList(){
        $data['tableName'] = $this->menuMaster;
        return $this->rows($data);
    }

    public function save($data){
        $data['menu_name'] = trim($data['menu_name']);
        if($this->checkDuplicate($data['menu_name'],$data['id']) > 0):
            $errorMessage['menu_name'] ="Menu Name is duplicate.";
             return ['status'=>0,'message'=>$errorMessage];
        else:
            $mainmenus=$this->getMainMenus(); 

            if(!empty($data['id'])):
                $oldData=$this->getMainMenuConf($data['id']);
                foreach($mainmenus as $row):
                    if(($row->menu_seq > $oldData->menu_seq) AND ($row->menu_seq <= $data['menu_seq'])):
                        $this->store($this->menuMaster,['id'=>$row->id,'menu_seq'=>($row->menu_seq - 1)],'Menu Master');
                    endif;
                    if(($row->menu_seq < $oldData->menu_seq) AND ($row->menu_seq >= $data['menu_seq'])):
                        $this->store($this->menuMaster,['id'=>$row->id,'menu_seq'=>($row->menu_seq + 1)],'Menu Master');
                    endif;
                endforeach;
            endif;
            $result= $this->store($this->menuMaster,$data,'Menu Master');
            if(empty($data['id'])):
                foreach($mainmenus as $row):
                    if($row->menu_seq >= $data['menu_seq']):
                        $this->store($this->menuMaster,['id'=>$row->id,'menu_seq'=>($row->menu_seq +1)],'Menu Master');
                    endif;
                endforeach;
            endif;
            return $result;
        endif;
     }

    public function checkDuplicate($menu_name,$id=""){
        $data['tableName'] = $this->menuMaster;
        $data['where']['menu_name'] = $menu_name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        $mainmenus=$this->getMainMenus(); 

        if(!empty($id)):
            $oldData=$this->getMainMenuConf($id);
            foreach($mainmenus as $row):
                if($row->menu_seq > $oldData->menu_seq):
                    $this->store($this->menuMaster,['id'=>$row->id,'menu_seq'=>($row->menu_seq - 1)],'Menu Master');
                endif;
            endforeach;
        endif;
        return $this->trash($this->menuMaster,['id'=>$id],'Menu Master');
    }
}
?>