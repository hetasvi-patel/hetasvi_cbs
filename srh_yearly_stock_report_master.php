<?php
include("classes/cls_yearly_stock_report_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
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
                <?php
                    if(isset($_bll_yearly)) $_bll_yearly->pageSearch();
                ?>
            </div>
        </div>
      </section>
    </div>
  </div>
  <?php include("include/footer.php"); ?>
</div>
<?php include("include/footer_includes.php"); ?>
<!-- Load libraries before your custom JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
jQuery(document).ready(function ($) {
    // REMOVE datepicker initialization for from_date/to_date (no such fields now)
    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }
    function getReportTitleForFilename() {
        return "YEARLY_STOCK_REPORT";
    }

    // PDF download
    $("#pdf-btn").click(function(){
        html2canvas(document.querySelector(".stock-report-section")).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
            const width = pdf.internal.pageSize.getWidth();
            const height = (canvas.height * width) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, width, height);
            const filename = getCurrentDateForFilename() + "_" + getReportTitleForFilename() + ".pdf";
            pdf.save(filename);
        });
    });

    // Excel download
    $("#excel-btn").click(function(){
        var companyName = "Cold Storage";
        var reportTitle = "YEARLY STOCK REPORT";
        // REMOVE fromDate/toDate from title
        var excelTitle = companyName + " " + reportTitle;

        var ws_data = [];
        ws_data.push([excelTitle]);

        ws_data.push([]);

        ws_data.push(["Month", "Opening Qty.", "Inward Qty.", "Outward Qty.", "Balance Qty."]);

        $(".stock-report-table tr").each(function(idx){
            if (idx === 0 || $(this).find(".item-head").length > 0) return;
            var row = [];
            $(this).find("td").each(function(){
                row.push($(this).text().trim());
            });
            if(row.length) ws_data.push(row);
        });

        var ws = XLSX.utils.aoa_to_sheet(ws_data);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Stock");

        const filename = getCurrentDateForFilename() + "_" + reportTitle.replace(/\s+/g,"_") + ".xlsx";
        XLSX.writeFile(wb, filename);
    });

    $("#item-search-dd").on("change", function() {
        var selIndex = $("#item-search-dd")[0].selectedIndex - 1;
        if(selIndex < 0) selIndex = 0;
        $("#item-page").val(selIndex);
        $("#yearly-stock-search-form").submit();
    });
    $("#prev-item").click(function() {
        let page = parseInt($("#item-page").val());
        if(page>0){
            $("#item-page").val(page-1);
            $("#item-search-dd")[0].selectedIndex = page; 
            $("#yearly-stock-search-form").submit();
        }
    });
    $("#next-item").click(function() {
        let page = parseInt($("#item-page").val());
        let total = $("#item-search-dd option").length-1;
        if(page < total-1){
            $("#item-page").val(page+1);
            $("#item-search-dd")[0].selectedIndex = page+2; 
            $("#yearly-stock-search-form").submit();
        }
    });
    $("#print-btn").click(function(){ window.print(); });
});
</script>
<?php include("include/footer_close.php"); ?>