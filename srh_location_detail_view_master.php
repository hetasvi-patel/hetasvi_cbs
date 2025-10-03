<?php
include("classes/cls_location_detail_view_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

if(!$canView) {
    $_SESSION["sess_message"] = "You don't have permission to view rack location details.";
    $_SESSION["sess_message_cls"] = "danger";
    $_SESSION["sess_message_title"] = "Permission Denied";
    $_SESSION["sess_message_icon"] = "exclamation-triangle-fill";
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
                <?php
                if(isset($_bll)) $_bll->pageSearch();
                ?>
            </div>
        </div>
      </section>
    </div>
  </div>
  <?php include("include/footer.php"); ?>
</div>
<?php include("include/footer_includes.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportTableToExcel(tableID, filename = ''){
    var table = document.getElementById(tableID);
    var wb = XLSX.utils.table_to_book(table, {sheet:"Location Detail View"});
    // Get current date in YYYY-MM-DD format
    var dateStr = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, (filename ? filename : dateStr + '_Location_Detail_View.xlsx'));
}
document.addEventListener("DOMContentLoaded", function() {
    let excelBtn = document.getElementById("btn-excel");
    if(excelBtn) excelBtn.onclick = function(){
        exportTableToExcel("rack-view-table");
    };
});
</script>