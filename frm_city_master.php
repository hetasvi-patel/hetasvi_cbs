<?php
    require_once("classes/cls_state_master.php"); //ADDED BY BHUMITA 13/07/2025
    include("classes/cls_city_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {
        if (!$canUpdate) {
            $_SESSION["sess_message"]="You don't have permission to update ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            header("Location: ".BASE_URL."srh_country_master.php");
            exit();
        }
        $_bll->fillModel();
        $label="Update";
    } else {
        if (!$canAdd) {
            $_SESSION["sess_message"]="You don't have permission to add ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            header("Location: ".BASE_URL."srh_city_master.php");
            exit();
        }
        $label="Add";
    }
    $state_bll=new bll_statemaster(); //ADDED BY BHUMITA 13/07/2025
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
            <form id="masterForm" action="classes/cls_city_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <?php
                    echo $_bll->getForm($transactionmode);
                ?>
            <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_city_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_city_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    <?php  /* ADDED BY BHUMITA 13/07/2025 */ ?>
    <div class="modal fade" id="addStateModal" tabindex="-1" aria-labelledby="addStateModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="stateForm"  method="post" class="form-horizontal needs-validation" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStateModalLabel">Add New State</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $state_bll->getForm("I",true,"col-12 col-sm-3 col-md-2 col-lg-2 col-xl-3 col-xxl-4","col-12 col-sm-9 col-md-10 col-lg-10 col-xl-9 col-xxl-8"); ?>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                        <input type="hidden" name="ajaxAdd" id="ajaxAdd" value="1" />
                        <input class="btn btn-success" type="submit" id="btnAddPopup" name="btnAddPopup" value= "Save">
                        <input class="btn btn-dark" type="button" id="btnCancelPopup" name="btnCancelPopup" value= "Cancel" data-bs-dismiss="modal">
                    </div>
                </form>
            </div>
        </div>
    </div>
   <?php  /* \ADDED BY BHUMITA 13/07/2025 */ ?>
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
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel="";
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    /* THIS FUNCTION IS MODIFIED BY BHUMITA 13/07/2025 */
    function checkDuplicate(input,form) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="city_id";
       let url="<?php echo "classes/cls_city_master.php"; ?>";
       let table_name="tbl_city_master";
       let scope_field_value = document.getElementById("state_id").value;
       let scope_field_name="state_id";
       let id_value=document.getElementById(id_column).value;
       if(form.id=="stateForm") {
           id_column="state_id";
           url="<?php echo "classes/cls_state_master.php"; ?>";
           table_name="tbl_state_master";
           scope_field_name="country_id";
           scope_field_value=$("#"+form.id+" [name="+scope_field_name+"]").val();
           id_value=0;
       }
       id_value = id_value ? id_value.trim() : 0;
       $.ajax({
            url: url,
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:table_name,scope_field_name:scope_field_name,scope_field_value:scope_field_value,action:"checkDuplicate"},
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
    /* \THIS FUNCTION IS MODIFIED BY BHUMITA 13/07/2025 */

    /* ADDED BY BHUMITA 13/07/2025 */
    if (document.querySelector('#state_id')) {
        document.getElementById("state_id").addEventListener("change", function (event) {
            fetchCountry("masterForm",this.value);
        });
    }
    $("#stateForm").on("submit", function(event) {
        event.preventDefault();
        let form = this;
        let stateDuplicateInputs = form.querySelectorAll(".duplicate");
        stateDuplicateInputs.forEach((input) => {
            checkDuplicate(input,form);
        });
        AddPopupData(form, "state");
    });
    /* \ADDED BY BHUMITA 13/07/2025 */

    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        duplicateInputs.forEach((input) => {
            checkDuplicate(input,form); // ADDED form parameter by BHUMITA 13/07/2025
        });
       checkFormValidation(form)
        setTimeout(function(){
            const invalidInputs = document.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
            
            $("#masterForm").submit();
            }
        },200);
    } );
    
       duplicateInputs.forEach((input) => {
        input.addEventListener('blur', function (event) {
            setTimeout(function() {
                // Pass both input and its parent form
                checkDuplicate(input, input.form);
            }, 100);
        });
    });
    
});
</script>
<?php
   frmAlert("frm_city_master.php");
?>
<?php
    include("include/footer_close.php");
?>