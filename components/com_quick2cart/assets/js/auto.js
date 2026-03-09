techjoomla.jQuery(function() {
	var availableTags = '';

	/*get foucus on the input box*/
		techjoomla.jQuery(".selections").click(function(){
		var ul_id = this.id;
		var setfocus = ul_id.split(".");
			techjoomla.jQuery("#"+setfocus[1]).focus();
			techjoomla.jQuery("#"+setfocus[1]).val('');
	});

	techjoomla.jQuery(".auto_fields")
		.autocomplete({
			minLength: 0,
			source: function(request, response ) {
				if(request.term){
					var geofields =techjoomla.jQuery(".auto_fields_hidden").serializeArray();
					techjoomla.jQuery.ajax({
						url: "?option=com_quick2cart&task=couponform.findauto&store="+techjoomla.jQuery("#store_ID").val()+"&element="+this.element[0].id+"&request_term="+request.term,
						type: "POST",
						data:  geofields,//{geofields:geofields,current:this.value},
						dataType: "json",
						success: function(data) {
							if(data != ""){
								availableTags=data;
							}
							// delegate back to autocomplete, but extract the last term
							response( techjoomla.jQuery.ui.autocomplete.filter(
							availableTags, extractLast( request.term ) ) );
						}
					});
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				this.value='';
				var add_li='<li class="selection" id="selection-'+this.id+'_'+techjoomla.jQuery(this).index()+'">';
				add_li+='<a class="close" id="close-'+techjoomla.jQuery(this).index()+'" onclick="remove_li(\''+this.id+'\','+ techjoomla.jQuery(this).index()+',\''+ui.item.value+'\');">×</a>'+ui.item.label+'</li>';
				techjoomla.jQuery(this).before(add_li);
				availableTags = '';
				add_tag(this.id,ui.item.value);
				//show_hide_others();
				return false;
			}
		});
	});

	function add_tag(id,push_val){
		var hidden_value =  techjoomla.jQuery("#"+id+"_hidden").val();
		if(hidden_value){
			hidden_values =	dosplit(hidden_value);
			if ( techjoomla.jQuery.inArray(push_val, hidden_values)=='-1') {
				hidden_values.push(push_val);
			}
			val_to_push=hidden_values.join("||");
		}
		else
			val_to_push=push_val;
		 techjoomla.jQuery("#"+id+"_hidden").val("|"+val_to_push+"|");
	}
	function remove_li(thisid,ind,item) {
		var temp = new Array();
		var flag=0;
		temp =	dosplit(document.getElementById(thisid+"_hidden").value);
		for(var i=0;i < temp.length ; i++)
		{
			if(temp[i]!= '')
			{
				if(trim(temp[i]) == item)
				{
					removeByIndex(temp, i);
					break;
				}
			}
		}
		val_to_push=temp.join("||");
		val_to_push=temp.join("||");
		if(val_to_push)
			 techjoomla.jQuery("#"+thisid+"_hidden").val("|"+val_to_push+"|");
		else
			 techjoomla.jQuery("#"+thisid+"_hidden").val("");

		 techjoomla.jQuery('#'+'selection-'+thisid+'_'+ind).remove();
		//show_hide_others();
	}


	techjoomla.jQuery(function() {
		if(document.getElementById('coupon_id').value!='')
		{
			//techjoomla.jQuery("input[name=geo_type]").change();
			var elements = techjoomla.jQuery('.auto_fields');

			terms= new Array();
			elements.each(function(){
				terms=dosplit(techjoomla.jQuery(this).val());
				terms_name=dosplit(techjoomla.jQuery('#'+this.id+'_hiddenname').val());
				for(var i = 0; i < terms.length; i++)
				{
					if(terms[i]){

					var add_li='<li class="selection" id="selection-'+this.id+'_'+i+'">';
add_li+='<a class="close" id="close-'+i+'" onclick="remove_li(\''+this.id+'\','+i+',\''+terms[i]+'\');">×</a>'+terms_name[i]+'</li>';
					techjoomla.jQuery(this).before(add_li);
					this.value='';
					add_tag(this.id,terms[i]);

					}
				}
			});
			//show_hide_others();
			}
	});

	/*show hide the options based on count
	function show_hide_others(){
	var seletion_childCount = techjoomla.jQuery("ul#selections_country > li").length;
		if(seletion_childCount > 1 || seletion_childCount == 0 )
		{
			techjoomla.jQuery("#geo_others").hide('fast');
			techjoomla.jQuery("input[name=geo_type]").removeAttr("checked");
			if(seletion_childCount == 0 )
				techjoomla.jQuery("#qtc_countryCurrency").hide('fast');
			else
				techjoomla.jQuery("#qtc_countryCurrency").show('fast');
		}
		else if(seletion_childCount == 1)
		{
			techjoomla.jQuery("#geo_others").show('fast');
			techjoomla.jQuery("#qtc_countryCurrency").show('fast');
			techjoomla.jQuery("#everywhere").attr("checked","checked");
			techjoomla.jQuery(".byregion_ul").hide();
    		techjoomla.jQuery(".bycity_ul").hide();
		}
	}	*/
	function extractLast( term ) {
		return split( term ).pop();
	}
	function split( val ) {
		return val.split( /,\s*/ );
	}

	function trim(str)
	{
		return ltrim(rtrim(str));
	}
	function ltrim(s)
	{
		var l=0;
		while(l < s.length && s[l] == ' ')
		{	l++; }
		return s.substring(l, s.length);
	}
	function rtrim(s)
	{
		var r=s.length -1;
		while(r > 0 && s[r] == ' ')
		{	r-=1;	}
		return s.substring(0, r+1);
	}
	function dosplit(value){
		value = value.replace(/^.(\s+)?/, "");
		value =value.replace(/(\s+)?.$/, "");
		return value.split("||");
	}
	function removeByIndex(arrayName,arrayIndex){
		arrayName.splice(arrayIndex,1);
	}

	// Add click function to add products in favourites list
	function toggleFavourite(event) 
	{
		event.preventDefault();	// Prevent default button behavior
	
		const favebutton = event.currentTarget;
		const productId = favebutton.dataset.productId;
		const isLoggedIn = favebutton.dataset.loggedIn === '1';
		const userId = favebutton.dataset.userId;
		const ajaxUrl = favebutton.dataset.ajaxUrl;

		// Redirect to login page if not logged in
		if (isLoggedIn) 
		{
			alert(Joomla.JText._('COM_QUICK2CART_LOGIN_ALERT'));
			return;
		}
	
		// Define button and SVG path element IDs
		const buttonId = `wishlistButton${productId}`;
		const heartPathId = `heartPath${productId}`;
		const button = document.getElementById(buttonId);
		const heartPath = document.getElementById(heartPathId);
	
		// Validate elements
		if (!button || !heartPath) 
		{
			console.error(`Missing elements: Button or heart path for product ID: ${productId}`);
			return;
		}
	
		// Determine the current favorite state based on color
		const currentColor = heartPath.getAttribute('fill');
		const isFavorite = currentColor === '#ff4343';
	
		// Toggle UI immediately for better UX
		heartPath.setAttribute('fill', isFavorite ? '#c2c2c2' : '#ff4343');
		const action = isFavorite ? 'remove' : 'add';
	
		// Send AJAX request to update the favorite state
		Joomla.request({
			url: ajaxUrl,
			method: 'POST',
			data: `product_id=${productId}&user_id=${userId}&action=${action}`,
			onSuccess: function (response) {
				try {
					const result = JSON.parse(response);
					if (!result.success) 
					{
						// Revert UI if the operation failed
						heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
						alert(result.message);
					} 
					else 
					{
						alert(result.message);
					}
				} 
				catch (error) 
				{
					alert(Joomla.JText._('COM_QUICK2CART_SERVER_ERROR'));
	
					// Revert UI on error
					heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
				}
			},
			onError: function () 
			{
				alert(Joomla.JText._('COM_QUICK2CART_ERROR_UPDATE'));
				// Revert UI on error
				heartPath.setAttribute('fill', isFavorite ? '#ff4343' : '#c2c2c2');
			}
		});
	}

