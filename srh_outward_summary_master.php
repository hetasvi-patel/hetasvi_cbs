<?php
include("classes/cls_outward_summary_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
?>

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
      <!-- Main content -->
      <section class="content">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
            <?php
                if (isset($_SESSION["sess_message"]) && $_SESSION["sess_message"] != "") {
                    echo '<div class="alert ' . $_SESSION["sess_message_cls"] . ' alert-dismissible fade show" role="alert">';
                    echo $_SESSION["sess_message"];
                    echo '<button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">&times;</button>';
                    echo '</div>';
                    $_SESSION["sess_message"] = "";
                    $_SESSION["sess_message_cls"] = "";
                    unset($_SESSION["sess_message"]);
                    unset($_SESSION["sess_message_cls"]);
                }
            ?>
            <?php
                if (isset($_bll))
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

    function parseDate(str) {
        if (!str) return null;
        var parts = str.split("/");
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    $(".date-filter").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close"
    });

    function setupOutwardDateHeaderPicker() {
        var finYearStartStr = $("#from-date").val(); 
        var finYearStart = parseDate(finYearStartStr);

        var today = new Date(); 
        var setYear = finYearStart ? finYearStart.getFullYear() : today.getFullYear();
        var defaultDate = new Date(setYear, today.getMonth(), today.getDate());

        $(".date-filter-outward").datepicker("destroy").datepicker({
            dateFormat: "dd/mm/yy",
            showButtonPanel: true,
            closeText: "Close",
            defaultDate: defaultDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "c-10:c+10"
        });
    }

    setupOutwardDateHeaderPicker();
    $("#from-date").on("change", setupOutwardDateHeaderPicker);

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

        return 'OUTWARD SUMMARY FROM ' + displayFromDate + ' TO ' + displayToDate;
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
                    "colvis",
                    <?php if ($canExcel) { ?>
                    {
                        extend: "print",
                        title: "",
                        exportOptions: {
                            columns: '.printout-col:visible'
                        },
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
                        title: companyTitle + ' ' + getSummaryTitle(),
                        filename: fileDate + "_outward_summary",
                        exportOptions: {
                            columns: ":visible",
                            autoFilter: true,
                        }
                    },
                    {
                        extend: "csvHtml5",
                        title: companyTitle + ' ' + getSummaryTitle(),
                        filename: fileDate + "_outward_summary",
                        exportOptions: {
                            columns: ":visible",
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
            { "className": "dt-head-left dt-body-left", "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20] }
        ],
        columns: [
            { data: "outward_no",      className: "printout-col", defaultContent: "" },
            { data: "outward_date",    className: "printout-col", defaultContent: "",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return data ? data : "";
                    }
                    return row["data-order"] || 0;
                }
            },
            { data: "customer",        className: "printout-col", defaultContent: "" },
            { data: "broker",          className: "printout-col", defaultContent: "" },
            { data: "item",            className: "printout-col", defaultContent: "" },
            { data: "marko",           className: "printout-col", defaultContent: "" },
            { data: "outward_qty",     className: "printout-col", defaultContent: "" },
            { data: "unit",            className: "printout-col", defaultContent: "" },
            { data: "outward_wt",      className: "printout-col", defaultContent: "" },
            { data: "delivery_to",     className: "printout-col", defaultContent: "" },
            { data: "driver_name",     className: "printout-col", defaultContent: "" },
            { data: "driver_mob_no",   className: "printout-col", defaultContent: "" },
            { data: "transporter",     className: "printout-col", defaultContent: "" },
            { data: "vehicle_no",      defaultContent: "" },
            { data: "inward_no",       defaultContent: "" },
            { data: "inward_date",     defaultContent: "",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return data ? data : "";
                    }
                    return row["data-order"] || 0;
                }
            },
            { data: "lot_no",          defaultContent: "" },
            { data: "stock_qty",       defaultContent: "" },
            { data: "location",        defaultContent: "" },
            { data: "inward_qty",      defaultContent: "" },
            { data: "inward_wt",       defaultContent: "" }
        ]
    });

    // Update the title when date filters change
    $("#from-date, #to-date").on("change", function() {
        table.buttons().container().find('.buttons-excel').attr('title', companyTitle + ' ' + getSummaryTitle());
        table.buttons().container().find('.buttons-csv').attr('title', companyTitle + ' ' + getSummaryTitle());
    });

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var from = $("#from-date").val();
            var to = $("#to-date").val();
            var dateColIndex = 1; // Outward Date column index
            var date = data[dateColIndex] || "";
            if (!from && !to) return true;
            if (!date) return false;

            function parseDate(str) {
                if (!str) return 0;
                var parts = str.split("/");
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
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

    $("#search-filters input").not(".date-filter-outward,.date-filter-inward").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
        table.column(colIndex).search(this.value).draw();
    });

    // Header dropdown filters
    $("#search-filters select.dropdown-filter").on("change", function () {
        let colIndex = $(this).attr("data-index");
        let val = $(this).val();
        table.column(colIndex).search(val ? "^" + val.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&") + "$" : "", true, false).draw();
    });

});
</script>

<?php
    include("include/footer_close.php");
?>