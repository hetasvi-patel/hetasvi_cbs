<?php
    include("classes/cls_rent_invoice_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
?>
<!-- CSS ADDED BY BHUIMITA ON 22/08/2025 -->
<style>
    .multiselect {
        width: 100%;
    }
    .selectBox {
        position: relative;
        cursor: pointer;
    }
    .selectBox select {
        width: 100%;
    }
    .overSelect {
        position: absolute;
        left: 0; right: 0; top: 0; bottom: 0;
    }
    #lotNoSelectOptions {
        display: none;
        border: 1px solid #ced4da;
        border-top: none;
        background-color: #fff;
        max-height: 180px;
        overflow-y: auto;
        position: absolute;
        width: 100%;
        z-index: 10;
        box-shadow: 0 4px 8px rgba(0,0,0,0.04);
    }
    #lotNoSelectOptions label {
        display: block;
        padding: 0.375rem 2.25rem 0.375rem .75rem;
        cursor: pointer;
        margin-bottom: 0;
        font-weight: normal;
        background: none;
        transition: background 0.2s;
    }
    #lotNoSelectOptions label:hover {
        background-color: #f1f1f1;
    }
    .invoice_tax_fields input {
        width:120px;
        box-sizing:border-box;
    }
</style>
<!-- \CSS ADDED BY BHUMITA ON 22/08/2025 -->
<?php
    include("include/header_close.php");
    $transactionmode="I";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    checkFrmPermission($transactionmode,$currentmenu_label,"srh_rent_invoice_master.php");
    if( $transactionmode=="U")       
    {
        $_bll->fillModel();
        $label="Update";
    } else {
            $label="Add";
    }
    /* ADDED BY BHUMITA ON 18/08/2025 */
    //invoice no and invoice sequence
    $rent_invoice_id = isset($_REQUEST['rent_invoice_id']) ? $_REQUEST['rent_invoice_id'] : null;
    $invoice_type = isset($_REQUEST['invoice_type']) ? $_REQUEST['invoice_type'] : 2;

    $sequence_data = getNextSequenceAndNo(
        $tbl_rent_invoice_master,
        'invoice_sequence',
        'invoice_no',
        'invoice_date',
        COMPANY_YEAR_ID,
        $rent_invoice_id,
        'rent_invoice_id',
        $invoice_type,
        'invoice_type'
    );
    $next_invoice_sequence = $sequence_data['next_sequence'];
    $invoice_no_formatted = $sequence_data['formatted_no'];
    $finYear = $sequence_data['fin_year'];
    $company_state=getCompanyField('state');
    /* \ADDED BY BHUMITA ON 18/08/2025 */

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
            <form id="masterForm" action="classes/cls_rent_invoice_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
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
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_rent_invoice_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_rent_invoice_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
     
<?php
$hidden_str="";
$table_name_detail="tbl_rent_invoice_detail";
$select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
$select->bindParam(1, $table_name_detail);
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);
    if($row) {
        $generator_options=json_decode($row["generator_options"]);
        if($generator_options) {
            $table_layout=$generator_options->table_layout;
            if($table_layout=="") {
                $table_layout="vertical";
            }
            $fields_names=$generator_options->field_name;
            $fields_types=$generator_options->field_type;
            $field_scale=$generator_options->field_scale;
            $dropdown_table=$generator_options->dropdown_table;
            $label_column=$generator_options->label_column;
            $value_column=$generator_options->value_column;
            $where_condition=$generator_options->where_condition;
            $fields_labels=$generator_options->field_label;
            $field_display=$generator_options->field_display;
            $field_required=$generator_options->field_required;
            $allow_zero=$generator_options->allow_zero;
            $allow_minus=$generator_options->allow_minus;
            $chk_duplicate=$generator_options->chk_duplicate;
            $field_data_type=$generator_options->field_data_type;
            $field_is_disabled=$generator_options->is_disabled;
            if(is_array($fields_names) && !empty($fields_names)) {
                if($table_layout=="horizontal") {
                    $modalcls="modal-xl";
                } else {
                    $modalcls="";
                }
?>
<!-- Modal -->
    <div class="detail-modal">
        <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable <?php echo $modalcls; ?>">
            <div class="modal-content">
            <form id="popupForm"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
              <div class="modal-header">
                  <h4 class="modal-title" id="modalToggleLabel">Add Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="box-body container-fluid">
                    <div class="form-group row" >
                    <?php    
                        for($i=0;$i<count($fields_names);$i++) {
                            if($table_layout=="vertical") {
                                $main_classes="row col-12 gy-1";
                                $label_layout_classes="col-sm-4 control-label";
                                $field_layout_classes="col-sm-8";
                            } else {
                                $main_classes="row col-sm-6 gy-1";
                                $label_layout_classes="col-sm-4 control-label";
                                $field_layout_classes="col-sm-8";
                            }
                            $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$is_disabled=0;$disabled_str="";$duplicate_str="";
                            $display_str="";
                            $cls_field_name="_".$fields_names[$i];
                                
                            if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                $required=1;
                            }
                            if(!empty($field_is_disabled) && in_array($fields_names[$i],$field_is_disabled)) {
                                $is_disabled=1;
                            }
                            
                            if(!empty($field_display) && in_array($fields_names[$i],$field_display)) {
                                $display_str="display";
                            }
                            $lbl_str='<label for="'.$fields_names[$i].'" class="'.$label_layout_classes.'">'.$fields_labels[$i].'';
                            if($required) {
                                $required_str="required";
                                $lbl_str.="*";
                                $error_container='<div class="invalid-feedback"></div>';
                            }
                            if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                                $error_container='<div class="invalid-feedback"></div>';
                                $duplicate_str="duplicate";
                                $lbl_str.="*";
                            }
                            if($is_disabled) {
                                $disabled_str="disabled";
                            }
                            if($fields_types[$i]=="email") {
                                $error_container='<div class="invalid-feedback"></div>';
                            }
                            $lbl_str.="</label>";
                            switch($fields_types[$i]) {
                                case "text":
                                case "email":
                                case "file":
                                case "date":
                                case "datetime-local":
                                case "radio":
                                case "checkbox":
                                case "number":
                                case "select":
                                    $value="";
                                    $field_str=""; $cls="";$flag=0;
                                        $table=explode("_",$fields_names[$i]);
                                        $field_name=$table[0]."_name";
                                        $fields=$fields_names[$i].", ".$table[0]."_name";
                                        $tablename="tbl_".$table[0]."_master";
                                        $selected_val="";
                                        if(isset(${"val_$fields_names[$i]"})) {
                                            $selected_val=${"val_$fields_names[$i]"};
                                        }
                                        if(!empty($where_condition[$i]))
                                            $where_condition_val=$where_condition[$i];
                                        else {
                                            $where_condition_val=null;
                                        }
                                    if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                        $cls.="form-check-input ".$display_str." ".$required_str;
                                        if($field_data_type[$i]=="bit") {
                                            $cls.=" chk";
                                        }
                                        if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                            $flag=1;
                                            $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i], $disabled_str).$error_container;
                                        } else {
                                            if(isset(${"val_$fields_names[$i]"}) &&  ${"val_$fields_names[$i]"}==1) {
                                                $chk_str="checked='checked'";
                                            }
                                            $value="1";
                                            $field_str.=addHidden($fields_names[$i],0);
                                            }
                                    } else {
                                        $cls.="form-control ".$required_str." ".$duplicate_str." ".$display_str;
                                        $chk_str="";
                                            if(isset(${"val_$fields_names[$i]"}))  {
                                            $value=$fields_names[$i];
                                            }
                                    }
                                    if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="datetime" || $fields_types[$i]=="timestamp")) {
                                        $value=date("Y-m-d",strtotime($value));
                                    }
                                    if($fields_types[$i]=="number") {
                                        $step="";$disabled_value="";$max_str="";
                                        if(!empty($field_scale[$i]) && $field_scale[$i]>0) {
                                            for($k=1;$k<$field_scale[$i];$k++) {
                                                $step.=0;
                                            }
                                            $step="0.".$step."1";
                                        } else {
                                            $step=1;
                                        }
                                        $step_str='step="'.$step.'"';
                                        $min=1; 
                                        if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                                            $min=0;
                                        if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                                        $min="";

                                        $min_str='min="'.$min.'"';
                                        $field_str.=addNumber($fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str,$fields_labels[$i],$disabled_value,$max_str).$error_container;
                                    }
                                    else if($fields_types[$i]=="select") {
                                        $cls="form-select ".$required_str." ".$duplicate_str." ".$display_str;
                                        if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                            $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val,$cls,$required_str,$disabled_str);
                                            $field_str.=$error_container;
                                        }
                                    } else {
                                            if($flag==0) {
                                                $field_str.=addInput($fields_types[$i],$fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str,$chk_str,$fields_labels[$i]).$error_container;
                                            }
                                    }
                                    break;
                                case "hidden":
                                    $lbl_str="";
                                    if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                        $hiddenvalue=0;
                                    else
                                        $hiddenvalue="";
                                    
                                        if(isset(${"val_$fields_names[$i]"})) {
                                            $hiddenvalue=${"val_$fields_names[$i]"};
                                        }
                                            if($fields_names[$i]!="rent_invoice_id") {
                                            $hidden_str.=addHidden($fields_names[$i],$hiddenvalue);
                                            }                                       
                                    break;
                                case "textarea":
                                    $cls.="form-control ".$required_str." ".$duplicate_str." ".$display_str;
                                    $value="";
                                    if(isset(${"val_$fields_names[$i]"}))
                                            $value=${"val_$fields_names[$i]"};
                                    $field_str.=addTextArea($fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str,3,$fields_labels[$i]).$error_container;
                                    break;
                                default:
                                    break;
                            } //switch ends
                                if($field_str) {
                        ?>
                            <div class="<?php echo $main_classes; ?>">
                                <?php echo $lbl_str; ?>
                                <div class="<?php echo $field_layout_classes; ?>">
                                <?php echo $field_str; ?>
                                </div>
                            </div>
                    <?php
                    }
                        } //for loop ends
                    
                    ?> 
                    </div>
              </div>
              </div>
              <div class="modal-footer">
                
                <?php echo $hidden_str; ?>
                <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value= "Save">
                <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_add" value= "Cancel" data-bs-dismiss="modal">
              </div>
                </form>
            </div> <!-- /.modal-content -->
          </div>  <!-- /.modal-dialog -->
        </div> <!-- /.modal -->
    </div>
    <!-- /Modal -->
    <?php
                    } // field_types if ends
                }
            } 
    ?>
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
/* ADDED BY BHUMTIA ON 23/08/2025 */
let company_id=<?php echo COMPANY_ID; ?>;
const financialYear = "<?php echo $finYear; ?>";   

let companyYearDetails = {
    start_date: '<?php echo isset($sequence_data["start_date"]) ? $sequence_data["start_date"] : date("Y-m-d"); ?>',
    end_date: '<?php echo isset($sequence_data["end_date"]) ? $sequence_data["end_date"] : date("Y-m-d"); ?>'
};
const transactionMode = $('#transactionmode').val();
let generatedInvoiceData = [];

<?php if ($transactionmode == "U" && !empty($generatedDetails)): ?>
    generatedInvoiceData = <?php echo json_encode($generatedDetails) ?>;
<?php endif; ?>
// Disable fields when edit
function disableInvoiceFields() {
    const fieldsToDisable = <?php echo json_encode($_bll->getDisabledOnEditFields()); ?>;
    fieldsToDisable.forEach(function(field) {
        let el = document.getElementById(field);
        if (!el) el = document.querySelector(`[name="${field}"]`);
        if (el) {
            el.setAttribute('disabled', 'disabled');
            $(`[name="${field}"]`).prop('disabled', true);
        }
    });
}
    
    /* ADDED BY MANSI ON 23/09/2025: Filter customers to only those with eligible lots for selected Invoice For + Invoice Type */
function rebuildCustomerOptions(customers) {
    const sel = document.getElementById('customer');
    if (!sel) return;
    const prev = sel.value;
    // clear options
    while (sel.options.length > 0) sel.remove(0);
    // placeholder
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = 'Select';
    sel.appendChild(opt0);
    if (Array.isArray(customers)) {
        customers.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.customer_id;
            opt.textContent = c.customer;
            sel.appendChild(opt);
        });
    }
    // reset selection and trigger change to refresh Lots
    sel.value = '';
    $('#customer').trigger('change');
}

function fetchEligibleCustomers() {
    if (transactionMode === 'U') return; // do not change on edit
    const invoiceForEl = document.getElementById('invoice_for');
    const invoiceFor = invoiceForEl ? (invoiceForEl.value || '') : '';
    const invoiceType = document.querySelector('input[name="invoice_type"]:checked')?.value || '';
    // Need both values to filter
    if (!invoiceFor || !invoiceType) {
        rebuildCustomerOptions([]);
        return;
    }
    $.ajax({
        url: 'classes/cls_rent_invoice_master.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'fetchEligibleCustomers', invoice_for: invoiceFor, invoice_type: invoiceType },
        success: function(rows) {
            console.log(rows);
            if (!Array.isArray(rows)) rows = [];
            rebuildCustomerOptions(rows);
        },
        error: function(xhr) {
            console.error('fetchEligibleCustomers error', xhr.responseText || xhr.statusText);
            rebuildCustomerOptions([]);
        }
    });
}
/* \ADDED BY MANSI ON 23/09/2025 */
       /* \ADDED BY Hetasvi ON 20/08/2025 */
   function disableAndPreserveFieldsOnGenerate() {
    const fieldsToDisable = [
        "invoice_date",
        "billing_till_date",
        "customer",
        "broker",
        "invoice_type",
        "invoice_for"
    ];
    fieldsToDisable.forEach(fieldName => {
        const fields = document.querySelectorAll(`[name="${fieldName}"]`);
        if (fields.length > 0) {
            fields.forEach(field => {
                if (!field.disabled) {
                    field.setAttribute("disabled", "disabled");
                }
            });
            if (fields[0].type === "radio" || fields[0].type === "checkbox") {
                const checked = document.querySelector(`[name="${fieldName}"]:checked`);
                if (checked) {
                    addOrUpdateHidden(fieldName, checked.value, checked);
                }
            } else if (fields[0].tagName.toLowerCase() === "select") {
                addOrUpdateHidden(fieldName, fields[0].value, fields[0]);
            } else {
                addOrUpdateHidden(fieldName, fields[0].value, fields[0]);
            }
        } else {
            const field = document.getElementById(fieldName);
            if (field && !field.disabled) {
                field.setAttribute("disabled", "disabled");
                addOrUpdateHidden(fieldName, field.value, field);
            }
        }
    });
    function addOrUpdateHidden(name, value, field) {
        let hidden = document.getElementById("hidden_" + name);
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.id = "hidden_" + name;
            hidden.name = name;
            hidden.value = value;
            field.parentNode.insertAdjacentElement("afterend", hidden);
        } else {
            hidden.value = value;
        }
    }
}
    /* \ADDED BY Hetasvi ON 20/08/2025*/
    if (transactionMode === 'U') {
        $('#generatedInvoiceGrid').show();
        disableInvoiceFields();
    }
    if (transactionMode === 'U' && $('#invoice_for').val() === '5') {
        $('#manual-invoice-details').show();
        $('#generatedInvoiceGrid').hide();
        $('#generate-btn-wrap').show();
        $('#generate').prop('disabled', true);
    }
    /* \Modified BY Hetasvi */
    function lotNoCheckboxStatusChange() {
        var multiselect = document.getElementById("lotNoSelectLabel");
        if (!multiselect) return;
        var option = multiselect.getElementsByTagName('option')[0];
        var optionsDiv = document.getElementById("lotNoSelectOptions");
        if (!optionsDiv) return;
        var allCheckbox = document.getElementById("lot_no_all");
        var checkboxes = optionsDiv.querySelectorAll('input[type=checkbox][name="lot_no[]"]');
        var checked = Array.from(checkboxes).filter(cb => cb.checked);
        var values = checked.map(cb => cb.value);
        if (allCheckbox) {
        allCheckbox.checked = (checked.length === checkboxes.length);
        }
        if (checked.length === checkboxes.length && checkboxes.length > 0) {
        option.innerText = "All";
        } else if (values.length > 0) {
        option.innerText = values.join(', ');
        } else {
        option.innerText = "Select Lot No";
        }
    }
    function toggleAllLotNoCheckboxes(allCheckbox) {
        const optionsDiv = document.getElementById('lotNoSelectOptions');
        if (!optionsDiv) return;
        const checkboxes = optionsDiv.querySelectorAll('input[type="checkbox"][name="lot_no[]"]');
        checkboxes.forEach(cb => {
        cb.checked = allCheckbox.checked;
        });
        lotNoCheckboxStatusChange();
    }
    /* \End Modified BY Hetasvi */
    function resetTax() {
        $('#sgst').val(0.00);
        $('#cgst').val(0.00);
        $('#igst').val(0.00);
    }
    function updateFieldsByInvoiceType() {
        let selectedType = document.querySelector('input[name="invoice_type"]:checked');
        let hsnDropdown = document.getElementById("hsn_code");
        if (selectedType) {
           if(company_id>0) {
                if(transactionMode=="I") {
                    hsnDropdown.removeAttribute('disabled');
                }
                let found=false;
                let unique_id1="1_"+company_id;
                let unique_id2="2_"+company_id;
                if (selectedType.value == '2') {
                    for (let option of hsnDropdown.options) {
                        if (option.dataset.unique_id === unique_id1) {
                            option.selected = true;
                            found=true;
                            break; // stop once found
                        }
                    }
                    if(!found) {
                        hsnDropdown.selectedIndex = 0;
                    }
                } else if (selectedType.value == '3') {
                    for (let option of hsnDropdown.options) {
                        if (option.dataset.unique_id === unique_id2) {
                            option.selected = true;
                            found=true;
                            break; // stop once found
                        }
                    }
                    if(!found) {
                        hsnDropdown.selectedIndex = 0;
                    }
                    // ADDED BY HETANSHREE
                    $('#invoice_for').val("1").trigger('change');
                    fetchEligibleCustomers(); 
                } else {
                    hsnDropdown.selectedIndex = 0
                    hsnDropdown.setAttribute('disabled', 'disabled');
                }
            }
            if(selectedType.value === "1" || selectedType.value === "3") {
                if(selectedType.value === "1")
                    resetTax();
                $('input[name="tax_amount"][value="1"]').prop('checked', true);
                $('input[name="tax_amount"]').prop('disabled', true);
            } else {
                if(transactionMode=="I") {
                    $('input[name="tax_amount"][value="2"]').prop('checked', true);
                    $('input[name="tax_amount"]').prop('disabled', false);
                }
                if ($('#customer').val()) {
                    $('#customer').trigger('change');
                }
            }
        }
        if ($('#hsn_code').val() && !$('#hsn_code').prop('disabled')) {
            $('#hsn_code').trigger('change');
        }
    }


/* \ADDED BY BHUMTIA ON 23/08/2025 */
document.addEventListener("DOMContentLoaded", function () {    
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       /* ADDED BY BHUMITA ON 19/09/2025 */
       if(input.name=="invoice_no"){
            column_value=document.getElementById("hid_invoice_no").value.trim();
            if(document.getElementById("invoice_sequence").value.trim()==0 || document.getElementById("invoice_sequence").value.trim()=="")
                return;
       };
       /* \ADDED BY BHUMITA ON 19/09/2025 */
       if (column_value == "") return;
       let id_column="rent_invoice_id";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_rent_invoice_master.php"; ?>",
            type: "POST",
             data: {
                column_name: input.name,
                column_value: column_value,
                id_name: id_column,
                id_value: id_value,
                table_name: "<?php echo $tbl_rent_invoice_master; ?>",
                action: "checkDuplicate",
                company_year_id: "<?php echo COMPANY_YEAR_ID; ?>" // <-- Add this on 29/09/2025 by Hetasvi
                  },   
            success: function(response) {
                response = parseInt(response);
                /* ADDED BY BHUMITA ON 19/09/2025 */
                if(input.name=="invoice_no") {
                    input=document.getElementById("invoice_sequence");
                }
                /* \ADDED BY BHUMITA ON 19/09/2025 */
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

    /* ADDED BY BHUMITA ON 21/08/2025 */
    $('#invoice_date').on('blur', function(){
        validateDate('invoice_date',companyYearDetails);
        setDefaultDates('billing_till_date',companyYearDetails);
    });
    $('#billing_till_date').on('blur', function(){
        validateDate('billing_till_date',companyYearDetails);
    });
    
   
    // Manual invoice calculation
    const qtyInput = document.getElementById('qty');
    const unitInput = document.getElementById('maual_unit');
    const weightInput = document.getElementById('weight');
    const rateInput = document.getElementById('rate_per_unit');
    const amountInput = document.getElementById('amount');
    const manualRentPerSelect = document.getElementById('manual_rent_per');
    function calculateWeight() {
        if (qtyInput && unitInput && weightInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const unit = parseFloat(unitInput.value) || 0;
            const weight = qty * unit;
            weightInput.value = isNaN(weight) ? '' : weight;
        }
    }
    function calculateAmount() {
        if (qtyInput && weightInput && rateInput && amountInput && manualRentPerSelect) {
            const qty = parseFloat(qtyInput.value) || 0;
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const selectedValue = manualRentPerSelect.value;
            let multiplier;
            if (selectedValue === '2') {
                multiplier = weight;
            } else {
                multiplier = qty;
            }
            const amount = multiplier * rate;
            amountInput.value = amount.toFixed(2);
        }
    }
    if (qtyInput) {
        qtyInput.addEventListener('input', function() {
            calculateWeight();
            calculateAmount();
        });
    }
    if (unitInput) {
        unitInput.addEventListener('input', function() {
            calculateWeight();
            calculateAmount();
        });
    }
    if (weightInput) {
        weightInput.addEventListener('input', calculateAmount);
    }
    if (rateInput) {
        rateInput.addEventListener('input', calculateAmount);
    }
    if (manualRentPerSelect) {
        manualRentPerSelect.addEventListener('change', calculateAmount);
    }
    calculateWeight();
    calculateAmount();
    /* Modified BY Hetasvi*/
    //Customer and lot no.
    var customerInput = document.getElementById('customer');
    var invoiceTypeInputs = document.querySelectorAll('input[name="invoice_type"]');
    var invoiceForSelect = document.getElementById('invoice_for'); // New: invoice_for select

    function updateLotDropdownFromInvoiceData(data) {
        const lotNoSelect = document.getElementById('lotNoSelectOptions');
        if (!lotNoSelect) return;

        if (!data || data.length === 0) {
            setLotNoOptions([]);
            return;
        }

        // Extract unique lot numbers from data
        const lotNumbers = [...new Set(data.map(item => item.lot_no).filter(lot => lot))];
        if (lotNumbers.length === 0) {
            setLotNoOptions([]);
            return;
        }

        // Populate lot number dropdown
        setLotNoOptions(lotNumbers.map(lot => ({ lot_no: lot })));
    }

    function fetchLotsForDropdown() {
        var customerId = $('#customer').val() || '';
        var invoiceType = document.querySelector('input[name="invoice_type"]:checked') ? 
                        document.querySelector('input[name="invoice_type"]:checked').value : '';
        var invoiceFor = $('#invoice_for').val() || '';

        if (!customerId || !invoiceType || !invoiceFor) {
            setLotNoOptions([]);
            return;
        }

        $.ajax({
            url: 'classes/cls_rent_invoice_detail.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action_detail: 'generateInvoice',
                customer: customerId,
                invoice_type: invoiceType,
                invoice_for: invoiceFor
            },
            success: function (data) {
                if (!Array.isArray(data)) {
                    setLotNoOptions([]);
                    return;
                }
                const lots = [...new Set(data.map(item => item.lot_no).filter(Boolean))].map(lot_no => ({ lot_no }));
                setLotNoOptions(lots);
            },
            error: function () {
                setLotNoOptions([], true);
            }
        });
    }
    // --- 2. Update event listeners ---

    // Customer change
    var customerInput = document.getElementById('customer');
    if (customerInput) {
        customerInput.addEventListener('change', fetchLotsForDropdown);
        if (customerInput.value) fetchLotsForDropdown();
        else setLotNoOptions([]);
    }

    // Invoice For change
     var invoiceForSelect = document.getElementById('invoice_for');
    if (invoiceForSelect) {
        invoiceForSelect.addEventListener('change', function(){
            fetchEligibleCustomers();   // ADDED: refresh customers when Invoice For changes
            fetchLotsForDropdown();     // existing behavior
        });
        if (invoiceForSelect.value) fetchEligibleCustomers(); // ADDED initial population
    }

    initLotNoMultiselect();
    function setLotNoOptions(lots, error = false) {
        const optionsDiv = document.getElementById('lotNoSelectOptions');
        if (!optionsDiv) return;
        if (error) {
            optionsDiv.innerHTML = '<div class="p-2 text-danger">Error loading lots</div>';
            return;
        }
        if (!lots || lots.length === 0) {
            optionsDiv.innerHTML = '<div class="p-2 text-muted">No lots found</div>';
            return;
        }
        let allCheckbox = `
            <label for="lot_no_all">
            <input type="checkbox" id="lot_no_all" onchange="toggleAllLotNoCheckboxes(this)" checked />
            All
            </label>
        `;
        let lotsCheckboxes = lots.map((lot, idx) =>
            `<label for="lot_no_${idx}">
            <input type="checkbox" id="lot_no_${idx}" value="${lot.lot_no}" onchange="lotNoCheckboxStatusChange()" name="lot_no[]" checked/>
            ${lot.lot_no}
            </label>`
        ).join('');
        optionsDiv.innerHTML = allCheckbox + lotsCheckboxes;
        lotNoCheckboxStatusChange();
    }
      
    function initLotNoMultiselect() {
        lotNoCheckboxStatusChange();
        const labelDiv = document.getElementById('lotNoSelectLabel');
        if (labelDiv) {
        labelDiv.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleLotNoCheckboxArea();
        });
        labelDiv.addEventListener('keydown', function(e) {
            if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            toggleLotNoCheckboxArea();
            }
        });
        }
        document.addEventListener("click", function(evt) {
        var flyout = document.getElementById('lotNoMultiselect');
        var target = evt.target;
        do {
            if (target == flyout) return;
            target = target.parentNode;
        } while (target);
        toggleLotNoCheckboxArea(true);
        });
    }
    function toggleLotNoCheckboxArea(onlyHide = false) {
        var checkboxes = document.getElementById("lotNoSelectOptions");
        if (!checkboxes) return;
        if (onlyHide) {
        checkboxes.style.display = "none";
        return;
        }
        checkboxes.style.display = (checkboxes.style.display !== "block") ? "block" : "none";
    }
 /* End Modified BY Hetasvi*/
    $('#hsn_code').on('change', function() {
        if ($(this).prop('disabled')) return;
        const hsnCodeId = $(this).val();
        if (!hsnCodeId) {
            resetTax();
            return;
        }
        $.ajax({
            url: 'classes/cls_rent_invoice_master.php',
            type: 'POST',
            data: { action: 'fetchHSNTaxRates', hsn_code_id: hsnCodeId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#sgst').val(response.sgst || 0.00);
                    $('#hid_sgst').val(response.sgst || 0.00);
                    $('#cgst').val(response.cgst || 0.00);
                    $('#hid_cgst').val(response.cgst || 0.00);
                    $('#igst').val(response.igst || 0.00);
                    $('#hid_igst').val(response.igst || 0.00);
                } else {
                    resetTax();
                    Swal.fire({ icon: 'error', title: 'Error', text: response.error || 'Failed to fetch tax rates' });
                }
                if(transactionMode=="I")
                {
                    recalculateInvoiceAmounts();
                }
            },
            error: function(response) {
                resetTax();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch tax rates' });
            }
        });
    });
    // events logic
    if (customerInput) {
        if (customerInput.value) {
            fetchLotsForDropdown();
        } else{
            setLotNoOptions([]);
        }
        customerInput.addEventListener('change', function(){
            fetchLotsForDropdown();
            if (!customerInput.value) {
                return;
            }
            $.ajax({
                url: 'classes/cls_rent_invoice_master.php',
                type: 'POST',
                data: { action: 'fetchCustomerState', customer_id: customerInput.value },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.state_name) {
                        const companyState = '<?php echo $company_state; ?>';
                        const customerState = response.state_name;
                        if (companyState.toLowerCase() === customerState.toLowerCase()) {
                            $('input[name="tax_amount"][value="2"]').prop('checked', true); // SGST+CGST
                        } else {
                            $('input[name="tax_amount"][value="3"]').prop('checked', true); // IGST
                        }
                    } else {
                        $('input[name="tax_amount"][value="2"]').prop('checked', true);
                    }
                },
                error: function() {
                    $('input[name="tax_amount"][value="2"]').prop('checked', true);
                },
                complete: function() {
                    const selectedType = $('input[name="invoice_type"]:checked').val();
                    if (selectedType==2 && transactionMode=="I") {
                        $('input[name="tax_amount"]').prop('disabled', false);
                    } else {
                        $('input[name="tax_amount"]').prop('disabled', true);
                    }
                }
            });
        });
    }

    function setInvoiceNumber() {
        const invoiceType = document.querySelector('input[name="invoice_type"]:checked') ? 
                            document.querySelector('input[name="invoice_type"]:checked').value : '';
        const invoiceID = document.getElementById('rent_invoice_id') ? document.getElementById('rent_invoice_id').value : '0';
        if (!invoiceType) return;
        $.ajax({
            url: 'classes/cls_rent_invoice_master.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'generateInvoiceNumber', invoice_type: invoiceType, invoice_id: invoiceID },
            success: function(response) {
                if (response.invoice_no!="") {
                    $('#invoice_no').val(response.invoice_no);
                    $('#hid_invoice_no').val(response.invoice_no);
                } 
                if(parseInt(response.invoice_sequence) > 0) {
                    $('#invoice_sequence').val(response.invoice_sequence);
                }
            },
            error: function() {
                console.log('Error generating invoice number');
            }
        });
    }
    if (invoiceTypeInputs && transactionMode=="I") {
        $('input[name="invoice_type"][value="2"]').prop('checked', true).focus(); 
    }
    invoiceTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            fetchLotsForDropdown();
            updateFieldsByInvoiceType();
            setInvoiceNumber();
        });
    });
    var invoiceForSelect = document.getElementById('invoice_for');
    if (invoiceForSelect && !invoiceForSelect.value) {
        //By default 'Regular' Invoice
        invoiceForSelect.value = "1";
        $('#invoice_for').val("1").trigger('change');
    }
    var taxAmountInputs = document.querySelectorAll('input[name="tax_amount"]');
    if (taxAmountInputs && transactionMode=="I") {
        $('input[name="tax_amount"][value="2"]').prop('checked', true); // by default SGST+CGST selected
    }

    // Generate Invoice
    function sumManualGridAmounts() {
        let total = 0;
        document.querySelectorAll('#searchDetail tbody tr:not(.norecords) td[data-label="amount"]').forEach(td => {
            const amt = parseFloat((td.textContent || '').replace(/,/g, '')) || 0;
            if (!isNaN(amt)) total += amt;
        });
        return total;
    }
    function sumGeneratedGridAmounts() {
        let total = 0;
        document.querySelectorAll('#generatedInvoiceTableBody tr:not(.norecords) td[data-label="Amount"]').forEach(td => {
            const amt = parseFloat((td.textContent || '').replace(/,/g, '')) || 0;
            if (!isNaN(amt)) total += amt;
        });
        return total;
    }
    // MANSI BY MODIFIED 
    function syncDisabledFields() {
        const basicAmount = document.getElementById("basic_amount");
        const basicAmountHidden = document.getElementById("hid_basic_amount");
        const netAmount = document.getElementById("net_amount");
        const netAmountHidden = document.getElementById("hid_net_amount");

        if (basicAmount && basicAmountHidden) {
            basicAmountHidden.value = basicAmount.value;
        }
        if (netAmount && netAmountHidden) {
            netAmountHidden.value = netAmount.value;
        }
        const sgst = document.getElementById("sgst");
        const sgstHidden = document.getElementById("hid_sgst");
        const sgstAmt = document.getElementById("sgst_amount");
        const sgstAmtHidden = document.getElementById("hid_sgst_amount");

        const cgst = document.getElementById("cgst");
        const cgstHidden = document.getElementById("hid_cgst");
        const cgstAmt = document.getElementById("cgst_amount");
        const cgstAmtHidden = document.getElementById("hid_cgst_amount");

        const igst = document.getElementById("igst");
        const igstHidden = document.getElementById("hid_igst");
        const igstAmt = document.getElementById("igst_amount");
        const igstAmtHidden = document.getElementById("hid_igst_amount");

        if (sgst && sgstHidden) sgstHidden.value = sgst.value;
        if (sgstAmt && sgstAmtHidden) sgstAmtHidden.value = sgstAmt.value;
        if (cgst && cgstHidden) cgstHidden.value = cgst.value;
        if (cgstAmt && cgstAmtHidden) cgstAmtHidden.value = cgstAmt.value;
        if (igst && igstHidden) igstHidden.value = igst.value;
        if (igstAmt && igstAmtHidden) igstAmtHidden.value = igstAmt.value;

        const subTotal = document.getElementById("sub_total");
        const subTotalHidden = document.getElementById("hid_sub_total");
        if (subTotal && subTotalHidden) subTotalHidden.value = subTotal.value;
    }

    // Modified calculateInvoiceAmount to return numeric value optionally formatted
    function calculateInvoiceAmount(rent_per_storageduration, rent_per_month, multiplier, invoice_for, invoice_days, storage_duration_id,returnFormatted = false) {
        let amount = 0;
        if (!rent_per_storageduration) rent_per_storageduration = 0;
        if (!rent_per_month) rent_per_month = 0;
        if (!multiplier) multiplier = 1;
        if (!invoice_for) invoice_for = 0;
        if (!invoice_days) invoice_days = 0;
    
        if (typeof storage_duration_id === 'number' || !isNaN(parseInt(storage_duration_id))) {
            cstorage_duration_id = parseInt(storage_duration_id);
            if (storage_duration_id === 1) {
               amount = rent_per_storageduration * multiplier * invoice_days;
            } else if (storage_duration_id === 2) {
               amount = rent_per_storageduration * multiplier * invoice_for;
            } else if (storage_duration_id === 3) {
                amount = rent_per_storageduration * multiplier * invoice_for;
            } else if (storage_duration_id === 4) {
               amount = rent_per_month * multiplier * invoice_for;
            } else if (storage_duration_id === 5 || storage_duration_id === 6 || storage_duration_id === 7) {
                const rent_per_day = rent_per_month / 30;
                amount = (multiplier * rent_per_month * invoice_for) + (multiplier * rent_per_day * invoice_days);
            } else if (storage_duration_id === 8) {
               amount = rent_per_month * multiplier * invoice_for;
            } else {
               // fallback
               amount = rent_per_storageduration * multiplier * invoice_for;
            }
        }
        if (returnFormatted) return formatNumber(amount);
        return amount;
    }

    // MODIFIED BY MANSI
    function recalculateInvoiceAmounts(baseAmount = null) {
       
        const basicAmountInput = document.getElementById("basic_amount");
        const invoiceFor = document.getElementById("invoice_for")?.value;

        let gridTotal = 0;
        if (invoiceFor === '5') {
            gridTotal = typeof sumManualGridAmounts === 'function' ? sumManualGridAmounts() : 0;
        } else {
            gridTotal = baseAmount !== null && !isNaN(baseAmount)
                ? baseAmount
                : (basicAmountInput?.dataset.baseAmount
                    ? parseFloat(basicAmountInput.dataset.baseAmount) || 0
                    : (typeof sumGeneratedGridAmounts === 'function' ? sumGeneratedGridAmounts() : 0));
        }

        if (basicAmountInput) {
            basicAmountInput.dataset.baseAmount = gridTotal.toFixed(2);
            basicAmountInput.value = gridTotal.toFixed(2);
        }
        const other1 = getFloat("other_expense1");
        const unloading = getFloat("unloading_exp");
        const loading = getFloat("loading_exp");
        const subTotalVal = gridTotal + other1 + unloading + loading;

        const subTotalInput = document.getElementById("sub_total");
        if (subTotalInput) {
            subTotalInput.value = subTotalVal.toFixed(2);
        }
        const sgstAmtInput = document.getElementById("sgst_amount");
        const cgstAmtInput = document.getElementById("cgst_amount");
        const igstAmtInput = document.getElementById("igst_amount");
        if (sgstAmtInput) sgstAmtInput.value = "0.00";
        if (cgstAmtInput) cgstAmtInput.value = "0.00";
        if (igstAmtInput) igstAmtInput.value = "0.00";

        let taxType = "";
        const taxRadio = document.querySelector('input[name="tax_amount"]:checked');
        if (taxRadio) taxType = taxRadio.value;

        const sgstField = document.getElementById("hid_sgst");
        const cgstField = document.getElementById("hid_cgst");
        const igstField = document.getElementById("hid_igst");

        let sgst = sgstField && sgstField.value ? parseFloat(sgstField.value) : 0.00;
        let cgst = cgstField && cgstField.value ? parseFloat(cgstField.value) : 0.00;
        let igst = igstField && igstField.value ? parseFloat(igstField.value) : 0.00;

        if (isNaN(sgst)) sgst = 0.00;
        if (isNaN(cgst)) cgst = 0.00;
        if (isNaN(igst)) igst = 0.00;

        if (sgst > 0) sgst = sgst / 100;
        if (cgst > 0) cgst = cgst / 100;
        if (igst > 0) igst = igst / 100;

        let sgstAmt = 0, cgstAmt = 0, igstAmt = 0;
        if (taxType === "2") {
            sgstAmt = subTotalVal * sgst;
            cgstAmt = subTotalVal * cgst;
            if (sgstAmtInput) sgstAmtInput.value = sgstAmt.toFixed(2);
            if (cgstAmtInput) cgstAmtInput.value = cgstAmt.toFixed(2);
        } /*else if (taxType === "3") {
            igstAmt = subTotalVal * igst;
            if (igstAmtInput) igstAmtInput.value = igstAmt.toFixed(2);
        }*/
        const netAmount = subTotalVal + sgstAmt + cgstAmt + igstAmt;
        const netAmountInput = document.getElementById("net_amount");
        if (netAmountInput) {
            netAmountInput.value = netAmount.toFixed(2);
        }
        syncDisabledFields();
    }
    const unloadingExpInput = document.getElementById("unloading_exp");
    if (unloadingExpInput) {
        unloadingExpInput.addEventListener("input", function () {
            recalculateInvoiceAmounts();
        });
    }
    const loadingExpInput = document.getElementById("loading_exp");
    if (loadingExpInput) {
        loadingExpInput.addEventListener("input", function () {
            recalculateInvoiceAmounts();
        });
    }
    function updateBasicAmountFromManualGrid() {
        const total = sumManualGridAmounts();
        const basicAmountInput = document.getElementById("basic_amount");
        if (basicAmountInput) {
            basicAmountInput.dataset.baseAmount = total.toFixed(2);
            recalculateInvoiceAmounts();
        }
    }
    function updateBasicAmountFromGeneratedGrid() {
        const total = sumGeneratedGridAmounts();
        const basicAmountInput = document.getElementById("basic_amount");
        if (basicAmountInput) {
            basicAmountInput.dataset.baseAmount = total.toFixed(2);
            recalculateInvoiceAmounts();
        }
    }
    function getGracedayAmt(rent_per_month, multiplier) {
        const oneDayRent=parseFloat((rent_per_month/30).toFixed(2));
        const oneDayAmt = multiplier * oneDayRent;
        return parseFloat(oneDayAmt.toFixed(2));
    }
    function editRent(cell) {
        const index = cell.dataset.index;
        const newRent = parseFloat((cell.textContent || '').replace(/,/g, '')) || 0;
        const rowData = generatedInvoiceData[index];
        const storageDurationId = rowData.storage_duration_id || 0;

        rowData.rent_per_month = newRent;
        rowData.rent_per_storage_duration=getRentPerStorageDuration(storageDurationId, newRent);
        
        const qty = parseInt(rowData.invoice_qty) || 0;
        const avgWeight= parseFloat(rowData.wt_per_kg || 0) || 0;
        const rentPerMonth = parseFloat(rowData.rent_per_month || 0) || 0;
        const rentPerStorageDuration = parseFloat(rowData.rent_per_storage_duration || 0) || 0;
        const invFor = parseInt(rowData.invoice_for) || 0;
        const invDay = parseInt(rowData.invoice_day) || 0;
        const multiplier=getAmountMultiplier(rowData.rent_per_id, qty,rowData.wt_per_kg);

        const amount = calculateInvoiceAmount(rentPerStorageDuration, rentPerMonth, multiplier, invFor, invDay, storageDurationId, false);
        rowData.invoice_amount = amount;

        const row = cell.closest('tr');
        row.querySelector('.amount').textContent = formatNumber(amount);

        generatedInvoiceData[index] = rowData;

        let totalAmt = 0;
        let graceTotal = 0;
        const graceDaysLocal = parseFloat($('#grace_days').val()) || 0;

        generatedInvoiceData.forEach(data => {
            const amt = parseFloat(data.invoice_amount) || 0;
            if (!isNaN(amt)) totalAmt += amt;

            const rowQty = parseInt(data.invoice_qty || 0);
            const rowRentMonth = parseFloat(data.rent_per_month || 0);
            const rowMultiplier = getAmountMultiplier(data.rent_per_id, rowQty,data.wt_per_kg);
            const rowOneDayAmt=getGracedayAmt(rowRentMonth,rowMultiplier);
            graceTotal += rowOneDayAmt * graceDaysLocal;
        });

        totalAmt -= graceTotal;
        if (totalAmt < 0) totalAmt = 0;
        const basicAmountInput = document.getElementById("basic_amount");
        if (basicAmountInput) {
            basicAmountInput.dataset.baseAmount = totalAmt.toFixed(2); // Save true base
        }
        ;
        const graceInfoRow = document.getElementById("generatedInvoiceTableBody").querySelector('.gracedaysRow');
        if (graceInfoRow) {
            graceInfoRow.innerHTML = `<td colspan="21" style="color:green;font-weight:600;text-align:left;">
                Grace days applied: <b>${graceDaysLocal}</b>. Deducted amount: <b>${number_format(graceTotal, 2, '.', ',')}</b> from basic amount.
            </td>`;
        }
        recalculateInvoiceAmounts(totalAmt);
    }
    if(document.querySelectorAll('.rent-editable')) {
        // Make rent editable update amount using calculateInvoiceAmount
        document.querySelectorAll('.rent-editable').forEach(cell => {
            cell.addEventListener('input', function () {
                editRent(this);
            }); // cell input
        }); // rent-editable foreach
    }

    document.getElementById("generate").addEventListener("click", function () {
        let lotNos;
        const gridContainer = document.getElementById("generatedInvoiceGrid");
        const tableBody = document.getElementById("generatedInvoiceTableBody");
        const customer = document.getElementById('customer')?.value || '';
       
        const invoiceFor = document.getElementById("invoice_for")?.value || '';
        const invoiceType = document.querySelector('input[name="invoice_type"]:checked')?.value || '';
        if(transactionMode=="U") {
            lotNos=document.getElementById("lot_no").value;
        } else {
             const lotNoCheckboxes = document.querySelectorAll('input[name="lot_no[]"]:checked');
            lotNos = Array.from(lotNoCheckboxes).map(cb => cb.value);
        }
        const customerInput=$('#customer');
        if (!customer) {
            showError(customerInput,null,'Please select customer');
            return;
        }
        // MANSI-Grace Days logic
        // Remove this block as it's redundant
        const graceDaysInput = $("#grace_days");
        const graceDays = graceDaysInput.val();
        if (graceDays!="" && (isNaN(graceDays) || graceDays < 1 || graceDays > 29)) {
            showError(graceDaysInput,null,'Grace Days must be between 1 and 29');
            return;
        } 
        $.ajax({
            url: "classes/cls_rent_invoice_detail.php",
            type: "POST",
            data: {
                action_detail: "generateInvoice",
                lot_no: lotNos,
                customer: customer,
                invoice_for: invoiceFor,
                invoice_type: invoiceType
            },
            dataType: "json",
            success: function (invoiceData) {
                tableBody.innerHTML = "";
                generatedInvoiceData = [];
                let totalAmount = 0;
                let graceDayRentTotal = 0;
                if (!invoiceData || invoiceData.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="21" style="text-align:center;">No records available.</td></tr>';
                } else {
                    invoiceData.forEach((data, index) => {
                        let amount=0;
                        const qty = parseFloat(data.qty || 0);
                        const storage_duration_name = (data.storage_duration_name || '').toLowerCase();
                        const rent_per_storage_duration=data.rent_per_storage_duration || 0.00;
                        const rent_per_month=data.rent_per_month || 0.00;
                        const invoice_for=data.invoice_for || 0;
                        const invoice_days=data.invoice_days || 0;
                        const multiplier=getAmountMultiplier(data.rent_per_id, qty,data.avg_wt_per_bag);
                        const oneDayAmt=getGracedayAmt(rent_per_month,multiplier);
                        graceDayRentTotal += oneDayAmt * graceDays;

                        // Use numeric amount from calculateInvoiceAmount
                        const amountNum = calculateInvoiceAmount(rent_per_storage_duration, rent_per_month, multiplier, invoice_for, invoice_days, data.storage_duration_id || 0, false);
                        amount = amountNum;
                        if (!isNaN(amount)) totalAmount += amount;

                        let invoiceWeight=qty * parseFloat(data.avg_wt_per_bag);
                        invoiceWeight = invoiceWeight ? invoiceWeight.toFixed(2) : '0.00';

                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td data-label="Invoice No">${data.in_no || ''}</td>
                        <td data-label="Invoice Date">${data.in_date || ''}</td>
                        <td data-label="Lot No">${data.lot_no || ''}</td>
                        <td data-label="Item">${data.item || ''}</td>
                        <td data-label="Marko">${data.marko || ''}</td>
                        <td data-label="Quantity">${data.qty || ''}</td>
                        <td data-label="Unit">${data.unit_name || ''}</td>
                        <td data-label="Weight (Kg)">${formatNumber(invoiceWeight) || ''}</td>
                        <td data-label="Storage Duration">${data.storage_duration_name || ''}</td>
                        <td data-label="Ren/Month" class="rent-editable" data-index="${index}" contenteditable="true">${rent_per_month}</td>
                        <td data-label="Per">${data.rent_per || ''}</td>
                        <td data-label="Out Date">${data.out_date || ''}</td>
                        <td data-label="Charges From">${data.charges_from || ''}</td>
                        <td data-label="Charges To">${data.charges_to || ''}</td>
                        <td data-label="Actual Month">${data.act_month || 0}</td>
                        <td data-label="Actual Days">${data.act_day || 0}</td>
                        <td data-label="Invoice For" class="invoice-for">${data.invoice_for_text || ''}</td>
                        <td data-label="Invoice Days" class="invoice-day">${invoice_days}</td>
                        <td data-label="Amount" class="amount">${formatNumber(amount)}</td>
                        <td data-label="Status">${data.status || ''}</td>
                        `;
                        tableBody.appendChild(row);

                       
                        generatedInvoiceData.push({
                            inward_id: data.inward_id || '',
                            inward_no: data.in_no || '',
                            inward_date: data.inward_date_db || '',
                            lot_no: data.lot_no || '',
                            item: data.item_id || '',
                            marko: data.marko || '',
                            invoice_qty: data.qty || '',
                            unit: data.unit_id || '',
                            wt_per_kg: data.avg_wt_per_bag || '',
                            storage_duration_id: data.storage_duration_id ?? null,
                            storage_duration_name: data.storage_duration_name || '', 
                            rent_per_storage_duration: rent_per_storage_duration,
                            rent_per_month: rent_per_month,
                            rent_per: data.rent_per_id || '',
                            outward_date: data.outward_date_db || '',
                            charges_from: data.charges_from_db || '',
                            charges_to: data.charges_to_db || '',
                            actual_month: data.act_month || 0,
                            actual_day: data.act_day || 0,
                            invoice_for: invoice_for,
                            invoice_day: invoice_days,
                            invoice_amount: amount || 0.00,
                            status: data.status || '',
                            gst_status: data.gst_status || '',
                            detailtransactionmode: 'I',
                            rent_per_id: data.rent_per_id || 0,
                            outward_detail_id: data.outward_detail_id || ''//ADDED BY MANSI OUTWARD_DETAIL_ID
                        });
                    }); // invoicedata foreach ends
                    totalAmount -= graceDayRentTotal;
                    if (totalAmount < 0) totalAmount = 0;
                    if(graceDays!="") {
                        let graceInfoRow = document.createElement("tr");
                        graceInfoRow.classList.add("gracedaysRow");
                        graceInfoRow.innerHTML = `<td colspan="21" style="color:green;font-weight:600;text-align:left;">
                            Grace days applied: <b>${graceDays}</b>. Deducted amount: <b>${number_format(graceDayRentTotal, 2, '.', ',')}</b> from basic amount.
                        </td>`;
                        tableBody.appendChild(graceInfoRow);
                    }
                    document.querySelectorAll('.rent-editable').forEach(cell => {
                        cell.addEventListener('input', function () {
                            editRent(this);
                        }); // cell input
                    });
                } // invoicedata else ends
                gridContainer.style.display = "block";
                const basicAmountInput = document.getElementById("basic_amount");
                if (basicAmountInput) {
                    basicAmountInput.dataset.baseAmount = totalAmount.toFixed(2); 
                }
                recalculateInvoiceAmounts(totalAmount); 
                 disableAndPreserveFieldsOnGenerate();//Added by Hetasvi on 20/09/25
            },  // success ends
            error: function (xhr, status, error) {
                console.error("AJAX Error:", { status, error, responseText: xhr.responseText, statusCode: xhr.status });
                tableBody.innerHTML = `<tr><td colspan="21" style="text-align:center;">Error loading data: ${xhr.responseText || error}</td></tr>`;
                gridContainer.style.display = "block";
            }
        }); // ajax ends
    }); // generate click ends

    const otherExpenseInput = document.getElementById("other_expense1");
    if (otherExpenseInput) {
        otherExpenseInput.addEventListener("input", function () {
            recalculateInvoiceAmounts(); 
        });
    }
    document.querySelectorAll('input[name="tax_amount"]').forEach(radio => {
        radio.addEventListener("change", function () {
            recalculateInvoiceAmounts();
        });
    });

    setTimeout(function(){
        // Outward sequence and inward_no logic
        setSequence("invoice_sequence","invoice_no",financialYear,true); 

        // Set and validate invoice date and billling dates
        setDefaultDates('invoice_date',companyYearDetails);
        setDefaultDates('billing_till_date',companyYearDetails);

        updateFieldsByInvoiceType();
        if (($('#hsn_code').val() && !$('#hsn_code').prop('disabled')) || transactionMode=="U") {
            $('#hsn_code').trigger('change');
        }
        if ($('#customer').val() || transactionMode=="U") {
            $('#customer').trigger('change');
        }
        // Ensure Customer list filtered on initial load (Add)
        if (transactionMode !== 'U') {
            fetchEligibleCustomers();
        }
    },500);
    duplicateInputs.forEach((input) => {
        input.addEventListener('blur', function (event) {
            setTimeout(function() {
                if(input.name==="invoice_sequence")
                {
                    input=document.getElementById("invoice_no");
                }
                checkDuplicate(input);
            },100);
        });
    });
    /* \ADDED BY BHUMITA ON 21/08/2025 */
    getSearchArray();

    $("#popupForm" ).on( "submit", function( event ) {
        event.preventDefault();
        const invalidInputs = this.querySelectorAll(".is-invalid");
        if(invalidInputs.length>0)
        {} else{
            saveData();
            updateBasicAmountFromManualGrid(); // ADDED BY BHUMTIA ON 23/08/2025
        }
    } );
   
    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        duplicateInputs.forEach((input) => {
            checkDuplicate(input);
        });
        syncDisabledFields(); // ADDED BY BHUMITA ON 23/08/2025
       checkFormValidation(form);
         setTimeout(function(){
      const invalidInputs = document.querySelectorAll(".is-invalid");
      if(invalidInputs.length>0) { } else {
        let invoiceMode = document.getElementById("invoice_for")?.value;
        let isManualMode = (invoiceMode === '5');
        const lotNoCheckboxes = document.querySelectorAll('input[name="lot_no[]"]:checked');
        const selectedLotNos = Array.from(lotNoCheckboxes).map(cb => cb.value);
        let allDetailRecords = [];
        const uniqueKeys = new Set();

            if (Array.isArray(jsonData)) {
                jsonData.forEach(record => {
                    if (!record.lot_no || record.lot_no === 'Select Lot No') {
                        selectedLotNos.forEach(lot => {
                            let newRecord = { ...record };
                            newRecord.lot_no = lot;
                            const key = `${newRecord.lot_no}_${newRecord.inward_no || ''}_${newRecord.item || ''}`;
                            if (!uniqueKeys.has(key)) {
                                uniqueKeys.add(key);
                                allDetailRecords.push(newRecord);
                            }
                        });
                        } else {
                            const key = `${record.lot_no}_${record.inward_no || ''}_${record.item || ''}`;
                            if (!uniqueKeys.has(key)) {
                                uniqueKeys.add(key);
                                allDetailRecords.push(record);
                            }
                        }
                    });
                }
                 if (Array.isArray(generatedInvoiceData) && generatedInvoiceData.length > 0) {
                  generatedInvoiceData.forEach(genRec => {
                    if (selectedLotNos.includes(genRec.lot_no)) {
                      if (genRec.storage_duration_id != null) {
                        genRec.storage_duration = genRec.storage_duration_id;
                      }
                      const key = `${genRec.lot_no}_${genRec.inward_no || ''}_${genRec.item || ''}_${genRec.outward_detail_id || 'stock'}`;//ADDED BY MANSI OUTWARD_DETAIL_ID
                      if (!uniqueKeys.has(key)) {
                        uniqueKeys.add(key);
                        allDetailRecords.push(genRec);
                      }
                    }
                  });
                }
               // const mergedArray = [...jsonData, ...allDetailRecords];
                if (isManualMode) {
                  document.getElementById("detail_records").value = JSON.stringify(jsonData);
                } else {
                  document.getElementById("detail_records").value = JSON.stringify(allDetailRecords);
                }

                document.getElementById("deleted_records").value = JSON.stringify(deleteData);
                $("#masterForm").submit();
              }
            },200);
    });
});
</script>
<?php
    frmAlert("frm_rent_invoice_master.php");
?>
<?php
    include("include/footer_close.php");
?>

