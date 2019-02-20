<?php

//	$t = array('../images/test1.jpg' => 'test1', '../images/test2.jpg'=> 'test2');
$t = array
(
array
(
  "user" => "user",
  "time" => "time",
  "where" => "where"
),
array
(
  "user" => "bob",
  "time" => "211111",
  "where" => "login"
)
);
file_put_contents('userLog.txt',serialize($t));
echo "done";

?>