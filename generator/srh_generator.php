<?php
//generate srh page
$_strgeneratesrh.='<?php
include("classes/cls_'.str_replace("tbl_","",$_tablename).'.php");
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
          '.ucwords(str_replace("_"," ",str_replace("tbl_","",$_tablename))).'
        </h1>
      </section>
      <!-- Main content -->
      <section class="content">
      <?php
            if($canAdd) {
        ?>
        <div style="margin-bottom:50px;">
            <button type="button" name="inputCreate" class="btn btn-primary  pull-right text-white" onclick="location.href=\'frm_'.str_replace("tbl_","",$_tablename).'.php\'">+ Add New</button>
        </div>
        <?php
            }
        ?>
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
             <?php
                //echo getMessageHTML();
            ?>';
            if($_fieldno > 0) {
$_strgeneratesrh.='
            <?php
                if(isset($_bll))
                    $_bll->pageSearch(); 
            ?>';
            }
$_strgeneratesrh.='
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
?>';
 if($_fieldno > 0) {
$_strgeneratesrh.='
<script src="dist/js/srh_functions.js"></script>
<script>
   jQuery(document).ready(function ($) {
    initializeDataTable("'.str_replace("tbl_","",$_tablename).'","<?php echo $canExcel; ?>");
});
</script>
<?php
    frmAlert("'.str_replace("tbl_","srh_",$_tablename).'.php");
?>
';
 }
$_strgeneratesrh.='
<?php
    include("include/footer_close.php");
?>
';
    $handle = fopen("../".str_replace("tbl_","srh_",$_tablename).".php", "w");
    fwrite($handle,$_strgeneratesrh); 
    //end of srh page
?>