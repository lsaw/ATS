		// Sets value of a <select> field
		function setSelectField(selectName, fieldName)
		{
			$("#" + selectName).val(fieldName)
		}
		// Called to:
		//		set changed text red
		//		turn on "update" and "reset" buttons
		//		and tell update section which table to update
		function editFunction(fieldPointer, table){
			fieldPointer.style.color="red"
			$("#idUpdateAll").css("visibility","visible")
			$("#idReset").css("visibility","visible")
			$("#" + table).val("true")
			$("#anyTable").val("true")
		}
		function confirmMove() {
			var h = $("#anyTable").val()
			if('true' == $("#anyTable").val() ){
				return confirm("You have unsaved edits.  Press 'cancel' then 'Update' to save,  'OK' to exit without saving.")
			}
			else {
				return true
			}
		}
