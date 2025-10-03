<?php
include("config/connection.php");
include("include/header.php");
include("include/theme_styles.php");
 include("include/header_close.php");
?>
<?php
if (isset($_POST["btn_login"])) {
    /** ADDED BY BHUMITA */
    $columns="user_id,person_name,permission_version";
    $login_id="";$login_pass="";
    if(isset($_POST["login_id"]) && !empty($_POST["login_id"])) {
        $login_id = $_POST["login_id"];
    }
    if(isset($_POST["login_pass"]) && !empty($_POST["login_pass"])) {
        $login_pass = $_POST["login_pass"];
    }
    $where = " and login_id = '".$login_id."' AND login_pass = '".$login_pass."' AND status = '1' AND company_id = '".COMPANY_ID."'";
    $stmt = $_dbh->prepare("CALL csms_search_detail(:columns,:tablename, :where)");
    $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
    $stmt->bindParam(':tablename', $tbl_user_master, PDO::PARAM_STR);
    $stmt->bindParam(':where', $where, PDO::PARAM_STR);
    $stmt->execute();
    /** \ADDED BY BHUMITA */
    
    if ($stmt->rowCount() > 0) {
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION["sess_user_id"] = $res["user_id"];
        $_SESSION["sess_person_name"] = $res["person_name"];
        $_SESSION["sess_permission_version"] = $res["permission_version"];

        echo "<script>location.href='".BASE_URL."dashboard.php'</script>";
        exit();
    } else {
        echo "<script>location.href='".ENCODED_BASE_URL."?err=1'</script>";
        exit();
    }
}
?>
<body class="hold-transition login-page ">
<?php
    include("include/body_open.php");
?>
<div class="login-box">
 <div class="login-logo">
    <a href="dashboard.php"><b>Cold</b>Storage</a>
</div>

  <!-- /.login-logo -->
  <div class="login-box-body">

      <p class="login-box-msg">Log in to start your session</p>
    <form method="post">
     <div class="form-group has-feedback">
        <input type="text" name="login_id" class="form-control" placeholder="Username">
         <i class="bi bi-person-circle"></i>
    </div>
    <div class="form-group has-feedback">
        <input type="password" name="login_pass" class="form-control" placeholder="Password">
        <i class="bi bi-lock-fill"></i>
    </div>  
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" name="btn_login" class="btn btn-primary btn-block btn-flat">Log In</button>
        </div>
        <!-- /.col -->
      </div>
        <?php if(isset($_REQUEST["err"]) && $_REQUEST["err"]==1) { ?>
      <div class="error mt-3 text-danger text-center">Invalid login ID or password.</div>
      <?php } ?>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("input[type='text']").focus();
    });
    document.documentElement.style.overflow = 'hidden'; 
</script>
<?php
    include("include/footer_close.php");
?>

