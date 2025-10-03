<?php 
//generate frm page
$_strgeneratefrm='<?php
    include("classes/cls_'.str_replace("tbl_","",$_tablename).'.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="I";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    checkFrmPermission($transactionmode,$currentmenu_label,"srh_'.str_replace("tbl_","",$_tablename).'.php");
    if( $transactionmode=="U")       
    {
        $_bll->fillModel();
        $label="Update";
    } else {
          $label="Add";
    }
';
 $_strgeneratefrm.='?>';
$_strgeneratefrm.='
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
            <form id="masterForm" action="classes/'.str_replace("tbl_","cls_",$_tablename).'.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <?php
                    echo $_bll->getForm($transactionmode);
                ?>
            ';
$_strgeneratefrm.='<!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                ';
                if($_detailtablename!="") {
                    $_strgeneratefrm.='<input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    ';
                }
                $_strgeneratefrm.='<input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location=\'srh_'.str_replace("tbl_","",$_tablename).'.php\'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById(\'masterForm\').reset();" >
                <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location=\'frm_'.str_replace("tbl_","",$_tablename).'.php\'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
    ';
if($_detailtablename!="") {
$_strgeneratefrm.=' 
<?php
$hidden_str="";
$table_name_detail="'.$_detailtablename.'";
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
                    <div class="form-group row" >';
    $_strgeneratefrm.='
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
            $lbl_str=\'<label for="\'.$fields_names[$i].\'" class="\'.$label_layout_classes.\'">\'.$fields_labels[$i].\'\';
            if($required) {
                $required_str="required";
                $lbl_str.="*";
                $error_container=\'<div class="invalid-feedback"></div>\';
            }
            if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                $error_container=\'<div class="invalid-feedback"></div>\';
                $duplicate_str="duplicate";
                $lbl_str.="*";
            }
            if($is_disabled) {
                $disabled_str="disabled";
            }
            if($fields_types[$i]=="email") {
                $error_container=\'<div class="invalid-feedback"></div>\';
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
                                $chk_str="checked=\'checked\'";
                            }
                            $value="1";
                            $field_str.=addHidden($fields_names[$i],0,"chk");
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
                        $step_str=\'step="\'.$step.\'"\';
                        $min=1; 
                        if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                            $min=0;
                        if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                        $min="";

                        $min_str=\'min="\'.$min.\'"\';
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
                            if($fields_names[$i]!="'.$_fieldname[0].'") {
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
                    
            ?>'; 
    $_strgeneratefrm.=' 
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
    ';
    }
$_strgeneratefrm.='<!-- /.container -->
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
';
if($_detailtablename!="") {
$_strgeneratefrm.='
<script src="dist/js/detail_functions.js"></script>
';
}
$_strgeneratefrm.='<script>
document.addEventListener("DOMContentLoaded", function () {    
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="'.$_fieldname[0].'";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/'.str_replace("tbl_","cls_",$_tablename).'.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo $'.$_tablename.'; ?>",action:"checkDuplicate"},
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
';


if($_detailtablename!="") {
     // Collect popup table data
    $_strgeneratefrm.='
         getSearchArray();

        $("#popupForm" ).on( "submit", function( event ) {
            event.preventDefault();
            const invalidInputs = this.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
                saveData();
            }
        } );
    ';
}
    $_strgeneratefrm.='
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
            ';
            if($_detailtablename!="") {
                $_strgeneratefrm.='const jsonDataString = JSON.stringify(jsonData);
                document.getElementById("detail_records").value = jsonDataString;

                const deletedDataString = JSON.stringify(deleteData);
                document.getElementById("deleted_records").value = deletedDataString;';
            }

            $_strgeneratefrm.='
            $("#masterForm").submit();
            }
        },200);
    } );
});
</script>
<?php
    frmAlert("'.str_replace("tbl_","frm_",$_tablename).'.php");
?>
';
$_strgeneratefrm.='<?php
    include("include/footer_close.php");
?>';
            
    $handle = fopen("../".str_replace("tbl_","frm_",$_tablename).".php", "w");
    fwrite($handle,$_strgeneratefrm); 
    //end of frm page
?>