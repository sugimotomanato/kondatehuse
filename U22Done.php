<?php 
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);
$email = htmlspecialchars($_POST['email'], ENT_QUOTES);

echo "<p>",$name,"</>";
echo "<p>",$password,"</>";
echo "<p>",$email,"</>";

?>
