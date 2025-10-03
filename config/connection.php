<?php
require __DIR__ . '/../vendor/autoload.php'; // ADDED BY BHUMITA
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ADDED BY BHUMITA */
/* GET COMPANY ID */
use Hashids\Hashids;

$hashids = new Hashids('',6);


$encodedId = $_GET['company_id'] ?? null;
if ($encodedId) { 
    $decoded = $hashids->decode($encodedId);
    if (!empty($decoded)) {
        $_SESSION["sess_encoded_company_id"]=$encodedId; 
        $realId = $decoded[0];
        $_SESSION["sess_company_id"]=$realId;
    } 
}else {
   /*$encoded = $hashids->encode(3); 
    echo "Try this: <a href='http://cbs5-pc/csms2/$encoded'>http://cbs5-pc/csms2/$encoded</a>";
    exit;*/
} 
/* \GET COMPANY ID */
/* \ADDED BY BHUMITA */

global $database_name;
global $_dbh;   
$servername = "localhost";
$username = "root";
//$password = "Root@1234";
$password = "";
$database_name="csms2";
date_default_timezone_set("Asia/Kolkata");
try {
  
  $_dbh = new PDO("mysql:host=$servername;dbname=".$database_name, $username, $password);
  // set the PDO error mode to exception
  $_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
include("variables.php"); // ADDED BY BHUMITA
?> 
