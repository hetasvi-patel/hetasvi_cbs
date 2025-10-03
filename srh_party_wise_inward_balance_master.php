<?php
include("classes/cls_party_wise_inward_balance_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
if (isset($_GET['preview'])) {
    $_bll->pagePreview();
    exit;
}
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php include("include/body_open.php"); ?>
<div class="wrapper">
<?php include("include/navigation.php"); ?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <section class="content">
        <div class="box">
            <div class="box-body">
            <?php
                if(isset($_SESSION["sess_message"]) && $_SESSION["sess_message"]!="") {
                    echo '<div class="alert '.$_SESSION["sess_message_cls"].' alert-dismissible fade show" role="alert">';
                    echo $_SESSION["sess_message"];
                    echo '<button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">&times;</button>';
                    echo '</div>';
                    $_SESSION["sess_message"]="";
                    $_SESSION["sess_message_cls"]="";
                    unset($_SESSION["sess_message"]);
                    unset($_SESSION["sess_message_cls"]);
                }
            ?>
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
        if (parts.length !== 3) return null;
        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // Months are 0-based
        var year = parseInt(parts[2], 10);
        if (isNaN(day) || isNaN(month) || isNaN(year) || day < 1 || day > 31 || month < 0 || month > 11 || year < 1 || year > 9999) {
            return null;
        }
        var d = new Date(year, month, day);
        if (d.getDate() !== day || d.getMonth() !== month || d.getFullYear() !== year) {
            return null; // Invalid date (e.g., 99/99/9999)
        }
        return d; // Returns Date object with time set to 00:00:00
    }

    function getFinancialYearStartYear() {
        var fyText = $("body").text().match(/FY\s*(\d{4})-(\d{4})/i);
        if (fyText && fyText[1]) {
            return parseInt(fyText[1], 10);
        }
        return (new Date()).getFullYear();
    }

    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    var fileDate = getCurrentDateForFilename();
    var currentDate = new Date().toLocaleDateString('en-GB');
    var companyTitle = 'Cold Storage';
    var summaryTitle = 'INWARD SUMMARY';

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        pageLength: 50,
        language: { emptyTable: "No data available in table" },
        layout: {
            topStart: {
                buttons: [
                    "colvis",
                    {
                        extend: "print",
                        title: "",
                        exportOptions: { columns: ':visible:not(:first-child)' },
                        customize: function (win) {
                            $(win.document.body).find('h1').remove();
                            $(win.document.body).prepend(
                                '<div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">' +
                                    '<div style="font-size:12px;text-align:left;width:33%;">' + currentDate + '</div>' +
                                    '<div style="font-size:16px;font-weight:bold;text-align:center;width:34%;">' + companyTitle + '</div>' +
                                    '<div style="font-size:12px;text-align:right;width:33%;">Page 1 of 1</div>' +
                                '</div>' +
                                '<hr style="border:0;border-top:2px solid #000;margin:0 0 2px 0;">' +
                                '<div style="text-align:center;font-weight:bold;font-size:15px;margin:8px 0 0 0;">' + summaryTitle + '</div>' +
                                '<hr style="border:0;border-top:5px solid #000;margin:2px 0 6px 0;">'
                            );
                        }
                    },
                    {
                        extend: "excelHtml5",
                        title: companyTitle + ' ' + summaryTitle,
                        filename: fileDate + "_inward_summary",
                        exportOptions: { columns: ':visible:not(:first-child)', autoFilter: true }
                    },
                    {
                        extend: "csvHtml5",
                        title: companyTitle + ' ' + summaryTitle,
                        filename: fileDate + "_inward_summary",
                        exportOptions: { columns: ':visible:not(:first-child)', autoFilter: true },
                        customize: function (data) {
                            var header = [
                                currentDate + ',,Cold Storage,,Page 1 of 1',
                                ',,,,',
                                ',' + summaryTitle + ',,,',
                                ',,,,' ].join('\n');
                            return header + '\n' + data;
                        }
                    },
                    {
                        text: 'Preview',
                        action: function (e, dt, node, config) {
                            var selected = [];
                            $(".row-select:checked").each(function () {
                                selected.push($(this).val());
                            });
                            var url = "srh_party_wise_inward_balance_master.php?preview=1";
                            if(selected.length > 0) {
                                url += "&ids=" + selected.join(",");
                            }
                            window.open(url, "_blank");
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

    $("#select-all").on("click", function () {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"].row-select', rows).prop('checked', this.checked);
    });

    // --- Datepicker Setup ---
    function setupInwardDateHeaderPicker() {
        var finYearStartStr = $("#from_date").val();
        var finYearStart = parseDate(finYearStartStr);
        var fyYear = getFinancialYearStartYear();
        var today = new Date();
        var setYear = finYearStart ? finYearStart.getFullYear() : fyYear;
        var defaultDate = new Date(setYear, today.getMonth(), today.getDate());

        $(".date-filter").datepicker("destroy").datepicker({
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
    }

    // Call datepicker setup on load
    setupInwardDateHeaderPicker();

    // Re-setup datepicker when from_date changes
    $("#from_date").on("change", setupInwardDateHeaderPicker);

    // Auto-submit form on dropdown change
// The column indexes below must match your table header order!
$('.customer-filter').on('change', function() {
    table.column(1).search(this.value).draw();
});
$('.broker-filter').on('change', function() {
    table.column(3).search(this.value).draw();
});
$('.item-filter').on('change', function() {
    table.column(5).search(this.value).draw();
});
$('.unit-filter').on('change', function() {
    table.column(6).search(this.value).draw();
});

    // Date filter submission (Search button or Enter key)
$(document).ready(function ($) {
    // Search button click handler
    $("#btn-date-search").on("click", function() {
        var table = $("#searchMaster").DataTable();
        var from = $("#from-date").val(); // Corrected ID
        var to = $("#to-date").val();     // Corrected ID
        var fromVal = from ? parseDate(from) : null;
        var toVal = to ? parseDate(to) : null;

        // Log for debugging (remove in production)
        console.log("From Date:", from, "Parsed:", fromVal);
        console.log("To Date:", to, "Parsed:", toVal);

        // If dates are undefined, invalid, or not provided, clear the table
        if (!from && !to || (from && !fromVal) || (to && !toVal)) {
            table.clear().draw();
            return;
        }

        // Apply the filter by redrawing the table
        table.draw();
    });

    // Ensure date fields trigger filter on change
    $("#from-date, #to-date").on("change", function() {
        $("#searchMaster").DataTable().draw();
    });

    // Custom search function
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var from = $("#from-date").val();
            var to = $("#to-date").val();
            var dateColIndex = 2; // Column index for Inward Date
            var date = data[dateColIndex] || "";
            var dateVal = parseDate(date);
            var fromVal = from ? parseDate(from) : null;
            var toVal = to ? parseDate(to) : null;

            // If dates are invalid or not provided, return false
            if (!from && !to || (from && !fromVal) || (to && !toVal)) {
                return false;
            }

            // If date in data is invalid, skip it
            if (!dateVal) {
                return false;
            }

            // Apply date range filter
            if (fromVal && dateVal < fromVal) {
                return false;
            }
            if (toVal && dateVal > toVal) {
                return false;
            }

            // Status filter logic
            var status = $("input[name='status_filter']:checked").val();
            var inQty = parseFloat(data[9]) || 0; // Inward Qty (column 9)
            var outQty = parseFloat(data[10]) || 0; // Outward Qty (column 10)

            if (!status || status === 'all') return true;
            if (status === 'pending') return (inQty - outQty) > 0;
            if (status === 'clear') return (inQty - outQty) === 0;
            return true;
        }
    );
});

    $("input[name='status_filter']").on("change", function() {
        $("#searchMaster").DataTable().draw();
    });
});
</script>
<?php include("include/footer_close.php"); ?>