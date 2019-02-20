<?php
function showRep($fundSource, $yearStart, $resAssData, $targetType){
	$fundSource = 'All' == $fundSource ? 'All funding sources' : $fundSource;
	$fundSource = 'Cat 3' == $fundSource ? 'Category 3' : $fundSource;
	echo"			<div class='multiReportsTableDiv'>
				<table id='repTable' class='manAsset'>
	";
/*	echo "					<thead>
						<tr>
							<th style='white-space:pre;'>PMIS
No.</th>
							<th style=''>Part</th>
							<th style=';white-space:pre;'>Park
ID</th>
							<th style=>PMIS Name</th>
							<th style='width:4em'>Rank</th>
							<th style='width:4em;white-space:pre;'>Fund
Src</th>
							<th class='pres' style='width:4em'>Preserve</th>
							<th class='pres' style='width:4em'>Enhance</th>
							<th class='pres' style='width:4em'>New</th>
							<th class='pres' style='width:5em'>Plan</th>
							<th style='width:5em'>Total</th>
						</tr>
					</thead>
";*/
	$sClass = 'even';
	$curYear = $yearStart;
	$totals = array('preserve'=>0, 'enhance'=>0, 'new'=>0, 'plan'=>0, 'total'=>0);
	$firstTime = true;
	foreach($resAssData as $rowAssData)	{
		if($firstTime){
			echo "					<thead>
						<tr class='odd'>
							<th></th>
							<th></th>
							<th></th>
							<th style='text-align:left;'>$fundSource FY {$rowAssData['pmis_year']} $targetType target</th>
							<th></th>
							<th ></th>
							<th class='pres' ></th>
							<th class='pres' ></th>
							<th class='pres' ></th>
							<th class='pres' ></th>
							<th></th>
						</tr>
					
					
";
			echo "					
						<tr>
							<th style='white-space:pre;'>PMIS
No.</th>
							<th style=''>Part</th>
							<th style=';white-space:pre;'>Park
ID</th>
							<th style=>PMIS Name</th>
							<th style='width:4em'>Rank</th>
							<th style='width:4em;white-space:pre;'>Fund
Src</th>
							<th class='pres' style='width:4em'>Preserve</th>
							<th class='pres' style='width:4em'>Enhance</th>
							<th class='pres' style='width:4em'>New</th>
							<th class='pres' style='width:5em'>Plan</th>
							<th style='width:5em'>Total</th>
						</tr>
					</thead>
					<tbody>
";
			$curYear = $rowAssData['pmis_year'];
			$firstTime = false;
		} // if($firstTime)
		if($curYear != $rowAssData['pmis_year']){
			foreach ($totals as $k=>&$v){
				$v = addCommas($v);
			}
			unset($v);
			$curYear = $rowAssData['pmis_year'];
			echo "						<tr class='odd'><td style='width:4em'></td>
							<td style=''></td>
							<td style=''></td>
							<td style='text-align:left;'><b>Totals</b></td>
							<td style=></td>
							<td style=></td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['preserve']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['enhance']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['new']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['plan']}</td>
							<td style='width:4em;text-align:right;'>{$totals['total']}</td>
						</tr>
					</tbody>
				</table>
";
			echo "			<div class='tableDivider even'></div>
";
			echo"
				<table id='repTable' class='manAsset'>
";

			echo "					<thead>
						<tr class='odd'>
							<th></th>
							<th></th>
							<th></th>
							<th style='text-align:left;'>$fundSource FY $curYear $targetType target</th>
							<th></th>
							<th class='pres'></th>
							<th class='pres' ></th>
							<th class='pres' ></th>
							<th class='pres' ></th>
							<th></th>
							<th></th>
						</tr>
	
						<tr>
							<th style='white-space:pre;'>PMI
No.</th>
							<th style=''>Part</th>
							<th style='white-space:pre;'>Park
ID</th>
							<th style=>PMIS Name</th>
							<th style='width:4em'>Rank</th>
							<th style='width:4em;white-space:pre;'>Fund
Src</th>
							<th class='pres' style='width:4em'>Preserve</th>
							<th class='pres' style='width:4em'>Enhance</th>
							<th class='pres' style='width:4em'>New</th>
							<th class='pres' style='width:5em'>Plan</th>
							<th style='width:5em'>Total</th>
						</tr>

					</thead>
					<tbody>
";
	
			foreach ($totals as $k=>&$val){
				$val=0;
			}
		} // if($curYear != $rowAssData['pmis_year'])
		$pmisName = '';
		$pmisName = str_replace('&','%26',$rowAssData['pmis_name']);
		$sClass = $sClass == 'even' ? 'odd' : 'even';
		$dol = addCommas($rowAssData['pmis_dollars']);
		echo "						<tr class='$sClass {$rowAssData['asset_action']}'>
							<td style='text-align:left'>{$rowAssData['pmis_number']}</td>
							<td style='text-align:right'>{$rowAssData['pmis_part']}</td>
							<td style='text-align:left'>{$rowAssData['park_id']}</td>
							<td style='text-align:left'>$pmisName</td>
							<td style='text-align:right'>{$rowAssData['pmis_region_rank']}</td>
							<td style='text-align:right'>{$rowAssData['pmis_fund_name']}</td>
";
		switch($rowAssData['asset_action']){
			case 'preserve':
				echo "							<td class='pres' style='text-align:right'>$dol</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td style='text-align:right'>$dol</td>
";
				$totals['preserve'] += $rowAssData['pmis_dollars'];
				$totals['total'] += $rowAssData['pmis_dollars'];
				break;
			case 'enhance':
				echo "							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>$dol</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td style='text-align:right'>$dol</td>
";
				$totals['enhance'] += $rowAssData['pmis_dollars'];
				$totals['total'] += $rowAssData['pmis_dollars'];
				break;
			case 'new':
				echo "							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>$dol</td>
							<td class='pres' style='text-align:right'>0</td>
							<td style='text-align:right'>$dol</td>
";
				$totals['new'] += $rowAssData['pmis_dollars'];
				$totals['total'] += $rowAssData['pmis_dollars'];
				break;
			case 'plan':
				echo "							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>0</td>
							<td class='pres' style='text-align:right'>$dol</td>
							<td style='text-align:right'>$dol</td>
";
				$totals['plan'] += $rowAssData['pmis_dollars'];
				$totals['total'] += $rowAssData['pmis_dollars'];
				break;
		}
		echo "						</tr>
";
	}// foreach($resAssData as $rowAssData)
	
	$sClass = $sClass == 'even' ? 'odd' : 'even';
	foreach ($totals as $k=>&$v){
		$v = addCommas($v);
	}
	unset($v);
	echo "						<tr class='$sClass'><td style='width:4em'></td>
							<td style=''></td>
							<td style=''></td>
							<td style='text-align:left;'><b>Totals</b></td>
							<td style=></td>
							<td style='width:4em'></td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['preserve']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['enhance']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['new']}</td>
							<td class='pres' style='width:4em;text-align:right;'>{$totals['plan']}</td>
							<td style='width:4em;text-align:right;'>{$totals['total']}</td>
						</tr>
					</tbody>
				</table>
";
	echo "				</tbody>
						";
						$sClass = $sClass == 'even' ? 'odd' : 'even';
	
		echo "			</table>
						<div class='tableDivider even'></div>
						<div class='tableDivider even'></div>
			</div>
	";
}
?>