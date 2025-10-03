<?php
include("classes/cls_item_stock_statement_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
if(!$canView) {
    if(!isset($_SESSION["sess_message"]) || $_SESSION["sess_message"]=="") {
        $_SESSION["sess_message"]="You don't have permission to view countries.";
        $_SESSION["sess_message_cls"]="danger";
        $_SESSION["sess_message_title"]="Permission Denied";
        $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
    }
    header("Location: ".BASE_URL."dashboard.php");
    exit;
}
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <section class="content-header"></section>
      <section class="content">
        <div class="box">
            <div class="box-body">
            <?php
                if(isset($_bll))
                    $_bll->pageSearch(); 
            ?>
            </div>
          </div>
      </section>
    </div>
  </div>
  <?php
    include("include/footer.php");
?>
</div>

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
                    "colvis"
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 },
            { "className": "dt-head-left dt-body-left", "targets": [0,1,2,3,4,5] }
        ]
    });

    $("#till_date").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true
    });

    // Dropdown filters for Item Name and Unit Name (column 0 and 1)
    $('#item_filter').on('change', function () {
        var val = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
        table.column(0).search(val, true, false).draw();
    });
    $('#unit_filter').on('change', function () {
        var val = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
        table.column(1).search(val, true, false).draw();
    });
});
</script>
<?php
    if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
        echo "<script>result=Swal.fire('".$_SESSION["sess_message_title"]."', '".$_SESSION["sess_message"]."', '".$_SESSION["sess_message_icon"]."');if (result.isConfirmed) {location.href='srh_item_stock_statement_master.php';}</script>";
        unset($_SESSION["sess_message"]);
        unset($_SESSION["sess_message_cls"]);
        unset($_SESSION["sess_message_title"]);
        unset($_SESSION["sess_message_icon"]);
    }
?>
<?php
    include("include/footer_close.php");
?>