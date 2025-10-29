<?php /* Master Modal Ver. : 1.1  */
class MasterModel extends CI_Model
{

    /* Get Paging Rows */
    public function pagingRows1($data)
    {
        $draw = $data['draw'];
        $start = $data['start'];
        $rowperpage = $data['length']; // Rows display per page
        $searchValue = $data['search']['value'];

        /********** Total Records without Filtering ***********/ {
            if (isset($data['select'])) :
                if (!empty($data['select'])) :
                    $this->db->select($data['select']);
                endif;
            endif;

            if (isset($data['join'])) :
                if (!empty($data['join'])) :
                    foreach ($data['join'] as $key => $value) :
                        $this->db->join($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['leftJoin'])) :
                if (!empty($data['leftJoin'])) :
                    foreach ($data['leftJoin'] as $key => $value) :
                        $this->db->join($key, $value, 'left');
                    endforeach;
                endif;
            endif;

            if (isset($data['where'])) :
                if (!empty($data['where'])) :
                    foreach ($data['where'] as $key => $value) :
                        $this->db->where($key, $value);
                    endforeach;
                endif;
            endif;
            if (isset($data['customWhere'])) :
                if (!empty($data['customWhere'])) :
                    foreach ($data['customWhere'] as $value) :
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'] . '.is_delete', 0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);

            if (isset($data['where_in'])) :
                if (!empty($data['where_in'])) :
                    foreach ($data['where_in'] as $key => $value) :
                        $this->db->where_in($key, $value,false);
                    endforeach;
                endif;
            endif;

            if (isset($data['group_by'])) :
                if (!empty($data['group_by'])) :
                    foreach ($data['group_by'] as $key => $value) :
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;

            $totalRecords = $this->db->get($data['tableName'])->num_rows();
            //print_r($this->db->last_query());
        }
        /********** End Count Total Records without Filtering ***********/



        /********** Count Total Records with Filtering ***********/ {
            if (isset($data['select'])) :
                if (!empty($data['select'])) :
                    $this->db->select($data['select']);
                endif;
            endif;

            if (isset($data['join'])) :
                if (!empty($data['join'])) :
                    foreach ($data['join'] as $key => $value) :
                        $this->db->join($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['leftJoin'])) :
                if (!empty($data['leftJoin'])) :
                    foreach ($data['leftJoin'] as $key => $value) :
                        $this->db->join($key, $value, 'left');
                    endforeach;
                endif;
            endif;

            if (isset($data['where'])) :
                if (!empty($data['where'])) :
                    foreach ($data['where'] as $key => $value) :
                        $this->db->where($key, $value);
                    endforeach;
                endif;
            endif;
            if (isset($data['customWhere'])) :
                if (!empty($data['customWhere'])) :
                    foreach ($data['customWhere'] as $value) :
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'] . '.is_delete', 0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);

            if (isset($data['where_in'])) :
                if (!empty($data['where_in'])) :
                    foreach ($data['where_in'] as $key => $value) :
                        $this->db->where_in($key, $value,false);
                    endforeach;
                endif;
            endif;

            $c = 0;
            // General Search
            if (!empty($searchValue)) :
                if (isset($data['searchCol'])) :
                    if (!empty($data['searchCol'])) :
                        $this->db->group_start();
                        foreach ($data['searchCol'] as $key => $value) :
                            if (!empty($value)) {
                                if ($c == 0) {
                                    $this->db->like($value, $searchValue);
                                    $c++;
                                } else {
                                    $this->db->or_like($value, $searchValue);
                                }
                            }
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
            endif;

            // Column Search
            if (isset($data['searchCol'])) :
                if (!empty($data['searchCol'])) :
                    foreach ($data['searchCol'] as $key => $value) :
                        if (!empty($value)) {
                            $csearch = $data['columns'][$key]['search']['value'];
                            if (!empty($csearch)) {
                                $this->db->like($value, $csearch);
                            }
                        }
                    endforeach;
                endif;
            endif;

            if (isset($data['group_by'])) :
                if (!empty($data['group_by'])) :
                    foreach ($data['group_by'] as $key => $value) :
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;

            $totalRecordwithFilter = $this->db->get($data['tableName'])->num_rows();
            //print_r($this->db->last_query());
        }
        /********** End Count Total Records with Filtering ***********/


        /********** Total Records with Filtering ***********/ {
            if (isset($data['select'])) :
                if (!empty($data['select'])) :
                    $this->db->select($data['select']);
                endif;
            endif;

            if (isset($data['join'])) :
                if (!empty($data['join'])) :
                    foreach ($data['join'] as $key => $value) :
                        $this->db->join($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['leftJoin'])) :
                if (!empty($data['leftJoin'])) :
                    foreach ($data['leftJoin'] as $key => $value) :
                        $this->db->join($key, $value, 'left');
                    endforeach;
                endif;
            endif;

            if (isset($data['where'])) :
                if (!empty($data['where'])) :
                    foreach ($data['where'] as $key => $value) :
                        $this->db->where($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['customWhere'])) :
                if (!empty($data['customWhere'])) :
                    foreach ($data['customWhere'] as $value) :
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'] . '.is_delete', 0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);

            if (isset($data['where_in'])) :
                if (!empty($data['where_in'])) :
                    foreach ($data['where_in'] as $key => $value) :
                        $this->db->where_in($key, $value, false);
                    endforeach;
                endif;
            endif;

            $c = 0;
            // General Search
            if (!empty($searchValue)) :
                if (isset($data['searchCol'])) :
                    if (!empty($data['searchCol'])) :
                        $this->db->group_start();
                        foreach ($data['searchCol'] as $key => $value) :
                            if (!empty($value)) {
                                if ($c == 0) {
                                    $this->db->like($value, $searchValue);
                                    $c++;
                                } else {
                                    $this->db->or_like($value, $searchValue);
                                }
                            }
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
            endif;

            // Column Search
            if (isset($data['searchCol'])) :
                if (!empty($data['searchCol'])) :
                    foreach ($data['searchCol'] as $key => $value) :
                        if (!empty($value)) {
                            $csearch = $data['columns'][$key]['search']['value'];
                            if (!empty($csearch)) {
                                $this->db->like($value, $csearch);
                            }
                        }
                    endforeach;
                endif;
            endif;

            if (isset($data['order_by'])) :
                if (!empty($data['order_by'])) :
                    foreach ($data['order_by'] as $key => $value) :
                        $this->db->order_by($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['group_by'])) :
                if (!empty($data['group_by'])) :
                    foreach ($data['group_by'] as $key => $value) :
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;

            $resultData = $this->db->limit($rowperpage, $start)->get($data['tableName'])->result();
            //print_r($this->db->last_query());
        }
        /********** End Total Records with Filtering ***********/

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $resultData
        ];
        return $response;
    }
    public function pagingRows($data){
        $draw = $data['draw'];
		$start = $data['start'];
		$rowperpage = $data['length']; // Rows display per page
		$searchValue = $data['search']['value'];		
		
		/********** Total Records without Filtering ***********/
		{
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['rightJoin'])):
                if(!empty($data['rightJoin'])):
                    foreach($data['rightJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left outer');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['crossJoin'])):
                if(!empty($data['crossJoin'])):
                    foreach($data['crossJoin'] as $key=>$value):
                        $this->db->join($key,$value,true);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
			
			if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;
			
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'].'.is_delete',0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    		
    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
            $totalRecords = $this->db->get($data['tableName'])->num_rows();
            //print_r($this->db->last_query());
		}
        /********** End Count Total Records without Filtering ***********/
        
        
        
        /********** Count Total Records with Filtering ***********/
        {
    		if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['rightJoin'])):
                if(!empty($data['rightJoin'])):
                    foreach($data['rightJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left outer');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['crossJoin'])):
                if(!empty($data['crossJoin'])):
                    foreach($data['crossJoin'] as $key=>$value):
                        $this->db->join($key,$value,true);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
			
			if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;
			
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'].'.is_delete',0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    		
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
                        foreach($data['searchCol'] as $key=>$value):
    						if(!empty($value)){
    							if($c == 0){
    								$this->db->like($value,$searchValue);$c++;
    							}else{
    								$this->db->or_like($value,$searchValue);
    							}
    						}
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
    		
    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
    		$totalRecordwithFilter = $this->db->get($data['tableName'])->num_rows();
    		//print_r($this->db->last_query());
        }
        /********** End Count Total Records with Filtering ***********/
		
		
        /********** Total Records with Filtering ***********/
        {
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;  
            
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['rightJoin'])):
                if(!empty($data['rightJoin'])):
                    foreach($data['rightJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left outer');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['crossJoin'])):
                if(!empty($data['crossJoin'])):
                    foreach($data['crossJoin'] as $key=>$value):
                        $this->db->join($key,$value,true);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
			
			if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;
			
            
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            //$this->db->where($data['tableName'].'.is_delete',0);
            $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
            $this->db->where($data['tableName'].'.is_delete',$isDelete);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
                        foreach($data['searchCol'] as $key=>$value):
    						if(!empty($value)){
    							if($c == 0){
    								$this->db->like($value,$searchValue);$c++;
    							}else{
    								$this->db->or_like($value,$searchValue);
    							}
    						}
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
            
            if(isset($data['order_by'])):
                if(!empty($data['order_by'])):
                    foreach($data['order_by'] as $key=>$value):
                        $this->db->order_by($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    
            if(isset($rowperpage) AND $rowperpage >= 0){$this->db->limit($rowperpage, $start);}
            $resultData = $this->db->get($data['tableName'])->result();
            //print_r($this->db->last_query());
        }
        /********** End Total Records with Filtering ***********/
        
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $resultData
        ];
        return $response;
    }
    /* Get All Rows */
    public function rows($data)
    {

        if (isset($data['select'])) :
            if (!empty($data['select'])) :
                $this->db->select($data['select']);
            endif;
        endif;

        if (isset($data['join'])) :
            if (!empty($data['join'])) :
                foreach ($data['join'] as $key => $value) :
                    $this->db->join($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['leftJoin'])) :
            if (!empty($data['leftJoin'])) :
                foreach ($data['leftJoin'] as $key => $value) :
                    $this->db->join($key, $value, 'left');
                endforeach;
            endif;
        endif;

        if (isset($data['where'])) :
            if (!empty($data['where'])) :
                foreach ($data['where'] as $key => $value) :
                    $this->db->where($key, $value);
                endforeach;
            endif;
        endif;
		
        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;
        
        if (isset($data['customWhere'])) :
            if (!empty($data['customWhere'])) :
                foreach ($data['customWhere'] as $value) :
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'] . '.is_delete', 0);

        if (isset($data['where_in'])) :
            if (!empty($data['where_in'])) :
                foreach ($data['where_in'] as $key => $value) :
                    $this->db->where_in($key, $value, false);
                endforeach;
            endif;
        endif;

        if (isset($data['like'])) :
            if (!empty($data['like'])) :
                $i = 1;
                $this->db->group_start();
                foreach ($data['like'] as $key => $value) :
                    if ($i == 1) :
                        $this->db->like($key, $value);
                    else :
                        $this->db->or_like($key, $value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if (isset($data['order_by'])) :
            if (!empty($data['order_by'])) :
                foreach ($data['order_by'] as $key => $value) :
                    $this->db->order_by($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['group_by'])) :
            if (!empty($data['group_by'])) :
                foreach ($data['group_by'] as $key => $value) :
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
        if (isset($data['limit'])) :
            if (!empty($data['limit'])) :
                $this->db->limit($data['limit']);
            endif;
        endif;

        if (isset($data['start']) && isset($data['length'])) :
            if (!empty($data['length'])) :
                $this->db->limit($data['length'], $data['start']);
            endif;
        endif;

        $result = $this->db->get($data['tableName'])->result();
        //print_r($this->db->last_query());
        return $result;
    }

    /* Get Single Row */
    public function row($data)
    {
        if (isset($data['select'])) :
            if (!empty($data['select'])) :
                $this->db->select($data['select']);
            endif;
        endif;

        if (isset($data['join'])) :
            if (!empty($data['join'])) :
                foreach ($data['join'] as $key => $value) :
                    $this->db->join($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['leftJoin'])) :
            if (!empty($data['leftJoin'])) :
                foreach ($data['leftJoin'] as $key => $value) :
                    $this->db->join($key, $value, 'left');
                endforeach;
            endif;
        endif;
        if (isset($data['where'])) :
            foreach ($data['where'] as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
		
        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;
        
        if (isset($data['customWhere'])) :
            if (!empty($data['customWhere'])) :
                foreach ($data['customWhere'] as $value) :
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

        if (isset($data['where_in'])) :
            if (!empty($data['where_in'])) :
                foreach ($data['where_in'] as $key => $value) :
					if (isset($data['win']) AND $data['win'] == 'false'):
						$this->db->where_in($key, $value,false);
					else:
						$this->db->where_in($key, $value);
					endif;
                endforeach;
            endif;
        endif;

        //$this->db->where($data['tableName'] . '.is_delete', 0);
        $isDelete=0;if(isset($data['where'][$data['tableName'].'.'.'is_delete'])){ $isDelete=$data['where'][$data['tableName'].'.'.'is_delete']; }
        $this->db->where($data['tableName'].'.is_delete',$isDelete);

        if (isset($data['like'])) :
            if (!empty($data['like'])) :
                $i = 1;
                $this->db->group_start();
                foreach ($data['like'] as $key => $value) :
                    if ($i == 1) :
                        $this->db->like($key, $value);
                    else :
                        $this->db->or_like($key, $value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if (isset($data['order_by'])) :
            if (!empty($data['order_by'])) :
                foreach ($data['order_by'] as $key => $value) :
                    $this->db->order_by($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['group_by'])) :
            if (!empty($data['group_by'])) :
                foreach ($data['group_by'] as $key => $value) :
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

        $result = $this->db->get($data['tableName'])->row();
        return $result;
    }

    public function customRow($data)
    {
        if (isset($data['select'])) :
            if (!empty($data['select'])) :
                $this->db->select($data['select']);
            endif;
        endif;

        if (isset($data['join'])) :
            if (!empty($data['join'])) :
                foreach ($data['join'] as $key => $value) :
                    $this->db->join($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['leftJoin'])) :
            if (!empty($data['leftJoin'])) :
                foreach ($data['leftJoin'] as $key => $value) :
                    $this->db->join($key, $value, 'left');
                endforeach;
            endif;
        endif;

        foreach ($data['where'] as $key => $value) :
            $this->db->where($key, $value);
        endforeach;
        if (isset($data['customWhere'])) :
            if (!empty($data['customWhere'])) :
                foreach ($data['customWhere'] as $value) :
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

        if (isset($data['where_in'])) :
            if (!empty($data['where_in'])) :
                foreach ($data['where_in'] as $key => $value) :
                    $this->db->where_in($key, $value);
                endforeach;
            endif;
        endif;

        $result = $this->db->get($data['tableName'])->row();
        return $result;
    }

    /* Save and Update Row */
    public function store($tableName, $data, $msg = "Record")
    {
        $id = $data['id'];
        unset($data['id']);
        if (empty($id)) :
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $this->db->insert($tableName, $data);
            $insert_id = $this->db->insert_id();

            $result = ['status' => 1, 'message' => $msg . " saved Successfully.", 'insert_id' => $insert_id];
            if (DEVELOPMENT == 1) :
                $result['qry'] = $this->db->last_query();
            endif;
            return $result;
        else :
            $data['updated_at'] = date("Y-m-d H:i:s");
            $this->db->where('id', $id);
            $this->db->update($tableName, $data);

            $result = ['status' => 1, 'message' => $msg . " updated Successfully.", 'insert_id' => -1];
            if (DEVELOPMENT == 1) :
                $result['qry'] = $this->db->last_query();
            endif;
            return $result;
        endif;
    }

    /* Update Row */
    public function edit($tableName, $where, $data, $msg = "Record")
    {
        $data['updated_at'] = date("Y-m-d H:i:s");
        if (!empty($where)) :
            foreach ($where as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
        $this->db->update($tableName, $data);
        return ['status' => 1, 'message' => $msg . " updated Successfully.", 'insert_id' => -1];
    }

    /* Update Row */
    public function editCustom($tableName, $customWhere, $data, $where = array())
    {
        $data['updated_at'] = date("Y-m-d H:i:s");
        if (!empty($where)) :
            foreach ($where as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
        if (isset($customWhere)) :
            if (!empty($customWhere)) :
                foreach ($customWhere as $value) :
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->update($tableName, $data);
        return ['status' => 1, 'message' => "Record updated Successfully.", 'insert_id' => -1];
    }

    /* Get Numbers of Rows */
    public function numRows($data, $deleteCheck = 1)
    {
        if (!empty($data['where'])) :
            foreach ($data['where'] as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
        
        if (isset($data['where_in'])) :
            if (!empty($data['where_in'])) :
                foreach ($data['where_in'] as $key => $value) :
                    $this->db->where_in($key, $value);
                endforeach;
            endif;
        endif;

        if (!empty($deleteCheck)) {
            $this->db->where($data['tableName'] . '.is_delete', 0);
        }
        return $this->db->get($data['tableName'])->num_rows();
    }

    /* Set Deleteed Flage */
    public function trash($tableName, $where, $msg = "Record")
    {
        if (!empty($where)) :
            foreach ($where as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
        $this->db->update($tableName, ['is_delete' => 1,'updated_at'=>date('Y-m-d H:i:s')]);
        return ['status' => 1, 'message' => $msg . " deleted Successfully."];
    }

    /* Delete Recored Permanent */
    public function remove($tableName, $where, $msg = "")
    {
        if (!empty($where)) :
            foreach ($where as $key => $value) :
                $this->db->where($key, $value);
            endforeach;
        endif;
        $this->db->delete($tableName);
        return ['status' => 1, 'message' => $msg . " deleted Successfully."];
    }

    /* Get Specific Row. Like : SUM,MAX,MIN,COUNT ect... */
    public function specificRow($data)
    {
        if (isset($data['select'])) :
            if (!empty($data['select'])) :
                $this->db->select($data['select']);
            endif;
        endif;

        if (isset($data['join'])) :
            if (!empty($data['join'])) :
                foreach ($data['join'] as $key => $value) :
                    $this->db->join($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['leftJoin'])) :
            if (!empty($data['leftJoin'])) :
                foreach ($data['leftJoin'] as $key => $value) :
                    $this->db->join($key, $value, 'left');
                endforeach;
            endif;
        endif;

        if (isset($data['where'])) :
            if (!empty($data['where'])) :
                foreach ($data['where'] as $key => $value) :
                    $this->db->where($key, $value);
                endforeach;
            endif;
        endif;
        if (isset($data['customWhere'])) :
            if (!empty($data['customWhere'])) :
                foreach ($data['customWhere'] as $value) :
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'] . '.is_delete', 0);

        if (isset($data['where_in'])) :
            if (!empty($data['where_in'])) :
                foreach ($data['where_in'] as $key => $value) :
                    $this->db->where_in($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['like'])) :
            if (!empty($data['like'])) :
                $i = 1;
                $this->db->group_start();
                foreach ($data['like'] as $key => $value) :
                    if ($i == 1) :
                        $this->db->like($key, $value);
                    else :
                        $this->db->or_like($key, $value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if (isset($data['order_by'])) :
            if (!empty($data['order_by'])) :
                foreach ($data['order_by'] as $key => $value) :
                    $this->db->order_by($key, $value);
                endforeach;
            endif;
        endif;

        if (isset($data['group_by'])) :
            if (!empty($data['group_by'])) :
                foreach ($data['group_by'] as $key => $value) :
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

        if (isset($data['resultType'])) :
            if ($data['resultType'] == "numRows")
                return $this->db->get($data['tableName'])->num_rows();
            if ($data['resultType'] == "resultRows")
                return $this->db->get($data['tableName'])->result();
        endif;

        $result =  $this->db->get($data['tableName'])->row();
        // print_r($this->db->last_query());
        return $result;
    }

    /* Print Executed Query */
    public function printQuery()
    {
        print_r($this->db->last_query());
        exit;
    }

    /* Custom Set OR Update Row */
    public function setValue($data)
    {
        if (!empty($data['where'])) :
            if (isset($data['where'])) :
                if (!empty($data['where'])) :
                    foreach ($data['where'] as $key => $value) :
                        $this->db->where($key, $value);
                    endforeach;
                endif;
            endif;

            if (isset($data['set'])) :
                if (!empty($data['set'])) :
                    foreach ($data['set'] as $key => $value) :
                        $v = explode(',', $value);
                        $setVal = "`" . $v[0] . "` " . $v[1];
                        $this->db->set($key, $setVal, FALSE);
                    endforeach;
                endif;
            endif;
            $this->db->update($data['tableName']);
            return ['status' => 1, 'message' => "Record updated Successfully.", 'qry' => $this->db->last_query()];
        endif;
        return ['status' => 0, 'message' => "Record updated Successfully.", 'qry' => "Query not fired"];
    }

    /* Save and Update Row */
    public function storeBatch($tableName, $itemData, $masterKey, $affectedData = array())
    {

        $transData = $this->getTransactionArray($tableName, $itemData, $masterKey);
        $batchData = $transData['tansItems'];
        $newRecords = array();
        $updatedRecords = array();
        $removedRecords = $transData['removedTrans'];
        foreach ($batchData as $data) :
            $id = $data['id'];
            unset($data['id']);
            if (empty($id)) :
                $this->db->insert($tableName, $data);
                // $insert_id = $this->db->insert_id();
                $newRecords = $data;
                /***	NEW ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
                if (!empty($affectedData)) :
                    foreach ($affectedData as $aData) :
                        $setVal = '';
                        if (!empty($aData['operation'])) :
                            $operator = ($aData['operation'] == 'plus') ? '+' : '-';
                            $setVal = "`" . $aData['tp_field_key'] . "` " . $operator . " " . $data[$aData['field_key']];
                        else :
                            $setVal = $data[$aData['field_key']];
                        endif;
                        $this->db->set($aData['tp_field_key'], $setVal, FALSE);
                        $this->db->where('id', $data[$aData['foreign_key']]);
                        $this->db->update($aData['table_name']);
                    endforeach;
                endif;
            else :
                $queryData = array();
                $queryData['tableName'] = $tableName;
                $queryData['where']['id'] = $id;
                $transRow = $this->row($queryData);
                $updatedRecords[] = $transRow;
                $this->db->where('id', $id);
                $this->db->update($tableName, $data);

                /***	MODIFIED ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
                if (!empty($affectedData)) :
                    foreach ($affectedData as $aData) :

                        $setVal = '';
                        if (!empty($aData['operation'])) :
                            if ($aData['operation'] == 'plus') :
                                $field_val = $data[$aData['field_key']] - $transRow->{$aData['field_key']};
                                $setVal = "`" . $aData['tp_field_key'] . "` + " . $field_val;
                            else :
                                $field_val = $data[$aData['field_key']] - $transRow->{$aData['field_key']};
                                $setVal = "`" . $aData['tp_field_key'] . "` - " . $field_val;
                            endif;
                        else :
                            $setVal = $data[$aData['field_key']];
                        endif;
                        $this->db->set($aData['tp_field_key'], $setVal, FALSE);
                        $this->db->where('id', $data[$aData['foreign_key']]);
                        $this->db->update($aData['table_name']);
                    endforeach;
                endif;
            endif;
        endforeach;

        /***	REMOVED ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
        if (!empty($affectedData)) :
            if (!empty($removedRecords)) :
                foreach ($removedRecords as $row) :
                    foreach ($affectedData as $aData) :
                        $operator = ($aData['operation'] == 'plus') ? '-' : '+';
                        $setVal = "`" . $aData['tp_field_key'] . "` " . $operator . " " . $row->{$aData['field_key']};
                        $this->db->set($aData['tp_field_key'], $setVal, FALSE);
                        $this->db->where('id', $row->{$aData['foreign_key']});
                        $this->db->update($aData['table_name']);
                    endforeach;
                endforeach;
            endif;
        endif;

        return ["allRecords" => $batchData, "newRecords" => $newRecords, "updatedRecords" => $updatedRecords, "removedRecords" => $removedRecords];
    }

    /* Set Transaction Aray For Store Batch */
    public function getTransactionArray($tableName, $itemData, $masterKey)
    {
        $transItems = array();
        $allIds = array();
        $removedTrans = array();
        if (!empty($itemData)) {
            $totalItems = count($itemData['id']);
            for ($idx = 0; $idx < $totalItems; $idx++) {
                $itms = array();
                foreach ($itemData as $key => $value) :
                    if ($key == 'id') :
                        if (empty($value[$idx]))
                            $itms[$masterKey[0]] = $masterKey[1];
                        else
                            $allIds[] = $value[$idx];
                    endif;
                    $itms[$key] = $value[$idx];
                endforeach;
                $transItems[] = $itms;
            }
        }
        $oldData['where'][$masterKey[0]] = $masterKey[1];
        $oldData['tableName'] = $tableName;
        $oldTransData = $this->rows($oldData);

        foreach ($oldTransData as $row) :
            if (!in_array($row->id, $allIds)) :
                $removedTrans[] = $row;
                $this->trash($tableName, ['id' => $row->id]);
            endif;
        endforeach;

        return ["tansItems" => $transItems, "removedTrans" => $removedTrans];
    }
    
    // Update Stock to Item Master
    public function updateItemStock($item_id=""){
		try{
            $this->db->trans_begin();
            if(!empty($item_id)){$this->db->where('item_master.id',$item_id);}
            $result = $this->db->get("item_master")->result();
            foreach($result as $row):
                $this->db->select("SUM(qty) as qty");
                $this->db->where('item_id',$row->id);
                $this->db->where('is_delete',0);
                $stockData = $this->db->get('stock_transaction')->row();
                
                //update Item Master
                if(!empty($stockData->qty)):
                    $data=['qty'=>$stockData->qty];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                else:
                    $data=['qty'=>0];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                endif;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
            endif;
            return true;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /* Company Information */
    public function getCompanyInfo()
    {
        $data['tableName'] = 'company_info';
        $data['where']['id'] = 1;
        return $this->row($data);
    }

    /* Master Options */
    public function getMasterOptions()
    {
        $data['tableName'] = 'master_options';
        $data['where']['id'] = 1;
        return $this->row($data);
    }

    /* Send notification to all users */
    public function notify($data)
    {
        $token = $this->checkPermissionForNotification($data['controller'], $data['action']);
        $result = array();
        if (!empty($token)) :
            $data['pushToken'] = $token;
            $result = $this->notification->sendMultipalNotification($data);
        endif;
        return $result;
    }

    public function checkPermissionForNotification($controllerNames = "", $action = "")
    {
        $tokens = array();
        if (!empty($controllerNames)) :
            $this->db->select('id,notify_on');
            $this->db->where_in('sub_controller_name', $controllerNames, false);
            $this->db->where('is_delete', 0);
            $subMenuData = $this->db->get('sub_menu_master')->result();

            if (!empty($subMenuData)) :
                foreach ($subMenuData as $row) :

                    $modualNotifyPermission = 0;
                    if (!empty($action)) :
                        $permission = explode(",", $row->notify_on);
                        if ($action == "W") :
                            $modualNotifyPermission = $permission[0];
                        elseif ($action == "M") :
                            $modualNotifyPermission = $permission[1];
                        elseif ($action == "D") :
                            $modualNotifyPermission = $permission[2];
                        endif;
                    endif;

                    if (!empty($modualNotifyPermission)) :
                        $this->db->select('emp_id');
                        $this->db->where('sub_menu_id', $row->id);
                        $this->db->where('is_read', 1);
                        $this->db->where('is_delete', 0);
                        $empIds = $this->db->get('sub_menu_permission')->result();

                        if (!empty($empIds)) :
                            $empIds = array_column($empIds, 'emp_id');
                            $empIds = array_unique($empIds);

                            $this->db->select("device_token");
                            $this->db->where('is_delete', 0);
                            $this->db->where('device_token !=', "");
                            $this->db->where_in('id', $empIds);
                            $this->db->where('id !=', $this->loginId);
                            $appTokens = $this->db->get('employee_master')->result();

                            $this->db->select("web_token");
                            $this->db->where('is_delete', 0);
                            $this->db->where('web_token !=', "");
                            $this->db->where_in('id', $empIds);
                            $this->db->where('id !=', $this->loginId);
                            $webTokens = $this->db->get('employee_master')->result();

                            foreach ($appTokens as $row) :
                                $tokens[] = $row->device_token;
                            endforeach;

                            foreach ($webTokens as $row) :
                                $tokens[] = $row->web_token;
                            endforeach;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
        $tokens = (!empty($tokens)) ? array_unique($tokens) : array();
        return $tokens;
    }
    public function getPrefixNumber($prefix,$no,$explodeBy = '/'){ $prfx = explode($explodeBy,$prefix);return $prfx[0].'/'.$no.'/'.$prfx[1]; }
}
