<?php $this->load->view('app/includes/header');?>
<style>
	.select2-container{width:85% !important;}
</style>
<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white"><?=$headData->pageTitle?></div>
	<!-- <div class="right">
		<a href="#" class="headerButton toggle-searchbox text-white">
			<ion-icon name="search-outline" role="img" class="md hydrated searchbtn" aria-label="search outline"></ion-icon>
		</a>
		
	</div> -->
</div>
<!-- <div id="search" class="appHeader bg-info"> -->
	<!--<form class="search-form">-->
		<!-- <div class="form-group searchbox">
			<input type="text" class="form-control quicksearch" placeholder="Search...">
			<i class="input-icon icon ion-ios-search"></i>
			<a href="#" class="ms-1 close toggle-searchbox"><i class="icon ion-ios-close-circle text-white"></i></a>
		</div> -->
	<!--</form>-->
<!-- </div> -->

<div class="extraHeader pe-0 ps-0">
	<div class="input-group">
		<select name="so_trans_id" id="so_trans_id" class="form-control select2" >
			<option value="">Select Order</option>
			<?php
				foreach($soTransData as $row):
					echo "<option value='".$row->id."' >".getPrefixNumber($row->trans_prefix,$row->trans_no)." [".$row->doc_no." | ".formatDate($row->trans_date)."]</option>";
				endforeach;
			?>
		</select>
		<button class="btn btn-primary square trackOrder" type="button" style="width:15%;">Go!</button>
		
	</div>
</div>

<!-- Start Main Content -->
<div id="appCapsule" class="extra-header-active full-height">
	<div class="tab-content mb-1">
		<ul class="listview image-listview media flush no-line list-grid mb-1"  id="trackData">
			<div style="text-align:center;height:50vh" class="bg-white"><img src="<?=base_url('assets/images/track_order.png')?>" style="height:280px;"></div>
		</ul>
	</div>
</div>
<!-- End Main Content -->


<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>

<script>
$(document).ready(function(){
	
	$(".select2").select2();
	var qsRegex;
	var isoOptions = {
		itemSelector: '.listItem',
		layoutMode: 'fitRows',
		filter: function() {return qsRegex ? $(this).text().match( qsRegex ) : true;}
	};
	// init isotope
	var $grid = $('.list-grid').isotope( isoOptions );
	var $qs = $('.quicksearch').keyup( debounce( function() {qsRegex = new RegExp( $qs.val(), 'gi' );$grid.isotope();}, 200 ) );

//$(document).on('keyup',".quicksearch",function(){console.log($(this).val());});


	$(document).on('click','.trackOrder',function(){		
		var so_trans_id = $('#so_trans_id').val();
		$.ajax({
				url:base_url + controller + '/trackOrderByTransId',
				type:'post',
				data:{so_trans_id:so_trans_id},
				dataType:'json',
				global:false,
				success:function(data)
				{
					$('#trackData').html(data.html)
				}
			});
	});
});

function searchItems(ele){
	console.log($(ele).val());
}

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


</script>