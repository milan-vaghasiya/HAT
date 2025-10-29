<?php 

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class MY_Apicontroller extends CI_Controller{
    
    private $data = array();
    
    public function __construct(){
        parent::__construct();
        $this->data = new StdClass;
        $this->checkAuth();
		$this->load->library('pagination');
		$this->load->library('fcm');

		$this->load->model('masterModel');
		$this->load->model('NotificationModel','notification');
		
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
		$this->load->model('JobcardModel','jobcard');
		$this->load->model('JobMaterialDispatchModel','jobMaterial');
		$this->load->model('ProcessApprovalModel','processApprove');
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
		$this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/AttendanceModel','attendance');
		$this->load->model('hr/LeaveModel','leave');
		$this->load->model('hr/LeaveSettingModel','leaveSetting');
		$this->load->model('hr/LeaveApproveModel','leaveApprove');
		$this->load->model('hr/PayrollModel','payroll');
		$this->load->model('PermissionModel','permission');
		$this->load->model('hr/ManualAttendanceModel','manualAttendance');
		$this->load->model('hr/ExtraHoursModel','extraHours');
		$this->load->model('hr/DesignationModel','designation');
		$this->load->model('hr/BiometricModel','biometric');

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

		$this->load->model('production_v2/JobWorkVendorModel','jobWorkVendor_v2');
		$this->load->model('production_v2/JobModel','jobcard_v2');
		$this->load->model('production_v2/ProcessMovementModel','processApprove_v2');
		
		
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        //print_r($headData);exit;
        $currentDate = $headData->currentFormDate;
		$this->data->currentFormDate = $currentDate;
		//$this->financialYearList = $this->getFinancialYearList($this->session->userdata('issueDate'));	
		$this->setHeaderVariables('notification,process,machine,store,party,item,itemCategory,purchaseEnquiry,purchaseOrder,purchaseInvoice,comment,jobcard,jobMaterial,processApprove,jobWork,production,salesEnquiry,salesOrder,productInspection,challan,salesInvoice,leads,reportModel,department,employee,attendance,leave,leaveSetting,leaveApprove,payroll,jobMaretialRequest,grnModel,purchaseRequest,jobWorkOrder,finalInspection,transModel,masterOption,productionReports,qualityReports,inChallan,outChallan,stockVerify,operation,ticketModel,shiftModel,proformaInv,storeReportModel,salesReportModel,activities,purchaseReport,permission,packings,preDispatch,toolsIssue,stockJournal,packingInstruction,ledger,paymentTrans,jobWorkInspection,lineInspection,assignInspector,processSetup,setupInspection,feasibilityReason,cftAuthorization,familyGroup,paymentVoucher,mainMenuConf,subMenuConf,group,expenseMaster,taxMaster,debitNote,creditNote,policy,biometric,designation,extraHours,manualAttendance,materialGrade,gstExpense,journalEntry,accountingReport,productionReportsNew,logSheet,contactDirectory,jobWorkVendor_v2,jobcard_v2,processApprove_v2,notify,generalIssue');
    }

    public function setHeaderVariables($modelNames){
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        
		$this->data->dates = explode(' AND ',$headData->financialYear);
		$this->shortYear = date('y',strtotime($this->data->dates[0])).'-'.date('y',strtotime($this->data->dates[1]));
		$this->startYearDate = $this->data->startYearDate = date('Y-m-d',strtotime($this->data->dates[0]));
		$this->endYearDate = $this->data->endYearDate = date('Y-m-d',strtotime($this->data->dates[1]));
		
		if($this->endYearDate <= date("Y-m-d")):
			$this->data->maxDate = $this->endYearDate;
		else:
			$this->data->maxDate = date('Y-m-d');
		endif;
		
		$this->loginId = $headData->loginId;
		$this->RTD_STORE = $headData->RTD_STORE;
		$this->PKG_STORE = $headData->PKG_STORE;
		$this->PROD_STORE = $headData->PROD_STORE;
		$models = explode(',',$modelNames);
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->data->dates;
			$this->{$modelName}->loginID = $headData->loginId;
			$this->{$modelName}->userName = $headData->emp_name;
			$this->{$modelName}->userRole = $headData->role;
			$this->{$modelName}->userRoleName = $headData->roleName;
			$this->{$modelName}->shortYear = date('y',strtotime($this->data->dates[0])).'-'.date('y',strtotime($this->data->dates[1]));
			$this->{$modelName}->startYear = date('Y',strtotime($this->data->dates[0]));
			$this->{$modelName}->endYear = date('Y',strtotime($this->data->dates[1]));
			$this->{$modelName}->startYearDate = date('Y-m-d',strtotime($this->data->dates[0]));
			$this->{$modelName}->endYearDate = date('Y-m-d',strtotime($this->data->dates[1]));
			$this->{$modelName}->RTD_STORE = $headData->RTD_STORE;
			$this->{$modelName}->PKG_STORE = $headData->PKG_STORE;
			$this->{$modelName}->PROD_STORE = $headData->PROD_STORE;
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

    public function printJson($data){
		print json_encode($data);exit;
	}

    public function printDecimal($val){
		return number_format($val,0,'','');
	}

    public function checkAuth(){
        if($token = $this->input->get_request_header('Authorization')):
            $this->load->model('LoginModel','loginModel');
            $result = $this->loginModel->checkToken($token);
            if($result == 0):
				http_response_code(401);
                $this->printJson(['status'=>0,'message'=>"Unauthorized",'data'=>null]);
            endif;
            if(!$this->input->get_request_header('headData')):
				http_response_code(401);
                $this->printJson(['status'=>0,'message'=>"Header data not found.",'data'=>null]);
            endif;
            return true;  
        else:
			http_response_code(401);
            $this->printJson(['status'=>0,'message'=>"Authorization data not found",'data'=>null]);
        endif;
    }
}
?>