<?php
// $rowName = name for row
// $id = id base name so id becomes id$idSpring 
// $field = field from database
// $name = name of input
// $data = query result
// $sp, $su, $fa, $wi = index of seasonal values in $data
// $showTotal = true means show total at end
function makeAnnPerfRowSeas($rowName, $id, $field, $name, $data, $sp, $su, $fa, $wi, $showTotal, $displayComma=false, $places=2){
	
	$txt = '';
	$val=array();
	$tot = '';
	if('' != $field){// for '' use any field that is non-numeric
		$tot = $data[$sp][$field] + $data[$su][$field] + $data[$fa][$field] + $data[$wi][$field];
	}
	if($displayComma){
		$val['spring']=	'' == $data[$sp][$field] ? 0 : number_format($data[$sp][$field],$places);
		$val['summer']=	'' == $data[$su][$field] ? 0 : number_format($data[$su][$field],$places);
		$val['fall']=	'' == $data[$fa][$field] ? 0 : number_format($data[$fa][$field],$places);
		$val['winter']=	'' == $data[$wi][$field] ? 0 : number_format($data[$wi][$field],$places);
		$val['tot']=	'' == $tot ? 0 : number_format($tot,$places);
	}else{
		$val['spring']=$data[$sp][$field];
		$val['summer']=$data[$su][$field];
		$val['fall']=$data[$fa][$field];
		$val['winter']=$data[$wi][$field];
		$val['tot']=$tot;
	}
	$txt .= "					<tr>
						<td style='text-align:right'>$rowName</td>
";
	if($showTotal){
		$txt .= "						<td style='text-align:right'><input class='$id' onkeyup='updateTotal(this)' type='text' id='id" . $id . "Spring' style='width:100%;text-align:right;' value='{$val['spring']}' name='" . $name . "Spring'></td>
						<td style='text-align:right'><input class='$id' onkeyup='updateTotal(this)' type='text' id='id" . $id. "Summer' style='width:100%;text-align:right;' value='{$val['summer']}' name='" . $name . "Summer'></td>
						<td style='text-align:right'><input class='$id' onkeyup='updateTotal(this)' type='text' id='id" . $id . "Fall' style='width:100%;text-align:right;' value='{$val['fall']}' name='" . $name . "Fall'></td>
						<td style='text-align:right'><input class='$id' onkeyup='updateTotal(this)' type='text' id='id" . $id . "Winter' style='width:100%;text-align:right;' value='{$val['winter']}' name='" . $name . "Winter'></td>
						<td style='text-align:right'><span id='id" . $id . "Total' style='width:100%;text-align:right;' name='" . $name . "Total'>{$val['tot']}</span></td>
";
	}else{
		$txt .= "						<td style='text-align:right'><input onkeyup='editFunction(this)' type='text' id='id" . $id . "Spring' style='width:100%;text-align:right;' value='{$val['spring']}' name='" . $name . "Spring'></td>
						<td style='text-align:right'><input onkeyup='editFunction(this)' type='text' id='id" . $id. "Summer' style='width:100%;text-align:right;' value='{$val['summer']}' name='" . $name . "Summer'></td>
						<td style='text-align:right'><input onkeyup='editFunction(this)' type='text' id='id" . $id . "Fall' style='width:100%;text-align:right;' value='{$val['fall']}' name='" . $name . "Fall'></td>
						<td style='text-align:right'><input onkeyup='editFunction(this)' type='text' id='id" . $id . "Winter' style='width:100%;text-align:right;' value='{$val['winter']}' name='" . $name . "Winter'></td>
";
	}
	$txt .= "					</tr>
";
	return $txt;
}
function makeAnnPerfRowAnn($rowName, $id, $field, $name, $data){

	$txt = '';
	if('partner_percent' == $field){
		$val = $data[0][$field] * 100;
	}else{
		$val = $data[0][$field];
	}
	$txt .= "					<tr>
						<td style='text-align:right'>$rowName</td>
						<td style='text-align:right'><input onkeyup='editFunction(this)' type='text' id='id" . $id . "' style='width:100%;text-align:right;' value='$val' name='" . $name . "'></td>
					</tr>
";
	return $txt;
}
// sets array data to null
// data of the form $data[$i][field]
function setDataToNull(&$data, $i, $fields){
	foreach ($fields as $field){
		$data[$i][$field] = '';
	}
}

?>