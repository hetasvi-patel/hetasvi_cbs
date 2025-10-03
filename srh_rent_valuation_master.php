<?php
include("classes/cls_rent_valuation_master.php");
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

if(isset($_SESSION['company_year_id'])) {
    $company_year_id = $_SESSION['company_year_id'];
} else {
    $resYear = $_dbh->query("SELECT company_year_id FROM tbl_company_year_master ORDER BY start_date DESC LIMIT 1");
    $rowYear = $resYear->fetch(PDO::FETCH_ASSOC);
    $company_year_id = $rowYear ? $rowYear['company_year_id'] : 1;
}

$res = $_dbh->query("SELECT start_date FROM tbl_company_year_master WHERE company_year_id = $company_year_id");
$row = $res->fetch(PDO::FETCH_ASSOC);
$startYear = $row ? date('Y', strtotime($row['start_date'])) : date('Y');
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
var company_year_id = <?php echo json_encode($company_year_id); ?>;
</script>
<script>
jQuery(document).ready(function ($) {
    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    function getFYYear() {
        var fyText = $("body").find(":contains('FY ')").filter(function(){
            return $(this).text().trim().match(/^FY \d{4}-\d{4}/);
        }).first().text().trim();

        if(!fyText) {
            fyText = $(".navbar, .header, .top-bar, .main-header").find(":contains('FY ')").filter(function(){
                return $(this).text().trim().match(/^FY \d{4}-\d{4}/);
            }).first().text().trim();
        }

        if(!fyText) return (new Date()).getFullYear();

        var match = fyText.match(/^FY\s*(\d{4})-(\d{4})/);
        if (match && match[1]) {
            return parseInt(match[1]);
        }

        return (new Date()).getFullYear();
    }

    function updateDatePickersWithFYYear() {
        var fyYear = getFYYear();
        $("#till_date").datepicker("option", {
            yearRange: (fyYear-1) + ":" + (fyYear+2),
            defaultDate: new Date(fyYear, 0, 1)
        });
        $(".date-filter").not("#till_date").datepicker("option", {
            yearRange: (fyYear-1) + ":" + (fyYear+2),
            defaultDate: new Date(fyYear, 0, 1)
        });
        var tillDateVal = $("#till_date").val();
        if (tillDateVal) {
            var parts = tillDateVal.split("/");
            if (parts.length === 3 && parseInt(parts[2]) !== fyYear) {
                $("#till_date").val(parts[0] + "/" + parts[1] + "/" + fyYear);
            }
        }
        $(".date-filter").not("#till_date").each(function() {
            var val = $(this).val();
            if (val) {
                var parts = val.split("/");
                if (parts.length === 3 && parseInt(parts[2]) !== fyYear) {
                    $(this).val(parts[0] + "/" + parts[1] + "/" + fyYear);
                }
            }
        });
    }

    var fyYear = getFYYear();
    var currentDate = new Date();
    var day = String(currentDate.getDate()).padStart(2, "0");
    var month = String(currentDate.getMonth() + 1).padStart(2, "0");
    var defaultTillDate = day + "/" + month + "/" + fyYear;

    if (!$("#till_date").val() || $("#till_date").val().trim() === "") {
        $("#till_date").val(defaultTillDate);
    } else {
        var parts = $("#till_date").val().split("/");
        if(parts.length === 3 && parseInt(parts[2]) !== fyYear) {
            $("#till_date").val(parts[0] + "/" + parts[1] + "/" + fyYear);
        }
    }

    var fileDate = getCurrentDateForFilename();
    var currentDateFormatted = defaultTillDate;
    var companyTitle = 'Cold Storage';
    var summaryTitle = 'RENT VALUATION';

    function formatHeaderDate(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('/');
        if (parts.length === 3) {
            return parts[0].padStart(2, '0') + '-' + parts[1].padStart(2, '0') + '-' + parts[2];
        }
        return dateStr;
    }

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        pageLength: 50,
        language: {
            emptyTable: "No records found for the selected date."
        },
        layout: {
            topStart: {
                buttons: [
                    "colvis",
                    <?php if($canExcel) { ?>
                    {
                        extend: "excelHtml5",
                        filename: fileDate + "_rent_valuation",
                        title: function() {
                            var tillDateVal = $("#till_date").val();
                            var formattedTillDate = formatHeaderDate(tillDateVal);
                            return companyTitle + ' ' + summaryTitle + " AS ON " + formattedTillDate;
                        },
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16], 
                            autoFilter: true,
                        }
                    },
                    <?php } ?>
                ]
            }
        },
        columnDefs: [
    { "orderable": false, "targets": 0 },
    { "className": "dt-head-left dt-body-left", "targets": "_all" },
 { "visible": false, "targets": 1 } // Hide the customer column in the table
]
    });

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            const tillDate = $("#till_date").val();
            const dateColIndex = 13;
            const date = data[dateColIndex] || "";
            if (!tillDate) return true;
            if (!date) return false;
            function parseDate(str) {
                if (!str || !/^\d{2}\/\d{2}\/\d{4}$/.test(str)) return null;
                const parts = str.split("/");
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
            const dateVal = parseDate(date);
            const tillDateVal = parseDate(tillDate);

            if (dateVal && tillDateVal && dateVal > tillDateVal) return false;
            return true;
        }
    );

    function refreshTable() {
        table.draw();
    }

    $("#till_date").on("change", function() {
        refreshTable();
    });

    $("#customer_filter").on("change", function () {
        $("#mainFilterForm").submit();
    });

    $("#till_date").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close",
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            updateDatePickersWithFYYear();
            refreshTable();
        }
    });

    $(".date-filter").not("#till_date").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close",
        changeMonth: true,
        changeYear: true,
        yearRange: function() {
            var fyYear = window.company_year_id || (new Date()).getFullYear();
            return (fyYear-1) + ":" + (fyYear+2);
        },
        beforeShow: function(input, inst) {
            var today = new Date();
            var fyYear = window.company_year_id || today.getFullYear();
            var defaultDate = new Date(fyYear, today.getMonth(), today.getDate());
            $(input).datepicker("option", "defaultDate", defaultDate);
            setTimeout(function() {
                inst.dpDiv.datepicker("setDate", defaultDate);
            }, 1);
        },
        onSelect: function() {
            updateDatePickersWithFYYear();
            refreshTable();
        }
    });

    updateDatePickersWithFYYear();

    $("#search-filters input").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
         console.log("Searching column index:", colIndex, "for value:", this.value);
        if (typeof colIndex !== "undefined" && colIndex !== undefined && colIndex !== null) {
            if ($(this).hasClass('date-filter')) {
                const searchDate = $(this).val();
                table.column(colIndex).search(searchDate, true, false).draw();
            } else {
                table.column(colIndex).search(this.value, true, false).draw();
            }
        }
    });

    // Total Amount Calculation
    function updateTotalAmount() {
        var amountColIndex = 7; 
        var total = 0;
        table.rows({filter:'applied'}).every(function(rowIdx, tableLoop, rowLoop){
            var data = this.data();
            var val = data[amountColIndex];
            val = typeof val === 'string' ? val.replace(/,/g, '') : val;
            var num = parseFloat(val);
            if (!isNaN(num)) total += num;
        });
        $("#total_amount").val(total.toLocaleString("en-IN", {maximumFractionDigits:2}));
    }
    table.on('draw', updateTotalAmount);
    $(document).ready(function(){
        updateTotalAmount();
        $("#customer_filter, #till_date, .date-filter").on("change keyup", function(){ setTimeout(updateTotalAmount, 100); });
    });
});
</script>

<?php
include("include/footer_close.php");
?>