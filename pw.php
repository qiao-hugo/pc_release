<?php
$salt=mb_substr('mayonghu', 0, 2,'utf-8');
$salt = '$1$'.str_pad($salt,9,'0');
echo crypt('123456', $salt);


$db=mysql_connect('localhost:33307','root',123456);
mysql_select_db('vtigercrm600',$db);
$result=mysql_query("SELECT * FROM `vtiger_users` limit 1");
//$row=mysql_fetch_row($result);
/*while($row = mysql_fetch_array($result))
  {
  echo "<tr>";
  echo "<td>" . $row['FirstName'] . "</td>";
  echo "<td>" . $row['LastName'] . "</td>";
  echo "</tr>";
  }
  */
print_r($result);