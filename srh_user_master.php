<?php
include("classes/cls_user_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
checkSrhPermission();
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          User Master
        </h1>
      </section>
      <!-- Main content -->
      <section class="content">
      <?php
            if($canAdd) {
        ?>
        <div style="margin-bottom:50px;">
            <button type="button" name="inputCreate" class="btn btn-primary  pull-right text-white" onclick="location.href='frm_user_master.php'">+ Add New</button>
        </div>
        <?php
            }
        ?>
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
             <?php
                //echo getMessageHTML();
            ?>
            <?php
                if(isset($_bll))
                    $_bll->pageSearch(); 
            ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- ./wrapper -->

<?php
    include("include/footer_includes.php");
?>
<script src="dist/js/srh_functions.js"></script>
<script>
   jQuery(document).ready(function ($) {
    initializeDataTable("user_master","<?php echo $canExcel; ?>");
});
</script>
<?php
    frmAlert("srh_user_master.php");
?>

<?php
    include("include/footer_close.php");
?>
