var q2c = {
	q2cModLocation: {
		init: function() {
			var locatorSection = document.getElementById("locator-input-section");
			var input = document.getElementById("autocomplete");

			/** Intiate function here*/
			function init() {
				var locatorButton = document.getElementById("locator-button");
				locatorButton.addEventListener("click", locatorButtonPressed);
				input.addEventListener("keyup", autocompleteAddressEnter);
			}

			function autocompleteAddressEnter() {
				q2c.q2cModLocation.clearErrorMsg();
			}

			function locatorButtonPressed() {
				locatorSection.classList.add("loading");

				navigator.geolocation.getCurrentPosition(function (position) {
					getUserAddressBy(position.coords.latitude, position.coords.longitude);
				},

				function (error) {
					locatorSection.classList.remove("loading");
					alert(Joomla.JText._('MOD_Q2C_LOCATION_LOCATOR_DENIED'));
				});
			}

			function getUserAddressBy(lat, long) {
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function () {
					if (this.readyState == 4 && this.status == 200) {
						var pincode = JSON.parse(this.responseText).results[0].address_components.filter(function(component){
							return component.types.includes('postal_code');
							});

						var address = JSON.parse(this.responseText);
						setAddressToInputField(address.results[0].formatted_address, pincode[0].long_name);
					}
				};
				xhttp.open("GET", "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + long + "&key=" + googleMapLocationKey, true);
				xhttp.send();
			}

			function setAddressToInputField(address, pincode) {
				input.value = address;
				locatorSection.classList.remove("loading");
				q2c.q2cModLocation.setAddressToLocationModule(address, pincode);
			}

			var options = {};

			if (countryISOCode != '')
			{
				var restrictedCountries = {};
				restrictedCountries.country = countryISOCode;
				options.componentRestrictions = restrictedCountries;
			}

			var autocomplete = new google.maps.places.Autocomplete(input, options);

			autocomplete.addListener("place_changed", () => {
				var place = autocomplete.getPlace();
				var mapPlacePostalcode = "";
				var mapPlaceFormattedAddress = "";

				for (var i = 0; i < place.address_components.length; i++)
				{
					for (var j = 0; j < place.address_components[i].types.length; j++)
					{
						if (place.address_components[i].types[j] == "postal_code")
						{
							if (place.address_components[i].long_name != undefined && place.address_components[i].long_name != "")
							{
								mapPlacePostalcode = place.address_components[i].long_name;
							}

							if (place.formatted_address != undefined && place.formatted_address != "")
							{
								mapPlaceFormattedAddress = place.formatted_address;
							}
						}
					}
				}

				q2c.q2cModLocation.setAddressToLocationModule(mapPlaceFormattedAddress, mapPlacePostalcode);
			});

			init();
		},
		toggleLocatorButton: function () {
			var location_popover_container = document.getElementById('modq2c_location_popover_container');

			if (location_popover_container.style.display == 'none')
			{
				location_popover_container.style.display = 'flex';
			}
			else
			{
				location_popover_container.style.display = 'none';
			}
		},
		updateUserSelectedAddress: function (element) {
			var elms = document.getElementById(element.id).getElementsByTagName("div");
			var userSelectedAddress = elms[0].innerText;
			var pincode = "";

			var pincodes = userSelectedAddress.split(",").filter(function(value){
				return (!isNaN(value.trim()));
			});

			var filtered = pincodes.filter(function (el) {
				return (el != null && el != '' && el != undefined);
			});

			if (filtered.length >= 1)
			{
				pincode = filtered[0];
			}

			q2c.q2cModLocation.setAddressToLocationModule(userSelectedAddress, pincode);
		},
		setAddressToLocationModule: function (address, pincode) {
			if (address == "" || pincode == "")
			{
				document.cookie = "q2cModLocationPincode=" + '';
				document.cookie = "q2cModLocationAddress=" + '';
				q2c.q2cModLocation.raiseErrorMsg(Joomla.JText._('MOD_Q2C_LOCATION_SELECT_LOCALITY_OR_PINCODE'));

				return;
			}

			if (address == null && pincode == undefined)
			{
				document.getElementById("city").innerHTML = defaultAddress;
				document.cookie = "q2cModLocationPincode=" + '';
				document.cookie = "q2cModLocationAddress=" + '';
				q2c.q2cModLocation.clearErrorMsg();

				return;
			}
			else
			{
				var servicablePincodes = shippingPluginpincodeArray.split(',').map(function(item) {
					return parseInt(item, 10);
				});

				if (isPinwiseShippingPlgEnable == '1' && servicablePincodes.length !== 0 && servicablePincodes.length !== undefined)
				{
					if (servicablePincodes.includes(parseInt(pincode, 10)))
					{
						document.getElementById("city").innerHTML = address;
						document.cookie = "q2cModLocationPincode=" + pincode;
						document.cookie = "q2cModLocationAddress=" + address;
						q2c.q2cModLocation.clearErrorMsg();
						jQuery("#myLocationModal").modal('hide');
					}
					else
					{
						document.getElementById("city").innerHTML = defaultAddress;
						document.cookie = "q2cModLocationPincode=" + '';
						q2c.q2cModLocation.raiseErrorMsg(Joomla.JText._('MOD_Q2C_LOCATION_NO_SHIPPING_AVAILABLE'));
					}
				}
				else
				{
					document.getElementById("city").innerHTML = address;
					document.cookie = "q2cModLocationPincode=" + pincode;
					document.cookie = "q2cModLocationAddress=" + address;
					q2c.q2cModLocation.clearErrorMsg();
					jQuery("#myLocationModal").modal('hide');
				}
			}
		},
		raiseErrorMsg: function (msg) {
			document.getElementById('q2cModLocationErrorMsg').innerHTML = msg;
			document.getElementById('q2cModLocationErrorMsg').style.display = "contents";
		},
		clearErrorMsg: function () {
			document.getElementById('q2cModLocationErrorMsg').innerHTML = "";
			document.getElementById('q2cModLocationErrorMsg').style.display = "none";
		}
	}
};
