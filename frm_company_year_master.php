<?php
    include("classes/cls_company_year_master.php");
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
            header("Location: ".BASE_URL."srh_company_year_master.php");
            exit();
        }
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
            <form id="masterForm" action="classes/cls_company_year_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <?php
                    echo $_bll->getForm($transactionmode);
                ?>
            <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_company_year_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_comapany_year_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
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
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="<?php echo "company_year_id" ?>";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_company_year_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo "tbl_company_year_master"; ?>",action:"checkDuplicate"},
            success: function(response) {
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
    /* ADDED BY BHUMITA ON 10/07/2025 */
    $(document).on('change', '#company_year_type', function() {
        var selectedType = $(this).val().trim();
        $.ajax({
            url: "classes/cls_company_year_master.php",
            type: "POST",
            data: { company_year_type: selectedType, action: "getLastYear" },
            success: function(response) {
                var data = JSON.parse(response);
                var lastStart = data.last_start;
                var startDate, endDate;

                if (selectedType === '1') {
                    if (lastStart) {
                        var d = new Date(lastStart);
                        startDate = new Date(d.getFullYear() + 1, 3, 1);
                        endDate = new Date(d.getFullYear() + 2, 2, 31);
                    } else {
                        var currentYear = new Date().getFullYear();
                        startDate = new Date(currentYear + 1, 3, 1);
                        endDate = new Date(currentYear + 2, 2, 31);
                    }
                } else if (selectedType === '2') {
                    if (lastStart) {
                        var d = new Date(lastStart);
                        // go one more step back each time!
                        startDate = new Date(d.getFullYear() - 1, 3, 1);
                        endDate = new Date(d.getFullYear(), 2, 31);
                    } else {
                        var currentYear = new Date().getFullYear();
                        startDate = new Date(currentYear - 1, 3, 1);
                        endDate = new Date(currentYear, 2, 31);
                    }
                } else {
                    alert('Invalid company type selected.');
                    return;
                }
                $('#start_date').val(formatDate(startDate));
                $('#end_date').val(formatDate(endDate));

                $('#hid_start_date').val(formatDate(startDate));
                $('#hid_end_date').val(formatDate(endDate));
            },
            error: function() {
                alert("Could not fetch last year data.");
            }
        });
    });
    /* \ADDED BY BHUMITA ON 10/07/2025 */
    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        duplicateInputs.forEach((input) => {
            checkDuplicate(input);
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
    
});
</script>
<?php
    frmAlert("frm_company_year_master.php");
?>
<?php
    include("include/footer_close.php");
?>