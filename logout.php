<?php
include_once("config/connection.php");
$company_id= ($_SESSION["sess_encoded_company_id"]) ? $_SESSION["sess_encoded_company_id"] : "";
unset($_SESSION['sess_user_id']);
unset($_SESSION['sess_person_name']);
unset($_SESSION['sess_company_year_id']); 
unset($_SESSION['sess_selected_year']);
unset($_SESSION["sess_encoded_company_id"]);
session_destroy();
echo "<script>location.href='".ENCODED_BASE_URL."'</script>";
exit;
?>