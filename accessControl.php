<?php // accessControl.php
// First check for current session by looking for 'uid'.  As it is possible to
//	end up here before a session has started, check for it first and start one
// if needed.  Then check for 'uid' as an indication that there is a session in
// progress.
include_once 'mySqli.php';
include_once 'helper.php';

date_default_timezone_set('America/New_York');

// initialize parameters
$uid = $pwd = $sessId = $level = "";

// if there is no 'uid', then there is no session in progress, so start one
if(!isset($_SESSION['uid']))
{
	session_start();
}

// Get session id
$sessId = session_id();

// make sure user has not timed out
if(isset($_SESSION['startTime']))
{
	// sending false allows manageSessionTime to end if there is a time out
//	manageSessionTime(false);
}

// it is possible to have just started a session (above) so check for 'uid' again
if(isset($_SESSION['uid']))
{ // we are here because we have a session in progress, which presumably means 
  // we are good to go.  But lets check sessionId anyway.  Also, if somehow we 
  // also have a POST, then that is a big problem, so force session quit and
  // re-login
	
	$uid = $_SESSION['uid'];
	$level = $_SESSION['level'];
//	LS 15/03/23 take this out to see if it fixes time out issues when you leave the site and come back in
//	if(!($sessId == $_SESSION['sessId']) || isset($_POST['uid']))
//	{// oops, somebody is trying to barge in by posting while a session is in progress
//		reDirectHome('timeOut.php');
//		exit;
//	}
	// need to reset the clock, or they only have 30 minutes regardles of use
	// now each time through accessControl() time resets.
	$_SESSION['startTime'] = time();
}
// if session'uid' and post'uid' exist, see above, so will only enter here if no sess'uid'
// note that if 'uid' is posted so is 'pwd' and 'navPlat'
elseif(isset($_POST['uid']))
{
//	$uid = sanitizeStringMySql($_POST['uid']);
//	$pwd = sanitizeStringMySql($_POST['pwd']);
$uid = $_POST['uid'];
$pwd = $_POST['pwd'];
	// encrypt the password
	$cryptpass = md5($salt1.$pwd.$salt2);

	$query = "SELECT * FROM users WHERE user=? AND pass=?";
	$paramTypes = 'ss';
	$params = array($uid, $cryptpass);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

	// If row exists, good login, so get the sitedata file name from database
	// If row exists then have good 'uid'/'pwd' combo.  Now check for sessId
	// If we already have seessId then duplicate login, only one at a time allowed
	if (!empty($result) && '' != $result[0]['user'] )// Login OK
	{
		$level = $result[0]['level'];
		// add sessionId to database
		$query = "UPDATE users SET sessId='$sessId' WHERE user='$uid'";
		if(!queryMysqli($mysqli, $query)) die ("Database not available at this time:" . mysql_error());
		
		$_SESSION['uid'] 				= $uid;
		$_SESSION['startTime'] 			= time();
		$_SESSION['level']	 			= $result[0]['level'];
		$_SESSION['sessId']				= $sessId;
		
		setcookie('USNPSATS',$sessId,time() + 86400,'/');
	}
	else
	{
	// Kill $_SESSION
	$_SESSION = array();
	
	// output access denied page which gives the user the option of
	// going back to login or to home page
	echo getHeaderTextGeneral();
	echo <<<_END
		<title>Access Denied</title>
	</head>
	<body style="text-align:center">
		<table class="login" cellspacing="5">
			<th colspan="2" style="color:red">Access Denied</th>
	    	 <tr>
	    	 	<td>Your user ID/password is incorrect.</td>
			</tr>
			<tr>
				<td>To try logging in again, click <a style="color:blue" href="login.php">here</a>.</td>
			</tr>
		</table>
	</body>
</html>
_END;
  exit;
	}
}
else 
{
// have not logged in yet
// This outputs the login page	
	echo getHeaderTextGeneral();
	echo "		<title>ATS Management Login Page</title>";
echo <<<_END
	</head>
	<body style="text-align:center">
		<table class="login" cellspacing="5">
			<th colspan="2">Please login</th>
			<form method="post" action="$_SERVER[SCRIPT_NAME]">
		    	 <tr>
		     		<td>Username</td>
		     		<td><input type="text" maxlength="16" name="uid" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" maxlength="12" name="pwd" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="submit" value="Log in" /></td>
				</tr>
			</form>
		</table>
	</body>
</html>
_END;

  exit;
}
?>