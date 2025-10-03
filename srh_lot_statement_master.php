<?php
include("classes/cls_lot_statement_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

if(!$canView) {
    $_SESSION["sess_message"] = "You don't have permission to view lot statements.";
    $_SESSION["sess_message_cls"] = "danger";
    $_SESSION["sess_message_title"] = "Permission Denied";
    $_SESSION["sess_message_icon"] = "exclamation-triangle-fill";
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
<!-- Load libraries before your custom JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
jQuery(document).ready(function ($) {
    $("#search-lot-date-from, #search-lot-date-to").datepicker({
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
                        }
                    }).appendTo(buttonPane);
                }
            }, 1);
        }
    });

    $("#btn-print").click(function () {
        window.print();
    });

    $("#btn-pdf").click(function () {
        html2canvas(document.querySelector(".lot-statement-content")).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });
            const width = pdf.internal.pageSize.getWidth();
            const height = (canvas.height * width) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, width, height);
            pdf.save("lot_statement.pdf");
        });
    });

    $("#btn-excel").click(function () {
        // Initialize workbook
        const wb = XLSX.utils.book_new();
        const allData = [];

        // Add report header
        const fromDate = $("#search-lot-date-from").val();
        const toDate = $("#search-lot-date-to").val();
        const curDate = "<?php echo date('d/m/Y'); ?>";
        allData.push(["Inter Continental Enterprise (Cold Storage) Pvt. Ltd."]);

        allData.push([]); // Empty row for spacing

        // Define table headers based on the image
        const headers = [
            "Inward No./Inward Date", "Broker", "Item", "Variety",
            "Inward Qty.", "Inward Wt./Unit", "Outward No.", "Outward Date",
            "Outward Qty.", "Remark"
        ];
        allData.push(headers);

        // Iterate through each lot-statement-section
        $(".lot-statement-section").each(function () {
            const $section = $(this);
            const $header = $section.find(".lot-statement-header");
            const inwardNo = $header.find("span:contains('Inward No.')").text().replace("Inward No. : ", "").trim();
            const inwardDate = $header.find("span:contains('Inward Date')").text().replace("Inward Date : ", "").trim();
            const party = $header.find("span:contains('Party')").text().replace("Party : ", "").trim();
            const broker = $header.find("span:contains('Broker')").text().replace("Broker : ", "").trim();

            // Get table rows
            $section.find(".lot-statement-table tr").each(function () {
                const $row = $(this);
                // Process only data rows (skip header, hr, and totals/stock rows)
                if ($row.find("td").length > 0 && !$row.hasClass("lot-totals") && $row.find("hr").length === 0) {
                    const lotNo = $row.find("td:eq(0)").text().trim(); // Lot No.
                    const itemName = $row.find("td:eq(1)").text().trim(); // Item Name
                    const variety = $row.find("td:eq(2)").text().trim(); // Variety
                    const inwardQty = $row.find("td:eq(3)").text().trim(); // In. Qty.
                    const inwardWt = $row.find("td:eq(4)").text().trim(); // In. Wt.
                    const unit = $row.find("td:eq(5)").text().trim(); // Unit
                    const outNo = $row.find("td:eq(6)").text().trim(); // Out No.
                    const outDate = $row.find("td:eq(7)").text().trim(); // Out Date
                    const outQty = $row.find("td:eq(8)").text().trim(); // Out Qty.
                    const outWt = $row.find("td:eq(9)").text().trim(); // Out Wt.
                    const vehNo = $row.find("td:eq(10)").text().trim(); // Veh. No.
                    const delTo = $row.find("td:eq(11)").text().trim(); // Del. To.

                    // Combine Inward No. and Inward Date
                    const inwardNoDate = `${inwardNo} / ${inwardDate}`;
                    // Combine Inward Wt. and Unit
                    const inwardWtUnit = `${inwardWt} ${unit}`;

                    // Determine Remark (e.g., based on Veh. No. or Del. To. being empty or specific conditions)
                    let remark = "";
                    if (!vehNo && !delTo) {
                        remark = "Packing Not proper";
                    }

                    // Row data
                    const rowData = [
                        inwardNoDate,
                        broker,
                        itemName,
                        variety,
                        inwardQty,
                        inwardWtUnit,
                        outNo,
                        outDate,
                        outQty,
                        remark
                    ];

                    allData.push(rowData);
                }
            });
        });

        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(allData);

        // Adjust column widths
        const colWidths = allData.reduce((acc, row) => {
            row.forEach((cell, i) => {
                const cellLength = cell ? cell.toString().length : 10;
                acc[i] = Math.max(acc[i] || 10, cellLength + 2); // Add padding
            });
            return acc;
        }, []);
        ws["!cols"] = colWidths.map(w => ({ wch: w }));

        // Append worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Lot Statement");

        // Generate and download Excel file
        const filename = "lot_statement_" + fromDate.replace(/\//g, '-') + "_to_" + toDate.replace(/\//g, '-') + ".xlsx";
        XLSX.writeFile(wb, filename);
    });

    $("#party-filter, #lot-filter, #item-filter, #broker-filter").on("change", function() {
        $("#lot-statement-search-form").submit();
    });
    $("#search-date-btn").on("click", function() {
        $("#lot-statement-search-form").submit();
    });
    $("#search-filters input, #global-search").off("keyup change");
});

$(function() {
    if (performance && performance.navigation.type === 1) {
        window.location.href = window.location.pathname;
    }
});
</script>
<?php
    if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
        echo "<script>result=Swal.fire('".$_SESSION["sess_message_title"]."', '".$_SESSION["sess_message"]."', '".$_SESSION["sess_message_icon"]."');if (result.isConfirmed) {location.href='srh_lot_statement_master.php';}</script>";
        unset($_SESSION["sess_message"]);
        unset($_SESSION["sess_message_cls"]);
        unset($_SESSION["sess_message_title"]);
        unset($_SESSION["sess_message_icon"]);
    }
?>
<?php
    include("include/footer_close.php");
?>