<?php
include_once("include/functions.php");
/* ADDED BY BHUMITA */
if(basename($_SERVER['PHP_SELF'])!="index.php") {
    if(!isset($_SESSION["sess_user_id"]) || $_SESSION["sess_user_id"]==0) {
        // If the user is not logged in, redirect to the login page
        echo "<script>location.href='".ENCODED_BASE_URL."'</script>";
        exit();
    } else {
        // Check if the user has permission to access the page
        checkPermissionVersion();
    }
} else {
    // If the user is already logged in, redirect to the dashboard
    if(isset($_SESSION["sess_user_id"]) && $_SESSION["sess_user_id"]>0) {
        echo "<script>location.href='".BASE_URL."dashboard.php'</script>";
        exit();
    }
}
$menu_label="";$company_name="";
if(basename($_SERVER['PHP_SELF'])=="index.php") {
    $menu_label="Login";
} else if(basename($_SERVER['PHP_SELF'])=="dashboard.php") {
    $menu_label="Dashboard";
} else {
    $menu_label=getCurrentMenuLabel();
}

$company_name=getCompanyField('company_name');

/* \ADDED BY BHUMITA */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $company_name." | ".$menu_label;?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">  
    
<!-- Bootstrap 5.33 -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
 
  <!-- jQuery UI CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css" integrity="sha512-TFee0335YRJoyiqz8hA8KV3P0tXa5CpRBSoM0Wnkn7JoJx1kaq1yXL/rb8YFpWXkMOjRcv5txv+C6UluttluCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- DataTables -->
  <!--<link rel="stylesheet" href="plugins/datatables/datatables.min.css">-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href=" https://cdn.datatables.net/2.2.2/css/dataTables.semanticui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.min.css">
       <link rel="stylesheet" href=" https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


