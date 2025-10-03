<?php
include("classes/cls_user_right_master.php");
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
          User Right Master
        </h1>
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">User Right Master</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <div style="margin-bottom:50px;">
            <button type="button" name="inputCreate" class="btn btn-info  pull-right" onclick="location.href='frm_user_right_master.php'">+ Add New</button>
        </div>
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
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
<script>
   jQuery(document).ready(function ($) {
    var table = $("#searchMaster").DataTable({
        colReorder: true,
        layout: {
            topStart: {
                buttons: [
                "colvis",
                 {
                    extend: "csvHtml5",
                    title: "user_right_master",
                     exportOptions: {
                        columns: ":visible"
                    }
                },
                {
                    extend: "excelHtml5",
                    title: "user_right_master",
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                },
                {
                    extend: "pdfHtml5",
                    title: "user_right_master",
                     exportOptions: {
                        columns: ":visible"
                    }
                },
                {
                    extend: "print",
                     exportOptions: {
                        columns: ":visible"
                    }
                }
            ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 }
        ]
    });
    $("#searchMaster thead input").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
        console.log(colIndex);
        table.column(colIndex).search(this.value).draw();
    });
});
</script>

<?php
    include("include/footer_close.php");
?>
