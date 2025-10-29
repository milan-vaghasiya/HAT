<?php $this->load->view('includes/header'); ?>
<form autocomplete="off" id="saveAssignShift">
    <div class="page-wrapper">
        <div class="container-fluid bg-container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="card-title">Assign Shift</h4>
                                </div>
                                <div class="col-md-3">
                                    <select name="dept_id" id="dept_id" class="form-control single-select float-right">
                                        <option value="">Select Department</option> 
                                        <option value="0">Select All</option> 
                                        <?php
                                            foreach($deptData as $row):
                                                echo '<option value="'.$row->id.'">'.$row->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error dept_id"></div>
                                </div>
                                <div class="col-md-2">
                                    <select name="category_id" id="category_id" class="form-control single-select float-right">
                                        <option value="">Select Category</option> 
                                        <option value="0">Select All</option> 
                                        <?php
                                            foreach($categoryData as $row):
                                                echo '<option value="'.$row->id.'">'.$row->category.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error category_id"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="shiftId" id="shiftId" class="form-control single-select" style="max-width: 70%;">
                                            <option value="">Select Shift</option> 
                                            <?php
                                                foreach($shiftData as $row):
                                                    echo '<option value="'.$row->id.'">'.$row->shift_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn waves-effect waves-light btn-success loaddata" title="Load Data">
                                                <i class="fas fa-sync-alt"></i> Load
                                            </button>
                                        </div>
                                        <div class="error shiftId"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="assignTable" class="table table-bordered jdt">
                                    <thead class="thead-info" id="theadData">
										<tr class="clonTR">
                                            <th>#</th>
                                            <th>Employee Name</th>
                                            <th>Emp. Code</th>
                                            <th>Contact No.</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                        <!--<div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right" onclick="saveAssignShift('saveAssignShift');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="saveAssignShift('saveAssignShift');">SAVE SHIFT</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	assignTable();
	$('.jdt thead tr').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(0) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		if($(this).index()!=0){$(this).html( '<input type="text" class="searchInput" style="width:100%;"/>' );}else{$(this).html('');}
	});
    $(document).on('click','.loaddata',function(e){
        $(".error").html("");
		var valid = 1;
        var shiftId = $('#shiftId').val();
        var dept_id = $('#dept_id').val();
        var category_id = $('#category_id').val();

		if($("#shiftId").val() == ""){$(".shiftId").html("Shift is required.");valid=0;}
		if($("#dept_id").val() == ""){$(".dept_id").html("Department is required.");valid=0;}
		if($("#category_id").val() == ""){$(".category_id").html("Category is required.");valid=0;}
        if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getAssignShift',
                data: {shiftId:shiftId,dept_id:dept_id,category_id:category_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    if(data.status===0){
                        $(".error").html("");
                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                    } else {
						$("#assignTable").dataTable().fnDestroy();
                        $("#tbodyData").html(data.tbodyData);
						assignTable();
                    }
                }
            });
        }
    });
});

function saveAssignShift(formId){
	
	$('.searchInput').each( function (i) {$(this).val(""); $(this).trigger("change");});
	
	setTimeout(function(){
		var fd = $('#'+formId).serialize();
		$.ajax({
			url: base_url + controller + '/saveAssignShift',
			data:fd,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {
					$("."+key).html(value);
				});
			}else if(data.status==1){
				// $('.jdt').dataTable().fnDestroy();assignTable();
				$('.loaddata').trigger('click');
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				$('#shift_id').val(data.shift_id);  $('#shift_id').comboSelect();         
			}else{
				// $("#assignTable").dataTable().fnDestroy();assignTable();
				$('.loaddata').trigger('click');
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}				
		});
	}, 50);
}
//Datatable with column filter for jpTable Class
function assignTable()
{
	var assignTable = $('#assignTable').DataTable( 
	{
		responsive: true,
        "paging":   false,
		//'stateSave':true,
		"autoWidth" : false,
		// order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							// { orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		// pageLength:25,
		language: { search: "" },
		// lengthMenu: [
        //     [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        // ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['excel','colvis']
	});
	assignTable.buttons().container().appendTo( '#assignTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	
	//Datatable Column Filter
    $('.jdt thead tr:eq(0) th').each( function (i) {
		if($(this).index()!=0)
		{
			$( 'input', this ).on( 'keyup change', function () {
				if ( assignTable.column(i).search() !== this.value ) {assignTable.column(i).search( this.value ).draw();}
			});
		}else{$(this).html('');}
	} );
}
</script>