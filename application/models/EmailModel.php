<?php

class EmailModel extends MasterModel{
	
    private $emailLogs = "email_logs";
	
	public function sendMail($postData,$attachmentArray)
	{
	    //$attachmentArray = Array();
	    $this->load->library('email');
		$this->email->clear(TRUE);

		/* $emailConfig = Array(
			'protocol' 	=> 'smtp',
			'smtp_host' => 'smtp.gmail.com',
			'smtp_port' => 587,
			'smtp_user' => 'nativebitoffice@gmail.com',
			'smtp_pass' => 'nboimmgnsxsmyqvn',
			'mailtype'  => 'html', 
			'charset'   => 'utf-8'
		); */

		$this->email->set_newline("\r\n");
		$sent_to = explode(',',$postData['receiver_email']);

		if(count($sent_to) > 0){
			$this->email->from($postData['sender_email'], 'JAY JALARAM PRECISION COMPONENT LLP');
			$this->email->to($postData['receiver_email']);

			$this->email->subject($postData['subject']);
			$this->email->message($postData['mail_body']);
			if(!empty($attachmentArray)){
				foreach($attachmentArray as $file_name){
					if(!empty($file_name)){
						$this->email->attach($file_name);
					}
				}
			}

			$this->email->set_mailtype('html');
			if($this->email->send()){
				$mailData['mail_type'] = $postData['mail_type'];
				$mailData['sender_email'] = $postData['sender_email'];
				$mailData['receiver_email'] = $postData['receiver_email'];
				$mailData['subject'] = $postData['subject'];
				$mailData['mail_body'] = $postData['mail_body'];
				$mailData['ref_id'] = $postData['ref_id'];
				$mailData['ref_no'] = $postData['ref_no'];
				$mailData['created_by'] = (isset($postData['created_by']))?$postData['created_by']:$this->loginID;
				$this->db->insert($this->emailLogs,$mailData);
				
				return ['status'=>1,"message"=>"Email has been successfully Sent"];
			}else{
				return ['status'=>0,"message"=>$this->email->print_debugger()];
			}
		}else{return ['status'=>0,"message"=>"Receiver Email Not found"];}
	}
	
}
?>