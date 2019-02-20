<?php
include 'loggy.php';
include_once 'helper.php';
include_once 'mySqli.php';

logUser('report');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$title = isset($_GET['title']) ? $_GET['title'] : '';

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
		<link href="dataTables/css/demo_table.css" rel="stylesheet" type="text/css" />
		<link href="dataTables/css/NPSdataTable.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="dataTables/js/jquery.dataTables.js"></script>
		<script type="text/javascript" src="dataTables/js/grouping.js"></script>
		<script type="text/javascript">
			$(document).ready( function () {
				var y = getYears()
				$.ajax({
					type: 'POST',
					url: 'getReportTableHTML.php',
					data: {report:'<?php echo $type?>',years:y},
					dataType: 'json',
					success: function(result){
						$('#dailyRidesTable').html(result.data)
						$('#dailyRidesTotRow').html(result.totals)
						$('#dailyRidesTable').dataTable({
							"bSort":false,
							"bFilter":false,
							"bLengthChange": false,
							"bPaginate": false,
							"bDestroy":true,
							"bAutoWidth":false,
							"bInfo":false
						} ).rowGrouping({bExpandableGrouping: true});
						fixGroupHeaders(result.parkTotals);
						var th = adjustTotalCostTable();
					}
				});
			});
			function getYears(){
				var g = [2010,2012,2014,2016]
				return g
			}
			function adjustTotalCostTable()
			{
				var thHolder=new Array();
				var i=0;
				$('#dailyRidesTable tr:first > th').each(function(){
					thHolder[i]=$(this).width()
					i++
				});
				i=0;
				$('#dailyRidesTotRow tr:first > td').each(function(){
					$(this).width(thHolder[i]);
					i++
				});
				return thHolder
			};
			function fixGroupHeaders(parkTotals)
			{
				var ys = getYears()
				$('#dailyRidesTable tbody:first tr[id^="group-id"] >td').attr('colspan',1);
				var name, y;
				for(name in parkTotals)
				{
					var lName = name.toLowerCase()
					for(y in ys){
						$('#dailyRidesTable tbody:first td[data-group="' + lName + '"]').parent().append("<td style='text-align:right' class='group'>" + parkTotals[name][ys[y]] + "</td>");
					}
				}
			}
		</script>
	</head>
	<body>
	<form method="post" autocomplete="off" action="$_SERVER[SCRIPT_NAME]">
		<div id="perfMeasTitle">
			<div>
				<p><a href='index.htm'>Return to Admin Page</a>&nbsp;&nbsp;<a href='perfMeas.php'>Return to Performance Measures Page</a></p>
			</div>
		</div> <!--  pickers -->
		<div class="clear"></div>
	</form>
		<div id=perfMeasTitle>
			<p><?php echo $title?></p>
		</div>
		<div id="dailyRidesHolder">
			<div id="perfMeasContainer">
				<table cellpadding="0" cellspacing="0" border="0" class="display dataTable" id="dailyRidesTable">
				</table> <!-- costTable -->
			</div> <!-- container -->
			<div id='totals'>
				<table cellpadding="0" cellspacing="0" border="0" id='dailyRidesTotRow' class='display dataTable'>
				</table> <!-- costTotRow -->
			</div> <!-- totals -->
			<div id="bottom"></div>
		</div> <!-- super -->
	</body>
</html>
