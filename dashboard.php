<?php
include("config/connection.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
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
          Dashboard
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
          if(isset($_SESSION["sess_message"]) && $_SESSION["sess_message"]!="") {
        ?>
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
            <?php
                   echo getMessageHTML();
            ?>
            </div>
            <!-- /.box-body -->
          </div>
        <!-- /.box -->
        <?php
          }
        ?>
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
    include("include/footer_close.php");
?>
