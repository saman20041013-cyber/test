<?php
if (isset($_POST['login'])) {
$user=$_POST['user'];
$pass=$_POST['pass'];
$myarr = array("saman" =>123 ,"sana"=>1234 );
foreach ($myarr as $k1 => $k2) {
if ($k1==$user && $k2==$pass) {
     header("Location: order.php?msg= successfully");
      exit();
}}}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="login.css">
  <title>login with saman&sana </title>
</head>

<body>
  <div class="saraki">
    <h2>login</h2>
    <form method="post" >
      <div class="input">
<label >Username:</label>
        <input type="text" name="user" placeholder="username" required>        
         <label>password:</label>
        <input type="password" name="pass" placeholder="password" required>
       
      </div>
      <div class="remeber">
        <label><input type="checkbox">remember me </label>
       
      </div>
      <button type="submit" name="login">login</button>
      <div class="rigister">
        <p> dont have any account </p>
      </div>

    </form>
  </div>

</body>
</html>