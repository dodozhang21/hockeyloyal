var loyal_filter_ajax = function (the_object, state, fan_type, nonce) {
	if(!nonce) {
		nonce = $(the_object).attr("data-nonce");
	}
	$.ajax({
		type : "post",
		dataType : "json",
		url : myAjax.ajaxurl,
		data : {action: "filter_grid", state : state, fan_type: fan_type, nonce: nonce},
		success: function(response) {
			if(response.type == "success") {
			   $('#grid').html(response.html);
			   loyal_update_dropdowns(response.state, response.fan_type);
			}
			else {
			   alert(response.error);
			}
		}
	});
}

var loyal_update_dropdowns = function(state, fan_type) {
	//Close all open dropdowns.
	$('.wrapper-dropdown').removeClass('active');
	
	//Find the label for state
	new_state = $('#state-prov a[data-state="'+state+'"]');
	new_state_state = $(new_state).attr('data-state');
	new_state_long = loyal_shortner($(new_state).html());
	$('#state-prov .label').attr('data-state', new_state_state);
	$('#state-prov .label span').html(new_state_long);
	
	
	//Find the label for the fan type
	new_fan_type = $('#fan-type a[data-fan-type="'+fan_type+'"]');
	new_fan_type_type = $(new_fan_type).attr('data-fan-type');
	new_fan_type_name = loyal_shortner($(new_fan_type).html());
	$('#fan-type .label').attr('data-fan-type', new_fan_type_type);
	$('#fan-type .label span').html(new_fan_type_name);
}

var loyal_shortner = function (to_shorten) {
	if(to_shorten.length > 15) {
		new_return = to_shorten.substring(0,15)+'...';
		return new_return;
	}
	else {
		return to_shorten;
	}
}
	
jQuery(document).ready( function() {

	//Ajax for the state filter field
	$('#state-prov a').on('click', function(){
		state = $(this).attr("data-state");
		fan_type = $('#fan-type .label').attr("data-fan-type");
		
		loyal_filter_ajax($(this), state, fan_type);
		
		return false;

	});
	
	$('#fan-type a').on('click', function(){
		state = $('#state-prov .label').attr("data-state");
		fan_type = $(this).attr("data-fan-type");
		
		loyal_filter_ajax($(this), state, fan_type);
		
		return false;

	});

});