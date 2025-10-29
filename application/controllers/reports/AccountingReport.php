<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class AccountingReport extends MY_Controller
{
    private $indexPage = "report/account_report/index";
    private $sales_register = "report/account_report/sales_register";
    private $sales_register_item = "report/account_report/sales_register_item";
    private $purchase_register = "report/account_report/purchase_register";
    private $stock_register = "report/account_report/stock_register";
    private $receivable = "report/account_report/receivable";
    private $payable = "report/account_report/payable";
    private $bank_book = "report/account_report/bank_book";
    private $cash_book = "report/account_report/cash_book";
    private $account_ledger = "report/account_report/account_ledger";
    private $debit_note = "report/account_report/debitNote_register";
    private $credit_note = "report/account_report/creditNote_register";
    private $sales_report = "report/account_report/sales_report";
    private $purchase_report = "report/account_report/purchase_report";
    private $account_ledger_detail = "report/account_report/account_ledger_detail";
    private $outstanding = "report/account_report/outstanding";
    private $gstr1_report = "report/account_report/gstr1_report";
    private $gstr2_report = "report/account_report/gstr2_report";
    private $profit_and_loss = "report/account_report/profit_and_loss";
    private $balance_sheet = "report/account_report/balance_sheet";
    private $trail_balance = "report/account_report/trail_balance";
    private $fund_management = "report/account_report/fund_management";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Accounting Report";
        $this->data['headData']->controller = "reports/accountingReport";
        $this->data['floatingMenu'] = '';//$this->load->view('report/account_report/floating_menu', [], true);
    }

    public function index()
    {
        $this->data['pageHeader'] = 'Reports';
        $this->load->view($this->indexPage, $this->data);
    }


    public function receivableReport()
    {
        $this->data['pageHeader'] = 'RECEIVABLE';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->receivable, $this->data);
    }

    public function payableReport()
    {
        $this->data['pageHeader'] = 'PAYABLE';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->payable, $this->data);
    }

    public function salesReport()
    {
        $this->data['pageHeader'] = 'SALES REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->sales_report, $this->data);
    }

    public function purchaseReport()
    {
        $this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->purchase_report, $this->data);
    }

    public function getReceivable()
    {
        $data = $this->input->post();
        $receivable = $this->accountingReport->getReceivable($data['from_date'], $data['to_date']);
        $i = 1;
        $tbody = "";
        $totalClBalance = 0;
        foreach ($receivable as $row) :
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $row->account_name . '</td>
                <td>' . $row->group_name . '</td>
                <td>' . $row->cl_balance . '</td>
            </tr>';
            $totalClBalance += $row->cl_balance;
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'totalClBalance' => $totalClBalance]);
    }

    public function getPayable()
    {
        $data = $this->input->post();
        $payable = $this->accountingReport->getPayable($data['from_date'], $data['to_date']);
        $i = 1;
        $tbody = "";
        $totalClBalance = 0;
        foreach ($payable as $row) :
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $row->account_name . '</td>
                <td>' . $row->group_name . '</td>
                <td>' . $row->cl_balance . '</td>
            </tr>';
            $totalClBalance += $row->cl_balance;
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'totalClBalance' => $totalClBalance]);
    }

    public function getSalesReport()
    {
        $data = $this->input->post();
        $salesReport = $this->accountingReport->getAccountReportData($data['from_date'], $data['to_date'], '6,7,8,10,11,13');
        $i = 1;
        $tbody = "";
        $otherAmt = 0;
        foreach ($salesReport as $row) :
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->vou_name_s . '</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . round($otherAmt, 2) . '</td>
                <td>' . $row->net_amount . '</td>
            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function getPurchaseReport()
    {
        $data = $this->input->post();
        $purchaseReport = $this->accountingReport->getAccountReportData($data['from_date'], $data['to_date'], '12,14');
        $i = 1;
        $tbody = "";
        $otherAmt = 0;
        foreach ($purchaseReport as $row) :
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->doc_no . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->vou_name_s . '</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . round($otherAmt, 2) . '</td>
                <td>' . $row->net_amount . '</td>
            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }



    //UPDATE BY JP @19-04-2022
    public function getStockRegister_old()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $stockType = '';
            if ($data['stock_type'] == '0') {
                $stockType = 'stockQty = 0';
            }
            if ($data['stock_type'] == 1) {
                $stockType = 'stockQty > 0';
            }
            $itemData = $this->accountingReport->getStockRegister($data['item_type'], $stockType);
            $thead = "";
            $tbody = "";
            $tfoot="";
            $i = 1;
            $receiptQty = 0;
            $issuedQty = 0;
            $totalAmt = 0;
            $treceiptQty = 0;
            $tissuedQty = 0;
            $tbalanceQty = 0;
            
            if (!empty($itemData)) :
                foreach ($itemData as $row) :
                    $data['item_id'] = $row->id;
                    $bQty = 0;
                    $receiptQty = $row->rqty;
                    $issuedQty = $row->iqty;
                    $itmStock = $row->stockQty;
                    if (!empty($row->stockQty)) {
                        $bQty = $row->stockQty;
                    }
                    $balanceQty = 0;
                    if ($row->item_type == 1) {
                        $balanceQty = round($bQty, 3);
                    } else {
                        $balanceQty = round($receiptQty - abs($issuedQty), 3);
                    }
                    $balanceQty = round($bQty, 3);
                    $price = (!empty($row->inrrate)) ? ($row->price * $row->inrrate) : $row->price;
                    $tamt = ($balanceQty > 0) ? round($balanceQty * $price, 2) : 0;
                    /*if($data['stock_type'] == 0){
                        if(intVal($balanceQty) == 0){
                            $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_name.'</td>
                                <td class="text-right">'.floatVal($receiptQty).'</td>
                                <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                                <td class="text-right">'.floatVal($balanceQty).'</td>
                                <td class="text-right">'.number_format($tamt,2).'</td>
                            </tr>';
                            $totalAmt += $tamt;
                        }
                    }elseif($data['stock_type'] == 1){
						if(intVal($balanceQty) > 0){
                            $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_name.'</td>
                                <td class="text-right">'.floatVal($receiptQty).'</td>
                                <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                                <td class="text-right">'.floatVal($balanceQty).'</td>
                                <td class="text-right">'.number_format($tamt,2).'</td>
                            </tr>';
                            $totalAmt += $tamt;
                        }
					}else{
                        $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_name.'</td>
                                <td class="text-right">'.floatVal($receiptQty).'</td>
                                <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                                <td class="text-right">'.floatVal($balanceQty).'</td>
                                <td class="text-right">'.number_format($tamt,2).'</td>
                            </tr>';
                            $totalAmt += $tamt;
                    }*/
                    $tbody .= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . (($row->item_type == 1) ? '[' . $row->item_code . '] ' . $row->item_name : $row->item_name) . '</td>
                                <td class="text-right">' . floatVal($receiptQty) . '</td>
                                <td class="text-right">' . abs(floatVal($issuedQty)) . '</td>
                                <td class="text-right">' . floatVal($balanceQty) . '</td>
                                <td class="text-right">' . number_format($tamt, 2) . '</td>
                            </tr>';
                    $totalAmt += $tamt;
                    $treceiptQty += $receiptQty; 
                    $tissuedQty += $issuedQty;
                    $tbalanceQty += $balanceQty;
                endforeach;
                $thead .= '<tr>
                            <th class="text-center" colspan="5">Stock Register</th>
                            <th class="text-right">' . number_format($totalAmt, 2) . '</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th class="text-right">Receipt Qty.</th>
                            <th class="text-right">Issued Qty.</th>
                            <th class="text-right">Balance Qty.</th>
                            <th class="text-right">Amount</th>
                        </tr>';
               
            else :
                $tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
                $thead .= '<tr class="text-center">
                            <th colspan="7">Stock Register</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Receipt Qty.</th>
                            <th>Issued Qty.</th>
                            <th>Balance Qty.</th>
                            <th>Amount</th>
                        </tr>';
               
            endif;
            $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    /*******************************************************************************************/
    /*** FROM SHINING TOOLS**/

    public function salesRegisterReport(){
	    $this->data['headData']->pageTitle = "SALES REGISTER";
        $this->data['pageHeader'] = 'SALES REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->sales_register,$this->data);
    }

	public function salesRegisterReportItemWise(){
	    $this->data['headData']->pageTitle = "SALES REGISTER ITEMWISE";
        $this->data['pageHeader'] = 'SALES REGISTER ITEMWISE';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->sales_register_item,$this->data);
    }

    public function getSalesRegisterReport($jsonData=""){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}       
        $result = $this->salesInvoice->getInvoiceSummary($postData);
        $salesRegisterReport = $this->salesInvoice->getSalesInvDataBillWise($postData);
        $i=1; $tbody="";
        foreach($salesRegisterReport as $row):
            $trno = $row->trans_number;
            //if(in_array($this->userRole,[-1,1,3])){$trno= '<a href="'.base_url('salesInvoice/edit/'.$row->id).'" target="_blank" datatip="Edit Invoice" flow="left"> '.$row->trans_number.'</a>';}
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$trno.'</td>
                <td class="text-left">'.$row->party_name.'</td>
                <td>'.$row->taxable_amount.'</td>
                <td>'.$row->net_amount.'</td>
                
            </tr>';
        endforeach; 
        
        $reportTitle = 'BILL WISE SALES REGISTER';
        $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
        $thead .= '<tr>
                        <th>#</th>
                        <th>Invoice Date</th>
                        <th>Invoice No.</th>
                        <th>Customer Name</th>
                        <th>Taxable Amount</th>
                        <th>Net Amount</th>							
                </tr>';
        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        $letter_head = base_url('assets/images/letterhead_top.png');
        
        $pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="receivableData">'.$tbody.'</tbody>
                            
                        </table>';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                            <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
                        </tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                        <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
                        <tr>
                            <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'</td>

                        </tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';
			
        if(!empty($postData['pdf']))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/SalesRegister.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else{$this->printJson(['status'=>1, 'tbody'=>$tbody,'taxable_amount'=>$result->taxable_amount,'net_amount'=>$result->net_amount]);}
    }
    
    public function getSalesRegisterReportItemWise($jsonData=""){
        if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}   
        $result = $this->salesInvoice->getInvoiceSummary($postData);
        $salesRegisterReport = $this->salesInvoice->getSalesInvDataItemWise($postData);
        $i=1; $tbody="";
        foreach($salesRegisterReport as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.date("d-m-Y",strtotime($row->trans_date)).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->item_name.'</td>
                <td>'.$row->qty.'</td>
                <td>'.$row->price.'</td>
                <td  class="text-right">'.$row->disc_amount.'</td>
                <td  class="text-right">'.$row->amount.'</td>
            </tr>';
        endforeach; 
            $reportTitle = 'BILL WISE SALES REGISTER';
            $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                            <th>#</th>
                            <th>Invoice No.</th>
                            <th>Invoice Date</th>
                            <th>Customer Name</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Disc.Amount</th>
                            <th>Amount</th>							
                    </tr>';
            $companyData = $this->salesInvoice->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);
			$letter_head = base_url('assets/images/letterhead_top.png');
			
			$pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
								<thead class="thead-info" id="theadData">'.$thead.'</thead>
								<tbody id="receivableData">'.$tbody.'</tbody>
								
							</table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                            <tr>
                                <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
                            </tr>
                        </table>
                        <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                            <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                        </table>
                        <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
                            <tr>
                                <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'</td>

                            </tr>
                        </table>';
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
							<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
        if(!empty($postData['pdf']))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/SalesRegister.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else{$this->printJson(['status'=>1, 'tbody'=>$tbody,'taxable_amount'=>$result->taxable_amount,'net_amount'=>$result->net_amount]);}
 
            
       
    }

    /*** Sales Register As per Sales Invoice ***/
    public function getDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'REPORT';
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'BILLWISE';$row->listType = 'REPORT';$row->userRole = $this->userRole;
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
	public function getItemWiseDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'REPORT';
        $result = $this->salesInvoice->getItemWiseDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'ITEMWISE';$row->listType = 'REPORT';$row->userRole = $this->userRole;
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    //  Create By : JP @24-05-2022 11:25 AM
    public function getInvoiceSummary($jsonData = '')
    {
        if (!empty($jsonData)) {
            $postData = (array) json_decode(urldecode(base64_decode($jsonData)));
        } else {
            $postData = $this->input->post();
        }
        //print_r($postData);exit;
        $result = $this->salesInvoice->getInvoiceSummary($postData);
        $reportTitle = 'BILL WISE SALES REGISTER';
        $report_date = date('d-m-Y', strtotime($postData['from_date'])) . ' to ' . date('d-m-Y', strtotime($postData['to_date']));

        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        $letter_head = base_url('assets/images/letterhead_top.png');
        $InvData = $this->salesInvoice->getSalesInvDataBillWise($postData); //print_r($InvData);exit;

        $tbody = "";
        $thead = "";
        $i = 1;

        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">' . $reportTitle . ' (' . $report_date . ')</th></tr>' : '';
        $thead .= '<tr>
						<th>#</th>
						<th>Invoice No.</th>
						<th>Invoice Date</th>
						<th>Customer Name</th>
						<th>Taxable Amount</th>
						<th>Net Amount</th>							
				</tr>';
        foreach ($InvData as $row) :
            $tbody .= '<tr>
				<td>' . $i++ . '</td>
				<td>' . $row->trans_number . '</td>
				<td>' . date("d-m-Y", strtotime($row->trans_date)) . '</td>
				<td>' . $row->party_name . '</td>
				<td class="text-right">' . $row->taxable_amount . '</td>
				<td class="text-right">' . $row->net_amount . '</td>
				</tr>';
        endforeach;

        $pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">' . $thead . '</thead>
							<tbody id="receivableData">' . $tbody . '</tbody>
						</table>';
        $htmlHeader = '<img src="' . $letter_head . '">';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">' . $companyData->company_name . '</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : ' . $report_date . '</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">' . $reportTitle . '</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">
						    ' . $result->taxable_amount . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $result->net_amount . '
						</td>
					</tr>
				</table>';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';

        $custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId, $postData);
        if (!empty($customerList)) {
            foreach ($customerList as $row) {
                $select = (!empty($postData['party_id']) and $postData['party_id'] == $row->id) ? 'selected' : '';
                $custOption .= '<option value="' . $row->id . '" ' . $select . '>' . $row->party_name . ' | ' . $row->city_name . '</option>';
            }
        }
        $custOption .= '</select>';
        if (!empty($postData['pdf'])) {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath . '/SalesRegister.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L', '', '', '', '', 5, 5, 30, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);

            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        } else {
            $this->printJson(['taxable_amount' => $result->taxable_amount, 'gst_amount' => $result->gst_amount, 'net_amount' => $result->net_amount, 'custOption' => $custOption]);
        }
    }

    //  Create By : Karmi @26-05-2022
    public function getInvoiceSummarybillWise($jsonData = '')
    {
        if (!empty($jsonData)) {
            $postData = (array) json_decode(urldecode(base64_decode($jsonData)));
        } else {
            $postData = $this->input->post();
        }
        $result = $this->salesInvoice->getInvoiceSummary($postData);
        $reportTitle = 'ITEM WISE SALES REGISTER';
        $report_date = date('d-m-Y', strtotime($postData['from_date'])) . ' to ' . date('d-m-Y', strtotime($postData['to_date']));

        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        $letter_head = base_url('assets/images/letterhead_top.png');
        $InvData = $this->salesInvoice->getSalesInvDataItemWise($postData); //print_r($InvData);exit;

        $tbody = "";
        $thead = "";
        $i = 1;

        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">' . $reportTitle . ' (' . $report_date . ')</th></tr>' : '';
        $thead .= '<tr>
					<th class="text-center">#</th>
					<th class="text-center">Invoice No.</th>
					<th class="text-center">Invoice Date</th>
					<th class="text-left">Customer Name</th>
					<th class="text-right">Item Name</th>
					<th class="text-right">Qty</th>
					<th class="text-right">Rate</th>
					<th class="text-right">Discount</th>
					<th class="text-right">Amount</th>
				</tr>';
        foreach ($InvData as $row) :
            $tbody .= '<tr>
				<td>' . $i++ . '</td>
				<td>' . $row->trans_number . '</td>
				<td>' . date("d-m-Y", strtotime($row->trans_date)) . '</td>
				<td>' . $row->party_name . '</td>
				<td>' . $row->item_name . '</td>
				<td>' . $row->qty . '</td>
				<td>' . $row->price . '</td>
				<td  class="text-right">' . $row->disc_amount . '</td>
				<td  class="text-right">' . $row->amount . '</td>
				</tr>';
        endforeach;

        $pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">' . $thead . '</thead>
							<tbody id="receivableData">' . $tbody . '</tbody>
						</table>';
        $htmlHeader = '<img src="' . $letter_head . '">';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">' . $companyData->company_name . '</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : ' . $report_date . '</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">' . $reportTitle . '</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">' . $result->taxable_amount . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $result->net_amount . '</td>
					</tr>
				</table>';

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';

        /*** Custome Select Box ***/
        $custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId, $postData);
        if (!empty($customerList)) {
            foreach ($customerList as $row) {
                $select = (!empty($postData['party_id']) and $postData['party_id'] == $row->id) ? 'selected' : '';
                $custOption .= '<option value="' . $row->id . '" ' . $select . '>' . $row->party_name . ' | ' . $row->city_name . '</option>';
            }
        }
        $custOption .= '</select>';

        if (!empty($postData['pdf'])) {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath . '/SalesRegister.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L', '', '', '', '', 5, 5, 30, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);

            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        } else {
            $this->printJson(['taxable_amount' => $result->taxable_amount, 'net_amount' => $result->net_amount, 'custOption' => $custOption]);
        }
    }


    public function purchaseRegisterReport()
    {
        $this->data['headData']->pageTitle = "PURCHASE REGISTER";
        $this->data['pageHeader'] = 'PURCHASE REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->purchase_register, $this->data);
    }

    public function getPurchaseRegisterReport()
    {
        $data = $this->input->post();
        $purchaseRegisterReport = $this->accountingReport->getAccountReportData($data['from_date'], $data['to_date'], 12);
        $i = 1;
        $tbody = "";
        foreach ($purchaseRegisterReport as $row) :
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->doc_no . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->currency . '</td>
                <td>' . $row->net_amount . '</td>
                <td></td>
            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function stockRegisterReport_old()
    {
        $this->data['headData']->pageTitle = "STOCK REGISTER";
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->stock_register, $this->data);
    }

    //Created BY Karmi @27/05/2022
    public function getStockRegisterData($jsonData = '')
    {
        if (!empty($jsonData)) {
            $postData = (array) decodeURL($jsonData);
        } else {
            $postData = $this->input->post();
        }
        $stockRegisterReport = $this->accountingReport->getStockReportData($postData);
        $i = 1;
        $tbody = "";
        foreach ($stockRegisterReport as $row) :
            $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->item_name . '</td>
                    <td>' . $row->category_name . '</td>
                    <td>' . $row->store_name . '</td>
                    <td>' . $row->location . '</td>
                    <td>' . $row->current_stock . '</td>
                </tr>';
        endforeach;
        $reportTitle = 'STOCK REGISTER';
        $report_date = date('d-m-Y', strtotime($postData['from_date'])) . ' to ' . date('d-m-Y', strtotime($postData['to_date']));
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">' . $reportTitle . ' (' . $report_date . ')</th></tr>' : '';
        $thead .= '<tr>										
                        <th style="min-width:25px;">#</th>
                        <th style="min-width:80px;">Item Code</th>
                        <th style="min-width:80px;">Item Name</th>
                        <th style="min-width:50px;">Category</th>
                        <th style="min-width:100px;">Store</th>
                        <th style="min-width:50px;">Rack</th>
                        <th style="min-width:50px;">Current Stock</th>
                    </tr>';
        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        $letter_head = base_url('assets/images/letterhead_top.png');

        $pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
                                <thead class="thead-info" id="theadData">' . $thead . '</thead>
                                <tbody id="receivableData">' . $tbody . '</tbody>
                                
                            </table>';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                            <tr>
                                <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">' . $reportTitle . '</td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">' . $companyData->company_name . '</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">' . $report_date . '</td>
                            </tr>
                        </table>
                        <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                            <tr><td class="org-address text-center" style="font-size:13px;">' . $companyData->company_address . '</td></tr>
                        </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                            <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                        </tr>
                    </table>';

        if (!empty($postData['pdf'])) {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath . '/StockRegister.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L', '', '', '', '', 5, 5, 19, 5, 3, 3, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);

            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        } else {
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        }
    }
        
    public function outstandingReport(){
		$this->data['headData']->pageTitle = "OUTSTANDING LEDGER";
        $this->data['pageHeader'] = 'OUTSTANDING LEDGER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->outstanding,$this->data);
    }
    
    public function getOutstanding($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}
		if($postData['report_type']==2){$postData['from_date'] = $this->startYearDate;$postData['to_date'] = $this->endYearDate;};
        $outstandingData = $this->accountingReport->getOutstanding($postData);
        $i=1; $tbody="";$totalClBalance = 0;$daysTotal=Array();$below30 = 0;$age60 = 0;$age90 = 0;$age120 = 0;$above120 = 0; $tbodyPdf="";
		$reportTitle = 'OUTSTANDING LEDGER';
		$report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));
		$rangeLength = (!empty($postData['days_range'])) ? count($postData['days_range']) : 0;
		$totalHeadCols = ($rangeLength > 0) ? ($rangeLength + 7) : 6;
		if($postData['report_type'] == 1)
		{
			$reportTitle = ($postData['os_type'] == 'R') ? 'RECEIVABLE SUMMARY REPORT' : 'PAYABLE SUMMARY REPORT';
			$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="'.$totalHeadCols.'">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
			$thead .= '<tr>
							<th>#</th>
							<th>Account Name</th>
							<th>City</th>
							<th>Contact Person</th>
							<th>Contact Number</th>
							<th class="text-right">Closing Balance</th>
						</tr>';
		}
		else
		{
			$reportTitle = ($postData['os_type'] == 'R') ? 'RECEIVABLE AGEWISE REPORT' : 'PAYABLE AGEWISE REPORT';
			$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="'.$totalHeadCols.'">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
			$thead .= '<tr>
							<th>#</th>
							<th>Account Name</th>
							<th>City</th>
							<th>Contact Person</th>
							<th>Contact Number</th>
							<th class="text-right">Closing Balance</th>';
		    $i=1;$dayCols = '';
		    if(!empty($postData['days_range']))
		    {
    		    foreach($postData['days_range'] as $days){
    		        
    		        if($i == 1){$dayCols .= '<th class="text-right">Below '.$days.'</th>';}
    		        if($i == $rangeLength){$dayCols .= '<th class="text-right">Above '.$days.'</th>';}
    		        if($i < $rangeLength){$dayCols .= '<th class="text-right">'.($days+1).' - '.$postData['days_range'][$i].'</th>';}
    		        $i++;
    		    }
		    }
		    $thead .= $dayCols;
		    $thead .= '</tr>';
		}
		foreach($outstandingData as $row):
			$ageGroup = '';
			if($postData['report_type'] == 2)
			{
			    if($rangeLength > 0)
			    {
    			    for($x=1;$x<=($rangeLength+1);$x++)
    			    {
    			        $fieldName = 'd'.$x; $daysTotal[$x-1] = 0; 
    			        $ageGroup .= '<td class="text-right">'.number_format($row->{$fieldName},2).'</td>';
    			        $daysTotal[$x-1] += $row->{$fieldName};
    			    }
			    }
			}
			$accountName = $row->account_name;
			if(empty($jsonData))
			{
				$accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . $row->id.'/'.$this->startYearDate.'/'.$this->endYearDate) . '" class="getAccountData" data-id="'.$row->id.'" target="_blank" datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
				
			}
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$accountName.'</td>
				<td>'.$row->city_name.'</td>
				<td>'.$row->contact_person.'</td>
				<td>'.$row->party_mobile.'</td>
				<td class="text-right">'.number_format($row->cl_balance,2).'</td>'.$ageGroup.'
			</tr>';

			$totalClBalance += $row->cl_balance;
			
		endforeach;
		
		if($postData['report_type'] == 1)
		{$tfoot = '<tr><th colspan="5" class="text-right">Total</th><th class="text-right">'.moneyFormatIndia($totalClBalance).'</th></tr>';}
		else
		{
			$tfoot = '<tr class="text-right"><th colspan="5" class="text-right">Total</th>';
			$tfoot .= '<th>'.number_format($totalClBalance,2).'</th>';
			foreach($daysTotal as $total){$tfoot .= '<th>'.number_format($total,2).'</th>';}
			$tfoot .= '</tr>';
		}
		
		if(!empty($jsonData))
		{
			$companyData = $this->salesInvoice->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);
			$letter_head = base_url('assets/images/letterhead_top.png');
			
			$pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
								<thead class="thead-info" id="theadData">'.$thead.'</thead>
								<tbody id="receivableData">'.$tbody.'</tbody>
								<tfoot class="thead-info tfoot">'.$tfoot.'</tfoot>
							</table>';
			$htmlHeader = '<img src="' . $letter_head . '">';
			$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
							<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
							<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
			
			$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/Outstanding.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
			$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetTitle($reportTitle);
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
            //$mpdf->SetProtection(array('print'));
    
    		$mpdf->AddPage('L','','','','',5,5,19,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else
		{
		    //echo '<table id="commanTable" class="table table-bordered">'.$thead.$tbody.$tfoot.'</table>';exit;
			$this->printJson(['status'=>1, 'tbody'=>$tbody,'thead'=>$thead,'tfoot'=>$tfoot,'totalClBalance'=>number_format($totalClBalance,2)]);
		}
    }
    public function bankBookReport(){
        $this->data['pageHeader'] = 'BANK BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->bank_book,$this->data);
    }
    public function getBankBook(){
        $data = $this->input->post();
        $bankBook = $this->accountingReport->getBankCashBook($data['from_date'],$data['to_date'],'"BA","BOL","BOA"');
        $i=1; $tbody="";
        foreach($bankBook as $row):
        $accountName = '<a href="javascript:void(0);" class="getAccountData" data-toggle="modal" data-target="#accountDetails" data-id="'.$row->id.'"  datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$accountName.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->op_balance.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>'; 
          
        endforeach;        
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    
    public function cashBookReport(){
        $this->data['pageHeader'] = 'CASH BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->cash_book,$this->data);
    }
    
    public function getCashBook(){
        $data = $this->input->post();
        $cashBook = $this->accountingReport->getBankCashBook($data['from_date'],$data['to_date'],'"CS"');
        $i=1; $tbody="";
        foreach($cashBook as $row):
        $accountName = '<a href="javascript:void(0);" class="getAccountData" data-toggle="modal" data-target="#accountDetails" data-id="'.$row->id.'"  datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$accountName.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->op_balance.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>';
        
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    
    public function accountLedgerReport(){
	    $this->data['headData']->pageTitle = "ACCOUNT LEDGER";
        $this->data['pageHeader'] = 'ACCOUNT LEDGER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->account_ledger,$this->data);
    }

    //Updated By Karmi @21/04/2022
    public function getAccountLedger($jsonData=""){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}   
        //print_r($postData); exit;

        $ledgerSummary = $this->accountingReport->getLedgerSummary($postData['from_date'],$postData['to_date']);
        $i=1; $tbody="";
        foreach($ledgerSummary as $row):
            if(empty($jsonData))			{
                $accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . $row->id.'/'.$postData['from_date'].'/'.$postData['to_date']) . '" class="getAccountData" data-id="'.$row->id.'" target="_blank" datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            }else{$accountName = $row->account_name;}
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$accountName.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->op_balance.'</td>
                <td>'.$row->cr_balance.'</td>
                <td>'.$row->dr_balance.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>';
        endforeach; 

        $reportTitle = 'ACCOUNT LEDGER';
        $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));   
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
        $thead .= '<tr>
                    <th>#</th>
                    <th>Account Name</th>
                    <th>Group Name</th>
                    <th>Opening Amount</th>
                    <th class="text-right">Credit Amount</th>
                    <th class="text-right">Debit Amount</th>
                    <th class="text-right">Closing Amount</th>
                </tr>';
        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        $letter_head = base_url('assets/images/letterhead_top.png');
        
        $pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="receivableData">'.$tbody.'</tbody>
                            
                        </table>';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                            <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                        </tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                        <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';
        if(!empty($postData['pdf']))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/AccountLedger.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,19,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else{$this->printJson(['status'=>1, 'tbody'=>$tbody]);}       
        
    }

    
    public function ledgerDetail($acc_id,$start_date,$end_date){
	    $this->data['headData']->pageTitle = "ACCOUNT LEDGER DETAIL";
        $this->data['pageHeader'] = 'ACCOUNT LEDGER DETAIL';
        $this->data['acc_id'] = $acc_id;
        $this->data['acc_name']=$this->party->getParty($acc_id)->party_name;
        $this->data['startDate'] = $start_date;
        $this->data['endDate'] = $end_date;
        $this->load->view($this->account_ledger_detail,$this->data);
    }
    //Updated By Karmi @21/04/2022
    public function getLedgerTransaction($jsonData=""){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();} 
        
        $ledgerTransactions = $this->accountingReport->getLedgerDetail($postData['from_date'],$postData['to_date'],$postData['acc_id']); //print_r($ledgerTransactions);exit;
        $ledgerBalance = $this->accountingReport->getLedgerBalance($postData['from_date'],$postData['to_date'],$postData['acc_id']);
        $ledgerBalance->cl_balance = abs($ledgerBalance->cl_balance);
        $ledgerBalance->dr_balance = abs($ledgerBalance->dr_balance);
        $i=1; $tbody="";
        foreach($ledgerTransactions as $row):
            if(empty($jsonData))
			{
                $paymentVoucher = '<button type="button" class="btn waves-effect waves-light btn-outline-primary float-center addVoucher " data-button="both" data-modal_id="modal-lg" data-id="'.$row->id.'" data-partyid="'.$postData['acc_id'].'" data-function="addPaymentVoucher" data-form_title="Add Payment ">Payment</button>';
            }
            else{$paymentVoucher = moneyFormatIndia($row->cr_amount - $row->dr_amount); }
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.date('d-m-Y',strtotime($row->trans_date)).'</td>
                <td>'.$row->account_name.'</td>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->trans_number.'</td>
                <td class="text-right">'.moneyFormatIndia($row->cr_amount).'</td>
                <td class="text-right">'.moneyFormatIndia($row->dr_amount).'</td>
                <td style="text-align: center;">'.$paymentVoucher.'</td>
            </tr>';
        endforeach;    
        
        $acc_name=$this->party->getParty($postData['acc_id'])->party_name;
        $reportTitle = $acc_name;
        $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));   
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Voucher Type</th>
                        <th>Ref.No.</th>
                        <th>Amount(CR.)</th>
                        <th>Amount(DR.)</th>
                        <th>Balance</th>
                    </tr>';
            $companyData = $this->salesInvoice->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);
			$letter_head = base_url('assets/images/letterhead_top.png');
			
			$pdfData = '<table id="commanTable" class="table table-bordered poTopTable" repeat_header="1">
								<thead class="thead-info" id="theadData">'.$thead.'</thead>
								<tbody id="receivableData">'.$tbody.'</tbody>
								
							</table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;" style="width:100%;">
                            <tr>
                                <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
                            </tr>
                        </table>
                        <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                            <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                        </table>
                        <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
                            <tr>
                                <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"> Closing Balance: '.$ledgerBalance->cl_balance.' '.$ledgerBalance->cl_balance_type.'</td>

                            </tr>
                        </table>';
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
							<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
        if(!empty($postData['pdf']))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/AccountLedgerDetail.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $stylesheet = file_get_contents(base_url('assets/css/style.css'));
            //$stylesheet = file_get_contents(base_url('assets/css/jp_helper.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else{$this->printJson(['status'=>1, 'tbody'=>$tbody,'ledgerBalance'=>$ledgerBalance]);}   
    }

    
    public function debitNoteRegisterReport(){
        $this->data['pageHeader'] = 'DEBIT NOTE REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->debit_note,$this->data);
    }

    public function creditNoteRegisterReport(){
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['pageHeader'] = 'CREDIT NOTE REGISTER';
        $this->load->view($this->credit_note,$this->data);
    }

    
    public function getDebitNote(){
        $data = $this->input->post();
        $debitNote = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],14);
        $i=1; $tbody="";
        foreach($debitNote as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->currency.'</td>
                <td>'.$row->net_amount.'</td>
                <td></td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function getCreditNote(){
        $data = $this->input->post();
        $creditNote = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],13);
        $i=1; $tbody="";
        foreach($creditNote as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->currency.'</td>
                <td>'.$row->net_amount.'</td>
                <td></td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
 
    public function gstr1Report(){
        $this->data['headData']->pageTitle = "GSTR 1 REPORT";
        $this->data['pageHeader'] = 'GSTR 1 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->gstr1_report, $this->data);
    }

    public function getGstr1ReportData($jsonData = ""){
        if (!empty($jsonData)) {
            $data = (array) decodeURL($jsonData);
        } else {
            $data = $this->input->post();
        }
        $data['entry_type']='6,7,8';
        $companyData = $this->accountingReport->getCompanyInfo();
        $salesReport = $this->accountingReport->getGstData($data);
        $i = 1;
        $tbody = '';
        $tfoot = '';
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($salesReport as $row) :
            $tbody .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfoot = "
        <tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";
        //$salesTable .= $tbody.'</tbody><tfoot id="footerData">'.$tfoot.'</tfoot></table>';
        $data['entry_type']='13';
        $salesReturnReport = $this->accountingReport->getGstData($data);
        $i = 1;
        $tbodyReturn = "";
        $tfootReturn = "";
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($salesReturnReport as $row) :
            $tbodyReturn .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfootReturn = "<tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";

        if (!empty($data['file_type']) && $data['file_type'] == 'EXCEL') {

            $tableHeaderS = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR1 - SALES</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableHeaderSR = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR1 - SALES RETURN</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableSubHeader = '
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="3">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Taxable Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Invoice Date</th>
										<th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
									</tr>';
            $salesTable = $tableHeaderS . $tableSubHeader . $tbody . $tfoot . '</table>';
            $salesReturnTable = $tableHeaderSR . $tableSubHeader . $tbodyReturn . $tfootReturn . '</table>';

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];

            // Sales Sheet
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->loadFromString($salesTable);
            $spreadsheet->getSheet(0)->setTitle('Sales');
            $salesSheet = $spreadsheet->getSheet(0);
            $sales_hcol = $salesSheet->getHighestColumn();
            $sales_hrow = $salesSheet->getHighestRow();
            $salesFullRange = 'A1:' . $sales_hcol . $sales_hrow;

            foreach (range('A', $sales_hcol) as $col) {
                $salesSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesSheet->getStyle('A1:' . $sales_hcol . '3')->applyFromArray($styleArray);
            $salesSheet->getStyle('A' . $sales_hrow . ':' . $sales_hcol . $sales_hrow)->applyFromArray($fontBold);
            $salesSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesSheet->getStyle('A' . $sales_hrow)->applyFromArray($alignRight);
            $salesSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesSheet->getStyle($salesFullRange)->applyFromArray($borderStyle);


            // Sales Return Sheet
            $reader->setSheetIndex(1);
            $salesReturnSheet = $spreadsheet->createSheet();
            $salesReturnSheet->setTitle('Sales Return');
            $spreadsheet = $reader->loadFromString($salesReturnTable, $spreadsheet);
            $salesreturn_hcol = $salesReturnSheet->getHighestColumn();
            $salesreturn_hrow = $salesReturnSheet->getHighestRow();
            $salesReturnFullRange = 'A1:' . $salesreturn_hcol . $salesreturn_hrow;

            foreach (range('A', $salesreturn_hcol) as $col) {
                $salesReturnSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesReturnSheet->getStyle('A1:' . $salesreturn_hcol . '3')->applyFromArray($styleArray);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow . ':' . $salesreturn_hcol . $salesreturn_hrow)->applyFromArray($fontBold);
            $salesReturnSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow)->applyFromArray($alignRight);
            $salesReturnSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesReturnSheet->getStyle($salesReturnFullRange)->applyFromArray($borderStyle);

            $fileDirectory = realpath(APPPATH . '../assets/uploads/');
            $fileName = '/GSTR1' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/') . $fileName);
        } else {

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tbodyReturn' => $tbodyReturn, 'tfoot' => $tfoot, 'tfootReturn' => $tfootReturn]);
        }
    }

    public function gstr2Report(){
        $this->data['headData']->pageTitle = "GSTR 2 REPORT";
        $this->data['pageHeader'] = 'GSTR 2 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getSupplierList();
        $this->load->view($this->gstr2_report, $this->data);
    }

    public function getGstr2ReportData($jsonData = ""){
        if (!empty($jsonData)) {
            $data = (array) decodeURL($jsonData);
        } else {
            $data = $this->input->post();
        }
        $companyData = $this->accountingReport->getCompanyInfo();
        $purchaseReport = $this->accountingReport->getAccountReportData($data['from_date'], $data['to_date'], '12',$data['party_id'],$data['state_code']);

        $i = 1;$tbody = '';$tfoot = '';$total_amount = 0;$taxable_amount = 0; $cgst = 0;$sgst = 0;$igst = 0;$cess = 0;$gst_amount = 0;

        foreach ($purchaseReport as $row) :
            $tbody .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . $row->doc_no . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfoot = "
        <tr>
            <th colspan='7' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";
        $purchaseReturnReport = $this->accountingReport->getAccountReportData($data['from_date'], $data['to_date'], '14',$data['party_id'],$data['state_code']);
        $i = 1;
        $tbodyReturn = "";$tfootReturn = "";
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($purchaseReturnReport as $row) :
            $tbodyReturn .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfootReturn = "<tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";
        if (!empty($data['file_type']) && $data['file_type'] == 'EXCEL') {

            $tableHeaderP = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR2 - PURCHASE</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="6">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableHeaderPR = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR2 - PURCHASE RETURN</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableSubHeaderP = '
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="4">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Taxable Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Original Invoice No.</th><th>Invoice Date</th>
										<th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
									</tr>';
            $tableSubHeaderPR = '
            <tr>
                <th rowspan="2">GSTIN</th>
                <th rowspan="2">Customer Name</th>
                <th colspan="2">Place Of supply</th>
                <th colspan="3">Invoice Detail</th>
                <th rowspan="2">Total Tax(%)</th>
                <th rowspan="2">Taxable Value</th>
                <th colspan="5">Amount Of Tax</th>
            </tr>
            <tr>
                <th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Invoice Date</th>
                <th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
            </tr>';
            $purchaseTable = str_replace('&','&amp;',$tableHeaderP . $tableSubHeaderP . $tbody . $tfoot . '</table>');
            $purchaseReturnTable = str_replace('&','&amp;',$tableHeaderPR . $tableSubHeaderPR . $tbodyReturn . $tfootReturn . '</table>');
            
            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];

            // Sales Sheet
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->loadFromString($purchaseTable);
            $spreadsheet->getSheet(0)->setTitle('Purchase');
            $salesSheet = $spreadsheet->getSheet(0);
            $sales_hcol = $salesSheet->getHighestColumn();
            $sales_hrow = $salesSheet->getHighestRow();
            $salesFullRange = 'A1:' . $sales_hcol . $sales_hrow;

            foreach (range('A', $sales_hcol) as $col) {
                $salesSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesSheet->getStyle('A1:' . $sales_hcol . '3')->applyFromArray($styleArray);
            $salesSheet->getStyle('A' . $sales_hrow . ':' . $sales_hcol . $sales_hrow)->applyFromArray($fontBold);
            $salesSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesSheet->getStyle('A' . $sales_hrow)->applyFromArray($alignRight);
            $salesSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesSheet->getStyle($salesFullRange)->applyFromArray($borderStyle);


            // Sales Return Sheet
            $reader->setSheetIndex(1);
            $salesReturnSheet = $spreadsheet->createSheet();
            $salesReturnSheet->setTitle('Purchase Return');
            $spreadsheet = $reader->loadFromString($purchaseReturnTable, $spreadsheet);
            $salesreturn_hcol = $salesReturnSheet->getHighestColumn();
            $salesreturn_hrow = $salesReturnSheet->getHighestRow();
            $salesReturnFullRange = 'A1:' . $salesreturn_hcol . $salesreturn_hrow;

            foreach (range('A', $salesreturn_hcol) as $col) {
                $salesReturnSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesReturnSheet->getStyle('A1:' . $salesreturn_hcol . '3')->applyFromArray($styleArray);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow . ':' . $salesreturn_hcol . $salesreturn_hrow)->applyFromArray($fontBold);
            $salesReturnSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow)->applyFromArray($alignRight);
            $salesReturnSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesReturnSheet->getStyle($salesReturnFullRange)->applyFromArray($borderStyle);

            $fileDirectory = realpath(APPPATH . '../assets/uploads/');
            $fileName = '/GSTR2' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/') . $fileName);
        } else {

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tbodyReturn' => $tbodyReturn, 'tfoot' => $tfoot, 'tfootReturn' => $tfootReturn]);
        }
    }
    

    public function profitAndLoss(){
        $this->data['pageHeader'] = 'Profit and Loss';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->profit_and_loss,$this->data);
    }

    public function getProfitAndLossData(){
        if(!empty($jsonData)):
            $postData = (Array) json_decode(urldecode(base64_decode($jsonData)));
        else:
            $postData = $this->input->post();
        endif;
        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];
        
        $data = ['from_date' => $from_date, "to_date" => $to_date, 'nature'=>"'Expenses','Income'", 'bs_type_code'=>"'T','P'", 'balance_type' => "lb.cl_balance > 0"];
        $productAmount = $this->accountingReport->_productOpeningAndClosingAmount($data);
        $incomeAccountDetails = $this->accountingReport->_accountWiseDetail($data);
        $data['balance_type'] = "lb.cl_balance < 0";
        $expenseAccountDetails = $this->accountingReport->_accountWiseDetail($data);

        $data['balance_type'] = "gs.cl_balance > 0";
        $incomeGroupSummary = $this->accountingReport->_groupWiseSummary($data);
        $data['balance_type'] = "gs.cl_balance < 0";
        $expenseGroupSummary = $this->accountingReport->_groupWiseSummary($data);

        $pnlData = $this->_generatePNL($productAmount,$expenseGroupSummary,$expenseAccountDetails,$incomeGroupSummary,$incomeAccountDetails,$is_consolidated);  
        
        $tbody = '';
        foreach($pnlData as $row):
            $particularL = (!empty($row["isHeadL"]))?"<b style='font-weight:700;'>".$row["particularL"]."</b>":"<span style='margin-left:10px;'>".$row["particularL"]."</span>";

            $amountLL = "";
            if(!empty($row['isHeadL'])):
                $amountLL = "<b style='font-weight:700;'>".((!empty($row['amountLL']))?number_format($row['amountLL'],2):"")."</b>";
            else:
                $amountLL = ((!empty($row['amountLL']))?number_format($row['amountLL'],2):"");
            endif;

            $amountLR = "";
            if(!empty($row['isHeadL'])):
                $amountLR = "<b style='font-weight:700;'>".((!empty($row['amountLR']))?number_format($row['amountLR'],2):((!empty($row['particularL']) && $row['isHeadL'])?"0.00":""))."</b>";
            else:
                $amountLR = ((!empty($row['amountLR']))?number_format($row['amountLR'],2):"");
            endif;

            $particularR = (!empty($row["isHeadR"]))?"<b style='font-weight:700;'>".$row["particularR"]."</b>":"<span style='margin-left:10px;'>".$row["particularR"]."</span>";

            $amountRL = "";
            if(!empty($row['isHeadR'])):
                $amountRL = "<b style='font-weight:700;'>".((!empty($row['amountRL']))?number_format($row['amountRL'],2):"")."</b>";
            else:
                $amountRL = ((!empty($row['amountRL']))?number_format($row['amountRL'],2):"");
            endif;

            $amountRR = "";
            if(!empty($row['isHeadR'])):
                $amountRR = "<b style='font-weight:700;'>".((!empty($row['amountRR']))?number_format($row['amountRR'],2):((!empty($row['particularR']) && $row['isHeadR'])?"0.00":""))."</b>";
            else:
                $amountRR = ((!empty($row['amountRR']))?number_format($row['amountRR'],2):"");
            endif;

            $tbody .= '<tr class="'.(($row['isTotal'] == 1)?"bg-light":"").'">
                <td>
                    '.$particularL.'
                </td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:110px;">'.$amountLL.'</td>';
            endif;
            $tbody .= '<td style="width:110px;">'.$amountLR.'</td>
                <td>'.$particularR.'</td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:110px;">'.$amountRL.'</td>';
            endif;
            $tbody .= '<td style="width:110px;">'.$amountRR.'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function _generatePNL($productAmount,$expenseGroupSummary,$expenseAccountDetails,$incomeGroupSummary,$incomeAccountDetails,$is_consolidated){
        $sideTL = array(); $sideTR = array(); $sidePL = array(); $sidePR = array();
        $openingStock = array_sum(array_column($productAmount,'op_amount'));
        $closingStock = array_sum(array_column($productAmount,'cl_amount'));

        if(!empty($openingStock)):
            $sideTL[] = ['perticular'=>"Opening Stock","amountL"=>"","amountR"=>$openingStock,"is_head"=>1];
            if($is_consolidated == 0):
                foreach($productAmount as $row):
                    $sideTL[] = ['perticular'=>$row->product_name,"amountL"=>$row->op_amount,"amountR"=>"","is_head"=>0];
                endforeach;
            endif;
        endif;

        $p=1;
        foreach($expenseGroupSummary as $row):
            if($row->bs_type_code == "T"):
                $sideTL[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($expenseAccountDetails,"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $sideTL[] = ['perticular'=>$expenseAccountDetails[$key]->name,"amountL"=>$expenseAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                    endforeach;  
                endif;  
            else:
                $sidePL[$p] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($expenseAccountDetails,"group_name"),$row->group_name);
                    $pp = $p+1;
                    foreach($accountDetailsKey as $k=>$key):
                        $sidePL[$pp] = ['perticular'=>$expenseAccountDetails[$key]->name,"amountL"=>$expenseAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                        $pp++;
                    endforeach;                        
                endif;
                $p++;  
            endif;
        endforeach;

        $p=1;
        foreach($incomeGroupSummary as $row):
            if($row->bs_type_code == "T"):
                if($row->group_name != "Stock-in-Hand (Clo.)"):                    
                    $sideTR[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                    if($is_consolidated == 0):
                        $accountDetailsKey = array_keys(array_column($incomeAccountDetails,"group_name"),$row->group_name);
                        foreach($accountDetailsKey as $k=>$key):
                            $sideTR[] = ['perticular'=>$incomeAccountDetails[$key]->name,"amountL"=>$incomeAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                        endforeach;
                    endif;  
                endif;  
            else:
                $sidePR[$p] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($incomeAccountDetails,"group_name"),$row->group_name);
                    $pp = $p+1;
                    foreach($accountDetailsKey as $k=>$key):
                        $sidePR[$pp] = ['perticular'=>$incomeAccountDetails[$key]->name,"amountL"=>$incomeAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                        $pp++;
                    endforeach;
                endif;
                $p++;
            endif;
        endforeach;

        if(!empty($closingStock)):
            $sideTR[] = ['perticular'=>"Stock-in-Hand (Clo.)","amountL"=>"","amountR"=>$closingStock,"is_head"=>1];
            if($is_consolidated == 0):
                foreach($productAmount as $row):
                    $sideTR[] = ['perticular'=>$row->product_name,"amountL"=>$row->cl_amount,"amountR"=>"","is_head"=>0];
                endforeach;
            endif;
        endif;

        $countTL = count($sideTL);
        $countTR = count($sideTR);

        $rowCounterT = ($countTL >= $countTR)?$countTL:$countTR;
        $profitLossData = array();
        $particularTL = "";$amountTLL="";$amountTLR="";$isHeadTL="";
        $particularTR = "";$amountTRL="";$amountTRR="";$isHeadTR="";
        $totalAmountTL = 0; $totalAmountTR = 0;

        for($i = 0; $i < $rowCounterT ; $i++):
            $particularTL = "";$amountTLL="";$amountTLR="";$isHeadTL="";
            if(isset($sideTL[$i])):
                $particularTL = $sideTL[$i]['perticular'];
                $amountTLL = $sideTL[$i]['amountL'];
                $amountTLR = $sideTL[$i]['amountR'];
                $isHeadTL = $sideTL[$i]['is_head'];
                $totalAmountTL += (!empty($sideTL[$i]['amountR']))?$sideTL[$i]['amountR']:0;
            endif;

            $particularTR = "";$amountTRL="";$amountTRR="";$isHeadTR="";
            if(isset($sideTR[$i])):
                $particularTR = $sideTR[$i]['perticular'];
                $amountTRL = $sideTR[$i]['amountL'];
                $amountTRR = $sideTR[$i]['amountR'];
                $isHeadTR = $sideTR[$i]['is_head'];
                $totalAmountTR += (!empty($sideTR[$i]['amountR']))?$sideTR[$i]['amountR']:0;
            endif;

            $profitLossData[] = ["particularL"=>$particularTL,'amountLL'=>$amountTLL,'amountLR'=>$amountTLR,'isHeadL'=>$isHeadTL,"particularR"=>$particularTR,'amountRL'=>$amountTRL,'amountRR'=>$amountTRR,'isHeadR'=>$isHeadTR,'isTotal'=>0];
        endfor;

        $cfAmount = 0;
        if($totalAmountTL > $totalAmountTR):
            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>0,"particularR"=>"Gross Loss c/o",'amountRL'=>"",'amountRR'=>abs($totalAmountTR - $totalAmountTL),'isHeadR'=>1,'isTotal'=>0];

            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>$totalAmountTL,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>$totalAmountTL,'isHeadR'=>1,'isTotal'=>1];

            $sidePL[0] = ['perticular'=>"Gross Loss b/f","amountL"=>"","amountR"=>abs($totalAmountTR - $totalAmountTL),"is_head"=>1];

            $cfAmount = $totalAmountTL;
        elseif($totalAmountTL < $totalAmountTR):
            $profitLossData[] = ["particularL"=>"Gross Profit c/f",'amountLL'=>"",'amountLR'=>abs($totalAmountTR - $totalAmountTL),'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>0,'isTotal'=>0];

            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>$totalAmountTR,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>$totalAmountTR,'isHeadR'=>1,'isTotal'=>1];

            $sidePR[0] = ['perticular'=>"Gross Profit b/f","amountL"=>"","amountR"=>abs($totalAmountTR - $totalAmountTL),"is_head"=>1];

            $cfAmount = $totalAmountTR;
        endif;

        $countPL = count($sidePL);
        $countPR = count($sidePR);

        $rowCounterP = ($countPL >= $countPR)?$countPL:$countPR;
        $particularPL = "";$amountPLL="";$amountPLR="";$isHeadPL="";
        $particularPR = "";$amountPRL="";$amountPRR="";$isHeadPR="";
        $totalAmountPL = 0; $totalAmountPR = 0;
        for($j = 0; $j < $rowCounterP ; $j++):
            $particularPL = "";$amountPLL="";$amountPLR="";$isHeadPL="";
            if(isset($sidePL[$j])):
                $particularPL = $sidePL[$j]['perticular'];
                $amountPLL = $sidePL[$j]['amountL'];
                $amountPLR = $sidePL[$j]['amountR'];
                $isHeadPL = $sidePL[$j]['is_head'];
                $totalAmountPL += (!empty($sidePL[$j]['amountR']))?$sidePL[$j]['amountR']:0;
            endif;

            $particularPR = "";$amountPRL="";$amountPRR="";$isHeadPR="";
            if(isset($sidePR[$j])):
                $particularPR = $sidePR[$j]['perticular'];
                $amountPRL = $sidePR[$j]['amountL'];
                $amountPRR = $sidePR[$j]['amountR'];
                $isHeadPR = $sidePR[$j]['is_head'];
                $totalAmountPR += (!empty($sidePR[$j]['amountR']))?$sidePR[$j]['amountR']:0;
            endif;

            $profitLossData[] = ["particularL"=>$particularPL,'amountLL'=>$amountPLL,'amountLR'=>$amountPLR,'isHeadL'=>$isHeadPL,"particularR"=>$particularPR,'amountRL'=>$amountPRL,'amountRR'=>$amountPRR,'isHeadR'=>$isHeadPR,'isTotal'=>0];
        endfor;

        if($totalAmountPL > $totalAmountPR):
            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>0,"particularR"=>"Net Loss",'amountRL'=>"",'amountRR'=>abs($totalAmountPL-$totalAmountPR),'isHeadR'=>1,'isTotal'=>0];  
            
            $profitLossData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountPL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountPL,'isHeadR'=>1,'isTotal'=>1];
        elseif($totalAmountPL < $totalAmountPR):
            $profitLossData[] = ["particularL"=>"Net Profit",'amountLL'=>"",'amountLR'=>abs($totalAmountPL - $totalAmountPR),'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>0,'isTotal'=>0];

            $profitLossData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountPR,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountPR,'isHeadR'=>1,'isTotal'=>1];
        endif;

        return $profitLossData;
    }

    public function balanceSheet(){
        $this->data['pageHeader'] = 'Balance Sheet';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->balance_sheet,$this->data);
    }

    public function getBalanceSheetData(){
        if(!empty($jsonData)):
            $postData = (Array) json_decode(urldecode(base64_decode($jsonData)));
        else:
            $postData = $this->input->post();
        endif;

        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];

        $data = ['from_date' => $from_date, "to_date" => $to_date, 'nature'=>"'Liabilities','Assets'", 'bs_type_code'=>"'B'", 'balance_type' => "lb.cl_balance > 0"];
        $productAmount = $this->accountingReport->_productOpeningAndClosingAmount($data);
        
        $liabilitiesAccountDetails = $this->accountingReport->_accountWiseDetail($data);
        $data['balance_type'] = "lb.cl_balance < 0";
        $assetsAccountDetails = $this->accountingReport->_accountWiseDetail($data);

        $data['balance_type'] = "gs.cl_balance > 0";
        $liabilitiesGroupSummary = $this->accountingReport->_groupWiseSummary($data);
        $data['balance_type'] = "gs.cl_balance < 0";
        $assetsGroupSummary = $this->accountingReport->_groupWiseSummary($data);

        $data['openingAmount'] = array_sum(array_column($productAmount,'op_amount'));
        $data['closingAmount'] = array_sum(array_column($productAmount,'cl_amount'));
        $data['extra_where'] = "gm.bs_type_code IN ('T','P')";
        $netPnlAmount = $this->accountingReport->_netPnlAmount($data);

        $balanceSheetData = $this->_generateBalanceSheet($productAmount,$liabilitiesGroupSummary,$liabilitiesAccountDetails,$assetsGroupSummary,$assetsAccountDetails,$netPnlAmount,$is_consolidated);

        $tbody = '';
        foreach($balanceSheetData as $row):
            $particularL = (!empty($row["isHeadL"]))?"<b style='font-weight:700;'>".$row["particularL"]."</b>":"<span style='margin-left:10px;'>".$row["particularL"]."</span>";

            $amountLL = "";
            if(!empty($row['isHeadL'])):
                $amountLL = "<b style='font-weight:700;'>".((!empty($row['amountLL']))?number_format($row['amountLL'],2):"")."</b>";
            else:
                $amountLL = ((!empty($row['amountLL']))?number_format($row['amountLL'],2):"");
            endif;

            $amountLR = "";
            if(!empty($row['isHeadL'])):
                $amountLR = "<b style='font-weight:700;'>".((!empty($row['amountLR']))?number_format($row['amountLR'],2):((!empty($row['particularL']) && $row['isHeadL'])?"0.00":""))."</b>";
            else:
                $amountLR = ((!empty($row['amountLR']))?number_format($row['amountLR'],2):"");
            endif;

            $particularR = (!empty($row["isHeadR"]))?"<b style='font-weight:700;'>".$row["particularR"]."</b>":"<span style='margin-left:10px;'>".$row["particularR"]."</span>";

            $amountRL = "";
            if(!empty($row['isHeadR'])):
                $amountRL = "<b style='font-weight:700;'>".((!empty($row['amountRL']))?number_format($row['amountRL'],2):"")."</b>";
            else:
                $amountRL = ((!empty($row['amountRL']))?number_format($row['amountRL'],2):"");
            endif;

            $amountRR = "";
            if(!empty($row['isHeadR'])):
                $amountRR = "<b style='font-weight:700;'>".((!empty($row['amountRR']))?number_format($row['amountRR'],2):((!empty($row['particularR']) && $row['isHeadR'])?"0.00":""))."</b>";
            else:
                $amountRR = ((!empty($row['amountRR']))?number_format($row['amountRR'],2):"");
            endif;

            $tbody .= '<tr class="'.(($row['isTotal'] == 1)?"bg-light":"").'">
                <td>
                    '.$particularL.'
                </td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:110px;">'.$amountLL.'</td>';
            endif;
            $tbody .= '<td style="width:110px;">'.$amountLR.'</td>
                <td>'.$particularR.'</td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:110px;">'.$amountRL.'</td>';
            endif;
            $tbody .= '<td style="width:110px;">'.$amountRR.'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function _generateBalanceSheet($productAmount,$liabilitiesGroupSummary,$liabilitiesAccountDetails,$assetsGroupSummary,$assetsAccountDetails,$netPnlAmount,$is_consolidated = 0){
        $balanceSheetData = array();
        $sideTL = array(); $sideTR = array(); $sidePL = array(); $sidePR = array();
        $openingStock = array_sum(array_column($productAmount,'op_amount'));
        $closingStock = array_sum(array_column($productAmount,'cl_amount'));

        $assetsData = array(); $liabilitiesData = array();
        $currentAssets = 0;
        foreach($liabilitiesGroupSummary as $row):
            if($row->group_name != "Profit & Loss A/c"):
                $liabilitiesData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($liabilitiesAccountDetails,"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $liabilitiesData[] = ['perticular'=>$liabilitiesAccountDetails[$key]->name,"amountL"=>$liabilitiesAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                    endforeach;
                endif; 
            endif;
        endforeach;

        foreach($assetsGroupSummary as $row):
            if($row->group_name != "Profit & Loss A/c"):
                if($row->group_name == "Current Assets"):
                    $currentAssets = 1;
                    $assetsData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance + $closingStock,"is_head"=>1];
                    if($is_consolidated == 0):
                        $assetsData[] = ['perticular'=>"Closing Stock","amountL"=>$closingStock,"amountR"=>"","is_head"=>0];
                    endif;
                else:
                    $assetsData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1];
                endif;
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($assetsAccountDetails,"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $assetsData[] = ['perticular'=>$assetsAccountDetails[$key]->name,"amountL"=>$assetsAccountDetails[$key]->cl_balance,"amountR"=>"","is_head"=>0];
                    endforeach;
                endif; 
            endif;
        endforeach;

        if($currentAssets == 0):
            if(!empty($closingStock)):
                $assetsData[] = ['perticular'=>"Current Assets","amountL"=>"","amountR"=>$closingStock,"is_head"=>1];
                if($is_consolidated == 0):
                    $assetsData[] = ['perticular'=>"Closing Stock","amountL"=>$closingStock,"amountR"=>"","is_head"=>0];
                endif;
            endif;
        endif;

        if(in_array("Profit & Loss A/c",array_column($assetsGroupSummary,'group_name'))):
            $key = array_search("Profit & Loss A/c",array_column($assetsGroupSummary,'group_name'));
            $netPnlAmount->net_pnl_amount = abs($netPnlAmount->net_pnl_amount) - abs($assetsGroupSummary[$key]->cl_balance);
        endif;

        if(in_array("Profit & Loss A/c",array_column($liabilitiesGroupSummary,'group_name'))):
            $key = array_search("Profit & Loss A/c",array_column($liabilitiesGroupSummary,'group_name'));
            $netPnlAmount->net_pnl_amount = abs($netPnlAmount->net_pnl_amount) - abs($liabilitiesGroupSummary[$key]->cl_balance);
        endif;

        $netPnlAmount->net_pnl_amount = round($netPnlAmount->net_pnl_amount,2);
        if($netPnlAmount->net_pnl_amount < 0):
            $assetsData[] = ['perticular'=>"Profit & Loss A/c","amountL"=>"","amountR"=>abs($netPnlAmount->net_pnl_amount),"is_head"=>1];
        elseif($netPnlAmount->net_pnl_amount > 0):
            $liabilitiesData[] = ['perticular'=>"Profit & Loss A/c","amountL"=>"","amountR"=>abs($netPnlAmount->net_pnl_amount),"is_head"=>1];
        endif;

        $countAssets = count($assetsData);
        $countLiablities = count($liabilitiesData);

        $rowCounter = ($countAssets >= $countLiablities)?$countAssets:$countLiablities;
        $particularL = "";$amountLL="";$amountLR="";$isHeadLL="";
        $particularA = "";$amountAL="";$amountAR="";$isHeadAR="";
        $totalAmountL = 0; $totalAmountA = 0;
        for($i = 0 ; $i < $rowCounter ; $i++):
            $particularL = "";$amountLL="";$amountLR="";$isHeadLL="";
            if(isset($liabilitiesData[$i])):
                $particularL = $liabilitiesData[$i]['perticular'];
                $amountLL = $liabilitiesData[$i]['amountL'];
                $amountLR = $liabilitiesData[$i]['amountR'];
                $isHeadLL = $liabilitiesData[$i]['is_head'];
                $totalAmountL += (!empty($liabilitiesData[$i]['amountR']))?$liabilitiesData[$i]['amountR']:0;
            endif;

            $particularA = "";$amountAL="";$amountAR="";$isHeadAR="";
            if(isset($assetsData[$i])):
                $particularA = $assetsData[$i]['perticular'];
                $amountAL = $assetsData[$i]['amountL'];
                $amountAR = $assetsData[$i]['amountR'];
                $isHeadAR = $assetsData[$i]['is_head'];
                $totalAmountA += (!empty($assetsData[$i]['amountR']))?$assetsData[$i]['amountR']:0;
            endif;

            $balanceSheetData[]  = ["particularL"=>$particularL,'amountLL'=>$amountLL,'amountLR'=>$amountLR,'isHeadL'=>$isHeadLL,"particularR"=>$particularA,'amountRL'=>$amountAL,'amountRR'=>$amountAR,'isHeadR'=>$isHeadAR,'isTotal'=>0];
        endfor;

        if($totalAmountL > $totalAmountA):   
            $balanceSheetData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>1,"particularR"=>"Difference In Balance Sheet",'amountRL'=>"",'amountRR'=>$totalAmountL - $totalAmountA,'isHeadR'=>1,'isTotal'=>0];
            
            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountL,'isHeadR'=>1,'isTotal'=>1];
        elseif($totalAmountL < $totalAmountA):
            $balanceSheetData[] = ["particularL"=>"Difference In Balance Sheet",'amountLL'=>"",'amountLR'=>$totalAmountA - $totalAmountL,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>1,'isTotal'=>0];

            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountA,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountA,'isHeadR'=>1,'isTotal'=>1];
        elseif($totalAmountL == $totalAmountA):
            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountA,'isHeadR'=>1,'isTotal'=>1];
        endif;

        return $balanceSheetData;
    }

    public function trailBalance(){
        $this->data['pageHeader'] = 'Trail Balance';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->trail_balance,$this->data);
    }

    public function getTrailBalanceData(){
        if(!empty($jsonData)):
            $postData = (Array) json_decode(urldecode(base64_decode($jsonData)));
        else:
            $postData = $this->input->post();
        endif;

        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];

        $data = ['from_date'=>$from_date,'to_date'=>$to_date];
        $productAmount = $this->accountingReport->_productOpeningAndClosingAmount($data);
        $accountSummary = $this->accountingReport->_trailAccountSummary($data);

        $group_ids = implode(",",array_column($accountSummary,'group_id'));
        $data['extra_where'] = "gs.cl_balance <> 0
        AND gm.id IN (".$group_ids.")";

        $subGroupSummary = $this->accountingReport->_trailSubGroupSummary($data);
        $mainGroupSummary = $this->accountingReport->_trailMainGroupSummary($data);

        $trailBalance = $this->_generateTrailBalance($productAmount,$accountSummary,$subGroupSummary,$mainGroupSummary,$is_consolidated);

        $tbody = '';
        foreach($trailBalance as $row):
            $particular = "";
            if($row['is_main'] == 1):
                $particular = "<b style='font-weight:700;'>".$row["particular"]."</b>";
            elseif($row['is_sub'] == 1):
                $particular = "<b style='font-weight:600; margin-left:10px;'>".$row["particular"]."</b>";
            else:
                $particular = "<span style='margin-left:20px;'>".$row["particular"]."</span>";
            endif;

            $cl_balance = "";
            if($row['is_main'] == 1):
                /* if(!empty($row["cl_balance"])):
                    $cl_balance = "<b style='font-weight:700;'>".(($row["cl_balance"] > 0)?number_format($row["cl_balance"],2)." Cr.":number_format(abs($row["cl_balance"]),2)." Dr.")."</b>";
                endif; */
            elseif($row['is_sub'] == 1):
                $cl_balance = "<b style='font-weight:600;'>".(($row["cl_balance"] > 0)?number_format($row["cl_balance"],2)." Cr.":number_format(abs($row["cl_balance"]),2)." Dr.")."</b>";
            else:
                $cl_balance = (($row["cl_balance"] > 0)?number_format($row["cl_balance"],2)." Cr.":number_format(abs($row["cl_balance"]),2)." Dr.");
            endif;

            $cr_amount = "";
            $dr_amount = "";
            if($row['is_main'] == 1):
                $cr_amount = "<b style='font-weight:700;'>".((!empty($row['credit_amount']))?number_format($row['credit_amount'],2):"")."</b>";
                $dr_amount = "<b style='font-weight:700;'>".((!empty($row['debit_amount']))?number_format($row['debit_amount'],2):"")."</b>";
            endif;

            $tbody .= '<tr class="'.(($row['is_total'] == 1)?"bg-light":"").'">
                <td class="text-left">
                    '.$particular.'
                </td>
                <td class="text-center" style="width:140px;">'.$cl_balance.'</td>
                <td class="text-center" style="width:140px;">'.$dr_amount.'</td>
                <td class="text-center" style="width:140px;">'.$cr_amount.'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function _generateTrailBalance($productAmount,$accountSummary,$subGroupSummary,$mainGroupSummary,$is_consolidated = 0){
        $openingStock = array_sum(array_column($productAmount,'op_amount'));
        
        $dataRow = array();$total_debit_amount = 0; $total_credit_amount = 0;
        foreach($mainGroupSummary as $row):
            if($row->group_name == "Current Assets"):
                if($openingStock > 0):
                    $row->debit_amount = $row->debit_amount + $openingStock;
                    $row->cl_balance = $row->credit_amount - $row->debit_amount;
                endif;
            endif;
            $dataRow[] = ['particular' => $row->group_name, 'debit_amount' => (!empty($row->debit_amount)?$row->debit_amount:0), 'credit_amount' => (!empty($row->credit_amount)?$row->credit_amount:0), 'cl_balance' => (!empty($row->cl_balance)?$row->cl_balance:0), 'is_main' => 1, 'is_sub' => 0,'is_total'=>($is_consolidated == 0)?1:0];

            if($is_consolidated == 0):
                if($row->group_name == "Current Assets"):
                    if($openingStock > 0):
                        $dataRow[] = ['particular' => "Opening Stock", 'debit_amount' => $openingStock, 'credit_amount' => 0, 'cl_balance' => $openingStock, 'is_main' => 0, 'is_sub' => 1,'is_total'=>0];
                    endif;
                endif;
                
                $subGroupKey = array();
                $subGroupKey = array_keys(array_column($subGroupSummary,"bs_id"),$row->id);                
                foreach($subGroupKey as $k=>$key):
                    $dataRow[] = ['particular' => $subGroupSummary[$key]->group_name, 'debit_amount' => (!empty($subGroupSummary[$key]->debit_amount)?$subGroupSummary[$key]->debit_amount:0), 'credit_amount' => (!empty($subGroupSummary[$key]->credit_amount)?$subGroupSummary[$key]->credit_amount:0), 'cl_balance' => (!empty($subGroupSummary[$key]->cl_balance)?$subGroupSummary[$key]->cl_balance:0), 'is_main' => 0, 'is_sub' => 1,'is_total'=>0];

                    $accountKey = array();
                    $accountKey = array_keys(array_column($accountSummary,"group_id"),$subGroupSummary[$key]->id);
                    foreach($accountKey as $ak=>$acc_key):
                        $dataRow[] = ['particular' => $accountSummary[$acc_key]->name, 'debit_amount' => (!empty($accountSummary[$acc_key]->debit_amount)?$accountSummary[$acc_key]->debit_amount:0), 'credit_amount' => (!empty($accountSummary[$acc_key]->credit_amount)?$accountSummary[$acc_key]->credit_amount:0), 'cl_balance' => (!empty($accountSummary[$acc_key]->cl_balance)?$accountSummary[$acc_key]->cl_balance:0), 'is_main' => 0, 'is_sub' => 0,'is_total'=>0];
                    endforeach;
                endforeach;                
            endif; 
            $total_debit_amount += $row->debit_amount;
            $total_credit_amount += $row->credit_amount;
        endforeach;

        $totalAmount = 0;
        if($total_debit_amount > $total_credit_amount):
            $totalAmount = $total_debit_amount;
            $dataRow[] = ['particular' => "Difference In Trail Balance", 'debit_amount' => 0, 'credit_amount' => ($total_debit_amount - $total_credit_amount), 'cl_balance' => 0,'is_main' => 1, 'is_sub' => 0,'is_total'=>0];
        elseif($total_debit_amount < $total_credit_amount):
            $totalAmount = $total_credit_amount;
            $dataRow[] = ['particular' => "Difference In Trail Balance", 'debit_amount' => ($total_credit_amount - $total_debit_amount), 'credit_amount' => 0, 'cl_balance' => 0, 'is_main' => 1, 'is_sub' => 0,'is_total'=>0];
        else:
            $totalAmount = $total_debit_amount;
        endif;

        $dataRow[] = ['particular' => "Total", 'debit_amount' => $totalAmount, 'credit_amount' => $totalAmount, 'cl_balance' => 0, 'is_main' => 1, 'is_sub' => 0,'is_total'=>1];

        return $dataRow;
    }
    
        //UPDATED BY MEGHAVI 15-03-2022
    public function stockRegisterReport(){
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->stock_register,$this->data);
    }
    
    //UPDATE BY JP @19-04-2022
    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $stockType = '';
            if($data['stock_type'] == '0'){$stockType = 'stockQty = 0';}
            if($data['stock_type'] == 1){$stockType = 'stockQty > 0';}
            $itemData = $this->accountingReport->getStockRegister($data,$stockType);
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;$totalAmt=0; $treceiptQty = 0; $tissuedQty = 0; $tbalanceQty = 0;
            
            if(!empty($itemData)):
                foreach($itemData as $row):  
                    $data['item_id'] = $row->id;
                    $bQty = 0;
                    $receiptQty = $row->rqty; 
                    $issuedQty = $row->iqty;
                    $itmStock = $row->stockQty;
                    if(!empty($row->stockQty)){$bQty = $row->stockQty;}
                    
                    $balanceQty=0;
                    if($row->item_type == 1){ $balanceQty = round($bQty,3); } 
                    else { $balanceQty = round($receiptQty - abs($issuedQty),3); } 
                    
                    //$balanceQty = round($bQty,3);
                    
                    $price = (!empty($row->inrrate))? ($row->price * $row->inrrate) : $row->price;
                    $tamt = ($balanceQty > 0)? round($balanceQty * $price, 2) : 0;
                    
                    $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.(($row->item_type == 1)?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'</td>
                                <td class="text-right">'.floatVal($receiptQty).'</td>
                                <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                                <td class="text-right">'.floatVal($balanceQty).'</td>
                                <td class="text-right">'.number_format($tamt,2).'</td>
                            </tr>';
                    $totalAmt += $tamt;
                    $treceiptQty += $receiptQty; 
                    $tissuedQty += $issuedQty;
                    $tbalanceQty += $balanceQty;
                endforeach;
                $thead .= '<tr>
                            <th class="text-center" colspan="5">Stock Register</th>
                            <th class="text-right">'.number_format($totalAmt,2).'</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th class="text-right">Receipt Qty.</th>
                            <th class="text-right">Issued Qty.</th>
                            <th class="text-right">Balance Qty.</th>
                            <th class="text-right">Amount</th>
                        </tr>';
                $tfoot = '<tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">' .number_format($treceiptQty,2). '</th>
                        <th class="text-right">' .number_format(abs($tissuedQty),2). '</th>
                        <th class="text-right">' .number_format($tbalanceQty,2). '</th>
                        <th class="text-right">' .number_format($totalAmt,2). '</th>
                    </tr>';            
            else:
                //$tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
                $thead .= '<tr class="text-center">
                            <th colspan="7">Stock Register</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Receipt Qty.</th>
                            <th>Issued Qty.</th>
                            <th>Balance Qty.</th>
                            <th>Amount</th>
                        </tr>';
                $tfoot = '<tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                    </tr>';     
            endif;
            $this->printJson(['status'=>1, 'thead'=>$thead ,'tbody'=>$tbody,'tfoot'=>$tfoot]);
        endif;
    }
    
    public function fundManagement(){
        $this->data['pageHeader'] = 'FUND MANAGEMENT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->fund_management,$this->data);
    }

    public function getFundManagementData(){
        $postData = $this->input->post();
        $balance = $this->accountingReport->getBankCashBook($postData['from_date'],$postData['from_date'],'"BA","BOL","BOA","CS"');
        $totalOpBalance = array_sum(array_column($balance,'cl_balance'));
        $accountSummary = $this->accountingReport->getFundManagementData($postData);
        
        $tbody = '';$cl_amount = $totalOpBalance;$cr_balance = $dr_balance = 0;
        foreach($accountSummary as $row):
            $cr_amount = ($row->due_amount > 0)?round($row->due_amount,2):0;
            $dr_amount = ($row->due_amount < 0)?round(abs($row->due_amount),2):0;
            $cl_amount += $row->due_amount;$cr_balance += $cr_amount;$dr_balance += $dr_amount;
            $tbody .= '<tr>
                <td>'.$row->due_date.'</td>
                <td class="text-left">'.$row->party_name.'</td>
                <td class="text-right">'.moneyFormatIndia($cr_amount).'</td>
                <td class="text-right">'.moneyFormatIndia($dr_amount).'</td>
                <td class="text-right">'.moneyFormatIndia(abs($cl_amount)).' '.(($cl_amount > 0)?"CR.":"DR.").'</td>
            </tr>';
        endforeach;

        $totalOpBalance = (!empty($totalOpBalance))?(($totalOpBalance > 0)?moneyFormatIndia($totalOpBalance)." CR.":abs($totalOpBalance)." DR."):0;
        $cl_amount = (!empty($cl_amount))?(($cl_amount > 0)?moneyFormatIndia($cl_amount)." CR.":abs($cl_amount)." DR."):0;
        $this->printJson(['status'=>1,'tbody'=>$tbody,'op_balance'=>$totalOpBalance,'cr_balance'=>moneyFormatIndia($cr_balance),'dr_balance'=>moneyFormatIndia($dr_balance),'cl_balance'=>$cl_amount]);
    }
}
