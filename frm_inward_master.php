<?php
    include("classes/cls_inward_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="";
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {    
        $_bll->fillModel();
        $label="Update";
    } else {
        $label="Add";
        
    }
$stmt = $_dbh->prepare("SELECT item_id, item_name, item_gst FROM tbl_item_master");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                

// To this:
$stmt = $_dbh->prepare("SELECT packing_unit_id, packing_unit_name, unloading_charge FROM tbl_packing_unit_master WHERE status = 1");
$stmt->execute();
$packingUnits = $stmt->fetchAll(PDO::FETCH_ASSOC);

global $_dbh;
$next_inward_sequence = 1;
$inward_no_formatted = '';
$finYear = '';
try {
    $companyYearId = $_SESSION['sess_company_year_id'] ?? null;

    if ($companyYearId) {
        $stmt = $_dbh->prepare("
            SELECT 
                CONCAT(LPAD(YEAR(start_date) % 100, 2, '0'), '-', LPAD(YEAR(end_date) % 100, 2, '0')) AS short_range,
                start_date, end_date
            FROM tbl_company_year_master 
            WHERE company_year_id = ?
        ");
        $stmt->execute([$companyYearId]);
        $yearRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($yearRow) {
            $finYear = $yearRow['short_range'];
            $startDate = $yearRow['start_date'];
            $endDate = $yearRow['end_date'];
            $stmt2 = $_dbh->prepare("
                SELECT MAX(inward_sequence) AS max_seq
                FROM tbl_inward_master 
                WHERE inward_date BETWEEN ? AND ?
            ");
            $stmt2->execute([$startDate, $endDate]);
            $seqRow = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($seqRow && is_numeric($seqRow['max_seq'])) {
                $next_inward_sequence = $seqRow['max_seq'] + 1;
            }
            $sequence_padded = str_pad($next_inward_sequence, 4, '0', STR_PAD_LEFT);
            $inward_no_formatted = $sequence_padded . '/' . $finYear;
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
$stmt = $_dbh->prepare("SELECT id, value, Lable FROM view_storage_duration");
$stmt->execute();
$durations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $_dbh->prepare("SELECT id, value, Lable FROM view_rent_type"); // or view_storage_duration
$stmt->execute();
$rent = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="srh_inward_master.php"><i class="fa fa-dashboard"></i> Inward Master</a></li>
          <li class="active"><?php echo $label; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_inward_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_inward_master";
            $lbl_array=array();
            $field_array=array();
            $err_array=array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
                        $table_layout=$generator_options->table_layout;
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
                        $after_detail=$generator_options->after_detail;

                         $old_table_layout=$table_layout;
                        if($table_layout=="horizontal") {
                            $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                            $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
                        } else {
                            $label_layout_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                            $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                        }
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                               if($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") {
                                    $table_layout="horizontal";
                                } else{
                                    $table_layout=$old_table_layout;
                                } 
                               $table_layout = ($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") ? "horizontal" : $old_table_layout;
                                if ($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") {
                                    if ($fields_names[$i] == "inward_sequence") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-1 mt-3";
                                    }
                                    else if ($fields_names[$i] == "inward_no") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-4 col-md-2 col-lg-2 col-xxl-1 mt-3";
                                    }
                                } else {
                                    if ($table_layout == "horizontal") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-8 col-md-9 col-lg-10";
                                    } else {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                                    }
                                }
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";
                                 $cls_field_name="_".$fields_names[$i];$is_disabled=0;$disabled_str="";
                                 
                                if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                    $required=1;
                                }
                                if (!empty($field_is_disabled) && in_array($fields_names[$i], $field_is_disabled)) {
                                            $disabled_str = "disabled";
                                        }
                                    if(
                                        (!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) ||
                                        $fields_names[$i] == 'inward_no' ||
                                        $fields_names[$i] == 'inward_sequence'
                                    ) {
                                        $error_container='<div class="invalid-feedback"></div>';
                                        $duplicate_str="duplicate";
                                    }
                                  if($fields_labels[$i]) {
                                    $lbl_str='<label for="'.$fields_names[$i].'" class="'.$label_layout_classes.'">'.$fields_labels[$i].'';
                                     if($table_layout=="vertical") {
                                        $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                                    } 
                                } else {
                                    if($table_layout=="vertical") {
                                        $field_layout_classes="col-12";
                                    } 
                                }   
                                if($required) {
                                    $required_str="required";
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $lbl_str.="*";
                                }
                                if($is_disabled) {
                                    $disabled_str="disabled";
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
                                        $value="";$field_str="";$cls="";$flag=0;
                                         $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected_val="";
                                            if(isset($_bll->_mdl->$cls_field_name)) {
                                                $selected_val=$_bll->_mdl->$cls_field_name;
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                            if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                             $cls.=$required_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $flag=1;
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i]).$error_container;
                                            }
                                            else{
                                                    if($transactionmode=="U" && $_bll->_mdl->$cls_field_name==1) {
                                                        $chk_str="checked='checked'";
                                                    }
                                                    $value="1";
                                                    $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                            }
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str;
                                            $chk_str="";
                                              if (($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") && $transactionmode != "U") {
                                                    if ($fields_names[$i] == "inward_sequence") {
                                                        $value = $next_inward_sequence;
                                                    } else {
                                                        $value = $inward_no_formatted;
                                                    }
                                                    $readonly_str = "readonly";
                                                } else {
                                                $value = isset($_bll->_mdl) ? $_bll->_mdl->$cls_field_name : "";
                                            }
                                        }
                                         if($fields_types[$i]=="number") {
                                            $step="";
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
                                         }
                                    if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="date         time" || $fields_types[$i]=="timestamp")) {
                                                $value=date("Y-m-d",strtotime($value));
                                         }
                                         
                                   if ($fields_names[$i] == 'inward_date' && empty($value)) {
                                            $value = '';
                                        }

                                        if ($fields_names[$i] == 'billing_starts_from' && empty($value)) {
                                            $value = '';
                                        }

                                        // Add error containers for both date fields
                                        if ($fields_names[$i] == 'inward_date') {
                                            $error_container = '<div id="inward_date_error" class="invalid-feedback"></div>';
                                        } elseif ($fields_names[$i] == 'billing_starts_from') {
                                            $error_container = '<div id="billing_starts_from_error" class="invalid-feedback"></div>';
                                        }

                                         if($fields_types[$i]=="select") {
                                            $cls="form-select ".$required_str." ".$duplicate_str;
                                           
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i]))
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str).$error_container;
                                        } else {
                                            if($flag==0) {
                                                $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$min_str.' '.$step_str.' '.$chk_str.'  '.$disabled_str.' '.$required_str.' />
                                                '.$error_container;
                                            }
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                        if($fields_names[$i]!="modified_by" && $fields_names[$i]!="modified_date") {
                                            if($fields_names[$i]=="company_id") {
                                                $hiddenvalue=COMPANY_ID;
                                            }
                                             if($fields_names[$i]=="company_year_id") {
                                                $hiddenvalue=COMPANY_YEAR_ID;
                                            }                                           
                                            else if($fields_names[$i]=="created_by") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=USER_ID;
                                                }
                                            } else if($fields_names[$i]=="created_date") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=date("Y-m-d H:i:s");
                                                }
                                            } else {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } 
                                            }
                                            $hidden_str.='
                                            <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'"  />';
                                        }
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset($_bll->_mdl)){
                                             $value=$_bll->_mdl->$cls_field_name;
                                            }
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" '.$disabled_str.' placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'"  '.$required_str.' >'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 $cls_err="";
                                    $lbl_err="";
                                   
                                if(empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i],$after_detail))) {
                                    if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                <div class="row mb-3 align-items-center">
                                <?php
                                    }
                                    echo $lbl_str;
                                    if($field_str) {
                                        $extra_margin_class = ($fields_names[$i] == 'inward_date') ? ' mt-3' : '';
                                    ?>
                                    <div class="<?php echo $field_layout_classes." ".$cls_err.$extra_margin_class; ?>">
                                    <?php
                                        echo $field_str;
                                        echo $lbl_err;
                                    ?>
                                    </div>
                                <?php
                                    }
                                if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                </div>
                                <?php
                                    } // verticle condition ends
                                } else {
                                    $lbl_array[]=$lbl_str;
                                    $field_array[]=$field_str;
                                    $err_array[]=$lbl_err;
                                    $clserr_array[]=$cls_err;
                                }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            
            ?>
                 </div><!-- /.row -->
              </div>
              <!-- /.box-body -->
            <!-- detail table content-->
                <div class="box-body">
                    <div class="box-detail">
                        <?php
                            if(isset($_blldetail))
                                $_blldetail->pageSearch(); 
                        ?>
                        <button type="button" name="detailBtn" id="detailBtn" class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#modalDialog"  onclick="openModal()">Add Detail Record</button>
                </div>
              </div>
              <!-- /.box-body detail table content -->
<?php
    if(!empty($field_array)) {
?>
     <!-- remaining main table content-->
    <div class="box-body">
    <div class="form-group row gy-2">
    <?php
        for ($j = 0; $j < count($field_array); $j++) {
            echo $lbl_array[$j];
            if ($field_array[$j]) {
    ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array[$j]; ?>">
                <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
                ?>
            </div>
    <?php
            }
        }
    ?>
    </div>  
</div>
<?php
    } // empty detail array if ends
?>
<!-- .box-footer -->
              <div class="box-footer">
               <?php echo  $hidden_str; ?>
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value="<?php echo USER_ID; ?>">
                <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_inward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location=window.history.back();">
                  <input type="hidden" id="inward_no_hidden" name="inward_no" value="<?php echo $inward_no_formatted; ?>">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
     <!-- Modal -->
<!-- Modal -->
<div class="detail-modal">
    <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
        <form id="popupForm" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
          <div class="modal-header">
              <h4 class="modal-title" id="modalToggleLabel">Add Inward Details</h4>
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
                                <input type="text" class="form-control" id="lot_no" name="lot_no" placeholder="Enter Lot No" required />
                                <div class="invalid-feedback">Please enter the Lot No.</div>
                            </div>
                        </div>
                        
                        <!-- Item Dropdown -->
                        <div class="row mb-2">
                            <label for="item" class="col-12 col-sm-4 control-label">Item *</label>
                            <div class="col-12 col-sm-8">
                                <select class="form-select" id="item" name="item" required>
                                    <option value="">Select Item</option>
                                    <?php 
    
                                    foreach ($items as $item): ?>
                                        <option value="<?php echo htmlspecialchars($item['item_id']) ?>" 
                                                data-gst="<?php echo htmlspecialchars($item['item_gst']) ?>">
                                            <?php echo htmlspecialchars($item['item_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select an item.</div>
                            </div>
                        </div>
                        
                        <!-- GST Type Radio Buttons -->
                        <div class="row mb-2">
                            <label class="col-12 col-sm-4 control-label">GST Type</label>
                            <div class="col-12 col-sm-8 pt-2 d-flex">
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="gst_type" value="1" checked
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Applicable
                                    </label>
                                </div>
                                <div class="form-check form-check-inline" style="margin-left: 20px;">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="gst_type" value="2"
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Exempted
                                    </label>
                                </div>
                                <div class="form-check form-check-inline" style="margin-left: 20px;">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="gst_type" value="3"
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Not Applicable
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="variety" class="col-12 col-sm-4 control-label">Variety</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="variety" name="variety" placeholder="Enter Variety">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="packing_unit" class="col-12 col-sm-4 control-label">Packing Unit *</label>
                            <div class="col-12 col-sm-8">
                                <select class="form-select" id="packing_unit" name="packing_unit" required>
                                    <option value="">Select Unit</option>
                                    <?php 
                                    foreach ($packingUnits as $unit): ?>
                                        <option value="<?php echo htmlspecialchars($unit['packing_unit_id']) ?>">
                                            <?php echo htmlspecialchars($unit['packing_unit_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a packing unit.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="inward_qty" class="col-12 col-sm-4 control-label">Inward Qty *</label>
                            <div class="col-12 col-sm-8">
                                <input type="number" class="form-control" id="inward_qty" name="inward_qty" placeholder="Enter Inward Qty" required>
                                <div class="invalid-feedback">Please enter the inward quantity.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="inward_wt" class="col-12 col-sm-4 control-label">Inward Wt.(Kg.)</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="inward_wt" name="inward_wt" placeholder="Enter Inward Wt">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="avg_wt_per_bag" class="col-12 col-sm-4 control-label">Avg. Wt. / Unit</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="avg_wt_per_bag" name="avg_wt_per_bag" placeholder="Enter Avg Wt Unit">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label class="col-12 col-sm-4 control-label">Location *</label>
                            <div class="col-12 col-sm-8">
                                <div class="row g-0">
                                    <!-- Chamber Dropdown -->
                                    <div class="col-4">
                                        <select class="form-select rounded-0" id="chamber" name="chamber" required>
                                            <option value="">Chamber</option>
                                            <?php 
                                            $chambers = $_dbh->query("SELECT chamber_id, chamber_name FROM tbl_chamber_master")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($chambers as $chamber): ?>
                                                <option value="<?php echo htmlspecialchars($chamber['chamber_id']) ?>">
                                                    <?php echo htmlspecialchars($chamber['chamber_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a chamber.</div>
                                    </div>
                                    
                                    <!-- Floor Dropdown -->
                                    <div class="col-4">
                                        <select class="form-select rounded-0" id="floor" name="floor" required>
                                            <option value="">Floor</option>
                                            <?php 
                                            $floors = $_dbh->query("SELECT floor_id, floor_name FROM tbl_floor_master")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($floors as $floor): ?>
                                                <option value="<?php echo htmlspecialchars($floor['floor_id']) ?>">
                                                    <?php echo htmlspecialchars($floor['floor_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a floor.</div>
                                    </div>
                                    
                                    <!-- Rack Input -->
                                    <div class="col-4">
                                        <input type="text" class="form-control rounded-0" id="rack" name="rack" placeholder="Rack">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="location" class="col-12 col-sm-4 control-label">Location</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="location_display" placeholder="Location" disabled>
                                <input type="hidden" id="location" name="location">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="moisture" class="col-12 col-sm-4 control-label">Moisture</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="moisture" name="moisture" placeholder="Enter Moisture">
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
                                    <select class="form-select rounded-0" id="storageDuration" name="storage_duration" required>
                                        <option value="">Select Duration</option>
                                        <?php foreach ($durations as $duration): ?>
                                            <option value="<?php echo htmlspecialchars($duration['id']); ?>"
                                                data-value="<?php echo htmlspecialchars($duration['value']); ?>"
                                                data-label="<?php echo htmlspecialchars($duration['Lable']); ?>"
                                                <?php if ($duration['value'] === 'Seasonal') echo 'data-is-seasonal="true"'; ?>>
                                            <?php echo htmlspecialchars($duration['value']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a storage duration.</div>
                                </div>
                                    
                                    <!-- Rent Per Dropdown -->
                                    <div class="col-6 ps-1">
                                    <select class="form-select rounded-0 seasonal-required" id="rentPer" name="rentPer" required>
                                        <option value="">Rent Per</option>
                                        <?php foreach ($rent as $row): ?>
                                             <option value="<?php echo htmlspecialchars($row['id']) ?>">    
                                                <?php echo htmlspecialchars($row['Lable']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select rent type.</div>
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
                                <input type="date" class="form-control seasonal-required" id="seasonal_start_date" name="seasonal_start_date">
                                <div class="invalid-feedback">Please select a start date.</div>
                            </div>
                            <div class="col-sm-2 text-end">
                                <label for="seasonal_end_date" class="control-label">End Date *</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="date" class="form-control seasonal-required" id="seasonal_end_date" name="seasonal_end_date">
                                <div class="invalid-feedback">Please select an end date.</div>
                            </div>
                        </div>
                    </div>
                        
                        <div class="row mb-2" id="rentPerMonthRow">
                            <label for="rentPerMonth" class="col-12 col-sm-4 control-label">Rent / Month</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="rentPerMonth" name="rent_per_month" placeholder="Enter Rent / Month">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="rentStorageDuration" id="rentLabel" class="col-12 col-sm-4 control-label">Rent / Storage Duration</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="rentStorageDuration" name="rent_per_storage_duration" placeholder="Enter Rent / Storage Duration" >
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                                <label for="unloading_charge_amount" class="col-12 col-sm-4 control-label">Unloading Charge</label>
                                <div class="col-12 col-sm-8">
                                    <input type="text" class="form-control" id="unloading_charge_amount" name="unloading_charge" placeholder="Enter Unloading Charge">
                                </div>
                            </div>

                        
                        <div class="row mb-2">
                            <label for="remark" class="col-12 col-sm-4 control-label">Remark</label>
                            <div class="col-12 col-sm-8">
                                <textarea class="form-control" id="remark" name="remark" rows="2" placeholder="Enter Remark"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inward_detail_id" name="inward_detail_id" value="0">
            <input type="hidden" id="detailtransactionmode" name="detailtransactionmode" value="I">
            <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value="Save">
            <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_cancel" value="Cancel" data-bs-dismiss="modal">
          </div>
        </form>
        </div>
      </div>
    </div>
</div>
    <!-- /Modal -->
    
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
const financialYear = "<?php echo $finYear; ?>";
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    const detailIdLabel = "inward_detail_id";
    let conversionFactor = 0; // Store conversion factor globally
    let isDuplicate = false; // Track duplicate status
    
    // DOM elements
    const tableBody = document.getElementById("tableBody");
    const form = document.getElementById("popupForm");
    const modalDialog = document.getElementById("modalDialog");
    const modal = new bootstrap.Modal(modalDialog);
    
    // Initialize existing table rows
    document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
        if (!row.classList.contains("norecords")) {
            let rowData = {};
            rowData[row.dataset.label] = row.dataset.id;
            
            row.querySelectorAll("td[data-label]").forEach(td => {
                if (!td.classList.contains("actions")) {
                    rowData[td.dataset.label] = td.innerText;
                    if (td.dataset.label === "item_name" && td.dataset.item) {
                        rowData["item"] = td.dataset.item;
                    }
                    if (td.dataset.label === "packing_unit_name" && td.dataset.packing_unit) {
                        rowData["packing_unit"] = td.dataset.packing_unit;
                    }
                }
            });
            
            // Handle hidden fields
            const hiddenFields = ['inward_detail_id', 'seasonal_start_date', 'seasonal_end_date', 
                     'seasonal_rent', 'seasonal_rent_per', 'unloading_charge', 
                     'remark', 'rent_per'];
            hiddenFields.forEach(field => {
                const hiddenCell = row.querySelector(`td[data-label="${field}"]`);
                if (hiddenCell) {
                    rowData[field] = hiddenCell.getAttribute('data-value') ?? hiddenCell.innerText;
                }
            });
            
            rowData["detailtransactionmode"] = "U";
            jsonData.push(rowData);
        }
    });

    // Modal event listeners
    modalDialog.addEventListener("hidden.bs.modal", function () {
        clearForm(form);
        editIndex = -1;
        document.getElementById("detailtransactionmode").value = "I";
        conversionFactor = 0; // Reset conversion factor when modal closes
    });

    // Open modal function
    window.openModal = function(index = -1) {
        if (index >= 0) {
            editIndex = index;
            const data = jsonData[index];

            // Set dropdown values
            if (data['item'] !== undefined && data['item'] !== null) {
                const itemSelect = document.getElementById('item');
                if (itemSelect) itemSelect.value = String(data['item']);
            }
            
            if (data['packing_unit'] !== undefined && data['packing_unit'] !== null) {
                const packingUnitSelect = document.getElementById('packing_unit');
                if (packingUnitSelect) {
                    packingUnitSelect.value = String(data['packing_unit']);
                    // Fetch conversion factor for the selected packing unit
                    fetch("classes/cls_inward_master.php?action=fetchPackingUnitData&packing_unit=" + encodeURIComponent(data['packing_unit']))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                conversionFactor = parseFloat(data.conversion_factor) || 0;
                                const avgWtPerBagInput = document.getElementById("avg_wt_per_bag");
                                if (avgWtPerBagInput) avgWtPerBagInput.value = conversionFactor ? conversionFactor : "";
                            }
                        });
                }
            }

            if (data['rent_per'] !== undefined && data['rent_per'] !== null) {
                const rentPerSelect = document.getElementById('rentPer');
                if (rentPerSelect) {
                    rentPerSelect.value = String(data['rent_per']);
                    rentPerSelect.dispatchEvent(new Event('change'));
                }
            }

            // Set location fields
            if (data['location'] !== undefined && data['location'] !== null) {
                const locationDisplay = document.getElementById('location_display');
                if (locationDisplay) locationDisplay.value = data['location'];
                const locationParts = data['location'].split(' - ');
                
                if (locationParts.length >= 3) {
                    const chamberSelect = document.getElementById('chamber');
                    if (chamberSelect) {
                        for (let i = 0; i < chamberSelect.options.length; i++) {
                            if (chamberSelect.options[i].text === locationParts[0].trim()) {
                                chamberSelect.value = chamberSelect.options[i].value;
                                break;
                            }
                        }
                    }
                    
                    const floorSelect = document.getElementById('floor');
                    if (floorSelect) {
                        for (let i = 0; i < floorSelect.options.length; i++) {
                            if (floorSelect.options[i].text === locationParts[1].trim()) {
                                floorSelect.value = floorSelect.options[i].value;
                                break;
                            }
                        }
                    }
                    
                    const rackInput = document.getElementById('rack');
                    if (rackInput) {
                        rackInput.value = locationParts[2].trim();
                    }
                }
            }
            // SET SEASONAL DATES
            if (data['seasonal_start_date']) {
                const ssdInput = document.getElementById('seasonal_start_date');
                if (ssdInput) ssdInput.value = data['seasonal_start_date'];
            }
            if (data['seasonal_end_date']) {
                const sedInput = document.getElementById('seasonal_end_date');
                if (sedInput) sedInput.value = data['seasonal_end_date'];
            }
            // Set rent fields
            if (data['rent_per_month'] !== undefined && data['rent_per_month'] !== null) {
                const rentPerMonthInput = document.getElementById('rentPerMonth');
                if (rentPerMonthInput) {
                    rentPerMonthInput.value = data['rent_per_month'];
                    isRentPerMonthManuallyEdited = true;
                }
            }
            if (data['rent_per_storage_duration'] !== undefined && data['rent_per_storage_duration'] !== null) {
                const rentStorageDurationInput = document.getElementById('rentStorageDuration');
                if (rentStorageDurationInput) {
                    rentStorageDurationInput.value = data['rent_per_storage_duration'];
                    isRentStorageDurationManuallyEdited = true;
                }
            }

            // Set other form fields
            for (let key in data) {
                if (key === "item" || key === "packing_unit" || key === "location" || key === "seasonal_rent_per") continue;
                
                let input = form.elements[key];
                if (!input) input = form.querySelector(`textarea[name='${key}']`);
                
                if (input) {
                    if (input.type === "radio") {
                        const radio = form.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                        if (radio) radio.checked = true;
                    } else if (input.type !== "hidden") {
                        input.value = data[key] || "";
                    }
                }
            }
            
            // Set storage duration
            const storageDurationSelect = document.getElementById('storageDuration');
            if (storageDurationSelect && data['storage_duration']) {
                for (let i = 0; i < storageDurationSelect.options.length; i++) {
                    if (storageDurationSelect.options[i].text === data['storage_duration']) {
                        storageDurationSelect.value = storageDurationSelect.options[i].value;
                        break;
                    }
                }
                const event = new Event('change');
                storageDurationSelect.dispatchEvent(event);
            }
            
            document.getElementById("detailtransactionmode").value = "U";
        } else {
            clearForm(form);
            document.getElementById("detailtransactionmode").value = "I";
            conversionFactor = 0; // Reset conversion factor for new entry
        }
        
        modal.show();
        setTimeout(() => {
            const firstInput = form.querySelector("input:not([type=hidden]), select, textarea");
            if (firstInput) firstInput.focus();
        }, 100);
    };

    // Save data function
    function saveData() {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            let firstInvalid = null;
            
            form.querySelectorAll(":invalid").forEach(input => {
                input.classList.add("is-invalid");
                if (!firstInvalid) firstInvalid = input;
            });
            
            if (firstInvalid) firstInvalid.focus();
            return false;
        }
        
        const formData = new FormData(form);
        const newEntry = {};
        for (const [key, value] of formData.entries()) {
            newEntry[key] = value;
        }
      
        // Set item and packing unit names
        const itemDropdown = document.getElementById("item");
        const selectedItem = itemDropdown.options[itemDropdown.selectedIndex];
        newEntry['item_name'] = selectedItem.text;
        newEntry['item'] = selectedItem.value;
        
        const packingUnitDropdown = document.getElementById("packing_unit");
        const selectedUnit = packingUnitDropdown.options[packingUnitDropdown.selectedIndex];
        newEntry['packing_unit_name'] = selectedUnit.text;
        newEntry['packing_unit'] = selectedUnit.value;
        newEntry['rent_per'] = form.elements['rentPer'] ? form.elements['rentPer'].value : "";
        
        // Combine location fields
        const chamberSelect = document.getElementById('chamber');
        const floorSelect = document.getElementById('floor');
        const rackInput = document.getElementById('rack');
        const chamberText = chamberSelect && chamberSelect.selectedOptions.length > 0 ? chamberSelect.selectedOptions[0].text.trim() : '';
        const floorText = floorSelect && floorSelect.selectedOptions.length > 0 ? floorSelect.selectedOptions[0].text.trim() : '';
        const rackText = rackInput ? rackInput.value.trim() : '';
        const locationCombined = [chamberText, floorText, rackText].filter(Boolean).join(' - ');
        newEntry['location'] = locationCombined;
        
        const locationHidden = document.getElementById('location');
        if (locationHidden) locationHidden.value = locationCombined;
        
        // Handle storage duration and rent fields
        const storageDurationOption = form.elements['storageDuration'].options[form.elements['storageDuration'].selectedIndex];
        const value = (storageDurationOption.getAttribute('data-value') || storageDurationOption.value).trim().toLowerCase();
        const isSeasonal = storageDurationOption.getAttribute('data-is-seasonal') === "true";
        const storeBoth = (
            isSeasonal ||
            ['1 day','1 month','1 month 7 days','1 month 15 days','2 month','2 months'].includes(value)
        );
        const storeOnlyStorageDuration = (
            ['daily','weekly','fortnightly','monthly'].includes(value)
        );
        
        let rentPerMonth = form.elements['rentPerMonth'] ? (form.elements['rentPerMonth'].value ?? "") : "";
        let rentPerStorageDuration = form.elements['rentStorageDuration'] ? (form.elements['rentStorageDuration'].value ?? "") : "";
        
        if (storeBoth) {
            newEntry['rent_per_month'] = rentPerMonth;
            newEntry['rent_per_storage_duration'] = rentPerStorageDuration;
        } else if (storeOnlyStorageDuration) {
            newEntry['rent_per_month'] = "";
            newEntry['rent_per_storage_duration'] = rentPerStorageDuration;
        } else {
            newEntry['rent_per_month'] = rentPerMonth;
            newEntry['rent_per_storage_duration'] = rentPerStorageDuration;
        }

        // Set transaction mode
        const isEdit = editIndex >= 0;
        newEntry['detailtransactionmode'] = isEdit ? "U" : "I";
       
        if (isEdit) {
            newEntry['inward_detail_id'] = jsonData[editIndex]['inward_detail_id'] || "0";
        } else {
            newEntry['inward_detail_id'] = "0"; 
        }

        // Update or add data
        if (isEdit) {
            jsonData[editIndex] = newEntry;
            updateTableRow(editIndex, newEntry);
            showSuccessAlert("Updated Successfully", "The record has been updated successfully!");
        } else {
            jsonData.push(newEntry);
            appendTableRow(newEntry, jsonData.length - 1);
            showSuccessAlert("Added Successfully", "The record has been added successfully!");
        }
        
        calculateTotals();
        modal.hide();
        return false;
    }

    // Duplicate check for inputs with 'duplicate' class
document.querySelectorAll('input.duplicate').forEach(input => {
    input.addEventListener('blur', function() {
        const columnName = this.name;
        const columnValue = this.value;
        const idName = 'inward_id';
        const idValue = document.getElementById('inward_id')?.value || '0';
        const tableName = 'tbl_inward_master';

        if (!columnValue) {
            this.classList.remove('is-invalid');
            const errorContainer = this.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                errorContainer.textContent = '';
            }
            return;
        }

        fetch('classes/cls_inward_master.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                type: 'ajax',
                column_name: columnName,
                column_value: columnValue,
                id_name: idName,
                id_value: idValue,
                table_name: tableName
            })
        })
        .then(response => response.text())
        .then(response => {
            console.log(`Duplicate check for ${columnName}:`, response); // Debug log
            const result = parseInt(response, 10);
            const errorContainer = input.nextElementSibling;
            if (result === 1) {
                isDuplicate = true;
                input.classList.add('is-invalid');
                if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                    errorContainer.textContent = 'Duplicate Value';
                }
            } else {
                isDuplicate = false;
                input.classList.remove('is-invalid');
                if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                    errorContainer.textContent = '';
                }
            }
        })
        .catch(error => {
            console.error('Duplicate check failed:', error);
            isDuplicate = false;
            input.classList.remove('is-invalid');
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                errorContainer.textContent = '';
            }
        });
    });
});


    function appendTableRow(rowData, index) {
        const row = document.createElement("tr");
        row.setAttribute("data-id", rowData[detailIdLabel] || "");
        row.setAttribute("data-label", detailIdLabel);  
        
        addActions(row, index, rowData[detailIdLabel] || "");
        
        const displayFields = [
            'lot_no', 'item_name', 'gst_type', 'variety', 'packing_unit_name', 
            'inward_qty', 'inward_wt', 'avg_wt_per_bag', 'location', 'moisture',
            'storage_duration', 'rent_per_month', 'rent_per_storage_duration'
        ];
        
        displayFields.forEach(field => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", field);
            cell.textContent = rowData[field] || "";
            row.appendChild(cell);
        });
        
        const noRecordsRow = document.getElementById("norecords");
        if (noRecordsRow) {
            noRecordsRow.remove();
        }
        
        tableBody.appendChild(row);
    }

    function updateTableRow(index, rowData) {
        const row = tableBody.children[index];
        row.innerHTML = "";
        addActions(row, index, rowData[detailIdLabel] || "");
        
        const displayFields = [
            'lot_no', 'item_name', 'gst_type', 'variety', 'packing_unit_name', 
            'inward_qty', 'inward_wt', 'avg_wt_per_bag', 'location', 'moisture',
            'storage_duration', 'rent_per_month', 'rent_per_storage_duration'
        ];
        
        displayFields.forEach(field => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", field);
            cell.textContent = rowData[field] || "";
            row.appendChild(cell);
        });
    }

    function addActions(row, index, id) {
        const actionCell = document.createElement("td");
        actionCell.classList.add("actions");
        
        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.classList.add("btn", "btn-info", "btn-sm", "me-2", "edit-btn");
        editButton.setAttribute("data-index", index);
        editButton.setAttribute("data-id", id);
        
        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.classList.add("btn", "btn-danger", "btn-sm", "delete-btn");
        deleteButton.setAttribute("data-index", index);
        deleteButton.setAttribute("data-id", id);
        
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);
    }

    function deleteRow(index, id) {
        Swal.fire({
            title: "Are you sure you want to delete this record?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                if (id && id !== "0") {
                    const deletedRecord = jsonData[index];
                    deletedRecord.detailtransactionmode = "D";
                    deleteData.push(deletedRecord);
                }
                
                jsonData.splice(index, 1);
                rebuildTable();
                calculateTotals();
                Swal.fire("Deleted!", "The record has been deleted.", "success");
            }
        });
    }

    function rebuildTable() {
        tableBody.innerHTML = "";
        
        if (jsonData.length === 0) {
            const noRecordsRow = document.createElement("tr");
            noRecordsRow.id = "norecords";
            noRecordsRow.classList.add("norecords");
            
            const noRecordsCell = document.createElement("td");
            noRecordsCell.colSpan = 14; 
            noRecordsCell.textContent = "No records available";
            
            noRecordsRow.appendChild(noRecordsCell);
            tableBody.appendChild(noRecordsRow);
        } else {
            jsonData.forEach((data, index) => {
                appendTableRow(data, index);
            });
        }
    }

    // Helper functions
    function clearForm(form) {
        form.reset();
        form.querySelectorAll(".is-invalid").forEach(input => {
            input.classList.remove("is-invalid");
        });
        form.querySelectorAll(".invalid-feedback").forEach(el => {
            el.textContent = "";
        });
    }

    function showSuccessAlert(title, text) {
        Swal.fire({
            icon: "success",
            title: title,
            text: text,
            showConfirmButton: true,
            showClass: { popup: "" },
            hideClass: { popup: "" }
        });
    }

    function calculateTotals() {
        let totalQty = 0;
        let totalWt = 0;
        
        jsonData.forEach(row => {
            const qty = parseFloat(row.inward_qty) || 0;
            const wt = parseFloat(row.inward_wt) || 0;
            totalQty += qty;
            totalWt += wt;
        });
        
        document.getElementById('total_qty').value = totalQty;
        document.getElementById('total_wt').value = totalWt.toFixed(2); 
    }

    // Event listeners
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("edit-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            openModal(index);
        }
        
        if (event.target.classList.contains("delete-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            const id = event.target.getAttribute("data-id");
            deleteRow(index, id);
        }
    });

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        saveData();
    });

document.getElementById("btn_add").addEventListener("click", function(event) {
    let hasDuplicate = false;
    let firstDuplicateField = null;
    
    // Check only inward_no and inward_sequence fields
document.querySelectorAll('input.duplicate').forEach(input => {
    input.addEventListener('blur', function() {
        const columnName = this.name;
        const columnValue = this.value;
        const idName = 'inward_id';
        const idValue = document.getElementById('inward_id')?.value || '0';
        const tableName = 'tbl_inward_master';

        if (!columnValue) {
            this.classList.remove('is-invalid');
            const errorContainer = this.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                errorContainer.textContent = '';
            }
            return;
        }

        fetch('classes/cls_inward_master.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                type: 'ajax',
                column_name: columnName,
                column_value: columnValue,
                id_name: idName,
                id_value: idValue,
                table_name: tableName
            })
        })
        .then(response => response.text())
        .then(response => {
            console.log(`Duplicate check for ${columnName}:`, response);
            const result = parseInt(response, 10);
            const errorContainer = input.nextElementSibling;
            console.log(`Error container for ${columnName}:`, errorContainer);
            if (result === 1) {
                isDuplicate = true;
                input.classList.add('is-invalid');
                if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                    errorContainer.textContent = 'Duplicate Value';
                }
            } else {
                isDuplicate = false;
                input.classList.remove('is-invalid');
                if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                    errorContainer.textContent = '';
                }
            }
        })
        .catch(error => {
            console.error('Duplicate check failed:', error);
            isDuplicate = false;
            input.classList.remove('is-invalid');
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('invalid-feedback')) {
                errorContainer.textContent = '';
            }
        });
    });
});



    if (hasDuplicate) {
        event.preventDefault();
        if (firstDuplicateField) {
            firstDuplicateField.focus();
        }
        return;
    }

    // Set JSON strings
    document.getElementById("detail_records").value = JSON.stringify(jsonData);
    document.getElementById("deleted_records").value = JSON.stringify(deleteData);

    const transactionMode = document.getElementById("transactionmode").value;
    const isUpdate = transactionMode === "U";
    const message = isUpdate ? "Record updated successfully!" : "Record added successfully!";
    const title = isUpdate ? "Update Successful!" : "Save Successful!";
    const icon = "success";

    Swal.fire({
        icon: icon,
        title: title,
        text: message,
        confirmButtonText: "OK"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("masterForm").submit();
        }
    });
});

    // Weight calculation
    const grossInput = document.getElementById('gross_wt');
    const tareInput = document.getElementById('tare_wt');
    const netInput = document.getElementById('net_wt');
    const netSpan = document.getElementById('display_net_wt');

    function updateNetWt() {
        const gross = parseFloat(grossInput && grossInput.value) || 0;
        const tare = parseFloat(tareInput && tareInput.value) || 0;
        let net = gross - tare;
        if (net < 0) net = 0;
        
        if (netInput) netInput.value = net.toFixed(3);
        if (netSpan) netSpan.textContent = net.toFixed(3) + " Kg";
    }

    if (grossInput && tareInput && netInput) {
        grossInput.addEventListener('input', updateNetWt);
        tareInput.addEventListener('input', updateNetWt);
        updateNetWt();
        netInput.readOnly = true;
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
    const packingUnitDropdown = document.getElementById("packing_unit");
    const avgWtPerBagInput = document.getElementById("avg_wt_per_bag");

    function updateInwardWeight() {
        const qty = parseFloat(inwardQtyInput.value) || 0;
        if (conversionFactor > 0) {
            const calculatedWeight = (qty * conversionFactor).toFixed(2);
            inwardWeightInput.value = calculatedWeight;
        } else {
            inwardWeightInput.value = "";
        }
    }

    if (packingUnitDropdown) {
        packingUnitDropdown.addEventListener("change", function () {
            const selectedPackingUnit = packingUnitDropdown.value;
            
            if (selectedPackingUnit) {
                fetch("classes/cls_inward_master.php?action=fetchPackingUnitData&packing_unit=" + encodeURIComponent(selectedPackingUnit))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            conversionFactor = parseFloat(data.conversion_factor) || 0;
                            avgWtPerBagInput.value = conversionFactor ? conversionFactor : "";
                            updateInwardWeight();
                            calculateTotals();
                            
                            const unloadingChargeInput = document.getElementById("unloading_charge_amount");
                            if (unloadingChargeInput) {
                                unloadingChargeInput.value = data.unloading_charge || "";
                            }
                        } else {
                            conversionFactor = 0;
                            avgWtPerBagInput.value = "";
                            inwardWeightInput.value = "";
                            
                            const unloadingChargeInput = document.getElementById("unloading_charge_amount");
                            if (unloadingChargeInput) {
                                unloadingChargeInput.value = "";
                            }
                        }
                    })
                    .catch(error => {
                        conversionFactor = 0;
                        avgWtPerBagInput.value = "";
                        inwardWeightInput.value = "";
                        
                        const unloadingChargeInput = document.getElementById("unloading_charge_amount");
                        if (unloadingChargeInput) {
                            unloadingChargeInput.value = "";
                        }
                    });
            } else {
                conversionFactor = 0;
                avgWtPerBagInput.value = "";
                inwardWeightInput.value = "";
                
                const unloadingChargeInput = document.getElementById("unloading_charge_amount");
                if (unloadingChargeInput) {
                    unloadingChargeInput.value = "";
                }
            }
        });
    }

    if (inwardQtyInput) {
        inwardQtyInput.addEventListener("input", function() {
            updateInwardWeight();
            calculateTotals();
        });
    }

    // Inward number formatting
    const inwardSequenceInput = document.getElementById("inward_sequence");
    const inwardNoInput = document.getElementById("inward_no");
//    if (inwardSequenceInput && inwardSequenceInput.classList.contains("duplicate")) {
//        inwardSequenceInput.dispatchEvent(new Event("blur"));
//    }
    
    if (inwardSequenceInput && inwardNoInput) {
        inwardSequenceInput.addEventListener("input", function () {
            const sequence = this.value.padStart(4, '0');
            inwardNoInput.value = sequence + '/' + financialYear;
        });
    }

    // Storage duration and rent fields management
    const storageDuration = document.getElementById('storageDuration');
    const rentPerMonthRow = document.getElementById('rentPerMonthRow');
    const rentStorageDuration = document.getElementById('rentStorageDuration');
    const rentStorageDurationRow = document.getElementById('rentLabel').closest('.row.mb-2');
    const parent = rentPerMonthRow.parentNode;

    function swapRentRowsIfSeasonal() {
        const selectedOption = storageDuration.options[storageDuration.selectedIndex];
        const isSeasonal = selectedOption && selectedOption.getAttribute('data-is-seasonal') === "true";
        
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
        const selectedOption = storageDuration.options[storageDuration.selectedIndex];
        const value = selectedOption.getAttribute('data-value') || selectedOption.value;
        const isSeasonal = selectedOption.getAttribute('data-is-seasonal') === "true";
        const targetValues = ["daily","weekly","fortnightly","monthly"];

        if (targetValues.includes(value.toLowerCase())) {
            rentPerMonthRow.style.display = 'none';
            rentStorageDuration.disabled = false; 
        } else if (isSeasonal) {
            rentPerMonthRow.style.display = '';
            rentStorageDuration.disabled = false; 
        } else {
            rentPerMonthRow.style.display = '';
            rentStorageDuration.disabled = true; 
        }
    }

    // Storage duration label update
    storageDuration.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const valueLabel = selectedOption.getAttribute('data-label');
        const isSeasonal = selectedOption.getAttribute('data-is-seasonal');
        const rentLabel = document.getElementById('rentLabel');
        const seasonalFields = document.getElementById('seasonalFields');

        if (valueLabel) {
            rentLabel.textContent = 'Rent / ' + valueLabel;
        } else {
            rentLabel.textContent = 'Rent / Storage Duration';
        }
            
        if (isSeasonal === "true") {
            seasonalFields.style.display = "block";
        } else {
            seasonalFields.style.display = "none";
        }
    });

    // Initialize event listeners
    storageDuration.addEventListener('change', swapRentRowsIfSeasonal);
    storageDuration.addEventListener('change', toggleFields);
    swapRentRowsIfSeasonal();
    toggleFields();
    
    // Calculate initial totals
    calculateTotals();
});
</script>
<!--Billing_date/ Inward_date-->
<script>
$(document).ready(function () {
    // Page load પર જ today's date set કરો (if empty)
    if ($('#inward_date').val() === '') {
        var today = new Date();
        var formattedToday = today.toISOString().split('T')[0];
        $('#inward_date').val(formattedToday);
        $('#billing_starts_from').val(formattedToday);
    }

    function validateInwardDate() {
        var inwardDate = $('#inward_date').val();
        var errorContainer = $('#inward_date_error');
        if (inwardDate === '') return false;
        var inwardDateParts = inwardDate.split('-');
        if (inwardDateParts.length !== 3) {
            showError('Enter Proper Inward Date');
            return false;
        }
        var year = parseInt(inwardDateParts[0], 10);
        var month = parseInt(inwardDateParts[1], 10);
        var day = parseInt(inwardDateParts[2], 10);
        var validDate = new Date(year, month - 1, day);
        if (
            validDate.getFullYear() !== year ||
            validDate.getMonth() !== (month - 1) ||
            validDate.getDate() !== day
        ) {
            showError('Enter Proper Inward Date');
            return false;
        }
        var currentDate = new Date();
        var todayStr = currentDate.toISOString().split('T')[0];
        var selectedDate = new Date(inwardDate);
        var today = new Date(todayStr);
        selectedDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);

        if (selectedDate > today) {
            showError('Date Above Current Period');
            return false;
        }
        var currentMonth = today.getMonth();
        var currentYear = today.getFullYear();
        var prevMonth = currentMonth - 1;
        var prevYear = currentYear;
        if (prevMonth < 0) {
            prevMonth = 11;
            prevYear -= 1;
        }
        var selectedMonth = selectedDate.getMonth();
        var selectedYear = selectedDate.getFullYear();
        if (
            selectedYear < prevYear ||
            (selectedYear === prevYear && selectedMonth < prevMonth)
        ) {
            showError('Date Below Current Period');
            return false;
        }
        $('#billing_starts_from').val(inwardDate);
        return true;

        function showError(message) {
            errorContainer.text(message);
            $('#inward_date').addClass('is-invalid');
        }
    }

    // billing_starts_from field: (OPTIONAL) only if you want to auto-set on focus as fallback
    $('#billing_starts_from').one('focus', function () {
        if ($(this).val() === '') {
            var today = new Date();
            var formattedToday = today.toISOString().split('T')[0];
            $(this).val(formattedToday);
        }
    });

    $('#inward_date').on('blur', function () {
        validateInwardDate();
    });
});
</script>
<script>
// --- Get Rent Per Label by ID ---
function getRentPerLabelById(id) {
    if (id == "1") return "Quantity";
    if (id == "2") return "Kg";
    return "";
}

// --- Storage Duration Multiplier ---
function getStorageDurationMultiplier(storageValue) {
    const val = storageValue.trim().toLowerCase();
    switch(val) {
        case 'daily': return 1/30;
        case 'weekly': return 7/30;
        case 'fortnightly':
        case '15 days': return 15/30;
        case 'monthly':
        case 'month': return 1;
        case '1 month 1 day': return 1 + (1/30);
        case '1 month 7 days': return 1 + (7/30);
        case '1 month 15 days': return 1 + (15/30);
        case '2 months':
        case '2 month': return 2;
        default: return 1;
    }
}

// --- Track Manual Edits ---
let isRentPerMonthManuallyEdited = false;
let isRentStorageDurationManuallyEdited = false;

// --- Calculate Rent Per Storage Duration ---
function calculateRentStorageDuration() {
    const storageDurationSelect = $('#popupForm #storageDuration')[0];
    const selectedOption = storageDurationSelect?.options[storageDurationSelect.selectedIndex];
    const storageDurationValue = selectedOption?.text.trim();
    const isSeasonal = selectedOption?.getAttribute('data-is-seasonal') === "true";

    // Skip calculation if manually edited or seasonal
    if (isRentStorageDurationManuallyEdited || isSeasonal) return;

    let rentPerMonth = parseFloat($('#popupForm #rentPerMonth').val());
    if (isNaN(rentPerMonth)) rentPerMonth = 0;
    let multiplier = getStorageDurationMultiplier(storageDurationValue);
    let rentStorageDuration = 0;
    if (rentPerMonth > 0 && multiplier > 0) {
        rentStorageDuration = (rentPerMonth * multiplier).toFixed(2);
    }
    $('#popupForm #rentStorageDuration').val(rentStorageDuration);
}

// --- Fetch Rent Per Month, Customer-wise or Item-wise, with SEASONAL support ---
function fetchRentPerMonth() {
    const itemId = $('#popupForm #item').val();
    const unitId = $('#popupForm #packing_unit').val();
    const rentPerId = $('#popupForm #rentPer').val();
    const rentPer = getRentPerLabelById(rentPerId); // "Quantity" or "Kg"
    const customerId = $('#customer').val();
    const storageDurationSelect = $('#popupForm #storageDuration')[0];
    const selectedOption = storageDurationSelect?.options[storageDurationSelect.selectedIndex];
    const isSeasonal = selectedOption?.getAttribute('data-is-seasonal') === "true";

    // Only skip fetching if the field was manually edited and the user hasn't changed related fields
    if (!itemId || !rentPer || !unitId) {
        $('#popupForm #rentPerMonth').val('');
        $('#popupForm #rentStorageDuration').val('');
        return;
    }

    let params = {
        action: 'fetchRentPerMonth',
        item_id: itemId,
        unit_id: unitId,
        rent_per: rentPer
    };
    if (customerId) params.customer_id = customerId;
    if (isSeasonal) params.seasonal = 1;

    $.ajax({
        url: 'classes/cls_inward_master.php',
        method: 'GET',
        data: params,
        success: function (response) {
            if (typeof response === "string") {
                try { response = JSON.parse(response); } catch (e) { response = {}; }
            }
            if (response.success && response.rent_per_month !== null && response.rent_per_month !== "") {
                // Update fields only if not manually edited
                if (!isRentPerMonthManuallyEdited) {
                    $('#popupForm #rentPerMonth').val(response.rent_per_month);
                }
                if (!isRentStorageDurationManuallyEdited) {
                    $('#popupForm #rentStorageDuration').val(response.rent_per_month);
                }
                // For non-seasonal, calculate rentStorageDuration if not manually edited
                if (!isSeasonal && !isRentStorageDurationManuallyEdited) {
                    calculateRentStorageDuration();
                }
            } else {
                if (!isRentPerMonthManuallyEdited) $('#popupForm #rentPerMonth').val('');
                if (!isRentStorageDurationManuallyEdited) $('#popupForm #rentStorageDuration').val('');
            }
        },
        error: function () {
            if (!isRentPerMonthManuallyEdited) $('#popupForm #rentPerMonth').val('');
            if (!isRentStorageDurationManuallyEdited) $('#popupForm #rentStorageDuration').val('');
        }
    });
}

// --- Mark fields as manually edited ---
$('#popupForm #rentPerMonth').on('input', function() {
    isRentPerMonthManuallyEdited = true;
});

$('#popupForm #rentStorageDuration').on('input', function() {
    isRentStorageDurationManuallyEdited = true;
});

// --- Reset manual edit flags when saving or opening modal for new entry ---
$('#popupForm').on('submit', function(event) {
    event.preventDefault();
    saveData();
    // Reset manual edit flags after saving
    isRentPerMonthManuallyEdited = false;
    isRentStorageDurationManuallyEdited = false;
});

$('#modalDialog').on('hidden.bs.modal', function () {
    isRentPerMonthManuallyEdited = false;
    isRentStorageDurationManuallyEdited = false;
});

// --- Reset manual edit flags when related fields change ---
$('#popupForm #item, #popupForm #packing_unit, #popupForm #rentPer, #customer').on('change', function() {
    // Reset manual edit flags to allow auto-fetch
    isRentPerMonthManuallyEdited = false;
    isRentStorageDurationManuallyEdited = false;
    fetchRentPerMonth();
});

// --- Handle storageDuration change ---
// --- Handle storageDuration change ---
$('#popupForm #storageDuration').on('change', function() {
    const storageDurationSelect = $('#popupForm #storageDuration')[0];
    const selectedOption = storageDurationSelect?.options[storageDurationSelect.selectedIndex];
    const isSeasonal = selectedOption?.getAttribute('data-is-seasonal') === "true";

    // NEW: Detect if switched to non-seasonal and reset manual edit flags
    if (!isSeasonal) {
        isRentPerMonthManuallyEdited = false;
        isRentStorageDurationManuallyEdited = false;
    }

    // Only fetch rentPerMonth if fields are not manually edited
    if (!isRentPerMonthManuallyEdited && !isRentStorageDurationManuallyEdited) {
        fetchRentPerMonth();
    } else if (!isSeasonal && !isRentStorageDurationManuallyEdited) {
        // For non-seasonal, calculate rentStorageDuration if not manually edited
        calculateRentStorageDuration();
    }
});

// --- Ensure fields are always editable ---
$('#popupForm #rentPerMonth, #popupForm #rentStorageDuration').prop('readonly', false).prop('disabled', false);

// --- Initialize on document ready ---
$(document).ready(function(){
    fetchRentPerMonth();
});
</script>
     
<script>
  // seasonal validation - drashti
 document.addEventListener("DOMContentLoaded", function () {
    const storageDuration = document.getElementById("storageDuration");
    const seasonalFields = document.getElementById("seasonalFields");
    const seasonalInputs = seasonalFields.querySelectorAll(".seasonal-required");

    function checkSeasonal() {
      const selectedOption = storageDuration.options[storageDuration.selectedIndex];
      const isSeasonal = selectedOption.getAttribute("data-is-seasonal") === "true";

      if (isSeasonal) {
        seasonalFields.style.display = "block";
        seasonalInputs.forEach(input => input.setAttribute("required", "required"));
      } else {
        seasonalFields.style.display = "none";
        seasonalInputs.forEach(input => input.removeAttribute("required"));
      }
    }
    checkSeasonal();
    storageDuration.addEventListener("change", checkSeasonal);
  });  
      //location-drashti
  document.getElementById('chamber').addEventListener('change', updateLocation);
  document.getElementById('floor').addEventListener('change', updateLocation);
  document.getElementById('rack').addEventListener('input', updateLocation);

  function updateLocation() {
    const chamber = document.getElementById('chamber').selectedOptions.length > 0 
      ? document.getElementById('chamber').selectedOptions[0].text.trim() 
      : '';
    const floor = document.getElementById('floor').selectedOptions.length > 0 
      ? document.getElementById('floor').selectedOptions[0].text.trim() 
      : '';
    const rack = document.getElementById('rack').value.trim() || '';

    const parts = [chamber, floor, rack].filter(part => part !== '');
    const location = parts.join(' - ');
    
    document.getElementById('location_display').value = location;
document.getElementById('location').value = location;
  } 
</script>
<?php
    include("include/footer_close.php");
?>