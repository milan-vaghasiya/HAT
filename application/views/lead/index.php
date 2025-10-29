<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title">Leads Management
								<a href="#" class="reloadLeads text-primary" style="font-size:0px;"><i class="fas fa-sync"></i></a></h4>
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn waves-effect waves-light btn-primary float-right loadForm permission-write" data-button="both" data-modal_id="modal-xl" data-function="addLead" data-form_title="Add Lead"><i class="fa fa-plus"></i> New Lead</button>
								<a href="<?=base_url("salesEnquiry/addEnquiry")?>" class="btn waves-effect waves-light btn-success float-right m-r-10 permission-write"><i class="fa fa-plus"></i> New Enquiry</a>
                            </div>                             
                        </div>                                 
                    </div>
                    <div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="card">
									<div class="card-header bg-panel1 text-white headerSearch">
										<h4 class="card-title">Lead Inititated</h4>
										<div class="jpsearch" id="qs1">
											<input type="text" class="input quicksearch qs1" placeholder="Search Here ..." />
											<button class="search-btn"><i class="fas fa-search"></i></button>
										</div>
									</div>
									<div class="lead-widget scrollable" style="height:400px;">
										<div class="leadGrid grid1 panel1" data-isotope='{ "itemSelector": ".lead-row" }'>
											
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="card">
									<div class="card-header bg-success text-white headerSearch">
										<h4 class="card-title">Qualified Lead</h4>
										<div class="jpsearch" id="qs2">
											<input type="text" class="input quicksearch qs2" placeholder="Search Here ..." />
											<button class="search-btn"><i class="fas fa-search"></i></button>
										</div>
									</div>
									<div class="lead-widget scrollable" style="height:400px;">
										<div class="qulaifiedLead grid2" data-isotope='{ "itemSelector": ".lead-row" }'></div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="card">
									<div class="card-header bg-primary text-white headerSearch">
										<h4 class="card-title">Inquiries</h4>
										<div class="jpsearch" id="qs3">
											<input type="text" class="input quicksearch qs3" placeholder="Search Here ..." />
											<button class="search-btn"><i class="fas fa-search"></i></button>
										</div>
									</div>
									<div class="lead-widget scrollable" style="height:400px;">
										<div class="leadInquiry grid3" data-isotope='{ "itemSelector": ".lead-row" }'></div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="card">
									<div class="card-header bg-warning text-white headerSearch">
										<h4 class="card-title">Sales Quotation</h4>
										<div class="jpsearch" id="qs4">
											<input type="text" class="input quicksearch qs4" placeholder="Search Here ..." />
											<button class="search-btn"><i class="fas fa-search"></i></button>
										</div>
									</div>
									<div class="lead-widget scrollable" style="height:400px;">
										<div class="salesQuotations grid4" data-isotope='{ "itemSelector": ".lead-row" }'></div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/extra-libs/isotop/isotope.pkgd.min.js"></script>
<script src="<?=base_url()?>assets/js/custom/sales-enquiry-form.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/js/custom/sales-quotation.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
	var qsRegex;
	var isoOptions = {
		itemSelector: '.lead-row',
		layoutMode: 'fitRows',
		filter: function() {return qsRegex ? $(this).text().match( qsRegex ) : true;}
	};
	// init isotope
	var $grid1 = $('.grid1').isotope( isoOptions );
	var $qs1 = $('.qs1').keyup( debounce( function() {qsRegex = new RegExp( $qs1.val(), 'gi' );$grid1.isotope();}, 200 ) );
	
	var $grid2 = $('.grid2').isotope( isoOptions );
	var $qs2 = $('.qs2').keyup( debounce( function() {qsRegex = new RegExp( $qs2.val(), 'gi' );$grid2.isotope();}, 200 ) );
	
	var $grid3 = $('.grid3').isotope( isoOptions );
	var $qs3 = $('.qs3').keyup( debounce( function() {qsRegex = new RegExp( $qs3.val(), 'gi' );$grid3.isotope();}, 200 ) );
	
	var $grid4 = $('.grid4').isotope( isoOptions );
	var $qs4 = $('.qs4').keyup( debounce( function() {qsRegex = new RegExp( $qs4.val(), 'gi' );$grid4.isotope();}, 200 ) );
	
	setTimeout(function(){$('.reloadLeads').trigger('click');},100);
	
	$(document).on('click',".search-btn",function(){
		var id = '#' + $(this).parent().attr('id');
		$(id + ' .quicksearch').val('');
		if($(id + ' .search-btn i').hasClass('fa-search'))
		{
			$(id).css('width','98%');$(id + ' .search-btn').css('right','1.5%');
			$(id + " .quicksearch").css('width','100%');
			$(id + ' .search-btn i').removeClass('fa-search');$(id + ' .search-btn i').addClass('fa-times');
		}
		else
		{
			$(id).css('width','auto');$(id + ' .search-btn').css('right','16%');
			$(id + " .quicksearch").css('width','38px');
			$(id + ' .search-btn i').removeClass('fa-times');$(id + ' .search-btn i').addClass('fa-search');
		}
		$(id + " .quicksearch").focus();
	});
	
	
	$(document).on('click',".reloadLeads",function(){
		$.ajax({
			url: base_url + controller + '/getLeadData',
			type:'post',
			data:{id:""},
			dataType:'json',
			success:function(data){
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					//alert(data.leadData);
					$grid1.isotope('destroy');$grid2.isotope('destroy');$grid3.isotope('destroy');$grid4.isotope('destroy');
					$(".leadGrid").html('');$(".leadGrid").html(data.leadData);                 
					$(".qulaifiedLead").html('');$(".qulaifiedLead").html(data.qualifiedLeads);                 
					$(".leadInquiry").html('');$(".leadInquiry").html(data.leadInquiry);                 
					$(".salesQuotations").html('');$(".salesQuotations").html(data.salesQuotation);                 
					$grid1 = $('.grid1').isotope( isoOptions );	
					$grid2 = $('.grid2').isotope( isoOptions );	
					$grid3 = $('.grid3').isotope( isoOptions );	
					$grid4 = $('.grid4').isotope( isoOptions );	
				}
			}
		});
	});
	
	$(document).on('click',".loadForm",function(){
        var functionName = $(this).data("function");
        var fnSave = $(this).data("fnSave");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;
		if(fnSave == "" || fnSave == null){fnSave="save";}
        $.ajax({ 
            type: "GET",   
            url: base_url + controller + '/' + functionName,   
            data: {}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeLead('"+formId+"','"+fnSave+"');");
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			initMultiSelect();setPlaceHolder();
        });
    });	
	$(document).on('click','.leadAction',function(){
		var lead_id = $(this).data("id");
		var functionName = $(this).data("function");
		var fnSave = $(this).data("fnsave");
		var modalId = $(this).data('modal_id');
		var title = $(this).data('form_title');
		var formId = functionName;
		if(fnSave == "" || fnSave == null){fnSave="save";}
		$.ajax({ 
			type: "POST",   
			url: base_url + controller + '/' + functionName,   
			data: {lead_id:lead_id}
		}).done(function(response){
			$("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#lead_id").val(lead_id);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeLead('"+formId+"','"+fnSave+"');");
			$("#"+modalId+" .modal-footer .btn-close").show();
			$("#"+modalId+" .modal-footer .btn-save").show();
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			initMultiSelect();setPlaceHolder();
		});	
	});
	
	$(document).on('click','.leadActionStatic',function(){
	
		var send_data = { id:$(this).data('id'),lead_status:$(this).data('lead_status') };
		var fnSave = $(this).data('fnsave');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+$(this).data('action_name')+ ' this Lead?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/' + fnSave,
							data: send_data,
							type: "POST",
							dataType:"json",
							success:function(data)
							{
								if(data.status==0)
								{
									toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
								else
								{
									if(fnSave!='delete'){$(".modal").modal('hide');}$('.reloadLeads').trigger('click');
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
							}
						});
					}
				},
				cancel: {  btnClass: 'btn waves-effect waves-light btn-outline-secondary',action: function(){} }
			}
		});
	});
});
function debounce( fn, threshold ) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
	clearTimeout( timeout );
	var args = arguments;
	var _this = this;
	function delayed() {fn.apply( _this, args );}
	timeout = setTimeout( delayed, threshold );
  };
}

function editLead(data){
	var button = "";
	var fnEdit = $(this).data("fnEdit");if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnSave = $(this).data("fnSave");if(fnSave == "" || fnSave == null){fnSave="save";}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"storeLead('"+data.form_id+"','"+fnSave+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function storeLead(formId,fnSave){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/' + fnSave,
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
			$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center' });
			$('.reloadLeads').trigger('click');
		}else{
			$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center' });
		}
				
	});
}

function trashLead(id,fnSave='delete',name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/' + fnSave,
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								if(fnSave!='delete'){$(".modal").modal('hide');}$('.reloadLeads').trigger('click');
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {  btnClass: 'btn waves-effect waves-light btn-outline-secondary',action: function(){} }
		}
	});
}


</script>