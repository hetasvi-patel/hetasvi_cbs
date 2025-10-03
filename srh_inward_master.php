<?php
include("classes/cls_inward_master.php");
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
          Inward Master
        </h1>
      </section>
      <!-- Main content -->
      <section class="content">
      <?php
            if($canAdd) {
        ?>
        <div style="margin-bottom:50px;">
            <button type="button" name="inputCreate" class="btn btn-primary  pull-right text-white" onclick="location.href='frm_inward_master.php'">+ Add New</button>
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
<script>
   jQuery(document).ready(function ($) {
    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        layout: {
            topStart: {
                buttons: [
                "colvis",
                <?php if($canExcel) { ?>
                {
                    extend: "excelHtml5",
                    title: "inward_master",
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                },
                <?php } ?>
                {
                    extend: "pdfHtml5",
                    title: "inward_master",
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                },
                {
                    extend: "print",
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                }
            ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 }
        ]
    });
    $(".date-filter").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close",
        beforeShow: function (input) {
            setTimeout(function () {
            var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
            if (buttonPane.find(".ui-datepicker-clear").length === 0) {
                $("<button>", {
                    text: "Clear",
                    class: "ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all",
                    click: function () {
                        $(input).val("").datepicker("hide");
                        const colIndex = $(input).attr("data-index"); 
                        table.column(colIndex).search("").draw();
                    }
                }).appendTo(buttonPane);
            }
            }, 1);
        },
        onSelect: function (dateText) {
            let colIndex = $(this).attr("data-index");
            table.column(colIndex).search(dateText).draw(); // adjust index if needed
        }
    });
    $("#search-filters input, #search-filters select").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
        table.column(colIndex).search(this.value).draw();
    });
});
</script>

<?php  
/* MODIFIED BY HETANSHREE FOREIGNKEY DELETE ERROR MESSAGE*/
 if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
    $msg_title = json_encode($_SESSION["sess_message_title"]);
    $msg_text = json_encode($_SESSION["sess_message"]);
    $msg_icon = json_encode($_SESSION["sess_message_icon"]);
    echo "<script>
        var result = Swal.fire($msg_title, $msg_text, $msg_icon);
        if (result.isConfirmed) {location.href='srh_inward_master.php';}
    </script>";
    unset($_SESSION["sess_message"]);
    unset($_SESSION["sess_message_cls"]);
    unset($_SESSION["sess_message_title"]);
    unset($_SESSION["sess_message_icon"]);
} 
/* \ MODIFIED BY HETANSHREE FOREIGNKEY DELETE ERROR MESSAGE*/
?>
<?php
    include("include/footer_close.php");
?>
