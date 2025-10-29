$(document).ready(function(){
	isoTable();
});
function loadIsoDocuments()
{
    var from_date = "";
    var to_date = "";
    $.ajax({
        url:base_url + controller + '/loadIsoDocuments',
        type:'post',
        data:{from_date:from_date,to_date:to_date},
        dataType:'json',
        success:function(data){
            $("#tbodyData").html(data.tbody);
            isoTable();
        }
    });
}

function isoTable()
{
	var isoTable = $('#isoTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', {extend: 'excelHtml5', title: 'D QMS 01',exportOptions: {columns: "thead th:not(.noExport)"}}]
	});
	isoTable.buttons().container().appendTo( '#isoTable_wrapper toolbar' );
	$('#isoTable_filter .form-control-sm').css("width","97%");
	$('#isoTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#isoTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return isoTable;
}