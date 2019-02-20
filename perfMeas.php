<?php
include 'loggy.php';

logUser('performance meas');

echo getHeaderTextGeneral();
echo javascriptHeaderText();

?>
	</head>
	<body>
		<div id='container'>
			<p>Performance Measures</p>
			<div id=manAssets2>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='logout.php'>Logout</a></p></td>
					</tr>
				</table>
			</div>
			<div class='perfMeas'>
				<table id='perf'>
					<tr><td><a href="report.php?type=annualRides&title=Annual Rides">Annual Rides</a></td></tr>
					<tr><td><a href='report.php?type=dailyRides&title=Average Daily Riders per Peak Season Day'>Average Daily Riders per Peak Season Day</a></td></tr>
					<tr><td><a href='report.php?type=annualAutoMilesRemoved&title=Annual Auto Miles Removed'>Annual Auto Miles Removed</a></td></tr>
					<tr><td><a href='report.php?type=averageDailyRidersPerBus&title=Average Daily Riders per Bus'>Average Daily Riders per Bus</a></td></tr>
					<tr><td><a href='report.php?type=annualNPSCost&title=Annual NPS Cost'>Annual NPS Cost</a></td></tr>
					<tr><td><a href='report.php?type=annualNPSCostPerRide&title=Annual NPS Cost per Ride'>Annual NPS Cost per Ride</a></td></tr>
				</table>
			</div>
		</div>
	</body>
