<?php
    include("classes/cls_item_preservation_price_list_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="I";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    checkFrmPermission($transactionmode,$currentmenu_label,""); // removed third argument by BHUMITA on 30/07/2025
    if( $transactionmode=="U")       
    {
        $_bll->fillModel();
        $label="Update";
    } else {
          $label="Add";
    }
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
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
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $label; ?> Data
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_item_preservation_price_list_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <?php
                    echo $_bll->getForm($transactionmode);
                ?>
            <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                  <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_item_preservation_price_list_master.php'">
                <!-- SEARCH BUTTON REMOVED BY BHUMTIA ON 30/07/2025 -->
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_item_preservation_price_list_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- REMOVED DETAIL MODAL BY BHUMITA ON 29/07/2025 -->
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

<!-- REMOVED THE INCLUDED JS AND OTHER SCRIPTS BY BHUMITA ON 29/07/2025 -->
<script>
document.addEventListener("DOMContentLoaded", function () {    
    const masterForm = document.getElementById("masterForm");

    /* SCRIPT ADDED BY BHUMTIA ON 29/07/2025 */
    const masterId = $('#item_preservation_price_list_id').val() || 0;
    const itemId = $('#item_id').val();

    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }

    $(document).on('focus', '[data-field]', function () {
        const $this = $(this);
        if ($this.text().trim() === '0.00') {
            $this.text('');
        }
    });
    if (masterId > 0 && itemId) {
        fetchDetailRecords(itemId, masterId);
    }
    function populateMasterForm(data) {
        $('#item_preservation_price_list_id').val(data.item_preservation_price_list_id);
        $('#rent_per_kg_month').val(data.rent_per_kg_month);
        $('#rent_per_kg_season').val(data.rent_per_kg_season);
        $('#transactionmode').val('U');
    }
    function resetMasterForm() {
        $('#item_preservation_price_list_id').val(0);
        $('#rent_per_kg_month').val('');
        $('#rent_per_kg_season').val('');
        $('#transactionmode').val('I');
    }
    function fetchDetailRecords(itemId, masterId) {
        //console.log(itemId+" "+masterId);
        $.ajax({
            url: 'classes/cls_item_preservation_price_list_detail.php',
            type: 'POST',
            data: {
                actionDetail: 'fetchUnits',
                item_id: itemId,
                master_id: masterId
            },
            success: function (response) {
               //console.log("fetchDetailRecords<br>");
                //console.log(response)
                $('#gridContainer').html(response);

                $('[data-field]').each(function () {
                    $(this).data('original', $(this).text().trim());
                });
            }
        });
    }
    function checkMasterRecord(itemId,masterId) {
        $.ajax({
            url: 'classes/cls_item_preservation_price_list_master.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'checkMasterRecord',
                item_id: itemId,
                master_id: masterId
            },
            success: function (response) {
                //console.log("checkMasterRecord<br>");
                //console.log(response);
                if (response.exists) {
                    populateMasterForm(response.data);
                    fetchDetailRecords(itemId, response.data.item_preservation_price_list_id);
                    $('#transactionmode').val('U');
                } else if (response.prevYearData) {
       
                    const prevYearData = response.prevYearData;
                    populateMasterForm(prevYearData);
                    fetchDetailRecords(itemId, prevYearData.item_preservation_price_list_id);

                    // Always insert for new year
                    $('#item_preservation_price_list_id').val(0);
                    $('#transactionmode').val('I');
                } else {
                    // No record found
                    resetMasterForm();
                    fetchDetailRecords(itemId, 0);
                    $('#transactionmode').val('I');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error checking master record:', error);
                Swal.fire('Error', 'Failed to check item records', 'error');
            }
        });
    }
    function resetForm() {
        resetMasterForm();
        $('#item_id').val('');
        $('#detail_records').val('[]');
    }
    $('#item_id').on('change', function () {
        const masterId = $('#item_preservation_price_list_id').val() || 0;
        const itemId = $('#item_id').val();
        if (itemId>0) {
            checkMasterRecord(itemId,masterId);
        } else {
            resetForm();
        }
    });
    function collectChangedDetails() {
        const detailRecords = [];
        const gridRows = $('#gridContainer table tbody tr');

        gridRows.each(function () {
            const row = $(this);
            if (row.hasClass('norecords')) return true;

            const packingUnitId = row.data('id');
            const detailId = row.data('detail-id') || 0;
            const masterId = $('#item_preservation_price_list_id').val();

            const monthCell = row.find('[data-field="rent_per_qty_month"]');
            const seasonCell = row.find('[data-field="rent_per_qty_season"]');

            const rentPerQtyMonth = parseFloat(monthCell.text().trim()) || 0;
            const rentPerQtySeason = parseFloat(seasonCell.text().trim()) || 0;

            const originalMonth = parseFloat(monthCell.data('original')) || 0;
            const originalSeason = parseFloat(seasonCell.data('original')) || 0;

            // Only push rows that are changed or new or have a non-zero value
            if (rentPerQtyMonth !== originalMonth ||
                rentPerQtySeason !== originalSeason ||
                !detailId ||
                (rentPerQtyMonth > 0 || rentPerQtySeason > 0)) {
                detailRecords.push({
                    item_preservation_price_list_detail_id: detailId,
                    item_preservation_price_list_id: masterId,
                    packing_unit_id: packingUnitId,
                    rent_per_qty_month: rentPerQtyMonth,
                    rent_per_qty_season: rentPerQtySeason,
                    detailtransactionmode: (masterId>0 && detailId>0) ? 'U' : 'I'
                });
            }
        });

        return detailRecords;
    }
    /* \SCRIPT ADDED BY BHUMTIA ON 29/07/2025 */

    /* FUNCTION MODIFIED BY BHUMITA 29/07/2025 */
    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        checkFormValidation(form)
        setTimeout(function(){
            const invalidInputs = document.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
            const jsonDataString = JSON.stringify(collectChangedDetails());
            document.getElementById("detail_records").value = jsonDataString;
            $("#masterForm").submit();
            }
        },200);
    } );
    /* \FUNCTION MODIFIED BY BHUMITA 29/07/2025 */
});
</script>
<?php
    frmAlert("frm_item_preservation_price_list_master.php");
?>
<?php
    include("include/footer_close.php");
?>