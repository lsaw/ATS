<?php // signup.php

// This example has been slightly modified from the one in the book
// to correct a bug that could occur when creating an account

include_once 'loggy.php';
include_once 'validate_fields.php';
include_once 'helper.php';

if (isset($_POST['logout']))
{	
	reDirectHome('logout.php');
	exit;
}
if (isset($_POST['admin']))
{
	reDirectHome('index.htm');
	exit;
}
logUser('sign up');

echo getHeaderTextGeneral();
echo "<title>New User Signup</title>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>
</head><body style='text-align:center'><font face='verdana' size='2'>";

echo <<<_END
<script>
function checkUser(user)
{
	if (user.value == '')
	{
		document.getElementById('info').innerHTML = ''
		return
	}
	
	$.ajax({
   type: "POST",
   url: "checkuser.php",
   data: "user=" + user.value,
   success: function(result){
	document.getElementById('info').innerHTML = result
   }
	});
}
</script>
_END;

$error = $user = $pass = $fail = "";
//if (isset($_SESSION['user'])) destroySession();

if (isset($_POST['user']))
{
	$user = sanitizeStringMySqli($mysqli, $_POST['user']);
	$pass = sanitizeStringMySqli($mysqli, $_POST['pass']);
	$level = '' == sanitizeStringMySqli($mysqli, $_POST['level']) ? 3 : sanitizeStringMySqli($mysqli, $_POST['level']);
//	$cryptpass = md5($salt1.$pass.$salt2);
//	$cryptuser = md5($salt1.$user.$salt2);
	
	// Validate input
	$fail = validate_username($user);
	$fail .= validate_password($pass);
	
	// New user name and password pass...
	if($fail == "")
	{
		if ($user == "" || $pass == "")
		{
			$fail .= "Not all fields were entered<br />";
		}
		else
		{
			$query = "SELECT * FROM users WHERE user='$user'";
			$res = queryMysqli($mysqli, $query);
			if ($res->num_rows)
			{
				$fail .= "That username already exists<br />";
			}
			else
			{
				$success = $auth->createUser($user, $pass,$level);
				if($success){
					echo "<h4>Account created</h4>";
				}else{
					echo "<h4>Account not created</h4>";
				}
			}
		}
	}
}

echo <<<_END

		<table class="login" cellspacing="5" width="500px">
			<th colspan="2">Create new user</th>
_END;

// only output this if user input failed for some reason ($fail not empty)
if ($fail)
{
echo <<<_END
			<tr align="center">
				<td colspan="2" style="color:red">Sorry, the following errors were found<br />
					in your form: <p style="color:red"><font size=2><i>$fail</i></font></p></td>
			</tr>
_END;
}

echo <<<_END
			<form method="post" action="$_SERVER[SCRIPT_NAME]">$error
				<tr>
					<td align="left" width="150">Username</td>
		     		<td align="left" width="350"><input type="text" maxlength="16" name="user"  value='$user'
					onBlur='checkUser(this)'/><span id='info'></span></td>
				</tr>
				<tr>
					<td align="left" width="150">Password</td>
					<td align="left" width="350"><input type="password" maxlength="16" name="pass" /></td>
				</tr>
				<tr>
					<td align="left" width="150">Security Level</td>
					<td align="left" width="350"><input type="text" maxlength="16" name="level" /></td>
				</tr>
				<tr>
					<td></td>
					<td align="left"><input type="submit" value="Sign up" />
					    <input type="submit" name="logout" value="Log out" />
					    <input type="submit" name="admin" value="Admin" /></td>
				</tr>
			</form>
		</table>
	</body>
</html>
_END;
?>
