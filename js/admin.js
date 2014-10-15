jQuery(function($){

	$('#wpcomments-tabs').tabs();


	var meta_removed;
	
	//attaching hide and delete events for existing meta data
	$("#file-meta-input-holder li").each(function(i, item){
		$(item).find(".ui-icon-carat-2-n-s").click(function(e) {
			$(item).find("table").slideToggle(300);
		});
		// for delete box
		$(item).find(".ui-icon-trash").click(function(e) {
			$("#remove-meta-confirm").dialog("open");
			meta_removed = $(item);
		});	
	});
	
	$('.ui-icon-circle-triangle-n').click(function(e){
		$("#file-meta-input-holder li").find('table').slideUp();
	});
	$('.ui-icon-circle-triangle-s').click(function(e){
		$("#file-meta-input-holder li").find('table').slideDown();
	});
	
	
	
	$("#file-meta-input-holder").sortable({
		revert : true,
		stop : function(event, ui) {
			// console.log(ui);

			// only attach click event when dropped from right panel
			if (ui.originalPosition.left > 20) {
				$(ui.item).find(".ui-icon-carat-2-n-s").click(function(e) {
					$(this).parent('.postbox').find("table").slideToggle(300);
				});

				// for delete box
				$(ui.item).find(".ui-icon-trash").click(function(e) {
					$("#remove-meta-confirm").dialog("open");
					meta_removed = $(ui.item);
				});
			}
		}
	});

	// =========== remove dialog ===========
	$("#remove-meta-confirm").dialog({
		resizable : false,
		height : 160,
		autoOpen : false,
		modal : true,
		buttons : {
			"Remove" : function() {
				$(this).dialog("close");
				meta_removed.remove();
			},
			Cancel : function() {
				$(this).dialog("close");
			}
		}
	});

	$("#nm-input-types li").draggable(
			{
				connectToSortable : "#file-meta-input-holder",
				helper : "clone",
				revert : "invalid",
				stop : function(event, ui) {
					

					$('.ui-sortable .ui-draggable').removeClass(
							'input-type-item').find('div').addClass('postbox');

					// now replacing the icons with arrow
					$('.postbox').find('.ui-icon-arrow-4').removeClass(
							'ui-icon-arrow-4').addClass('ui-icon-carat-2-n-s');
					$('.postbox').find('.ui-icon-placehorder').removeClass(
							'ui-icon-placehorder').addClass(
							'ui-icon ui-icon-trash');

				}
			});

	// ================== new meta form creator ===================
});

function update_options(options){
	
	var opt = jQuery.parseJSON(options);
	
	jQuery("#wpcomment-settigns-saving").html('<img src="'+wpcomments_vars.doing+'" />');
	/*
	 * getting action from object
	 */
	
	
	/*
	 * extractElementData
	 * defined in nm-globals.js
	 */
	var data = extractElementData(opt);
	
	
	if (data.bug) {
		//jQuery("#reply_err").html('Red are required');
		alert('bug here');
	} else {

		/*
		 * [1]
		 * TODO: change action name below with prefix plugin shortname_action_name
		 */
		data.action = 'wpcomments_save_settings';

		jQuery.post(ajaxurl, data, function(resp) {

			jQuery("#wpcomment-settigns-saving").html(resp);
			// alert(resp);
			window.location.reload(true);

		});
	}
	
	/*jQuery.each(res, function(i, item){
		
		alert(i);
		
	});*/
}

/* ================ below script is for admin settings framework ============== */

// saving form meta
function save_file_meta() {

 
	jQuery("#nm-saving-form").html('<img src="'+wpcomments_vars.doing+'" />');
	
	var form_meta_values = new Array(); // {}; //Array();
	jQuery("#file-meta-input-holder li")
			.each(
					function(i, item) {

						var inner_array = {};
						inner_array['type'] = jQuery(item).attr(
								'data-inputtype');

						jQuery(this)
								.find('td.table-column-input')
								.each(
										function(i, col) {

											var meta_input_type = jQuery(col)
													.attr('data-type');
											var meta_input_name = jQuery(col)
													.attr('data-name');
											var cb_value = '';
											if (meta_input_type == 'checkbox') {
												cb_value = (jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val() === undefined ? '' : jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val());
									inner_array[meta_input_name] = cb_value;
											} else if (meta_input_type == 'textarea') {
												inner_array[meta_input_name] = jQuery(
														this)
														.find(
																'textarea[name="'
																		+ meta_input_name
																		+ '"]')
														.val();
											} else if (meta_input_type == 'select') {
												inner_array[meta_input_name] = jQuery(
														this)
														.find(
																'select[name="'
																		+ meta_input_name
																		+ '"]')
														.val();
											} else if (meta_input_type == 'html-conditions') {
												
												var all_conditions = {};
												var the_conditions = new Array();	//{};
												
												all_conditions['visibility'] = jQuery(
														this)
														.find(
																'select[name="condition_visibility"]')
														.val();
												all_conditions['bound'] = jQuery(
														this)
														.find(
																'select[name="condition_bound"]')
														.val();
												jQuery(this).find('div').each(function(i, div_box){
												
													var the_rule = {};
													
													the_rule['elements'] = jQuery(
															this)
															.find(
																	'select[name="condition_elements"]')
															.val();
													the_rule['operators'] = jQuery(
															this)
															.find(
																	'select[name="condition_operators"]')
															.val();
													the_rule['element_values'] = jQuery(
															this)
															.find(
																	'select[name="condition_element_values"]')
															.val();
													
													the_conditions.push(the_rule);
												});
												
												all_conditions['rules'] = the_conditions;
												inner_array[meta_input_name] = all_conditions;
											}else if (meta_input_type == 'pre-images') {
												var all_preuploads = new Array();
												jQuery(this).find('div.pre-upload-box table').each(function(i, preupload_box){
													var pre_upload_obj = {	link: jQuery(preupload_box).find('input[name="pre-upload-link"]').val(),
															title: jQuery(preupload_box).find('input[name="pre-upload-title"]').val(),
															price: jQuery(preupload_box).find('input[name="pre-upload-price"]').val(),};
													
													all_preuploads.push(pre_upload_obj);
												});
												
												inner_array['images'] = all_preuploads;
												
											} else {
												inner_array[meta_input_name] = jQuery.trim(jQuery(this).find('input[name="'+ meta_input_name+ '"]').val())
												// inner_array.push(temp);
											}

										});

						form_meta_values.push(inner_array);

					});

	//console.log(form_meta_values); return false;
	// ok data is collected, so send it to server now Huh?


	do_action = 'wpcomments_save_file_meta';

	var server_data = {
		action 					: do_action,
		file_meta : form_meta_values
	}
	
	jQuery.post(ajaxurl, server_data, function(resp) {

		console.log(resp);
		if (resp.status == 'success') {

			jQuery("#nm-saving-form").html(resp.message);
			window.location.reload(true);
		}

	}, 'json');
	
}

/*
 * extract data from html elements
 */

function extractElementData(elements) {

	var data = new Object;

	data.bug = false;
	jQuery.each(elements,
			function(i, item) {

			if(item.req == undefined || item.req == 0){
				item.req = false;
				
			}else{
				item.req = true;
				
			}
			
				switch (item.type) {
				
				case 'text':

					data[i] = jQuery("input[name^='" + i + "']").val();
					if(jQuery("input[name^='" + i + "']").val() == '' && item.req){
						jQuery("input[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;

				case 'select':

					data[i] = jQuery("select[name^='" + i + "']").val();
					if(jQuery("select[name^='" + i + "']").val() == '' && item.req){
						jQuery("select[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;

				case 'checkbox':
					
					var checkedVals = [];
					jQuery('input:checkbox[name^="' + i + '"]:checked').each(function() {
						checkedVals.push(jQuery(this).val());
					});
					
					data[i] = (checkedVals.length == 0) ? null : checkedVals;
					
					if (!jQuery("input:checkbox[name^='" + i + "']").is(':checked') && item.req){
						
						jQuery("input:checkbox[name^='" + i + "']").parent('label').css('color', 'red');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
						
					break;

				case 'radio':

					data[i] = jQuery(
							"input:radio[name^='" + i + "']:checked").val();
					if (!jQuery("input:radio[name^='" + i + "']").is(':checked') && item.req){
											
						jQuery("input:radio[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						alert(item.type+' is required');
					}
					break;
					
				case 'textarea':

					data[i] = jQuery("textarea[name^='" + i + "']").val();
					
					if(jQuery("textarea[name^='" + i + "']").val() == '' && item.req){
						jQuery("textarea[name^='" + i + "']").css('border', 'red 1px solid');
						data.bug = true;
						/*alert(item.type+' is required');*/
					}
					break;
				}

			});

	return data;
}


/*
 * function checking the checkbox for current value
 * current value: json object
 * @Author: Najeeb
 * 13 Oct, 2012
 */

function setChecked(elementName, currentValue){
	
	var elementCB = jQuery('input:checkbox[name="' + elementName + '"]');
	
	var currentValues = jQuery.parseJSON(currentValue);
	
	
	//console.log(currentValues);
	
	jQuery.each(elementCB, function(i, item){
		
		//console.log(item.id);
		var current_cb_id = item.id;
		
		jQuery.each(currentValues, function(i, item){
			
			//console.log(item + jQuery("#"+current_cb_id).attr('value'));
			if(jQuery("#"+current_cb_id).attr('value') == item){
				
				jQuery("#"+current_cb_id).attr('checked', true);
			}else{
				if ( jQuery("#"+current_cb_id).attr('checked') == true)
					jQuery("#"+current_cb_id).attr('checked', false);
			}
			//jQuery('input:checkbox[value="' + item + '"]').attr("checked", "checked");
		});
	});
	
	
	
}

/*
 * function checking the RADIO for current value
 * current value: single
 * @Author: Najeeb
 * 3 July, 2012
 */

function setCheckedRadio(elementName, currentValue) {

	var elementRadio = jQuery('input:radio[name="' + elementName + '"]');

	//console.log(elementRadio);
	jQuery.each(elementRadio, function(i, item) {

		//console.log(item.id);
		var current_radio_id = item.id;
		
		if (jQuery("#" + current_radio_id).attr('value') == currentValue) {

			jQuery("#" + current_radio_id).attr('checked', true);
		} else {
			if (jQuery("#" + current_radio_id).attr('checked') == true)
				jQuery("#" + current_radio_id).attr('checked', false);
		}
						
	});

}
