<?php 
require_once ('dbconnect.php'); 
$email='test@exemple.com';
$password=password_hash('123456' , PASSWORD_DEFAULT); 
$roles= json_encode(['ROLE_USER']); 
$stmt =$pdo-> prepare( "INSERT INTO users (email , password , roles ) VALUES (? , ? ,?) "); 
$stmt -> execute([$email , $password , $roles]); 
echo "Utilisateurs ajouté."; 
?> 
