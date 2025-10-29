var qsRegex;
var isoOptions = {
  itemSelector: ".listItem",
  layoutMode: "fitRows",
  filter: function () {
    return qsRegex ? $(this).text().match(qsRegex) : true;
  },
};
// init isotope
var isotopeList = $(".list-grid").isotope(isoOptions);
var $qs = $(".quicksearch").keyup(
  debounce(function () {
    qsRegex = new RegExp($qs.val(), "gi");
    isotopeList.isotope();
  }, 200)
);

$(document).ready(function () {
  checkPermission();
  $(document).on("click", ".closeSearch", function (e) {
    $(".quicksearch").val("");
    $(".quicksearch").trigger("keyup");
  });

  // $(".appBottomMenu .item").removeClass("active");
  console.log(menu_id);
  // $(".appBottomMenu")
  //   .find(".item:eq(" + menu_id + ")")
  //   .addClass("active");
  // $('.appBottomMenu').find('.item:eq(' + menu_id + ')').addClass('active');
  $(document).ajaxStart(function () {
    $(".ajaxLoader").show();
  });
  $(document).ajaxComplete(function () {
    $(".ajaxLoader").hide();
  });

  
	$(document).on('click','.pswHideShow',function(){
		var type = $('.pswType').attr('type');
		if(type == "password"){
			$(".pswType").attr('type','text');
			$(this).html('<i class="fa fa-eye-slash"></i>');
		}else{
			$(".pswType").attr('type','password');
			$(this).html('<i class="fa fa-eye"></i>');
		}
	});

  $(".select2").select2();setPlaceHolder();
});

/** Init Isotope Grid **/
function initListview(ele = ".list-grid") {
  if ($(document).find(ele).data("isotope")) {
    isotopeList.isotope("destroy").isotope(isoOptions);
  } else {
    isotopeList = $(ele).isotope(isoOptions);
  }
}

function debounce(fn, threshold) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
    clearTimeout(timeout);
    var args = arguments;
    var _this = this;

    function delayed() {
      fn.apply(_this, args);
    }
    timeout = setTimeout(delayed, threshold);
  };
}
$(".listTab a").on("shown.bs.tab", function (e) {
  $(".list-grid").isotope("layout");
});

function setPlaceHolder() {
  // $("input[name=item_name]").alphanum({allow: '-()."+@#%&*!|/[]{},?<>_=:^', allowSpace: true});
  var label = "";
  $("input").each(function () {
    if (!$(this).attr("placeholder")) {
      if (
        !$(this).hasClass("combo-input") &&
        $(this).attr("type") != "hidden"
      ) {
        label = "";
        inputElement = $(this).parent();
        if ($(this).parent().hasClass("input-group")) {
          inputElement = $(this).parent().parent();
        } else {
          inputElement = $(this).parent();
        }
        label = inputElement.children("label").text();
        label = label.replace("*", "");
        label = $.trim(label);
        if ($(this).hasClass("req")) {
          inputElement
            .children("label")
            .html(label + ' <strong class="text-danger">*</strong>');
        }
        if (label) {
          $(this).attr("placeholder", label);
        }
        $(this).attr("autocomplete", "off");
        var errorClass = "";
        var nm = $(this).attr("name");
        if ($(this).attr("id")) {
          errorClass = $(this).attr("id");
        } else {
          errorClass = $(this).attr("name");
          if (errorClass) {
            errorClass = errorClass.replace("[]", "");
          }
        }
        if (inputElement.find("." + errorClass).length <= 0) {
          inputElement.append('<div class="error ' + errorClass + '"></div>');
        }
      } else {
        $(this).attr("autocomplete", "off");
      }
    } else {
      if (
        !$(this).hasClass("combo-input") &&
        $(this).attr("type") != "hidden"
      ) {
        inputElement = $(this).parent();
        var errorClass = "";
        var nm = $(this).attr("name");
        if ($(this).attr("id")) {
          errorClass = $(this).attr("id");
        } else {
          errorClass = $(this).attr("name");
          if (errorClass) {
            errorClass = errorClass.replace("[]", "");
          }
        }
        if (inputElement.find("." + errorClass).length <= 0) {
          inputElement.append('<div class="error ' + errorClass + '"></div>');
        }
      } else {
        $(this).attr("autocomplete", "off");
      }
    }
  });
  $("textarea").each(function () {
    if (!$(this).attr("placeholder")) {
      label = "";
      label = $(this).parent().children("label").text();
      label = label.replace("*", "");
      label = $.trim(label);
      if ($(this).hasClass("req")) {
        $(this)
          .parent()
          .children("label")
          .html(label + ' <strong class="text-danger">*</strong>');
      }
      if (label) {
        $(this).attr("placeholder", label);
      }
      $(this).attr("autocomplete", "off");
      var errorClass = "";
      var nm = $(this).attr("name");
      if ($(this).attr("name")) {
        errorClass = $(this).attr("name");
      } else {
        errorClass = $(this).attr("id");
      }
      //if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
    }
  });
  $("select").each(function () {
    if (!$(this).attr("placeholder")) {
      label = "";
      var selectElement = $(this).parent();
      if ($(this).hasClass("single-select")) {
        selectElement = $(this).parent().parent();
      }
      label = selectElement.children("label").text();
      label = label.replace("*", "");
      label = $.trim(label);
      if ($(this).hasClass("req")) {
        selectElement
          .children("label")
          .html(label + ' <strong class="text-danger">*</strong>');
      }
      var errorClass = "";
      var nm = $(this).attr("name");
      if ($(this).attr("name") && $(this).attr("name").search("[]") != -1) {
        errorClass = $(this).attr("name");
      } else {
        errorClass = $(this).attr("id");
      }
      if (selectElement.find("." + errorClass).length <= 0) {
        selectElement.append('<div class="error ' + errorClass + '"></div>');
      }
    }
  });
}

$(document).on("change", "#country_id", function () {
  var id = $(this).val();
  if (id == "") {
    $("#state_id").html('<option value="">Select State</option>');
    $("#city_id").html('<option value="">Select City</option>');
    // $(".single-select").comboSelect();
  } else {
    $.ajax({
      url: base_url + "parties/getStates",
      type: "post",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        if (data.status == 0) {
          swal("Sorry...!", data.message, "error");
        } else {
          $("#state_id").html(data.result);
        //   $(".single-select").comboSelect();
          $("#state_id").focus();
        }
      },
    });
  }
});

$(document).on("change", "#state_id", function () {
  var id = $(this).val();
  if (id == "") {
    $("#city_id").html('<option value="">Select City</option>');
    // $(".single-select").comboSelect();
  } else {
    $.ajax({
      url: base_url + "parties/getCities",
      type: "post",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        if (data.status == 0) {
          swal("Sorry...!", data.message, "error");
        } else {
          $("#city_id").html(data.result);
        //   $(".single-select").comboSelect();
          $("#city_id").focus();
        }
      },
    });
  }
});

function changePsw(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'app/dashboard/changePassword',
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
			$('#'+formId)[0].reset();
			$('.modal').modal('hide');
			$('#successDialog .modal-body').html(data.message);
			$('#successDialog').modal('show');		
		}else{
			$('#errorDialog .modal-body').html(data.message);
			$('#errorDialog').modal('show');		}
				
	});
}

function checkPermission(){
	$('.permission-read').show();
	$('.permission-write').show();
	$('.permission-modify').show();
	$('.permission-remove').show();
	$('.permission-approve').show();
	console.log(permissionRead);
	if(permissionRead == "1"){ $('.permission-read').show(); }else{ $('.permission-read').hide(); }
	if(permissionWrite == "1"){ $('.permission-write').show(); }else{ $('.permission-write').hide(); }
	if(permissionModify == "1"){ $('.permission-modify').show(); }else{ $('.permission-modify').hide(); }
	if(permissionRemove == "1"){ $('.permission-remove').show(); }else{ $('.permission-remove').hide(); }
	if(permissionApprove == "1"){ $('.permission-approve').show();}else{ $('.permission-approve').hide(); }
}

function setPlaceHolder(){
  // $("input[name=item_name]").alphanum({allow: '-()."+@#%&*!|/[]{},?<>_=:^', allowSpace: true});
  var label="";
  $('input').each(function () {
    if(!$(this).attr('placeholder') )
    {
      if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
      {
        label="";
        inputElement = $(this).parent();
        if($(this).parent().hasClass('input-group')){inputElement = $(this).parent().parent();}else{inputElement = $(this).parent();}
        label = inputElement.children("label").text();
        label = label.replace('*','');
        label = $.trim(label);
        if($(this).hasClass('req')){inputElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
        if(label){$(this).attr("placeholder", label);}
        $(this).attr("autocomplete", 'off');
        var errorClass="";
        var nm = $(this).attr('name');
        if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
        if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
      }
      else{$(this).attr("autocomplete", 'off');}
      }
      else
    {
      if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
      {
        inputElement = $(this).parent();
        var errorClass="";
        var nm = $(this).attr('name');
        if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
        if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
      }
      else{$(this).attr("autocomplete", 'off');}
      }
  });
  $('textarea').each(function () {
    if(!$(this).attr('placeholder') )
    {
        label="";
      label = $(this).parent().children("label").text();
      label = label.replace('*','');
      label = $.trim(label);
      if($(this).hasClass('req')){$(this).parent().children("label").html(label + ' <strong class="text-danger">*</strong>');}
      if(label){$(this).attr("placeholder", label);}
      $(this).attr("autocomplete", 'off');
      var errorClass="";
      var nm = $(this).attr('name');
      if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
      //if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
    }
  });
  $('select').each(function () {
    if(!$(this).attr('placeholder') )
    {
      label="";
      var selectElement = $(this).parent();
      if($(this).hasClass('single-select')){selectElement = $(this).parent().parent();}
      label = selectElement.children("label").text();
      label = label.replace('*','');
      label = $.trim(label);
      if($(this).hasClass('req')){selectElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
      var errorClass="";
      var nm = $(this).attr('name');
      if($(this).attr('name') && ($(this).attr('name').search('[]') != -1)){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
      if(selectElement.find('.'+errorClass).length <= 0){selectElement.append('<div class="error '+ errorClass +'"></div>');}
    }
  });
}
