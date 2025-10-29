<?php
class NotifyPermissionModel extends MasterModel{
    private $menuMaster = "menu_master";
    private $subMenuMaster = "sub_menu_master";
    private $menuPermission = "menu_permission";
    private $subMenuPermission = "sub_menu_permission";

    public function getMainMenus(){
        $queryData = array();
        $queryData['tableName'] = $this->menuMaster;
        $queryData['order_by']['menu_seq'] = "ASC";
        return $this->rows($queryData);
    }

    public function getSubMenus($menu_id){
        $queryData = array();
        $queryData['tableName'] = $this->subMenuMaster;
        $queryData['where']['menu_id'] = $menu_id;
        $queryData['order_by']['sub_menu_seq'] = "ASC";
        return $this->rows($queryData);
    }

    public function getPermission(){
        $mainPermission = $this->getMainMenus();
        $dataRows = array();$subData = new stdClass();
        foreach($mainPermission as $row):
            if($row->is_master == 1):
                $subData->id = $row->id;
                $subData->sub_menu_seq = 1;
                $subData->sub_menu_icon = $row->menu_icon;
                $subData->sub_menu_name = $row->menu_name;
                $subData->sub_controller_name = $row->controller_name;
                $subData->menu_id = 0;
                $subData->is_report = 0;

                $subMenus = $subData;
                $row->subMenus = $subMenus;
            else:
                $subMenus = $this->getSubMenus($row->id);
                $row->subMenus = $subMenus;
            endif;
            $dataRows[] = $row;
        endforeach;
        return $dataRows;
    }

    public function getEmployeePermission(){
        $queryData = array();
        $queryData['tableName'] = $this->menuPermission;       
        $result['mainPermission'] = $this->rows($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->subMenuPermission;       
        $result['subMenuPermission'] = $this->rows($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            
            foreach($data['menu_id'] as $key=>$value):
                $subMasterNotify = array();
                foreach($data['sub_menu_id_'.$value] as $subKey => $subValue):
                    $subMenuWrite = (isset($data['sub_menu_write_'.$subValue.'_'.$value]))?$data['sub_menu_write_'.$subValue.'_'.$value][0]:0;
                    $subMenuModify = (isset($data['sub_menu_modify_'.$subValue.'_'.$value]))?$data['sub_menu_modify_'.$subValue.'_'.$value][0]:0;
                    $subMenuDelete = (isset($data['sub_menu_delete_'.$subValue.'_'.$value]))?$data['sub_menu_delete_'.$subValue.'_'.$value][0]:0;
                    $notify = $subMenuWrite.','.$subMenuModify.','.$subMenuDelete;
                    $subMasterNotify = [  
                        'notify_on' =>$notify                     
                    ];
                    $this->db->where('id',$subValue)->update($this->subMenuMaster,$subMasterNotify);
                endforeach;              
            endforeach;

            $result = ['status'=>1,'message'=>'Employee Permission saved successfully.'];
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