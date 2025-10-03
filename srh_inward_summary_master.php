<?php
include("classes/cls_inward_summary_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

if (!$canView) {
    if (!isset($_SESSION["sess_message"]) || $_SESSION["sess_message"] == "") {
        $_SESSION["sess_message"] = "You don't have permission to view inward master data.";
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
             echo getMessageHTML();
             ?>
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
    function parseDate(str) {
        if (!str) return null;
        var parts = str.split("/");
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    // Initialize main date pickers (from-date and to-date)
    $(".date-filter").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close"
    });

    // Setup for inward date header picker
    function setupInwardDateHeaderPicker() {
        var finYearStartStr = $("#from-date").val(); 
        var finYearStart = parseDate(finYearStartStr);

        var today = new Date(); 
        var setYear = finYearStart ? finYearStart.getFullYear() : today.getFullYear();
        var defaultDate = new Date(setYear, today.getMonth(), today.getDate());

        $(".date-filter-inward").datepicker("destroy").datepicker({
            dateFormat: "dd/mm/yy",
            showButtonPanel: true,
            closeText: "Close",
            defaultDate: defaultDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "c-10:c+10"
        });
    }
    
    setupInwardDateHeaderPicker();
    $("#from-date").on("change", setupInwardDateHeaderPicker);

    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    function getSummaryTitle() {
        var fromDate = $("#from-date").val();
        var toDate = $("#to-date").val();

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

        return 'INWARD SUMMARY FROM ' + displayFromDate + ' TO ' + displayToDate;
    }

    var fileDate = getCurrentDateForFilename();
    var currentDate = new Date().toLocaleDateString('en-GB');
    var companyTitle = 'Cold Storage';
    var summaryTitle = getSummaryTitle();

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        pageLength: 50,
        language: {
            emptyTable: "No data available in table"
        },
        deferRender: true,
        initComplete: function () {
            console.log("DataTable initialized with " + this.api().columns().count() + " columns");
        },
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: "colvis",
                        columns: ':not(.no-print)'
                    },
                    <?php if ($canExcel) { ?>
                    {
                        extend: "print",
                        title: "",
                        exportOptions: {
                            columns: ':not(.no-print):visible'
                        },
                        customize: function (win) {
                            $(win.document.head).append('<style>@page { size: landscape; }</style>');
                            $(win.document.body).find('table')
                                .css('width', '100%')
                                .css('max-width', '100%')
                                .css('font-size', '8pt');
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
                            $(win.document.body).find('tr').css('page-break-inside', 'avoid');
                            if (window.inwardCustomerInfo) {
                                var customerColIdx = table.column(':contains(Customer)').index();
                                $(win.document.body).find('tbody tr').each(function(i) {
                                    var info = window.inwardCustomerInfo[i+1];
                                    if (info) {
                                        var txt = info.name;
                                        if (info.city && info.state) {
                                            txt += " (" + info.city + " – " + info.state + ")";
                                        } else if (info.city) {
                                            txt += " (" + info.city + ")";
                                        }
                                        $(this).find('td').eq(customerColIdx).text(txt);
                                    }
                                });
                            }
                        }
                    },
                    {
                        extend: "excelHtml5",
                        title: companyTitle + ' ' + getSummaryTitle(),
                        filename: fileDate + "_inward_summary",
                        exportOptions: {
                            columns: ':visible', // <-- ALL columns
                            autoFilter: true,
                        }
                    },
                    {
                        extend: "csvHtml5",
                        title: companyTitle + ' ' + getSummaryTitle(),
                        filename: fileDate + "_inward_summary",
                        exportOptions: {
                            columns: ':visible', // <-- ALL columns
                            autoFilter: true
                        },
                        customize: function (data) {
                            var header = [
                                currentDate + ',,Cold Storage,,Page 1 of 1',
                                ',,,,',
                                ',' + getSummaryTitle() + ',,,',
                                ',,,,'
                            ].join('\n');
                            return header + '\n' + data;
                        }
                    }
                    <?php } ?>
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 },
            { "className": "dt-head-left dt-body-left", "targets": "_all" },
            {
                "targets": [3, 10, 11, 14, 16, 18], // indexes to hide in print
                "visible": true,
                "searchable": true,
                "className": "no-print"
            }
        ]
    });

    $("#from-date, #to-date").on("change", function() {
        table.buttons().container().find('.buttons-excel').attr('title', companyTitle + ' ' + getSummaryTitle());
        table.buttons().container().find('.buttons-csv').attr('title', companyTitle + ' ' + getSummaryTitle());
    });

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var from = $("#from-date").val();
            var to = $("#to-date").val();
            var dateColIndex = 1; // Inward Date column index
            var date = data[dateColIndex] || "";
            if (!from && !to) return true;
            if (!date) return false;

            var dateVal = parseDate(date);
            var fromVal = from ? parseDate(from) : null;
            var toVal = to ? parseDate(to) : null;

            if (fromVal && dateVal < fromVal) return false;
            if (toVal && dateVal > toVal) return false;
            return true;
        }
    );

    $("#btn-date-search").on("click", function() {
        table.draw();
    });

    $("#from-date, #to-date").on("keyup", function(e) {
        if (e.keyCode == 13) { table.draw(); }
    });

    $("#search-filters input").not(".date-filter-inward").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
        table.column(colIndex).search(this.value).draw();
    });

    $("#search-filters select.dropdown-filter").on("change", function () {
        let colIndex = $(this).attr("data-index");
        let val = $(this).val();
        table.column(colIndex).search(val ? "^" + val.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&") + "$" : "", true, false).draw();
    });

    $(".date-filter-inward").on("change", function () {
        let colIndex = $(this).attr("data-index");
        let val = $(this).val();
        if (val.length === 10 && /^\d{2}\/\d{2}\/\d{4}$/.test(val)) {
            table.column(colIndex).search("^" + val.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&") + "$", true, false).draw();
        } else {
            table.column(colIndex).search("").draw();
        }
    });

    $("#year-selector").on("change", function () {
        const selectedYear = $(this).val();
        $("#from-date").val(`01/04/${selectedYear}`);
        $("#to-date").val(`31/03/${parseInt(selectedYear) + 1}`);
        table.draw();
    });
});
</script>

<?php
include("include/footer_close.php");
?>