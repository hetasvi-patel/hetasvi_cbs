<?php
include("classes/cls_invoice_gst_summary_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

if(!$canView) {
    if(!isset($_SESSION["sess_message"]) || $_SESSION["sess_message"]=="") {
        $_SESSION["sess_message"] = "You don't have permission to view this page.";
        $_SESSION["sess_message_cls"] = "danger";
        $_SESSION["sess_message_title"] = "Permission Denied";
        $_SESSION["sess_message_icon"] = "exclamation-triangle-fill";
    }
    header("Location: ".BASE_URL."dashboard.php");
    exit;
}
?>

<body class="hold-transition skin-blue layout-top-nav">
<?php include("include/body_open.php"); ?>
<div class="wrapper">
    <?php include("include/navigation.php"); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <section class="content-header"></section>
            <section class="content">
                <div class="box">
                    <div class="box-body">
                        <?php if(isset($_bll)) $_bll->pageSearch(); ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php include("include/footer.php"); ?>
</div>
<?php include("include/footer_includes.php"); ?>

<script>
jQuery(document).ready(function ($) {

    function parseDate(str) {
        if (!str) return null;
        var parts = str.split("/");
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    function setupInvoiceDateHeaderPicker() {
        var finYearStartStr = $("#search-invoice-date-from").val(); 
        var finYearStart = parseDate(finYearStartStr);

        var today = new Date(); 
        var setYear = finYearStart ? finYearStart.getFullYear() : today.getFullYear();
        var defaultDate = new Date(setYear, today.getMonth(), today.getDate());

        $("#search-invoice-date-from, #search-invoice-date-to").datepicker("destroy").datepicker({
            dateFormat: "dd/mm/yy",
            showButtonPanel: true,
            closeText: "Close",
            defaultDate: defaultDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "c-10:c+10"
        });
    }
    setupInvoiceDateHeaderPicker();
    $("#search-invoice-date-from").on("change", setupInvoiceDateHeaderPicker);

    // Only date filter, so no column date picker needed

    function getCurrentDateForFilename() {
        var d = new Date();
        return String(d.getDate()).padStart(2, "0") + "-" +
            String(d.getMonth() + 1).padStart(2, "0") + "-" +
            d.getFullYear();
    }

    var fileDate = getCurrentDateForFilename();

    function getSummaryTitle() {
        var fromDate = $("#search-invoice-date-from").val();
        var toDate = $("#search-invoice-date-to").val();

        function formatDateForTitle(dateStr) {
            if (!dateStr) return "";
            var parts = dateStr.split('/');
            if (parts.length === 3) {
                return parts[0] + "-" + parts[1] + "-" + parts[2];
            }
            return dateStr;
        }

        var displayFromDate = fromDate ? formatDateForTitle(fromDate) : "01-04-2025";
        var displayToDate = toDate ? formatDateForTitle(toDate) : "31-03-2026";

        return 'INVOICE GST SUMMARY FROM ' + displayFromDate + ' TO ' + displayToDate;
    }

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
                        title: getSummaryTitle(),
                        filename: function() {
                            return fileDate + "_Invoice_GST_Summary";
                        },
                        exportOptions: {
                            format: {
                                body: function (data, row, column, node) {
                                    if ([9,10,11,12,13,14,15,16,17,18].includes(column)) {
                                        let num = parseFloat(data.replace(/[^\d.-]/g, ''));
                                        return isNaN(num) ? data : num.toFixed(2);
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 },
            { "className": "dt-head-left dt-body-left", "targets": "_all" }
        ]
    });

    // Only date filter applies
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var from = $("#search-invoice-date-from").val();
            var to = $("#search-invoice-date-to").val();
            var invoiceDate = data[2];

            if (from || to) {
                var date = invoiceDate ? $.datepicker.parseDate("dd/mm/yy", invoiceDate) : null;
                var fromDate = from ? $.datepicker.parseDate("dd/mm/yy", from) : null;
                var toDate = to ? $.datepicker.parseDate("dd/mm/yy", to) : null;
                if (date) {
                    if (fromDate && date < fromDate) return false;
                    if (toDate && date > toDate) return false;
                }
            }
            return true;
        }
    );

    $("#search-date-btn").on("click", function () {
        table.draw();
    });

    // No other filters, so don't bind input/select events

});
<?php
if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
    echo "result=Swal.fire('".$_SESSION["sess_message_title"]."', '".$_SESSION["sess_message"]."', '".$_SESSION["sess_message_icon"]."');";
    echo "if (result.isConfirmed) {location.href='srh_invoice_gst_summary_master.php';}";
    unset($_SESSION["sess_message"]);
    unset($_SESSION["sess_message_cls"]);
    unset($_SESSION["sess_message_title"]);
    unset($_SESSION["sess_message_icon"]);
}
?>
</script>
<?php include("include/footer_close.php"); ?>