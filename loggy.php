<?php
require_once 'authorize.php';
require_once 'mySqli.php';
require_once 'helper.php';

session_start();
$r = session_id();
$ok = 5;
$auth = new Auth();

if ( isset($_SESSION['uid']) ) {
	$ok = 0;
} elseif(isset($_POST['uid'])){
	$ok = $auth->login($_POST['uid'], $_POST['pwd']);
	$level = $auth->level();
}

if(0 == $ok){
	//Check we have the right user
	$logged_in = $auth->checkSession();

	if(empty($logged_in)){
		//Bad session, ask to login
		$auth->logout();
		header( 'Location: logout.php' );

	} else {
		$level = $auth->level();
		//User is logged in, show the page
		$ok = 0;
	}

}else {
	$message = 'Please try again'; // failure from previous attempt
	if(5 == $ok)
		$message = 'Please login';
	session_regenerate_id(true);
	echo getHeaderTextGeneral();
	echo "		<title>ATS Management Login Page</title>";
	echo <<<_END
	</head>
	<body style="text-align:center">
		<table class="login" cellspacing="5">
			<th colspan="2">$message</th>
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