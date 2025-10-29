<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_Controller extends CI_Controller{
	
	
	public function __construct(){
		parent::__construct();
		//echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
		$this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		$this->load->library('fcm');
		
		$this->load->model('masterModel');
		$this->load->model('NotificationModel','notification');
		$this->load->model('DashboardModel','dashboard');
		$this->load->model('ProcessModel','process');
		$this->load->model('MachineModel','machine');
		$this->load->model('TermsModel','terms');
		$this->load->model('MasterOptionsModel', 'masterOption');
		$this->load->model('StoreModel','store');
		$this->load->model('PartyModel','party');
		$this->load->model('ItemModel','item');
		$this->load->model('ItemCategoryModel','itemCategory');
		$this->load->model('PurchaseRequestModel','purchaseRequest');
		$this->load->model('PurchaseEnquiryModel','purchaseEnquiry');
		$this->load->model('PurchaseOrderModel','purchaseOrder');
		$this->load->model('GrnModel','grnModel');
		$this->load->model('PurchaseInvoiceModel','purchaseInvoice');
		$this->load->model('RejectionCommentModel','comment');
		
		//$this->load->model('JobMaterialDispatchModel','jobMaterial');
		$this->load->model('JobWorkModel','jobWork');
		$this->load->model('ProductionModel','production');
		$this->load->model('InstrumentModel','instrument');
		$this->load->model('InChallanModel','inChallan');
		$this->load->model('OutChallanModel','outChallan');

		$this->load->model('SalesEnquiryModel','salesEnquiry');
		$this->load->model('SalesOrderModel','salesOrder');
		$this->load->model('ProductInspectionModel','productInspection');
		$this->load->model('DeliveryChallanModel','challan');
		$this->load->model('SalesInvoiceModel','salesInvoice');
		$this->load->model('LeadModel','leads');
		$this->load->model('SalesQuotationModel','salesQuotation');
		$this->load->model('ReportModel','reportModel');
		$this->load->model('ProductReporModel','productReporModel');
		$this->load->model('TransactionMainModel','transModel');
		$this->load->model('ProformaInvoiceModel','proformaInv');

		$this->load->model('MaterialRequestModel','jobMaretialRequest'); 
		$this->load->model('JobWorkOrderModel','jobWorkOrder');
		$this->load->model('FinalInspectionModel','finalInspection');
		$this->load->model('StockVerificationModel', 'stockVerify');
		$this->load->model('ProductionOperationModel', 'operation');
		$this->load->model('MachineTicketModel', 'ticketModel');
		$this->load->model('ShiftModel', 'shiftModel');
		$this->load->model('MachineActivitiesModel', 'activities');
		$this->load->model('PackingModel', 'packings');
		$this->load->model('PreDispatchInspectModel', 'preDispatch');
		$this->load->model('ToolsIssueModel', 'toolsIssue');
		$this->load->model('StockJournalModel', 'stockJournal');
		$this->load->model('PackingInstructionModel', 'packingInstruction');
		$this->load->model('JobWorkInspectionModel', 'jobWorkInspection');
		$this->load->model('FeasibilityReasonModel','feasibilityReason');
		$this->load->model('CftAuthorizationModel', 'cftAuthorization');
		$this->load->model('FamilyGroupModel','familyGroup');
		
		$this->load->model('MainMenuConfModel','mainMenuConf');
		$this->load->model('SubMenuConfModel','subMenuConf');
		
		/*** Account Model ***/
		$this->load->model('LedgerModel','ledger');
		$this->load->model('PaymentTransactionModel','paymentTrans');
		$this->load->model('PaymentVoucherModel','paymentVoucher');
		$this->load->model('GroupModel','group');

		/***  Report Model ***/
		$this->load->model('report/ProductionReportModel','productionReports');
		$this->load->model('report/QualityReportModel','qualityReports');
		$this->load->model('report/StoreReportModel', 'storeReportModel');
		$this->load->model('report/SalesReportModel', 'salesReportModel');
		$this->load->model('report/PurchaseReportModel', 'purchaseReport');
		$this->load->model('report/AccountingReportModel', 'accountingReport');
		$this->load->model('report/ProductionReportNewModel','productionReportsNew');
		
		/*** HR Model ***/
		$this->load->model('hr/DepartmentModel','department');
		$this->load->model('hr/DesignationModel','designation');
		$this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/AttendanceModel','attendance');
		$this->load->model('hr/LeaveModel','leave');
		$this->load->model('hr/LeaveSettingModel','leaveSetting');
		$this->load->model('hr/LeaveApproveModel','leaveApprove');
		$this->load->model('hr/PayrollModel','payroll');
		$this->load->model('CategoryModel', 'category');
		$this->load->model('HolidaysModel', 'holiday');
		$this->load->model('AttendancePolicyModel', 'policy');
		$this->load->model('hr/ManualAttendanceModel','manualAttendance');
		$this->load->model('hr/ExtraHoursModel','extraHours');
		$this->load->model('hr/AdvanceSalaryModel','advanceSalary');
		$this->load->model('hr/SalaryStructureModel', 'salaryStructure');
		$this->load->model('hr/EmpLoanModel','empLoan');
		$this->load->model('PermissionModel','permission');
		$this->load->model('hr/SkillMasterModel', 'skillMaster');
		$this->load->model('hr/EmployeeFacilityModel', 'employeefacility');
		$this->load->model('hr/BiometricModel','biometric');
		$this->load->model('hr/LeaveAuthorityModel', 'leaveAuthorityModel');
		$this->load->model('hr/EmpRecruitmentModel','empRecruitment');

		$this->load->model('LineInspectionModel','lineInspection');
		$this->load->model('AssignInspectorModel','assignInspector');
		$this->load->model('ProcessSetupModel','processSetup');
		$this->load->model('SetupInspectionModel','setupInspection');
		$this->load->model('MaterialGradeModel','materialGrade');
		$this->load->model('ExpenseMasterModel','expenseMaster');
		$this->load->model('TaxMasterModel','taxMaster');
		$this->load->model('DebitNoteModel','debitNote');
		$this->load->model('CreditNoteModel','creditNote');
		$this->load->model('AttendancePolicyModel', 'policy');
		$this->load->model('GstExpenseModel','gstExpense');
		$this->load->model('JournalEntryModel','journalEntry');
		$this->load->model('LogSheetModel','logSheet');
		$this->load->model('ContactDirectoryModel','contactDirectory');
		$this->load->model('NotifyPermissionModel','notify');
		$this->load->model('GeneralIssueModel','generalIssue');
		$this->load->model('InspectionTypeModel','inspectionType');
		$this->load->model('NcReportModel','ncReport');
		$this->load->model('ScrapModel','scrap');
		$this->load->model('HsnMasterModel','hsnModel');
		$this->load->model('CommercialPackingModel','commercialPacking');
		$this->load->model('CommercialInvoiceModel','commercialInvoice');
		$this->load->model('CustomPackingModel','customPacking');
		$this->load->model('CustomInvoiceModel','customInvoice');
		$this->load->model('RejectionLogModel','rejectionLog');
		$this->load->model('RmProcessModel','rmProcessModel');
		$this->load->model('HoldAreaMovementModel','holdArea');
		$this->load->model('TransportModel','transport');
		$this->load->model('BankingModel','banking');

		$this->load->model('production_v2/JobWorkVendorModel','jobWorkVendor_v2');
		$this->load->model('production_v2/JobModel','jobcard_v2');
		$this->load->model('GenerateScrapModel','generateScrap');
		$this->load->model('ControlPlanModel','controlPlan');
		$this->load->model('PackingRequestModel','packingRequest');
		$this->load->model('CategoryModel', 'category');
		$this->load->model('GradeMasterModel', 'gradeMaster');
		$this->load->model('FeedbackPointModel','feedbackPoint');
				
		/** Production Model */
		$this->load->model('production/JobcardModel','jobcard');
		$this->load->model('production/ProcessMovementModel','processMovement');
		$this->load->model('production/OutsourceModel','outsource');
		$this->load->model('production/JobMaterialDispatchModel','jobMaterial');
		$this->load->model('production/PrimaryCFTModel','primaryCFT');
		$this->load->model('production/FinalCFTModel','finalCFT');
		$this->load->model('IssueRequisitionModel','issueRequisition');
		$this->load->model('ScrapGroupModel','scrapGroup');
		$this->load->model('MasterDetailModel','masterDetail');
		$this->load->model('DispatchPlanModel','dispatchPlan');
		$this->load->model('FeedbackModel','feedback');
		$this->load->model('CustomerComplaintsModel','customerComplaints');
		
		$this->load->model('QcInstrumentModel','qcInstrument');
		$this->load->model('QCPurchaseModel', 'qcPurchase'); 
	    $this->load->model('QCIndentModel', 'qcIndent');
		$this->load->model('QcPRModel', 'qcPRModel');
		$this->load->model('QcChallanModel','qcChallan');
		$this->load->model('CustomerReworkModel','customerRework');

		$this->data['currentFormDate'] = $this->session->userdata("currentFormDate");
		$this->financialYearList = $this->getFinancialYearList($this->session->userdata('issueDate'));	
		
		$this->OPR_MT_TYPE =$this->data['OPR_MT_TYPE'] =['Operation'=>['Turning','Milling'],'Material'=>['Casting','Material','OutSource']];
		$this->companyName = $this->data['companyName'] = $this->session->userdata("companyName");

		$this->setSessionVariables('notification,process,machine,store,party,item,itemCategory,purchaseEnquiry,purchaseOrder,purchaseInvoice,comment,jobcard,jobMaterial,jobWork,production,salesEnquiry,salesOrder,productInspection,challan,salesInvoice,leads,reportModel,jobMaretialRequest,grnModel,purchaseRequest,jobWorkOrder,finalInspection,transModel,masterOption,productionReports,qualityReports,inChallan,outChallan,stockVerify,operation,ticketModel,proformaInv,storeReportModel,salesReportModel,activities,purchaseReport,packings,preDispatch,toolsIssue,stockJournal,packingInstruction,ledger,paymentTrans,jobWorkInspection,lineInspection,assignInspector,processSetup,setupInspection,feasibilityReason,cftAuthorization,familyGroup,paymentVoucher,mainMenuConf,subMenuConf,group,expenseMaster,taxMaster,debitNote,creditNote,policy,materialGrade,gstExpense,journalEntry,accountingReport,productionReportsNew,logSheet,contactDirectory,jobWorkVendor_v2,jobcard_v2,notify,generalIssue,inspectionType,ncReport,scrap,hsnModel,commercialPacking,commercialInvoice,customPacking,customInvoice,rmProcessModel,rejectionLog,holdArea,empLoan,transport,banking,generateScrap,controlPlan,packingRequest,processMovement,gradeMaster,primaryCFT,finalCFT,issueRequisition,feedbackPoint,masterDetail,dispatchPlan,feedback,customerComplaints, department,designation,employee,attendance,leave,leaveSetting,leaveApprove,payroll,category,holiday,policy,manualAttendance,extraHours,advanceSalary,salaryStructure,empLoan,permission,skillMaster,employeefacility,biometric,leaveAuthorityModel,empRecruitment,qcInstrument,qcPurchase,qcIndent,qcPRModel,qcChallan,customerRework');
		
		$this->data['stockTypes'] = [-1=>'Opening Stock',0=>'',1=>'GRN', 2=>'Purchase Invoice', 3=>'Material Issue', 4=>'Delivery Challan', 5=>'Sales Invoice', 6=>'Manual Manage Stock', 7=>'Production Finish', 8 =>'Visual Inspection', 9 =>'Store Transfer', 10=>'Return Stock From Production', 11=>'In Challan', 12=>'Out Challan', 13=>'Tools Issue', 14 =>'Stock Journal', 15 =>'Packing Material', 16 =>'Packing Product', 17 =>'Rejection Scrap', 18 =>'Production Scrap', 19 =>'Credit Note', 20 =>'Debit Note', 21=>'General Issue', 22 =>'Stock Verification', 23 =>'Process Movement', 24 =>'Production Rejection', 25=>'Production Rejection Scrap', 26=>'Move to Allocation RM Store', 27=>'Move To Received RM Store', 28=>'Move To Packing Area', 29=>'RM Process',30 => 'Regular/Export Packing', 31 => 'Jobwork Return', 32 => 'Job Short Closed', 99=>'Stock Adjustment'];	
	}

	public function setSessionVariables($modelNames)
	{
		$this->data['dates'] = explode(' AND ',$this->session->userdata('financialYear'));
		$this->shortYear = $this->data['shortYear'] = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
		$this->startYearDate = $this->data['startYearDate'] = date('Y-m-d',strtotime($this->data['dates'][0]));
		$this->endYearDate = $this->data['endYearDate'] = date('Y-m-d',strtotime($this->data['dates'][1]));
		$this->startYear = date('Y',strtotime($this->data['dates'][0]));
		$this->endYear = date('Y',strtotime($this->data['dates'][1]));
		$this->data['start_year'] = $this->start_year = date('Y',strtotime($this->data['dates'][0]));
		$this->data['end_year'] = $this->end_year = date('Y',strtotime($this->data['dates'][1]));
		$this->loginId = $this->session->userdata('loginId');
		$this->userRole = $this->session->userdata('role');
		$this->RTD_STORE = $this->session->userdata('RTD_STORE');
		$this->PKG_STORE = $this->session->userdata('PKG_STORE');
		$this->INSP_STORE = $this->session->userdata('INSP_STORE');
		$this->PROD_STORE = $this->session->userdata('PROD_STORE');
		$this->GAUGE_STORE = $this->session->userdata('GAUGE_STORE');
		$this->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
		$this->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
		$this->HLD_STORE = $this->session->userdata('HLD_STORE');
		$this->RM_PRS_STORE = $this->session->userdata('RM_PRS_STORE');
		$this->MIS_PLC_STORE = $this->session->userdata('MIS_PLC_STORE');
		$this->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
		$this->data['ip_address'] = $this->get_client_ip();

		$this->controlPlanEnable = 0;
		
		$models = explode(',',$modelNames);
		
		if($this->endYearDate <= date("Y-m-d")){$this->data['maxDate'] = $this->endYearDate;}else{$this->data['maxDate'] = date('Y-m-d');}
		
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->data['dates'];
			$this->{$modelName}->loginID = $this->session->userdata('loginId');
			$this->{$modelName}->userName = $this->session->userdata('user_name');
			$this->{$modelName}->userRole = $this->session->userdata('role');
			$this->{$modelName}->userRoleName = $this->session->userdata('roleName');
			$this->{$modelName}->shortYear = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYear = date('Y',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYear = date('Y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYearDate = date('Y-m-d',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYearDate = date('Y-m-d',strtotime($this->data['dates'][1]));
			$this->{$modelName}->RTD_STORE = $this->session->userdata('RTD_STORE');
			$this->{$modelName}->PKG_STORE = $this->session->userdata('PKG_STORE');
			$this->{$modelName}->INSP_STORE = $this->session->userdata('INSP_STORE');
			$this->{$modelName}->PROD_STORE = $this->session->userdata('PROD_STORE');
			$this->{$modelName}->GAUGE_STORE = $this->session->userdata('GAUGE_STORE');
			$this->{$modelName}->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
			$this->{$modelName}->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
			$this->{$modelName}->HLD_STORE = $this->session->userdata('HLD_STORE');
			$this->{$modelName}->RM_PRS_STORE = $this->session->userdata('RM_PRS_STORE');
			$this->{$modelName}->MIS_PLC_STORE = $this->session->userdata('MIS_PLC_STORE');
		endforeach;
		return true;
	}
	
	public function getFinancialYearList($issueDate){
		$startYear  = ((int)date("m",strtotime($issueDate)) >= 4) ? date("Y",strtotime($issueDate)) : (int)date("Y",strtotime($issueDate)) - 1;
		$endYear  = ((int)date("m") >= 4) ? date("Y") + 1 : (int)date("Y");
		
		$startDate = new DateTime($startYear."-04-01");
		$endDate = new DateTime($endYear."-03-31");
		$interval = new DateInterval('P1Y');
		$daterange = new DatePeriod($startDate, $interval ,$endDate);
		$fyList = array();$val="";$label="";
		foreach($daterange as $dates)
		{
			$start_date = date("Y-m-d H:i:s",strtotime("01-04-".$dates->format("Y")." 00:00:00"));
			$end_date = date("Y-m-d H:i:s",strtotime("31-03-".((int)$dates->format("Y") + 1)." 23:59:59"));
			
			$val = $start_date." AND ".$end_date;
			$label = 'Year '.date("Y",strtotime($start_date)).'-'.date("Y",strtotime($end_date));
			$fyList[] = ["label" => $label, "val" => $val];
		}
		return $fyList;
	}
	
	public function getMonthListFY($format = "F-Y"){
		$monthList = array();
		$start    = (new DateTime($this->startYearDate))->modify('first day of this month');
        $end      = (new DateTime($this->endYearDate))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $i=0;
        foreach ($period as $dt) {
			$monthList[$i]['val'] = $dt->format("Y-m-d");
			$monthList[$i++]['label'] = $dt->format($format);
        }
		return $monthList;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("LoginOk")):
			//redirect( base_url() );
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}

	public function printDecimal($val){
		return number_format($val,0,'','');
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
	
	public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    public function get_client_ip1() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
	public function importExcelFile($file,$path,$sheetName){
		$item_excel = '';
		if(isset($file['name']) || !empty($file['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $file['name'];
			$_FILES['userfile']['type']     = $file['type'];
			$_FILES['userfile']['tmp_name'] = $file['tmp_name'];
			$_FILES['userfile']['error']    = $file['error'];
			$_FILES['userfile']['size']     = $file['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/'.$path);
			$config = ['file_name' => "".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['item_excel'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$item_excel = $uploadData['file_name'];
			endif;
			if(!empty($item_excel)):
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$item_excel);
				$fileData = array($spreadsheet->getSheetByName($sheetName)->toArray(null,true,true,true));
				return $fileData;
			else:
				return ['status'=>0,'message'=>'Data not found...!'];
			endif;
		else:
			return ['status'=>0,'message'=>'Please Select File!'];
		endif;
    }
	
}
?>