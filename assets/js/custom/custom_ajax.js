// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//
$.fn.dataTable.pipeline = function ( opts ) {
	// Configuration options
	var conf = $.extend( { pages: 5, url: '', data: null, method: 'POST' }, opts );
	
	// Private variables for storing the cache
	var cacheLower = -1;
	var cacheUpper = null;
	var cacheLastRequest = null;
	var cacheLastJson = null;
 
	return function ( request, drawCallback, settings ) {
		var ajax          = false;
		var requestStart  = request.start;
		var drawStart     = request.start;
		var requestLength = request.length;
		var requestEnd    = requestStart + requestLength;
		 
		if ( settings.clearCache ) { ajax = true; settings.clearCache = false; }
		else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) { ajax = true; }
		
		else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
				  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
				  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
		) { ajax = true; }
		
		cacheLastRequest = $.extend( true, {}, request );
 
		if ( ajax ) {
			if ( requestStart < cacheLower ) {
				requestStart = requestStart - (requestLength*(conf.pages-1)); 
				if ( requestStart < 0 ) {requestStart = 0;}
			}
			 
			cacheLower = requestStart;
			cacheUpper = requestStart + (requestLength * conf.pages);
 
			request.start = requestStart;
			request.length = requestLength*conf.pages;
 
			// Provide the same `data` options as DataTables.
			if ( typeof conf.data === 'function' ) { var d = conf.data( request ); if ( d ) {$.extend( request, d );} }
			else if ( $.isPlainObject( conf.data ) ) { $.extend( request, conf.data ); }
 
			return $.ajax( {
				"type":     conf.method,
				"url":      conf.url,
				"data":     request,
				"dataType": "json",
				global : false,
				"cache":    false,
				"success":  function ( json ) {
					cacheLastJson = $.extend(true, {}, json);
 
					if ( cacheLower != drawStart ) { json.data.splice( 0, drawStart-cacheLower ); }
					if ( requestLength >= 0 ) { json.data.splice( requestLength, json.data.length ); }
					 
					drawCallback( json );
					$(".bt-switch").bootstrapSwitch();
					checkPermission();
				}
			} );
		}
		else {
			json = $.extend( true, {}, cacheLastJson );
			json.draw = request.draw; // Update the echo for each response
			json.data.splice( 0, requestStart-cacheLower );
			json.data.splice( requestLength, json.data.length );
 
			drawCallback(json);
			checkPermission();
		}
	}
};
 
// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
	return this.iterator( 'table', function ( settings ) {settings.clearCache = true;} );
} );

/* For Export Buttons available inside jquery-datatable "server side processing" - Start
- due to "server side processing" jquery datatble doesn't support all data to be exported
- below function makes the datatable to export all records when "server side processing" is on */

function newexportaction(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function (e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
//For Export Buttons available inside jquery-datatable "server side processing" - End

function ssDatatable(ele,tableHeaders,tableOptions,dataSet={})
{	
	var textAlign ={};var srnoPosition = 1;
	if(tableHeaders.textAlign!=""){var textAlign = JSON.parse(tableHeaders.textAlign);}
	if(tableHeaders.srnoPosition=="" ||tableHeaders.srnoPosition==0){srnoPosition = tableHeaders.srnoPosition;}else{srnoPosition = '1';}
	
	var dataUrl = ele.attr('data-url');
	var tableId = ele.attr('id');
	if(tableHeaders[0] != ""){$('#' + tableId).append(tableHeaders.theads);}		
	var ssTableOptions = {
		"paging": true,
		"processing": true,
		"serverSide": true,
		//'serverMethod': 'post',
		// 'ajax': $.fn.dataTable.pipeline({ url: base_url + controller + dataUrl, data:dataSet , pages: 5 } ),
		'ajax': {url: base_url + controller + dataUrl,type:"POST", data:dataSet,global:false } ,
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		'stateSave':false,
		"autoWidth" : false,
		"ordering": false,
		pageLength: 50,
		"rowCallback": function (nRow, aData, iDisplayIndex) {
		    var oSettings = this.fnSettings ();
			$('td', nRow).eq(srnoPosition).html(oSettings._iDisplayStart+iDisplayIndex +1);
			return nRow;
	  	},
		language: { search: "" },
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500,-1 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows','Show All' ]
		],
		order:[],
		orderCellsTop: true,
		"columnDefs": 	[
							// { orderable: false, targets: [0,1] } ,
							{ className: "text-center", "targets": textAlign.center }, 
							{ className: "text-left", "targets": textAlign.left }, 
							{ className: "text-right", "targets": textAlign.right }
						],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', {
               "extend": 'excel',
               "text": 'Excel',
               "titleAttr": 'Excel',                               
               "action": newexportaction
            }, {text: 'Refresh',action: function ( e, dt, node, config ) {initTable(srnoPosition); }}],
		"fnInitComplete":function(){
		    $('.dataTables_scrollBody').perfectScrollbar();
		    $(".ssTable tbody tr").each(function () {
                $('td', this).each(function (i,v) {
                    var td = $(this);
                    var styleProps = $(".ssTable thead tr th:eq("+i+")").css(["text-align"]);
                    $.each( styleProps, function( prop, value ){td.css( prop, value ); });
                 });
            });
		},
		"fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();$(".bt-switch").bootstrapSwitch();checkPermission();}
	};
	
	// Append Search Inputs
	var headerRowCount = $('.ssTable thead tr').length;
	if ( headerRowCount == 1 ) {
		$('.ssTable-cf thead tr:eq(0)').clone(true).insertAfter( '.ssTable-cf thead tr:eq(0)' );
		var ignorCols = $(".ssTable-cf").data('ninput');//.split(",");
		var lastIndex = $(".ssTable-cf thead").find("tr:first th").length - 1;
		$(".ssTable-cf thead tr:eq(1) th").each( function (index,value) 
		{
			if(jQuery.inArray(index, ignorCols) != -1) {$(this).html( '' );}
			else
			{
				if((jQuery.inArray(-1, ignorCols) != -1) && index == lastIndex){$(this).html( '' );}
				else{$(this).html( '<input type="text" style="width:100%;" class="tblSearchInput" />' );$(this).css('padding','2px');}
			}
		});
	}
	
	$.extend( ssTableOptions, tableOptions );
	ssTable = ele.DataTable(ssTableOptions);
	ssTable.buttons().container().appendTo( '#' + tableId +'_wrapper toolbar' );
	$('.dataTables_filter').css("text-align","left");
	$('#' + tableId +'_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('#' + tableId +'_filter label').attr("id","search-form");	
	$('#' + tableId +'_filter .form-control-sm').css("width","97%");
	$('#' + tableId +'_filter .form-control-sm').attr("placeholder","Search.....");	
	
    var state = ssTable.state.loaded();
	$('.ssTable-cf thead tr:eq(1) th').each( function (i) {
		if ( state ) {
			var colSearch = state.columns[i].search;
			if ( colSearch.search ) {$('.ssTable-cf thead tr:eq(1) th:eq('+i+') input').val( colSearch.search );}
		}
		$( 'input', this ).on( 'keyup change', function () {
			if ( ssTable.column(i).search() !== this.value ) {ssTable.column(i).search( this.value ).draw();}
		});
	} );
	$('.page-wrapper').resizer(function() {ssTable.columns.adjust().draw(); });
	// if(tableId){initSpeechRecognitation();}
}

function jpDataTable(tableId){
	var jpDataTable = $('.jpDataTable').DataTable( {
		"paging": true,
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		pageLength: 50,
		language: { search: "" },
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		order:[],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength','copy', 'excel']
	});
	jpDataTable.buttons().container().appendTo( '#' + tableId +'_wrapper toolbar' );
	$('.dataTables_filter').css("text-align","left");
	$('#' + tableId +'_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('#' + tableId +'_filter label').attr("id","search-form");	
	$('#' + tableId +'_filter .form-control-sm').css("width","97%");
	$('#' + tableId +'_filter .form-control-sm').attr("placeholder","Search.....");	
	
	checkPermission();
	return jpDataTable;
}

function jpReportTable(tableId) {
	var jpReportTable = $('#'+tableId).DataTable({
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		order: [],
		"columnDefs": [
		    {type: 'natural',targets: 0},
			{orderable: false,targets: "_all"},
			{className: "text-center",targets: [0, 1]},
			{className: "text-center","targets": "_all"}
		],
		pageLength: 25,
		language: {search: ""},
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['pageLength', 'excel'],
		"fnInitComplete":function(){
		    $('.dataTables_scrollBody').perfectScrollbar();
		    $("#"+tableId+" tbody tr").each(function () {
                $('td', this).each(function (i,v) {
                    var td = $(this);
                    var styleProps = $("#"+tableId+" thead tr th:eq("+i+")").css(["text-align"]);
                    $.each( styleProps, function( prop, value ){td.css( prop, value ); });
                 });
            });
		},
	    "fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	jpReportTable.buttons().container().appendTo('#'+tableId+'_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	setTimeout(function(){ jpReportTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() {jpReportTable.columns.adjust().draw(false); });
	return jpReportTable;
}

// Datatable : Get Serverside Data For Tax Invoice
function defaultDtTable(ele,dataSet={})
{	
	var dataUrl = ele.attr('data-url');
	var tableId = ele.attr('id');	
	var dtTableOptions = {
		"paging": true,
		"processing": true,
		"serverSide": true,
		'ajax': {url: base_url + controller + dataUrl,type:"POST", data:dataSet,global:false } ,
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		'stateSave':false,
		"autoWidth" : false,
		pageLength: 50,
		language: { search: "" },
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,-1 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','Show All' ]
		],
		order:[],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-4'B><'col-sm-8 ctbtr'>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {initDtTable();}}, {text: 'Pdf',action: function ( e, dt, node, config ) {dtTablePdf();}}],
		"fnInitComplete":function(){
		    $('.dataTables_scrollBody').perfectScrollbar();
		    $(".dtTable tbody tr").each(function () {
                $('td', this).each(function (i,v) {
                    var td = $(this);
                    var styleProps = $(".dtTable thead tr th:eq("+i+")").css(["text-align"]);
                    $.each( styleProps, function( prop, value ){td.css( prop, value ); });
                 });
            });
		},
		"fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();checkPermission();}
	};
	
	// Append Search Inputs
	var headerRowCount = $('.dtTable-cf thead tr').length;
	if ( headerRowCount == 1 ) {
		$('.dtTable-cf thead tr:eq(0)').clone(true).insertAfter( '.dtTable-cf thead tr:eq(0)' );
		var ignorCols = $(".dtTable-cf").data('ninput');//.split(",");
		var lastIndex = $(".dtTable-cf thead").find("tr:first th").length - 1;
		$(".dtTable-cf thead tr:eq(1) th").each( function (index,value) 
		{
			if(jQuery.inArray(index, ignorCols) != -1) {$(this).html( '' );}
			else
			{
				if((jQuery.inArray(-1, ignorCols) != -1) && index == lastIndex){$(this).html( '' );}
				else{$(this).html( '<input type="text" style="width:100%;"/>' );}
			}
		});
	}
	
	dtTable = ele.DataTable(dtTableOptions);
	dtTable.buttons().container().appendTo( '#' + tableId +'_wrapper toolbar' );
	$('.dataTables_filter').css("text-align","left");
	$('#' + tableId +'_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('#' + tableId +'_filter label').attr("id","search-form");	
	$('#' + tableId +'_filter .form-control-sm').css("width","97%");
	$('#' + tableId +'_filter .form-control-sm').attr("placeholder","Search.....");	
	
	var state = dtTable.state.loaded();
	$('.dtTable-cf thead tr:eq(1) th').each( function (i) {
		if ( state ) {
			var colSearch = state.columns[i].search;
			if ( colSearch.search ) {$('.dtTable-cf thead tr:eq(1) th:eq('+i+') input').val( colSearch.search );}
		}
		$( 'input', this ).on( 'keyup change', function () {
			if ( dtTable.column(i).search() !== this.value ) {dtTable.column(i).search( this.value ).draw();}
		});
	} );
}

function jpDataTableFilter(tableId,state=false){
	
	// Append Search Inputs
	var srowposition = $('#'+tableId).data('srowposition');
	if(!srowposition){srowposition = 1;}
	var cloneFromTr = srowposition -1;
	var headerRowCount = $('.colSearch thead tr').length;
	
	if ( headerRowCount == srowposition ) {
    	$('.colSearch thead tr:eq('+cloneFromTr+')').clone(true).insertAfter( '.colSearch thead tr:eq('+cloneFromTr+')' );
    	var ignorCols = $(".colSearch").data('ninput');//.split(",");
    	var lastIndex = $(".colSearch thead").find("tr:first th").length - 1;
    	$(".colSearch thead tr:eq("+srowposition+") th").each( function (index,value) 
    	{
    		if(jQuery.inArray(index, ignorCols) != -1) {$(this).html( '' );}
    		else
    		{
    			if((jQuery.inArray(-1, ignorCols) != -1) && index == lastIndex){$(this).html( '' );}
    			else{$(this).html( '<input type="text" style="width:100%;"/>' );}
    		}
    	});
	}
	
	var jpDataTable = $('.jpDataTable').DataTable( {
		"paging": true,
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		'stateSave':true,
		"autoWidth" : false,
		pageLength: 50,
		language: { search: "" },
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		order:[],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength','copy', 'excel'],
		"fnInitComplete":function(){
		    $('.dataTables_scrollBody').perfectScrollbar();
		    $("#"+tableId+" tbody tr").each(function () {
                $('td', this).each(function (i,v) {
                    var td = $(this);
                    var styleProps = $("#"+tableId+" thead tr th:eq("+i+")").css(["text-align"]);
                    $.each( styleProps, function( prop, value ){td.css( prop, value ); });
                 });
            });
		},
		"fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	
	jpDataTable.buttons().container().appendTo( '#' + tableId +'_wrapper toolbar' );
	$('.dataTables_filter').css("text-align","left");
	$('#' + tableId +'_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('#' + tableId +'_filter label').attr("id","search-form");	
	$('#' + tableId +'_filter .form-control-sm').css("width","97%");
	$('#' + tableId +'_filter .form-control-sm').attr("placeholder","Search.....");	
	
	var state = jpDataTable.state.loaded();
	$('.colSearch thead tr:eq('+srowposition+') th').each( function (i) {
		if ( state ) {
			var colSearch = state.columns[i].search;
			if ( colSearch.search ) {$('.colSearch thead tr:eq('+srowposition+') th:eq('+i+') input').val( colSearch.search );}
		}
		$( 'input', this ).on( 'keyup change', function () {
			if ( jpDataTable.column(i).search() !== this.value ) {jpDataTable.column(i).search( this.value ).draw();}
		});
	} );
	
	$('.page-wrapper').resizer(function() {jpDataTable.columns.adjust().draw(); });
	return jpDataTable;
}