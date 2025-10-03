<?php
include("classes/cls_item_stock_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
if (!$canView) {
    if (!isset($_SESSION["sess_message"]) || $_SESSION["sess_message"] == "") {
        $_SESSION["sess_message"] = "You don't have permission to view this page.";
        $_SESSION["sess_message_cls"] = "danger";
        $_SESSION["sess_message_title"] = "Permission Denied";
        $_SESSION["sess_message_icon"] = "exclamation-triangle-fill";
    }
    header("Location: " . BASE_URL . "dashboard.php");
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

      <section class="content">
        <div class="box">
            <div class="box-body">
            <?php
            if (isset($_bll))
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
    // Filter input boxes removed, so no related JS needed
    // Date filter also removed, so datepicker and filter handlers not needed

    // Function to get current date for filename and display
    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    var fileDate = getCurrentDateForFilename();
    var companyTitle = 'Cold Storage';

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        pageLength: 50,
        layout: {
            topStart: {
                buttons: [
                    "colvis",
                    {
                        extend: "excelHtml5",
                        title: companyTitle + ' ITEM STOCK FOR ALL DATE',
                        filename: fileDate + "_item_stock",
                        exportOptions: {
                            columns: ':visible',
                            autoFilter: true,
                        }
                    }
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": [0, 2] },
            { "className": "dt-head-left dt-body-left", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] }
        ],
        columns: [
            { data: 'inward_no', defaultContent: '' },
            { data: 'inward_date', defaultContent: '' },
            { data: 'customer', defaultContent: '' },
            { data: 'broker', defaultContent: '' },
            { data: 'lot_no', defaultContent: '' },
            { data: 'item', defaultContent: '' },
            { data: 'marko', defaultContent: '' },
            { data: 'unit', defaultContent: '' },
            { data: 'inward_qty', defaultContent: '' },
            { data: 'inward_wt', defaultContent: '' },
            { data: 'stock_qty', defaultContent: '0' },
            { data: 'stock_wt', defaultContent: '0' },
            { data: 'location', defaultContent: '' },
            { data: 'storage_duration', defaultContent: '' },
            { data: 'rent', defaultContent: '' },
            { data: 'per', defaultContent: '' }
        ],
        error: function (xhr, error, code) {
            console.error('DataTables error:', error, code);
        }
    });
});
</script>
<?php
if (isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"] != "") {
    echo "<script>result=Swal.fire('" . $_SESSION["sess_message_title"] . "', '" . $_SESSION["sess_message"] . "', '" . $_SESSION["sess_message_icon"] . "');if (result.isConfirmed) {location.href='srh_item_stock_master.php';}</script>";
    unset($_SESSION["sess_message"]);
    unset($_SESSION["sess_message_cls"]);
    unset($_SESSION["sess_message_title"]);
    unset($_SESSION["sess_message_icon"]);
}
?>
<?php
include("include/footer_close.php");
?>