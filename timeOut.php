<?php //timeOut.php
include_once 'helper.php';
include_once 'mySqli.php';

if(!isset($_SESSION['uid']))
	session_start();
logUser('timeOut');

if(isset($_SESSION['siteLocalFolder']))
	{
		$folder = $_SESSION['siteLocalFolder'];
		include_once $folder . '/sitedata.php';
		include_once 'helperSite.php'; // can only be included AFTER siteLocalFolder.php
	}

destroy_session_and_data_mysqli($mysqli);

echo getHeaderTextGeneral();
echo <<<_END
		<title>Session Timeout</title>
	</head>
	<body style="text-align:center">
		<table class="login" cellspacing="5">
			<th colspan="2" style="color:red">Session has timed out</th>
			<tr>
				<td>Please log in again to continue.</td>
			</tr>
			<tr>
				<td>To try logging in again, click <a style="color:blue" href="login.php">here</a>.</td>
			</tr>
			<tr>
				<td>To return to the Home page, click <a style="color:blue" href="$siteHomePageURI">here</a>.</td>
			</tr>
		</table>
	</body>
</html>
_END
?>