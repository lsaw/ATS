		// removes commas from a field
		function stripCommas(num)
		{
			var rg = /,/g;
			var num1 = num.replace(rg, '');
			return num1;
		}
		// adds commas to a number for display
		// from http://www.mredkj.com/javascript/nfbasic.html
		function addCommas(nStr)
		{
			nStr += '';
			var x = nStr.split('.');
			var x1 = x[0];
			var x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
		// For editing a number already in comma seperated form.
		// fires editFunction functionality
		// then strips commas from field, and adds them back so they get placed properly
		function setCommas(fieldPointer, table)
		{
			editFunction(fieldPointer, table);
			var num = fieldPointer.value;
			var num1 = stripCommas(num);
			var numCom = addCommas(num1);
			fieldPointer.value = numCom;
		}