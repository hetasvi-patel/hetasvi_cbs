<?php
include("classes/cls_inward_outward_summary_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

// Get filter dates from request
$fromDate = $_REQUEST['from_date'] ?? '';
$toDate = $_REQUEST['to_date'] ?? '';

// Get dropdown values from request
$customer_search = $_REQUEST['customer_search'] ?? '';
$item_search = $_REQUEST['item_search'] ?? '';

// AJAX mode: If AJAX request, output only filters/table and exit
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $_REQUEST['from_date'] = $_POST['from_date'] ?? '';
    $_REQUEST['to_date'] = $_POST['to_date'] ?? '';
    $_REQUEST['customer_search'] = $_POST['customer_search'] ?? '';
    $_REQUEST['item_search'] = $_POST['item_search'] ?? '';
    $_bll->renderFilters($_REQUEST['from_date'], $_REQUEST['to_date']);
    $_bll->ajaxTable();
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
                        <?php echo getMessageHTML(); ?>
                        <div id="table-container">
                            <?php
                            if (isset($_bll)) {
                                $_bll->renderFilters($fromDate, $toDate);
                                $_bll->ajaxTable();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include("include/footer.php"); ?>
    <?php include("include/footer_includes.php"); ?>
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
        // Try to find FY label (e.g. "FY 2027-2028") in the DOM. Adjust selector if needed!
        var fyText = $("body").text().match(/FY\s*(\d{4})-(\d{4})/i);
        if (fyText && fyText[1]) {
            return parseInt(fyText[1], 10);
        }
        // fallback to current year
        return (new Date()).getFullYear();
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

        var displayFromDate = fromDate ? formatDateForTitle(fromDate) : "01-04-" + getFinancialYearStartYear();
        var displayToDate = toDate ? formatDateForTitle(toDate) : "31-03-" + (getFinancialYearStartYear() + 1);

        return 'INWARD/OUTWARD SUMMARY FROM ' + displayFromDate + ' TO ' + displayToDate;
    }

    var fileDate = getCurrentDateForFilename();
    var currentDate = new Date().toLocaleDateString('en-GB');
    var companyTitle = 'Cold Storage';

    function initializeTable() {
        if ($.fn.DataTable.isDataTable("#inwardOutwardSummary")) {
            $("#inwardOutwardSummary").DataTable().destroy();
        }
        $("#inwardOutwardSummary").DataTable({
            deferRender: true,
            ordering: false,
            paging: true,
            pageLength: 50,
            info: true,
            colReorder: true,
            layout: {
                topStart: {
                    buttons: [
                        'colvis',
                        {
                            extend: 'print',
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
                        {
                              extend: 'pdfHtml5',
    title: '',
    filename: fileDate + "_inward_outward_summary",
    orientation: 'landscape',
    pageSize: 'A4',
    exportOptions: { columns: ':visible', autoFilter: true },
customize: function (doc) {
    doc.defaultStyle.fontSize = 8;

    // Add header content
    doc.content.splice(0, 0, {
        margin: [0, 0, 0, 0],
        columns: [
            { text: currentDate, alignment: 'left', fontSize: 10 },
            { text: companyTitle, alignment: 'center', fontSize: 14, bold: true },
            { text: 'Page 1 of 1', alignment: 'right', fontSize: 10 }
        ]
    });

    // Add thin horizontal line (full width)
    doc.content.splice(1, 0, {
        canvas: [
            { type: 'line', x1: 10, y1: 0, x2: 760, y2: 0, lineWidth: 0.5, lineColor: '#000' }
        ],
        margin: [0, 2, 0, 2]
    });

    // Add summary title
    doc.content.splice(2, 0, {
        text: getSummaryTitle(),
        style: 'header',
        alignment: 'center',
        margin: [0, 5, 0, 5],
        bold: true,
        fontSize: 12
    });

    // Add thick horizontal line (full width)
    doc.content.splice(3, 0, {
        canvas: [
            { type: 'line', x1: 10, y1: 0, x2: 760, y2: 0, lineWidth: 1, lineColor: '#000' }
        ],
        margin: [0, 2, 0, 8]
    });

    // Set all column widths to auto
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
                            extend: 'excelHtml5',
                            title: companyTitle + ' ' + getSummaryTitle(),
                            filename: fileDate + "_inward_outward_summary",
                            exportOptions: { columns: ':visible', autoFilter: true }
                        }
                    ]
                },
                topEnd: { search: { placeholder: 'Search table' } }
            },
            columnDefs: [
                { orderable: false, targets: '_all' },
                { className: 'dt-head-left dt-body-left', targets: [0, 1, 2, 3, 5, 6, 7, 8, 9, 10] },
                { className: 'dt-head-right dt-body-right', targets: [4, 11] }
            ]
        });
    }

    // --- Datepicker with FY-default-year logic ---
    function setupDatePickers() {
        var fyYear = getFinancialYearStartYear();
        var today = new Date();

        $("#from-date, #to-date").each(function() {
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
                                    initializeTable();
                                }
                            }).appendTo(buttonPane);
                        }
                    }, 1);
                }
            });
        });
    }

    // First initialization
    initializeTable();
    setupDatePickers();

    function reloadTableAjax() {
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var customer_search = $('#customer-search').val();
        var item_search = $('#item-search').val();
        $.ajax({
            url: 'srh_inward_outward_summary_master.php',
            type: 'POST',
            data: {
                from_date: fromDate,
                to_date: toDate,
                customer_search: customer_search,
                item_search: item_search,
                ajax: '1'
            },
            beforeSend: function () {
                $('#table-container').html('<div style="text-align:center;padding:40px;"><span class="spinner-border"></span> Loading...</div>');
            },
            success: function (response) {
                $('#table-container').html(response);
                initializeTable();
                setupDatePickers();
            },
            error: function () {
                $('#table-container').html('<div class="alert alert-danger">Could not load summary table.</div>');
            }
        });
    }

    $(document).on('click', '#btn-date-search', function (e) {
        e.preventDefault();
        reloadTableAjax();
    });

    $(document).on('change', '#customer-search, #item-search', function (e) {
        reloadTableAjax();
    });

    $(document).on('keyup', '#from-date, #to-date', function (e) {
        if (e.keyCode === 13) reloadTableAjax();
    });

    // Update the title on date change for Excel button (if needed)
    $("#from-date, #to-date").on("change", function () {
        $(".buttons-excel").attr('title', companyTitle + ' ' + getSummaryTitle());
        initializeTable();
    });
});
</script>
</div>
</body>