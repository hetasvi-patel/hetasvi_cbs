<?php
include("classes/cls_inward_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
$transactionmode="I";
$currentmenu_label=getCurrentMenuLabel();
if(isset($_REQUEST["transactionmode"]))       
{    
    $transactionmode=$_REQUEST["transactionmode"];
}
checkFrmPermission($transactionmode,$currentmenu_label,"srh_inward_master.php");
if( $transactionmode=="U")       
{
    $_bll->fillModel();
    $label="Update";
} else {
        $label="Add";
}
/* ADDED BY BHUMITA ON 04/08/2025 */
$inward_id = isset($_REQUEST['inward_id']) ? $_REQUEST['inward_id'] : null;
$sequence_data = getNextSequenceAndNo(
    $tbl_inward_master,
    'inward_sequence',
    'inward_no',
    'inward_date',
    COMPANY_YEAR_ID,
    $inward_id,
    'inward_id'
);
$next_inward_sequence = $sequence_data['next_sequence'];
$inward_no_formatted = $sequence_data['formatted_no'];
$finYear = $sequence_data['fin_year'];
/* \ADDED BY BHUMITA ON 04/08/2025 */
/* ADDED BY HETANSHREE */
$has_outward = false;
if ($transactionmode == "U" && $inward_id) {
    $sql = "SELECT COUNT(*) FROM tbl_outward_detail od
            INNER JOIN tbl_inward_detail id ON od.inward_detail_id = id.inward_detail_id
            WHERE id.inward_id = ?";
    $stmt = $_dbh->prepare($sql);
    $stmt->execute([$inward_id]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $has_outward = true;
    }
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
            <form id="masterForm" action="classes/cls_inward_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
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
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_inward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_inward_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
<!--  ADDED BY BHUMITA ON 04/08/2025 -->
<!-- Modal -->
    <div class="detail-modal">
        <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
            <form id="popupForm"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
              <div class="modal-header">
                  <h4 class="modal-title" id="modalToggleLabel">Add Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="box-body container-fluid">     
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-12 col-lg-6">
                            <div class="row mb-2">
                                <label for="lot_no" class="col-12 col-sm-4 control-label">Lot No *</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addInput("text","lot_no","","required","","form-control display","","","Lot No."); ?>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <!-- Item Dropdown -->
                            <div class="row mb-2">
                                <label for="item" class="col-12 col-sm-4 control-label">Item *</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo getDropdown("tbl_item_master","item_id","item_name","STATUS_QUERY COMPANY_QUERY","item","","form-select display","required","","gst","item_gst"); ?>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <!-- GST Type Radio Buttons -->
                            <div class="row mb-2">
                                <label class="col-12 col-sm-4 control-label"></label>
                                <div class="col-12 col-sm-8 pt-2 d-flex inward_gst_type">
                                        <?php echo getChecboxRadios("view_item_gst_type","id","value","","gst_type","", " display", "", "radio", "");?>
                                    </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="marko" class="col-12 col-sm-4 control-label">Marko</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addInput("text","marko","","","","form-control display","","","Marko"); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="packing_unit" class="col-12 col-sm-4 control-label">Packing Unit *</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo getDropdown("tbl_packing_unit_master","packing_unit_id","packing_unit_name","STATUS_QUERY COMPANY_QUERY","packing_unit","","form-select display","required",""); ?>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="inward_qty" class="col-12 col-sm-4 control-label">Inward Qty. *</label>
                                <div class="col-12 col-sm-8">
                                   <?php echo addNumber("inward_qty","","required","","form-control display","","min=0","0.01","Inward Qty.","",""); ?>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="inward_wt" class="col-12 col-sm-4 control-label">Inward Wt.(Kg.)</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addNumber("inward_wt","","required","","form-control display","","min=0","0.01","Inward Wt.","",""); ?>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="avg_wt_per_bag" class="col-12 col-sm-4 control-label">Avg. Wt. / Unit</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addInput("text","avg_wt_per_bag","","","disabled","form-control display","","","Avg. Wt./Bag"); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label class="col-12 col-sm-4 control-label">Location *</label>
                                <div class="col-12 col-sm-8">
                                    <div class="row g-0">
                                        <!-- Chamber Dropdown -->
                                        <div class="col-4">
                                            <?php echo getDropdown("tbl_chamber_master","chamber_id","chamber_name","COMPANY_QUERY","chamber","","form-select","required",""); ?>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        
                                        <!-- Floor Dropdown -->
                                        <div class="col-4">
                                            <?php echo getDropdown("tbl_floor_master","floor_id","floor_name","COMPANY_QUERY","floor","","form-select","required",""); ?>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        
                                        <!-- Rack Input -->
                                        <div class="col-4">
                                            <?php echo addInput("text","rack","","","","form-control rounded-0","","","Rack"); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="location" class="col-12 col-sm-4 control-label">Location</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addInput("text","location","","","disabled","form-control display","","","Location"); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <label for="moisture" class="col-12 col-sm-4 control-label">Moisture</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addInput("text","moisture","","","","form-control","","","Moisture"); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-12 col-lg-6">
                            <!-- STORAGE DURATION DROPDOWN -->
                            <div class="row mb-2">
                                <label class="col-12 col-sm-4 control-label">Storage Duration *</label>
                                <div class="col-12 col-sm-8">
                                    <div class="row g-0">
                                        <div class="col-6 pe-1">
                                        <?php echo getDropdown("view_storage_duration","id","value","","storage_duration","","form-select display","required",""); ?>
                                        <div class="invalid-feedback"></div>
                                    </div> 
                                        <!-- Rent Per Dropdown -->
                                        <div class="col-6 ps-1">
                                        <?php echo getDropdown("view_rent_type","id","value","","rent_per","","form-select display","required",""); ?>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SEASONAL FIELDS SECTION -->
                            <div id="seasonalFields" style="display: none;">
                                <div class="row mb-2 justify-content-end align-items-center">
                                    <div class="col-sm-4 text-end" style="padding-right: 110px;">
                                        <label for="seasonal_start_date" class="control-label">Start Date*</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo addInput("date","seasonal_start_date","","required","","form-control","","","Start Date"); ?>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-sm-2 text-end">
                                        <label for="seasonal_end_date" class="control-label">End Date *</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo addInput("date","seasonal_end_date","","required","","form-control","","","End Date"); ?>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-2" id="rentPerMonthRow">
                                <label for="rent_per_month" class="col-12 col-sm-4 control-label">Rent/Month</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addNumber("rent_per_month","","","","form-control","","min=0","0.01","Rent/Month","",""); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-2" id="rentStorageDurationRow">
                                <label for="rent_per_storage_duration" id="rentLabel" class="col-12 col-sm-4 control-label">Rent/Storage Duration</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addNumber("rent_per_storage_duration","","","disabled","form-control display","","min=0","0.01","Rent/Storage Duration","",""); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                    <label for="unloading_charge" class="col-12 col-sm-4 control-label">Unloading Charge</label>
                                    <div class="col-12 col-sm-8">
                                        <?php echo addNumber("unloading_charge","","","","form-control display","","min=0","0.01","Unloading Charge","",""); ?>
                                    </div>
                                </div>

                            
                            <div class="row mb-2">
                                <label for="remark" class="col-12 col-sm-4 control-label">Remark</label>
                                <div class="col-12 col-sm-8">
                                    <?php echo addTextArea("remark","","","","form-control display","",2,"Remark"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="inward_id" name="inward_id" value="0">
                <input type="hidden" id="inward_detail_id" name="inward_detail_id" value="0">
                <input type="hidden" id="detailtransactionmode" name="detailtransactionmode" value="I">
                <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value="Save">
                <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_cancel" value="Cancel" data-bs-dismiss="modal">
            </div>
                </form>
            </div> <!-- /.modal-content -->
          </div>  <!-- /.modal-dialog -->
        </div> <!-- /.modal -->
    </div>
    <!-- /Modal -->
<!--  \ADDED BY BHUMITA ON 04/08/2025 -->
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
<script src="dist/js/detail_functions.js"></script>
<script src="dist/js/transaction_functions.js"></script>
<script>
var inwardHasOutward = <?php echo ($has_outward ? 'true' : 'false'); ?>;//ADDED BY HETANSHREE
document.addEventListener("DOMContentLoaded", function () { 
    /*ADDED BY HETANSHREE DISABLE VALUE*/
    var fieldsToDisable = ["inward_sequence", "customer", "broker", "inward_date"];
    if (typeof inwardHasOutward !== "undefined" && inwardHasOutward && "<?php echo $transactionmode; ?>" === "U") {
        fieldsToDisable.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.disabled = true;
                el.classList.add("bs-secondary-bg"); // Optional style
            }
        });
    }
    /* ADDED BY BHUMITA ON 04/08/2025 */
    const financialYear = "<?php echo $finYear; ?>";   
    let companyYearDetails = {
        start_date: '<?php echo isset($sequence_data["start_date"]) ? $sequence_data["start_date"] : date("Y-m-d"); ?>',
        end_date: '<?php echo isset($sequence_data["end_date"]) ? $sequence_data["end_date"] : date("Y-m-d"); ?>'
    };
    let isTotalUnloadingChargeManuallyEdited = false;
    let unloadingChargePerUnit = 0;
    /* \ADDED BY BHUMITA ON 04/08/2025 */
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
     /* \ADDED BY Hetasvi ON 01/09/2025 */
     const requiredInputs = masterForm.querySelectorAll(
        "input.required, select.required, textarea.required, .duplicate, .is-invalid"
    );
    requiredInputs.forEach(function(input) {
        input.addEventListener('blur', function () {
            validateInput(input);
        });
    });
    /* \ADDED BY Hetasvi ON 01/09/2025 */
    const firstInput = masterForm.querySelector("input:not([type=hidden]):not([readonly], select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="inward_id";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_inward_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo $tbl_inward_master; ?>",action:"checkDuplicate",company_year_id: "<?php echo COMPANY_YEAR_ID; ?>"},// <-- Add this on 29/09/2025 by HETASVI
            success: function(response) {
                response = parseInt(response);
                if (response == 1) {
                    input.classList.add("is-invalid");
                    input.focus();
                    let message="";
                    if(input.validationMessage)
                        message=input.validationMessage;
                    else
                        message="Duplicate Value";
                    if(input.nextElementSibling) 
                      input.nextElementSibling.textContent = message;
                      return false;
                } else {
                   input.classList.remove("is-invalid");
                    if(input.nextElementSibling) 
                        input.nextElementSibling.textContent = "";
                }
            },
            error: function() {
                console.log("Error");
            }
        }); // ajax completed
    }

    /* ADDED BY BHUMITA ON 04/08/2025 */
        duplicateInputs.forEach((input) => {
            input.addEventListener('blur', function (event) {
                setTimeout(function() {
                    checkDuplicate(input);
                    if(input.name === "inward_sequence") {
                        let otherInput = document.getElementById("inward_no");
                        if(otherInput) checkDuplicate(otherInput);
                    }
                }, 100);
            });
        });
                       
    // Inward sequence and inward_no logic
    setSequence("inward_sequence","inward_no",financialYear);              
    
    //Inward Date
    setDefaultDates('inward_date',companyYearDetails);
    $('#inward_date').on('blur', function(){
        validateDate('inward_date',companyYearDetails);
    });


    if($('#billing_starts_from')) {
        $('#billing_starts_from').one('focus', function () {
            if ($(this).val() === '') {
                const inwardDate = $('#inward_date').val();
                $(this).val(inwardDate || companyYearDetails.start_date);
            }
        });
    }

    // GST type radio buttons
    const itemDropdown = document.getElementById("item"); 
    const gstRadios = document.querySelectorAll('input[name="gst_type"]');
    itemDropdown.addEventListener("change", function () {
        const selectedOption = itemDropdown.options[itemDropdown.selectedIndex];
        const gstValue = selectedOption.getAttribute("data-gst");
        if (gstValue) {
            gstRadios.forEach(radio => {
                radio.checked = (radio.value === gstValue);
            });
        }
    });

    // Packing unit and weight calculations
    const inwardQtyInput = document.getElementById("inward_qty");
    const inwardWeightInput = document.getElementById("inward_wt");
    const avgWtPerBagInput = document.getElementById("avg_wt_per_bag");
    const avgWtPerBagHidden = document.getElementById("hid_avg_wt_per_bag");
    const packingUnitDropdown = document.getElementById("packing_unit");
    const unloadingChargeInput = document.getElementById('unloading_charge');
    const totalUnloadingChargeInput = document.getElementById('total_unloading_charge');
    let conversionFactor = 0;
    function updateInwardWeight() {
        const qty = parseFloat(inwardQtyInput.value) || 0;
        if(conversionFactor === 0) {
            conversionFactor = avgWtPerBagInput.value;
        }
        if (conversionFactor > 0) {
            const calculatedWeight = (qty * conversionFactor).toFixed(2);
            inwardWeightInput.value = calculatedWeight;
            avgWtPerBagInput.value = parseFloat(calculatedWeight/(qty || 1)).toFixed(2);
            avgWtPerBagHidden.value = avgWtPerBagInput.value;
        } else {
            inwardWeightInput.value = "";
        }
    }
    function updateAvgWtPerBag() {
        const qty = parseFloat(inwardQtyInput.value) || 0;
        const weight = parseFloat(inwardWeightInput.value) || 0;
        if (qty > 0) {
            avgWtPerBagInput.value = (weight / qty).toFixed(2);
            avgWtPerBagHidden.value = avgWtPerBagInput.value;
        } else {
            avgWtPerBagInput.value = 0;
            avgWtPerBagHidden.value = 0;
        }
    }
    /*HETANSHREE - ADDED BY MANSI New: Update grid to show total unloading charges (per_unit * qty)*/
    function updateGridUnloadingCharges() {
        const rows = document.querySelectorAll('#tableBody tr:not(.norecords)');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('[data-label="inward_qty"]').innerText) || 0;
            const perUnitCell = row.querySelector('[data-label="unloading_charge"]');
            const perUnit = parseFloat(perUnitCell.getAttribute('data-per-unit') || perUnitCell.innerText) || 0;
            perUnitCell.setAttribute('data-per-unit', perUnit.toFixed(2));
            perUnitCell.innerText = (perUnit * qty).toFixed(2);
        });
    }
    // MODIFIED BY MANSI Override calculateTotals to sum calculated unloading charge totals
    function calculateTotals() {
        let totalQty = 0;
        let totalWt = 0;
        let totalUnloadingCharge = 0;
        jsonData.forEach(row => {
            const qty = parseFloat(row.inward_qty) || 0;
            const wt = parseFloat(row.inward_wt) || 0;
            const unloadingCharge = parseFloat(row.unloading_charge) || 0;
            totalQty += qty;
            totalWt += wt;
            totalUnloadingCharge += unloadingCharge * qty; 
        });
        if ($("#total_qty")) {
            document.getElementById('total_qty').value = totalQty;
        }
        if ($("#total_wt")) {
            document.getElementById('total_wt').value = totalWt.toFixed(2);
        }
        if (totalUnloadingChargeInput && !isTotalUnloadingChargeManuallyEdited) {
            totalUnloadingChargeInput.value = totalUnloadingCharge.toFixed(2);
        }
    }
    
    function resetUnitFields() {
        conversionFactor=0;
        avgWtPerBagInput.value = 0;
        avgWtPerBagHidden.value = 0;
        inwardWeightInput.value = 0;
        if (unloadingChargeInput) {
            unloadingChargeInput.value = '';
            unloadingChargePerUnit = 0;
        }
    }
 
    if (packingUnitDropdown) {
        packingUnitDropdown.addEventListener("change", function () {
            const selectedPackingUnit = packingUnitDropdown.value;
            if (selectedPackingUnit) {
                    fetch("classes/cls_inward_master.php?action=fetchPackingUnitData&packing_unit=" + encodeURIComponent(selectedPackingUnit))
                    .then(response => {
                        return response.json(); 
                    })
                    .then(data => {
                        if (data.success) {
                            conversionFactor = parseFloat(data.conversion_factor) || 0;
                            unloadingChargePerUnit = parseFloat(data.unloading_charge) || 0; // Set per-unit
                            if (unloadingChargeInput) {
                                unloadingChargeInput.value = unloadingChargePerUnit.toFixed(2); // Display per-unit
                            }
                            updateInwardWeight();
                        } else {
                            resetUnitFields();
                        }
                    })
                    .catch(() => {
                        resetUnitFields();
                    });
            } else {
                resetUnitFields();
            }
        });
    }
    document.addEventListener("input", function (event) {
        if (inwardQtyInput && event.target === inwardQtyInput) {
             updateInwardWeight();
        }
        if (inwardWeightInput && event.target === inwardWeightInput) {
             updateAvgWtPerBag();
        }
        if (unloadingChargeInput && event.target === unloadingChargeInput) {
            unloadingChargePerUnit = parseFloat(event.target.value) || 0; // Update per-unit if manually edited
            isTotalUnloadingChargeManuallyEdited = false;
        }
        if (totalUnloadingChargeInput && event.target === totalUnloadingChargeInput) {
            isTotalUnloadingChargeManuallyEdited = true;
        }
    });

    // fetch floors for a chamber
    function resetFloorDropdown() {
        const floorDropdown = document.getElementById("floor");
        if (floorDropdown) {
            floorDropdown.innerHTML = ""; // Clear existing options
            const option = document.createElement("option");
            option.value = "";
            option.textContent = "Select Floor";
            option.setAttribute("data-label", "Select Floor");
            floorDropdown.appendChild(option);
        }
    }
    const chamberDropdown = document.getElementById("chamber");
    if (chamberDropdown) {
        chamberDropdown.addEventListener("change", function () {
            const selectedChamber = chamberDropdown.value; 
            if (selectedChamber) {
                fetch("classes/cls_inward_master.php?action=fetchFloors&chamber_id=" + encodeURIComponent(selectedChamber))
                .then(response =>  response.json())
                .then(data => {
                    const floorDropdown = document.getElementById("floor");
                    if (data.success && floorDropdown) {
                        floorDropdown.innerHTML = ""; // Clear existing options
                        resetFloorDropdown();
                        data.floors.forEach(floor => {
                            const option = document.createElement("option");
                            option.value = floor.floor_id;
                            option.textContent = floor.floor_name;
                            if(floor.company_name) {
                                option.textContent = floor.floor_name + " (" + floor.company_name + ")";
                            }
                            option.setAttribute("data-label", floor.floor_name);
                            floorDropdown.appendChild(option);
                        });
                        // Trigger change event to update location
                        floorDropdown.dispatchEvent(new Event("change"));
                    } else {
                        resetFloorDropdown();
                        document.getElementById('hid_location').value = '';
                        document.getElementById('location').value = '';
                    }
                })
                .catch(() => {
                    console.log("Error fetching floors");
                    resetFloorDropdown();
                    document.getElementById('hid_location').value = '';
                    document.getElementById('location').value = '';
                });
            } else {
                resetFloorDropdown();
                 document.getElementById('hid_location').value = '';
                document.getElementById('location').value = '';
            }
        });
        updateLocation();
    }
     // Location Update
    document.getElementById('floor').addEventListener('change', function() {
        const floor= this.selectedOptions[0].value;
        if (floor === "") {
             document.getElementById('rack').value=""; // Reset rack input on floor change
        }
        updateLocation();
    });
    document.getElementById('rack').addEventListener('input', updateLocation);
    function updateLocation() {
        const chamber = document.getElementById('chamber').selectedOptions.length > 0 
            ? document.getElementById('chamber').selectedOptions[0].getAttribute('data-label')
            : '';
        const floor = document.getElementById('floor').selectedOptions.length > 0 && document.getElementById('floor').selectedOptions[0].value !== ''
            ? document.getElementById('floor').selectedOptions[0].getAttribute('data-label')
            : '';
        const rack = document.getElementById('rack').value.trim() || '';
        const parts = [chamber, floor, rack].filter(part => part !== ''); // Filter out empty parts
        const location = parts.join(' - ');
        document.getElementById('hid_location').value = location;
        document.getElementById('location').value = location;
    }
    
    // Storage duration and rent fields calculation
    const storageDuration = document.getElementById('storage_duration');
    const rentPerMonthRow = document.getElementById('rentPerMonthRow');
    const rentStorageDuration = document.getElementById('rent_per_storage_duration');
    const rentStorageDurationRow = document.getElementById('rentStorageDurationRow');
    const parent = rentPerMonthRow.parentNode;
    let isRentPerMonthManuallyEdited = false;
    let isRentStorageDurationManuallyEdited = false;
    
    function swapRentRowsIfSeasonal() {
        const parent = rentPerMonthRow.parentNode;
        const storageDurationId = parseInt($('#popupForm #storage_duration').val(), 10) || 0;
        const isSeasonal = storageDurationId === 9;
        if (isSeasonal) {
            if (rentPerMonthRow.nextElementSibling === rentStorageDurationRow) {
                parent.insertBefore(rentStorageDurationRow, rentPerMonthRow);
            }
        } else {
            if (rentStorageDurationRow.nextElementSibling === rentPerMonthRow) {
                parent.insertBefore(rentPerMonthRow, rentStorageDurationRow);
            }
        }
    }

    function toggleFields() {
        const storageDurationId = parseInt($('#popupForm #storage_duration').val(), 10) || 0;
        const isSeasonal = storageDurationId === 9;
        const isNoBilling = storageDurationId === 10;
        const rpsd = $('#popupForm #rent_per_storage_duration');
        if (isNoBilling) {
            rpsd.prop('disabled', true);
        } else if (isSeasonal) {
            rpsd.prop('disabled', false);
        } else {
            rpsd.prop('disabled', true);
        }
    }
    function getRentPerLabelById(id) {
        if (id == "1") return "Quantity";
        if (id == "2") return "Kg";
        return "";
    }
    storageDuration.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const valueLabel = selectedOption?.getAttribute('data-label') || 'Storage Duration';
        const storageDurationId = parseInt(this.value, 10) || 0;
        const isSeasonal = storageDurationId === 9;
        const isNoBilling = storageDurationId === 10;

        // Update label
        const rentLabel = document.getElementById('rentLabel');
        rentLabel.textContent = 'Rent/' + valueLabel;
        $('#rent_per_storage_duration').attr('placeholder', 'Rent/' + valueLabel);

        // Seasonal fields show/hide
        const seasonalFields = document.getElementById('seasonalFields');
        seasonalFields.style.display = isSeasonal ? 'block' : 'none';

        // Enable/disable fields based on selection
        const rpm = $('#popupForm #rent_per_month');
        const rpsd = $('#popupForm #rent_per_storage_duration');

        if (isNoBilling) {
            // No Billing: both 0 and disabled
            rpm.val(0).prop('disabled', true);
            rpsd.val(0).prop('disabled', true);
            $('#popupForm #hid_rent_per_storage_duration').val(0);
        } else if (isSeasonal) {
            // Seasonal: rent_per_storage_duration editable, rent_per_month editable
            rpm.prop('disabled', false);
            rpsd.prop('disabled', false);
        } else {
            // Other durations: rent_per_month editable, rent_per_storage_duration auto-calculated (disabled)
            rpm.prop('disabled', false);
            rpsd.prop('disabled', true);
        }
    });
    storageDuration.addEventListener('change', swapRentRowsIfSeasonal);
    storageDuration.addEventListener('change', toggleFields);
    swapRentRowsIfSeasonal();
    toggleFields();

    $('#popupForm #rent_per_month, #popupForm #rent_per_storage_duration').on('input', function() {
        isRentPerMonthManuallyEdited = true;
        isRentStorageDurationManuallyEdited = true;
    });
    $('#popupForm #item, #popupForm #packing_unit, #popupForm #rent_per, #popupForm #storage_duration').on('change', function() {
        isRentPerMonthManuallyEdited = false;
        isRentStorageDurationManuallyEdited = false;
        fetchRentPerMonthAndCalculate();
    });

    function fetchRentPerMonthAndCalculate() {
        const itemId = $('#popupForm #item').val();
        const unitId = $('#popupForm #packing_unit').val();
        const rentPerId = $('#popupForm #rent_per').val();
        const customerId = $('#customer').val(); // Get customer ID from main form
        const companyYearId = $('#company_year_id').val();

        const storageDurationId = parseInt($('#popupForm #storage_duration').val(), 10) || 0;
        const isSeasonal = storageDurationId === 9;
        const isNoBilling = storageDurationId === 10;

        const rentPer = getRentPerLabelById(rentPerId);

        if (!itemId || !rentPer || !unitId || !companyYearId) {
            $('#popupForm #rent_per_month').val('');
            $('#popupForm #rent_per_storage_duration').val('');
            $('#popupForm #hid_rent_per_storage_duration').val('');
            return;
        }

        if (isNoBilling) {
            $('#popupForm #rent_per_month').val(0);
            $('#popupForm #rent_per_storage_duration').val(0);
            $('#popupForm #hid_rent_per_storage_duration').val(0);
            return;
        }

        let params = {
            action: 'fetchRentPerMonth',
            item_id: itemId,
            unit_id: unitId,
            rent_per: rentPer,
            customer_id: customerId || 0,
            company_year_id: companyYearId
        };
        
        if (isSeasonal) params.seasonal = 1;
        
        $.ajax({
            url: 'classes/cls_inward_master.php',
            method: 'POST',
            data: params,
         success: function (response) {
                let data;
                try {
                    data = typeof response === "string" ? JSON.parse(response) : response;
                } catch (e) {
                    console.error('Error parsing response:', e);
                    data = {};
                }
                if (data.success && data.rent_per_month !== null && data.rent_per_month !== "") {
                    $('#popupForm #rent_per_month').val(data.rent_per_month);
                    if (isSeasonal && data.rent_per_season !== null) {
                        $('#popupForm #rent_per_storage_duration').val(data.rent_per_season);
                        $('#popupForm #hid_rent_per_storage_duration').val(data.rent_per_season);
                    } else {
                        const rentStorageDuration = getRentPerStorageDuration(storageDurationId, data.rent_per_month)
                        $('#popupForm #rent_per_storage_duration').val(rentStorageDuration);
                        $('#popupForm #hid_rent_per_storage_duration').val(rentStorageDuration);
                    }
                } else {
                    $('#popupForm #rent_per_month').val('');
                    $('#popupForm #rent_per_storage_duration').val('');
                    $('#popupForm #hid_rent_per_storage_duration').val('');
                }
            },
            error: function (xhr, status, error) {
                console.error('Rent fetch error:', status, error);
                $('#popupForm #rent_per_month').val('');
                $('#popupForm #rent_per_storage_duration').val('');
                $('#popupForm #hid_rent_per_storage_duration').val('');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch rent rates. Please try again.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    }

    $('#popupForm #rent_per_month').on('input', function() {
        if (!isRentPerMonthManuallyEdited) return;
        const storageDurationId = parseInt($('#popupForm #storage_duration').val(), 10) || 0;
        const isSeasonal = storageDurationId === 9;
        const isNoBilling = storageDurationId === 10;
        if (isSeasonal || isNoBilling) return;
        const rentPerMonth = parseFloat(this.value) || 0;
        const rentPerStorageDuration=getRentPerStorageDuration(storageDurationId, rentPerMonth)
        $('#popupForm #rent_per_storage_duration').val(rentPerStorageDuration);
        $('#popupForm #hid_rent_per_storage_duration').val(rentPerStorageDuration);
    });
    $('#popupForm #rent_per_storage_duration').on('input', function() {
        $('#popupForm #hid_rent_per_storage_duration').val((parseFloat(this.value) || 0).toFixed(2));
    });
    fetchRentPerMonthAndCalculate();
   
    // Seasonal Validation
    const seasonalFields = document.getElementById("seasonalFields");
    function checkSeasonal() {
        const storageDurationId = parseInt(storageDuration.value, 10) || 0;
        const isSeasonal = storageDurationId === 9;
        if (isSeasonal) {
            seasonalFields.style.display = "block";
        } else {
            seasonalFields.style.display = "none";
            $('#seasonal_start_date').val('');
            $('#seasonal_end_date').val('');
        }
    }
    checkSeasonal();
    storageDuration.addEventListener("change", checkSeasonal);

    /* ADDED BY HETANSHREE AUTO FILL SEASONAL START DATE */
    const seasonalStartDate = document.getElementById('seasonal_start_date');
    const inwardDate = document.getElementById('inward_date');
    function setSeasonalStartDateDefault() {
        const storageDurationId = parseInt(storageDuration.value, 10) || 0;
        if (storageDurationId === 9) {
            if (seasonalStartDate && inwardDate && !seasonalStartDate.value) {
                seasonalStartDate.value = inwardDate.value;
            }
        }
    }
    storageDuration.addEventListener('change', setSeasonalStartDateDefault);
    setSeasonalStartDateDefault();
    /* \ADDED BY HETANSHREE AUTO FILL SEASONAL START DATE */

    // validate seasonal dates
    function validateSeasonalDates() {
        const storageDurationId = parseInt($('#storage_duration').val(), 10) || 0;
        const isSeasonal = storageDurationId === 9;

        if (!isSeasonal) return { isValid: true };

        const startDateInput = $('#seasonal_start_date');
        const endDateInput = $('#seasonal_end_date');
        const inwardDateInput = $('#inward_date');
        const startErrorContainer = startDateInput.siblings('.invalid-feedback');
        const endErrorContainer = endDateInput.siblings('.invalid-feedback');

        const startDate = startDateInput.val();
        const endDate = endDateInput.val();
        const inwardDate = inwardDateInput.val();

        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        const inwardDateObj = inwardDate ? new Date(inwardDate) : null;
        const finYearStart = new Date(companyYearDetails.start_date);
        const finYearEnd = new Date(companyYearDetails.end_date);

        if (startDate && isNaN(startDateObj.getTime())) {
            startErrorContainer.text('Invalid date format');
            startDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: startDateInput };
        }
        if (endDate && isNaN(endDateObj.getTime())) {
            startErrorContainer.text('Invalid date format');
            startDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: startDateInput };
        }

        if (inwardDateObj && !isNaN(inwardDateObj.getTime()) && startDateObj < inwardDateObj) {
            startErrorContainer.text('Season Start Date must be greater than or equal to Inward Date.');
            startDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: startDateInput };
        }

        if (endDateObj <= startDateObj) {
            endErrorContainer.text('Enter Season End Date greater than Season Start Date.');
            endDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: endDateInput };
        }

        if (startDateObj < finYearStart || startDateObj > finYearEnd) {
            startErrorContainer.text(`Start date must be within current selected year (${companyYearDetails.start_date} to ${companyYearDetails.end_date}).`);
            startDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: startDateInput };
        }

        if (endDateObj < finYearStart || endDateObj > finYearEnd) {
            endErrorContainer.text(`End date must be within current selected year (${companyYearDetails.start_date} to ${companyYearDetails.end_date}).`);
            endDateInput.addClass('is-invalid');
            return { isValid: false, invalidField: endDateInput };
        }
        return { isValid: true };
    }
    $('#seasonal_start_date, #seasonal_end_date, #inward_date').on('change blur', function() {
        validateSeasonalDates();
    });

// override window.openModal to include additional checks
if (typeof window.openModal === "function") {
    const originalOpenModal = window.openModal;
    window.openModal = function (index = -1) {
        originalOpenModal.apply(this, arguments);

        conversionFactor = 0;
        unloadingChargePerUnit = 0;
        checkSeasonal();
        toggleFields();
        swapRentRowsIfSeasonal();

        function setFieldReadonly(field, isReadonly, origValue) {
            // Remove any previous handlers
            ['_roMouseDownHandler','_roKeyDownHandler','_roFocusHandler','_roChangeHandler'].forEach(k => {
                if (field[k]) {
                    const ev = k.includes('Mouse') ? 'mousedown'
                            : k.includes('Key')   ? 'keydown'
                            : k.includes('Change')? 'change'
                            : 'focus';
                    field.removeEventListener(ev, field[k]);
                    field[k] = null;
                }
            });
            if (field.tagName === "INPUT") {
                field.readOnly = false;
            }
            field.removeAttribute('tabindex');
            field.removeAttribute('aria-disabled');
            field.classList.remove('bs-secondary-bg');
            field.style.backgroundColor = '';
            field.style.pointerEvents = '';

            if (isReadonly) {
                if (origValue !== undefined) {
                    field.dataset.origValue = origValue;
                }
                field.classList.add('bs-secondary-bg');
                field.style.backgroundColor = '#e9ecef';
                field.style.pointerEvents = 'none';
                field.tabIndex = -1;
                field.setAttribute('aria-disabled','true');

                field._roMouseDownHandler = e => e.preventDefault();
                field._roKeyDownHandler   = e => e.preventDefault();
                field._roFocusHandler     = e => e.target.blur();
                field._roChangeHandler    = e => { 
                    if (field.dataset.origValue !== undefined)
                        e.target.value = field.dataset.origValue;
                };

                field.addEventListener('mousedown', field._roMouseDownHandler);
                field.addEventListener('keydown',   field._roKeyDownHandler);
                field.addEventListener('focus',     field._roFocusHandler);
                field.addEventListener('change',    field._roChangeHandler);
            }
        }
        function setLotNoReadonly(input, isReadonly) {
            input.disabled = false;
            input.removeAttribute('autofocus');
            if (input._forceBlurHandler) {
                input.removeEventListener('focus', input._forceBlurHandler);
                input._forceBlurHandler = null;
            }
            if (isReadonly) {
                input.readOnly = true;
                input.tabIndex = -1;
                input.classList.add('bs-secondary-bg');
                input.style.backgroundColor = '#e9ecef';
                input._forceBlurHandler = e => e.target.blur();
                input.addEventListener('focus', input._forceBlurHandler);
            } else {
                input.readOnly = false;
                input.removeAttribute('tabindex');
                input.classList.remove('bs-secondary-bg');
                input.style.backgroundColor = '';
            }
        }

        /* ADDED BY HETANSHREE DISABLE FEILDS INWARD_QTY,INWARD_WT AND REMARK */
        const tableBody = document.getElementById("tableBody");
        const row = index >= 0 ? tableBody?.children[index] : null;
        const isFullyOutwarded = row ? row.getAttribute('data-fully-outwarded') === "1" : false;
        const isAnyInvoiced = row ? row.getAttribute('data-any-invoiced') === "1" : false;
        const shouldDisable = isFullyOutwarded || isAnyInvoiced;

        function setFieldDisabled(fieldId, isDisabled) {
            var el = document.getElementById(fieldId);
            if (el) {
                el.readOnly = isDisabled;
                if (isDisabled) {
                    el.style.backgroundColor = "#e9ecef";
                    el.setAttribute('tabindex', '-1');
                    el.setAttribute('aria-disabled', 'true');
                } else {
                    el.style.backgroundColor = "";
                    el.style.color = "";
                    el.removeAttribute('tabindex');
                    el.removeAttribute('aria-disabled');
                }
            }
        }
        setFieldDisabled("inward_qty", shouldDisable);
        setFieldDisabled("inward_wt", shouldDisable);
        setFieldDisabled("remark", shouldDisable);
       

        // USUAL FIELDS (disable if any outward exists)
        const isOutwarded = row ? row.getAttribute('data-outwarded') === "1" : false;
        const fieldsToReadonly = [
            "item", "marko", "packing_unit", "location", "chamber", "floor", "rack", "moisture",
            "storage_duration", "rent_per", "rent_per_month", "rent_per_storage_duration", "unloading_charge"
        ];
        fieldsToReadonly.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                setFieldReadonly(el, isOutwarded, el.value);
            }
        });

        // --- GST type radios readonly if outwarded ---
        const gstTypeRadios = document.querySelectorAll('input[name="gst_type"]');
        gstTypeRadios.forEach(radio => {
            if (radio._roClickHandler) {
                radio.removeEventListener('click', radio._roClickHandler);
                radio._roClickHandler = null;
            }
            if (radio._roKeyDownHandler) {
                radio.removeEventListener('keydown', radio._roKeyDownHandler);
                radio._roKeyDownHandler = null;
            }
            if (radio._roFocusHandler) {
                radio.removeEventListener('focus', radio._roFocusHandler);
                radio._roFocusHandler = null;
            }
            radio.removeAttribute('aria-disabled');
            radio.style.backgroundColor = '';
            radio.style.pointerEvents = '';
            radio.tabIndex = 0;

            if (isOutwarded) {
                if (!radio.checked) {
                    radio.style.backgroundColor = '#e9ecef';
                } else {
                    radio.style.backgroundColor = '';
                }
                radio.style.pointerEvents = 'none';
                radio.tabIndex = -1;
                radio.setAttribute('aria-disabled', 'true');
                radio._roClickHandler = e => e.preventDefault();
                radio._roKeyDownHandler = e => e.preventDefault();
                radio._roFocusHandler = e => e.target.blur();
                radio.addEventListener('click', radio._roClickHandler);
                radio.addEventListener('keydown', radio._roKeyDownHandler);
                radio.addEventListener('focus', radio._roFocusHandler);
            }
        });

        // Lot no is always readonly on edit (index >= 0)
        const lotNoInput = document.getElementById("lot_no");
        if (lotNoInput) {
            setLotNoReadonly(lotNoInput, index >= 0);
        }

        /* ADDED BY HETASVI inward Qty validation*/
        // Get outward used qty for current row
        let stockQty = 0;
        let outward_qty_used = 0;
        if (index >= 0) {
            const tableBody = document.getElementById("tableBody");
            const row = tableBody.children[index];
            const original_inward_qty = parseFloat(row.getAttribute("data-original-inward-qty")) || 0;
            outward_qty_used = parseFloat(row.getAttribute("data-outward-used")) || 0;
            stockQty = original_inward_qty - outward_qty_used;
        }

        // Attach validation to inward_qty input
        const inwardQtyInput = document.getElementById("inward_qty");
        if (inwardQtyInput) {
            inwardQtyInput._outward_qty_used = outward_qty_used;
            inwardQtyInput._stockQty = stockQty;

            inwardQtyInput.removeEventListener("input", inwardQtyInput._stockQtyHandler || (() => {}));

            inwardQtyInput._stockQtyHandler = function () {
                const value = this.value.trim();
                const inwardQty = parseFloat(value) || 0;
                const outward_qty_used = this._outward_qty_used || 0;
                const stockQty = this._stockQty || 0;
                const errorContainer = this.nextElementSibling; 
                if (value === "") {
                    this.classList.add("is-invalid");
                    if (errorContainer) {
                        errorContainer.textContent = this.validationMessage;
                    }
                    return;
                }
                if (outward_qty_used > 0) {
                    if (inwardQty < stockQty) {
                        this.classList.add("is-invalid");
                        if (errorContainer) {
                            errorContainer.textContent = "Inward Qty cannot be less than stock (" + stockQty + ")";
                        }
                    } else {
                        this.classList.remove("is-invalid");
                        if (errorContainer) {
                            errorContainer.textContent = "";
                        }
                    }
                } else {
                    this.classList.remove("is-invalid");
                    if (errorContainer) {
                        errorContainer.textContent = "";
                    }
                }
            };
            inwardQtyInput.addEventListener("input", inwardQtyInput._stockQtyHandler);
            if (index >= 0) { 
                inwardQtyInput._stockQtyHandler.call(inwardQtyInput);  
            }
        }
        /* end inward Qty validation by Hetasvi */
    };
}
    // override saveData to include additional checks
    if (typeof window.saveData === "function") {
        const originalSaveData = window.saveData;
        window.saveData = function () {
            originalSaveData.apply(this, arguments);
            updateGridUnloadingCharges();//ADDED BY MANSI
            calculateTotals();
        };
    }
    /* \ADDED BY BHUMITA ON 04/08/2025 */
    /* ADDED BY Hetasvi ON 02/09/2025 */
    //addActions
    if (typeof window.addActions === "function") {
        // Save original
        const originalAddActions = window.addActions;
        // Override
        window.addActions = function (row, index, id) {
            // Call original
            originalAddActions.apply(this, arguments);
            const actionCell = row.querySelector('td.actions');
            if (actionCell) {
                const deleteBtn = actionCell.querySelector('.delete-btn');
                // outwarded status is set on the row as data-outwarded="1" or "0"
                if (row.dataset.outwarded === "1") {
                    deleteBtn.disabled = true;
                } else {
                    deleteBtn.disabled = false;
                }
            }
        };
    }
    if (typeof window.appendTableRow === "function") {
        const originalAppendTableRow = window.appendTableRow;
        window.appendTableRow = function (rowData, index) {
            originalAppendTableRow.apply(this, arguments);

            const tableBody = document.getElementById("tableBody");
            const row = tableBody.children[tableBody.children.length - 1];

            // Check rowData.outwarded (from grid or jsonData)
            if (typeof rowData.outwarded !== "undefined") {
                row.setAttribute("data-outwarded", rowData.outwarded === "1" ? "1" : "0");
            } else {
                // If missing, try to get from jsonData (for old records)
                if (jsonData[index] && typeof jsonData[index].outwarded !== "undefined") {
                    row.setAttribute("data-outwarded", jsonData[index].outwarded === "1" ? "1" : "0");
                } else {
                    row.setAttribute("data-outwarded", "0");
                }
            }
            // Set delete button disabled based on data-outwarded
            const deleteBtn = row.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.disabled = (row.dataset.outwarded === "1");
            }
        };
    }

    if (typeof window.updateTableRow === "function") {
        const originalUpdateTableRow = window.updateTableRow;
        window.updateTableRow = function (index, rowData) {
            originalUpdateTableRow.apply(this, arguments);

            const tableBody = document.getElementById("tableBody");
            const row = tableBody.children[index];
            if (typeof rowData.outwarded !== "undefined") {
                row.setAttribute("data-outwarded", rowData.outwarded === "1" ? "1" : "0");
            } else {
                if (jsonData[index] && typeof jsonData[index].outwarded !== "undefined") {
                    row.setAttribute("data-outwarded", jsonData[index].outwarded === "1" ? "1" : "0");
                } else {
                    row.setAttribute("data-outwarded", "0");
                }
            }
            // Set delete button disabled based on data-outwarded
            const deleteBtn = row.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.disabled = (row.dataset.outwarded === "1");
            }
        };
    }
    if (typeof window.getSearchArray === "function") {
        window.getSearchArray = function () {
            // Must assign to both global and window
            let editIndex = -1;
            let newJsonData = [];
            document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
                let rowData = {};
                if(!row.classList.contains("norecords")) {
                    rowData[row.dataset.label]=row.dataset.id;
                    detailIdLabel=row.dataset.label;
                    editIndex++;
                    row.querySelectorAll("td[data-label]").forEach(td => {
                        if(!td.classList.contains("actions")){
                            if(td.dataset.value!="")
                                rowData[td.dataset.label] = td.dataset.value;
                            else
                                rowData[td.dataset.label] = td.innerText;
                        }
                    });
                    // outwarded
                    rowData["outwarded"] = row.dataset.outwarded ?? "0";
                    rowData["detailtransactionmode"]="U";
                    newJsonData[editIndex]=rowData;
                }
            });
            // Assign both ways
            jsonData = newJsonData;
            window.jsonData = newJsonData;
        };
    }
    function updateDeleteButtons() {
        const rows = document.querySelectorAll('#tableBody tr:not(.norecords)');
        rows.forEach(row => {
            const deleteBtn = row.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.disabled = (row.dataset.outwarded === "1");
            }
        });
    }
    /* MODIFIED BY MANSI DETAIL DELETE ISSUE*/
    let isDeleting = false;
    if (typeof window.deleteRow === "function") {
        window.deleteRow = function (index, id) {
            if (isDeleting) {
                console.log('Delete blocked due to ongoing deletion');
                return;
            }
            const confirmThen = (onYes) => {
                if (window.Swal && Swal.fire) {
                    Swal.fire({
                        title: 'Are you sure you want to delete this record?',
                        text: "You won't be able to revert it!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        focusCancel: true
                    }).then(res => {
                        if (res.isConfirmed) onYes();
                        else console.log('Deletion cancelled by user');
                    });
                } else {
                    if (confirm('Are you sure you want to delete this record?')) onYes();
                    else console.log('Deletion cancelled by user');
                }
            };
            confirmThen(() => {
                isDeleting = true;
                if (index >= 0 && index < jsonData.length) {
                    const deletedRow = jsonData.splice(index, 1)[0];
                    if (id && id !== "0") {
                        deletedRow.detailtransactionmode = "D";
                        if (!Array.isArray(deleteData)) deleteData = [];
                        deleteData.push(deletedRow);
                    }
                } else {
                    console.log('Invalid index for deletion:', index, 'jsonData length:', jsonData.length);
                    isDeleting = false;
                    return;
                }
                const tableBody = document.getElementById('tableBody');
                const rows = tableBody.querySelectorAll('tr:not(.norecords)');
                if (index >= 0 && index < rows.length) {
                    rows[index].remove();
                } else {
                    isDeleting = false;
                    return;
                }
                if (typeof window.getSearchArray === "function") {
                    window.getSearchArray();
                }
                updateDeleteButtons();

                const remainingRows = document.querySelectorAll('#tableBody tr:not(.norecords)');
                remainingRows.forEach((row, newIndex) => {
                    const editBtn = row.querySelector('.edit-btn');
                    const deleteBtn = row.querySelector('.delete-btn');
                    if (editBtn) editBtn.dataset.index = newIndex;
                    if (deleteBtn) deleteBtn.dataset.index = newIndex;
                });
                if (remainingRows.length === 0) {
                    if (!tableBody.querySelector('tr.norecords')) {
                        const noRecordsRow = document.createElement('tr');
                        noRecordsRow.id = 'norecords';
                        noRecordsRow.className = 'norecords';
                        noRecordsRow.innerHTML = '<td colspan="15">No records available.</td>';
                        tableBody.appendChild(noRecordsRow);
                        console.log('Added "No records available" row');
                    }
                }
                setTimeout(() => { isDeleting = false; }, 300); 
            });
        };
    }
    /* \ADDED BY Hetasvi ON 02/09/2025 */
    getSearchArray();
    updateDeleteButtons();// ADDED BY Hetasvi
    updateGridUnloadingCharges();// // ADDED BY MANSI 01/09/2025
    calculateTotals(); // ADDED BY BHUMITA ON 14/08/2025
   
    $("#popupForm" ).on( "submit", function( event ) {
        event.preventDefault();
        validateSeasonalDates();
        /* inward Qty validation by Hetasvi */
        const inwardQtyInput = document.getElementById("inward_qty");
        if (inwardQtyInput._stockQtyHandler) {
            inwardQtyInput._stockQtyHandler.call(inwardQtyInput);
        }
        if (inwardQtyInput.classList.contains("is-invalid")) {
            inwardQtyInput.focus();
            return false;
        }
        const invalidInputs = this.querySelectorAll(".is-invalid");
        if(invalidInputs.length>0)
        {} else{
            saveData();
        }
    } );

    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        duplicateInputs.forEach((input) => {
            checkDuplicate(input);
        });
       checkFormValidation(form);
        setTimeout(function(){
            const invalidInputs = document.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
            const jsonDataString = JSON.stringify(jsonData);
                document.getElementById("detail_records").value = jsonDataString;
                const deletedDataString = JSON.stringify(deleteData);
                document.getElementById("deleted_records").value = deletedDataString;
                $("#masterForm").submit();
            }
        },200);
    } ); 
});
</script>
<?php
    frmAlert("frm_inward_master.php");
?>
<?php
    include("include/footer_close.php");
?>