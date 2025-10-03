<?php
include("classes/cls_invoice_summary_master.php");
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
      <section class="content-header">
      </section>
      <section class="content">
        <div class="box">
            <div class="box-body">
             <?php
            ?>
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
    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    function getFinancialYearStartYear() {
        // Try to find FY label (e.g. "FY 2027-2028") in the DOM. Adjust selector as needed!
        var fyText = $("body").text().match(/FY\s*(\d{4})-(\d{4})/i);
        if (fyText && fyText[1]) {
            return parseInt(fyText[1], 10);
        }
        // fallback to current year
        return (new Date()).getFullYear();
    }

    function getSummaryTitle() {
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();

        function formatDateForTitle(dateStr) {
            if (!dateStr) return "";
            var parts = dateStr.split('/');
            if (parts.length === 3) {
                return parts[0] + "-" + parts[1] + "-" + parts[2];
            }
            return dateStr;
        }

        var displayFromDate = fromDate ? formatDateForTitle(fromDate) : "01-04-" + getFinancialYearStartYear();
        var displayToDate = toDate ? formatDateForTitle(toDate) : "31-03-" + (getFinancialYearStartYear() + 1);

        return 'INVOICE SUMMARY FROM ' + displayFromDate + ' TO ' + displayToDate;
    }

    var fileDate = getCurrentDateForFilename();
    var currentDate = new Date().toLocaleDateString('en-GB');
    var companyTitle = 'Cold Storage';
    var summaryTitle = getSummaryTitle();

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        layout: {
            topStart: {
                buttons: [
                    "colvis",
                    <?php if($canExcel) { ?>
                    {
                        extend: "print",
                        title: "",
                        customize: function (win) {
                            $(win.document.body).find('h1').remove();
                            $(win.document.body).prepend(
                                '<div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">' +
                                    '<div style="font-size:12px;text-align:left;width:33%;">' + currentDate + '</div>' +
                                    '<div style="font-size:16px;font-weight:bold;text-align:center;width:34%;">' + companyTitle + '</div>' +
                                    '<div style="font-size:12px;text-align:right;width:33%;">Page 1 of 1</div>' +
                                '</div>' +
                                '<hr style="border:0;border-top:2px solid #000;margin:0 0 2px 0;">' +
                                '<div style="text-align:center;font-weight:bold;font-size:15px;margin:8px 0 0 0;">' + getSummaryTitle() + '</div>' +
                                '<hr style="border:0;border-top:5px solid #000;margin:2px 0 6px 0;">'
                            );
                        }
                    },
                    <?php } ?>
                    {
                        extend: "pdfHtml5",
                        title: "",
                        filename: fileDate + "_invoice_summary",
                        customize: function (doc) {
                            doc.content.splice(0, 0, {
                                margin: [0, 0, 0, 0],
                                columns: [
                                    { text: currentDate, alignment: 'left', fontSize: 10 },
                                    { text: companyTitle, alignment: 'center', fontSize: 14, bold: true },
                                    { text: 'Page 1 of 1', alignment: 'right', fontSize: 10 }
                                ]
                            });
                            doc.content.splice(1, 0, {
                                canvas: [
                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                ],
                                margin: [0, 0, 0, 2]
                            });
                            doc.content.splice(2, 0, {
                                text: getSummaryTitle(),
                                style: 'header',
                                alignment: 'center',
                                margin: [0, 5, 0, 0],
                                bold: true,
                                fontSize: 12
                            });
                            doc.content.splice(3, 0, {
                                canvas: [
                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                ],
                                margin: [0, 0, 0, 8]
                            });
                            var tableNode = null;
                            for (var i = 0; i < doc.content.length; i++) {
                                if (doc.content[i].table !== undefined) {
                                    tableNode = doc.content[i];
                                    break;
                                }
                            }
                            if (tableNode) {
                                var colLen = tableNode.table.body[0].length;
                                tableNode.table.widths = Array(colLen).fill('*');
                            }
                        }
                    },
                    {
                        extend: "excelHtml5",
                        title: companyTitle + ' ' + getSummaryTitle(),
                        filename: fileDate + "_invoice_summary",
                        exportOptions: {
                            columns: ":visible",
                            autoFilter: true,
                        }
                    }
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 },
            { "className": "dt-head-left dt-body-left", "targets": [0,1,2,3,4,5] }
        ]
    });

    // --- Datepicker with FY-default-year logic ---
    function setupInvoiceDatePickers() {
        var fyYear = getFinancialYearStartYear();
        var today = new Date();
        
        $("#from_date, #to_date").each(function() {
            var val = $(this).val();
            var setYear = fyYear;
            if (val && /^\d{2}\/\d{2}\/\d{4}$/.test(val)) {
                var parts = val.split("/");
                setYear = parseInt(parts[2], 10);
            }
            var defaultDate = new Date(setYear, today.getMonth(), today.getDate());
            $(this).datepicker("destroy").datepicker({
                dateFormat: "dd/mm/yy",
                showButtonPanel: true,
                closeText: "Close",
                defaultDate: defaultDate,
                changeMonth: true,
                changeYear: true,
                yearRange: "c-10:c+10",
                beforeShow: function (input) {
                    setTimeout(function () {
                        var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
                        if (buttonPane.find(".ui-datepicker-clear").length === 0) {
                            $("<button>", {
                                text: "Clear",
                                class: "ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all",
                                click: function () {
                                    $(input).val("").datepicker("hide");
                                }
                            }).appendTo(buttonPane);
                        }
                    }, 1);
                }
            });
        });
    }
    
    // Call once on load
    setupInvoiceDatePickers();

    // Auto-submit form on dropdown change (for filtering without button click)
    $('select[name="invoice_type"], select[name="invoice_for"], select[name="customer"], select[name="item"], select[name="storage_duration"], select[name="per"]').on('change', function() {
        $('#invoice-summary-form').submit();
    });

    // Update the title when date filters change (no auto-draw since server-side)
    $("#from_date, #to_date").on("change", function() {
        table.buttons().container().find('.buttons-excel').attr('title', companyTitle + ' ' + getSummaryTitle());
    });
});
</script>
<?php
    if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
        echo "<script>result=Swal.fire('".$_SESSION["sess_message_title"]."', '".$_SESSION["sess_message"]."', '".$_SESSION["sess_message_icon"]."');if (result.isConfirmed) {location.href='srh_invoice_summary_master.php';}</script>";
        unset($_SESSION["sess_message"]);
        unset($_SESSION["sess_message_cls"]);
        unset($_SESSION["sess_message_title"]);
        unset($_SESSION["sess_message_icon"]);
    }
?>
<?php
    include("include/footer_close.php");