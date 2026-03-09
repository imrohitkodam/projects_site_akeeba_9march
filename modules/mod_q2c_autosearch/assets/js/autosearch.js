var quick2cart = {
	modAutosearch: {
		initJs: function() {
			jQuery(document).ready(function() {
				jQuery('#search_query_q2c_auto_suggest').keyup(function() {
					var search_query = jQuery(this).val();
					var form_data = {
						filter_search : search_query,
						
						/** global: noOfProductShow */
						list_limit : noOfProductShow
					}
					var resp_data_format="";
					jQuery.ajax({
						/** global: baseurl */
						url:baseurl+"index.php?option=com_quick2cart&format=json&task=category.getProductData",
						data : form_data,
						method : "post",
						dataType : "json",
						success : function(response) {
							document.getElementById("modq2c-autosuggest-data-container").style.display = "block";

							if (response.length > 0)
							{
								for (var i = 0; i < response.length; i++) {
									var productUrl = baseurl + 'index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' + response[i].item_id + '&Itemid=' + Itemid + '&module_search=1';
									resp_data_format=resp_data_format+"<a href='" + productUrl + "'><li class='select_product'>"+response[i].name+"</li></a>";
								};
								resp_data_format = resp_data_format + "</ul>";
							}
							else if(search_query !== null && search_query !== '')
							{
								resp_data_format = "<p style='color:red;'>" + Joomla.JText._('QTC_NO_PRODUCTS_FOUND') + "</p>";
							}

							if (search_query == "" || search_query == undefined)
							{
								document.getElementById("modq2c-autosuggest-data-container").style.display = "none";
							}

							jQuery("#modq2c-autosuggest-data-container").html(resp_data_format);
						}
					});
				});

				jQuery(document).on( "click", ".select_product", function(){
					var selected_product = jQuery(this).html();
					jQuery('#search_query_q2c_auto_suggest').val(selected_product);
					jQuery('#modq2c-autosuggest-data-container').html('');
				});
			});
		}
	}
}
