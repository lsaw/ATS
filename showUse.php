<?php
include 'loggy.php';
include_once 'helper.php';

logUser('show use');

echo <<<_END
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
		<title>Show Users</title>
	</head>
	<body>
_END;
echo loadUserData();
echo <<<_END
	</body>
</html>
_END;

exit;

?>