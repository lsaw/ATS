<?php //change_user_name.php

include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include_once 'validate_fields.php';// php validation code
logUser('change id/pass');

$username1 = $username2 = $password1 = $password2 = "";// initialize

if(!isset($_SESSION['uid']))
	session_start();

if(isset($_SESSION['siteLocalFolder']))
	{
		$folder = $_SESSION['siteLocalFolder'];
		include_once $folder . '/sitedata.php';
		include_once 'helperSite.php'; // can only be included AFTER siteLocalFolder.php
	}
	
// Top of document is same either way
echo getHeaderTextGeneral();

// If you have user data...
if (isset($_POST['change']))
{
	// sanitize for HTML entities
	if (isset($_POST['username1']))
		$username1 = sanitizeStringMySqli($mysqli, $_POST['username1']);
	if (isset($_POST['password1']))
		$password1 = sanitizeStringMySqli($mysqli, $_POST['password1']);
	if (isset($_POST['username2']))
		$username2 = sanitizeStringMySqli($mysqli, $_POST['username2']);
	if (isset($_POST['password2']))
		$password2 = sanitizeStringMySqli($mysqli, $_POST['password2']);

	// Validate input
	$fail = validateUP1IsUP2($username1, $username2, $password1, $password2);
	$fail .= validate_username($username1);
	$fail .= validate_password($password1);

	// New user name and password pass...
	if($fail == "")
	{

		$cryptpass = md5($salt1.$password1.$salt2);
		$uid = $_SESSION['uid'];
		$query = "UPDATE users SET pass= ? WHERE user=?";
		// Send the query
		$paramTypes = 'ss';
		$params = array(	$cryptpass,
							$uid);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);

		$query = "UPDATE users SET user= ? WHERE user=?";
		// Send the query
		$paramTypes = 'ss';
		$params = array(	$username1,
							$uid);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
		// username and password OK, so put up Manager's page of options
		// with new welcome
		echo loginTextTop();
		echo "<th>New user name/password successful. Welcome: $username1</th>
";
		echo loginTextBottom();
		exit;
	}// end: if($fail == "")
}// end: if (isset($_POST['change']))

// no user POST (first time through) or $fail is not empty
echo"		<title>ATS change username/password</title>";

echo <<<_END
	</head>
	<body style="text-align:center">
		<table class="login" cellspacing="5">
			<th colspan="2">Enter new name and password</th>
_END;
// only output this if user input failed for some reason ($fail not empty)
if (isset($_POST['change']))
{
echo <<<_END
			<tr align="center">
				<td colspan="2" style="color:red">Sorry, the following errors were found<br />
					in your form: <p style="color:red"><font size=2><i>$fail</i></font></p></td>
			</tr>
_END;
}
/*username requires 5 characters from:a-z, A-Z, 0-9.  - and _
Only characters a-z, A-Z, 0-9, - and _ allowed.<br/>
*/
echo <<<_END
			<form method="post" action="$_SERVER[SCRIPT_NAME]">
				<tr align="center">					
					<td colspan="2" style="color:blue">Username: at least 5 characters<br/>
													   from: a-z, A-Z, 0-9, and _-!@#%&*+=?<br/><br/>
													   Password: at least 6 characters<br/>
													   from: a-z, A-Z, 0-9, and _-!@#%&*+=?<br/>
													   requires one each from: a-z, A-Z and 0-9.<br/><br/>
													   To change password only, enter current<br/>
													   username and new password.</td>
				<tr align="center">
					<td>Username</td>
					<td><input type="text" maxlength="16" value="$username1" name="username1" /></td>
				</tr>
				<tr align="center">
					<td>Password</td>
					<td><input type="text" maxlength="12" value="$password1" name="password1" /></td>
				</tr>
				<tr align="center">
					<td colspan="2"><b>Verify new name and password</b></td>
				</tr>
				<tr align="center">
					<td>Username</td>
					<td><input type="text" maxlength="16" value="$username2" name="username2" /></td>
				</tr>
				<tr align="center">
					<td>Password</td>
					<td><input type="text" maxlength="12" value="$password2" name="password2" /></td>
				</tr>
				<tr align="center">
					<td colspan="2"><input type="submit" name="change" value="Change user name/password" /></td>
				</tr>
				<tr align="center">
					<td colspan="2"><button type="button" onclick="parent.location='login.php'">Return to login</button></td>
				</tr>
			</form>
		</table>
	</body>
</html>
_END;
?>