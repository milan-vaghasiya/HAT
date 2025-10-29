<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader">LIST OF CUSTOMERS</h4>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='custTable' class="table table-bordered">
                          
								<thead class="thead-info" id="theadData">
                                <tr class="text-center">
                                        <th colspan="3">LIST OF CUSTOMERS</th>
                                        <th>D/MKT/01 (00/01.01.16)</th>
                                </tr>
									<tr>
										<th>Sr. No.</th>
										<th>Customer's Name & Address</th>
										<th>Contact Details</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
                                    <?php
                                        if(!empty($customerData))
                                        {   
                                            $i=1;
                                            foreach($customerData as $row)
                                            {
                                    ?>
                                                <tr>
                                                    <td>
                                                        <?=$i?>
                                                    </td>
                                                    <td>
                                                        <?=$row->party_name?><br>
                                                        <?=$row->party_address?>
                                                    </td>
                                                    <td class="text-left">
                                                        <b>Person : </b><?=$row->contact_person?><br>
                                                        <b>Phone : </b><?=$row->party_phone?><br>
                                                        <b>Mobile : </b><?=$row->party_mobile?>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                    <?php
                                                $i++;
                                            }
                                        }
                                    ?>						
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable(); 

    $(document).on('click','.loadPdf',function(e){
		$(".error").html("");
		var valid = 1;
		if(valid)
		{
        	window.open(base_url + controller + '/printListOfCustomers').focus();
        }
    });

});
function reportTable()
{
	var reportTable = $('#custTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',data:0,action: function ( e, dt, node, config ) {location.reload();}}]
	});
	var printBtn = '<button class="btn btn-outline-primary loadPdf" type="button"><span>PDF</span></button>';
    reportTable.buttons().container().append(printBtn);
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>