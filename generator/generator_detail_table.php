<?php
if(isset($_POST["selectDetailTables"]) && $_POST["selectDetailTables"]!=""){
    $_detailtablename=$_POST["selectDetailTables"];
     $_sql_detail="SELECT COLUMN_NAME as `field_name`, DATA_TYPE as `data_type`, CHARACTER_MAXIMUM_LENGTH as `length`, NUMERIC_PRECISION as `precision`, NUMERIC_SCALE as `scale` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$database_name."' AND `TABLE_NAME`='".$_detailtablename."' ORDER BY `ORDINAL_POSITION`";
      $_pre_detail=$_dbh->prepare($_sql_detail);
      $_pre_detail->execute();

     // fetch generator option from generator table
     $select = $_dbh->prepare('SELECT generator_options,generator_id FROM tbl_generator_master WHERE table_name = ?');
     $select->bindParam(1, $_detailtablename);
     $select->execute();
     $rowdetail = $select->fetch(PDO::FETCH_ASSOC);
     $generator_options_arr=array();
    $table_layout="vertical";
     if(isset($_POST["submit_fields"])) {
        if(isset($_POST["table_layout"]))
            $table_layout=$_POST["detailtable_layout"];
        if(isset($_POST["detailfield_type"]))
            $detalfields_types=$_POST["detailfield_type"];
        if(isset($_POST["detaildropdown_table"]))
            $detaildropdown_table=$_POST["detaildropdown_table"];
        if(isset($_POST["detaillabel_column"]))
            $detaillabel_column=$_POST["detaillabel_column"];
        if(isset($_POST["detailvalue_column"]))
            $detailvalue_column=$_POST["detailvalue_column"];
        if(isset($_POST["detailwhere_condition"]))
            $detailwhere_condition=$_POST["detailwhere_condition"];
        if(isset($_POST["detailfield_label"]))
            $detailfields_labels=$_POST["detailfield_label"];
        if(isset($_POST["detailfield_display"]))
            $detailfield_display=$_POST["detailfield_display"];
        if(isset($_POST["detailfield_required"]))
            $detailfield_required=$_POST["detailfield_required"];
        if(isset($_POST["detailallow_zero"]))
            $detailallow_zero=$_POST["detailallow_zero"];
         if(isset($_POST["detailallow_minus"]))
            $detailallow_minus=$_POST["detailallow_minus"];
         if(isset($_POST["detailchk_duplicate"]))
            $detailchk_duplicate=$_POST["detailchk_duplicate"];
        if(isset($_POST["detailis_disabled"]))
            $detailis_disabled=$_POST["detailis_disabled"];
        if(isset($_POST["detailafter_detail"]))
            $detailafter_detail=$_POST["detailafter_detail"];
    } else {
        if($rowdetail) {
            $generator_options=$rowdetail["generator_options"];
            $generator_options_arr=json_decode($generator_options);
           if(isset($generator_options_arr->table_layout))
                $table_layout=$generator_options_arr->table_layout;
            $detalfields_types=$generator_options_arr->field_type;
            $detaildropdown_table=$generator_options_arr->dropdown_table;
            $detaillabel_column=$generator_options_arr->label_column;
            $detailvalue_column=$generator_options_arr->value_column;
            $detailwhere_condition=$generator_options_arr->where_condition;
            $detailfields_labels=$generator_options_arr->field_label;
            $detailfield_display=$generator_options_arr->field_display;
            $detailfield_required=$generator_options_arr->field_required;
            $detailallow_zero=$generator_options_arr->allow_zero;
            $detailallow_minus=$generator_options_arr->allow_minus;
            $detailchk_duplicate=$generator_options_arr->chk_duplicate;
            $detailis_disabled=$generator_options_arr->is_disabled;
        }
    }
     $table_layout_horizontal="";$table_layout_vertical="";
    if($table_layout=="horizontal") {
        $table_layout_horizontal='selected="selected"';
    } else {
        $table_layout_vertical='selected="selected"';
    }
    $_strgenerateflds_detail.='<h1 style="width:90%; margin:30px auto 0 auto;">'.$_detailtablename.'</h1>';
     $_strgenerateflds_detail.='<div style="width:90%; margin:20px auto;"><strong>Table Layout:</strong> <select name="detailtable_layout" id="detailtable_layout"><option value="vertical" '.$table_layout_vertical.'>Vertical</option><option '.$table_layout_horizontal.' value="horizontal">Horizontal</option></select></div>';
     $_strgenerateflds_detail.='<table border="1" width="90%" cellpadding="7" cellspacing="0" style="margin:20px auto 0 auto;">';
     $_strgenerateflds_detail.='<thead>';
     $_strgenerateflds_detail.='<tr>
        <td>Field Name</td>
        <td>Field Type</td>
        <td>Field Label</td>
        <td>Show on Search Page?</td>
        <td>Required?</td>
        <td>Allow Zero?</td>
        <td>Allow Minus?</td>
        <td >Is Disabled?</td>
        <td class="hidden">Check Duplicate?</td>
     </tr>';
     $_strgenerateflds_detail.='</thead>
     <tbody>';
     $hidden='selected="selected"';

    foreach($_pre_detail->fetchAll(PDO::FETCH_ASSOC) as $_meta)
    {
         $_detailfieldname[$i]=$_meta["field_name"];
          $_detailfieldno=$i;
    $field_label="";$text="";$number="";$textarea="";$select="";$checkbox="";$radio="";$email="";$date="";$datetime_local="";$password="";$checked_display="";$checked_required="";$checked_zero="";$checked_minus="";$checked_duplicate="";$style="";$is_disabled="";$detaildropdown_table_val="";$detaillabel_column_val="";$detailvalue_column_val="";$detailwhere_condition_val="";$dropdowncls="hidden";
     if(!empty($generator_options_arr)  || isset($_POST["submit_fields"])) {
           $hidden="";
             switch($detalfields_types[$i]) {
                case "text" :
                     $text='selected="selected"';
                      break;
                case "number" : 
                     $number='selected="selected"';
                      break;
                case "textarea" :
                     $textarea='selected="selected"';
                      break;
                case "select" :
                     $select='selected="selected"';
                      break;
                case "checkbox" :
                     $checkbox='selected="selected"';
                      break;
                case "radio" :
                     $radio='selected="selected"';
                      break;
                case "email" : 
                     $email='selected="selected"';
                      break;
                case "date" :
                     $date='selected="selected"';
                      break;
                case "datetime-local" :
                     $datetime_local='selected="selected"';
                      break;
                case "hidden" :
                     $hidden='selected="selected"';
                     //$style='display:none;';
                      break;
                case "password" : 
                    $password='selected="selected"';
                     break;
                default:
                      $hidden='selected="selected"';
                     break;
            }
            
            if(!empty($detaildropdown_table[$i])) {
                $detaildropdown_table_val=$detaildropdown_table[$i];
                $dropdowncls="";
            }
            if(!empty($detaillabel_column[$i]))
                $detaillabel_column_val=$detaillabel_column[$i];
           if(!empty($detailvalue_column[$i]))
                $detailvalue_column_val=$detailvalue_column[$i];
          if(!empty($detailwhere_condition[$i]))
                $detailwhere_condition_val=$detailwhere_condition[$i];

            if(!empty($detailfield_display) && in_array($_detailfieldname[$i],$detailfield_display)) {
                $checked_display='checked="checked"';
            }
            if(!empty($detailfield_required) && in_array($_detailfieldname[$i],$detailfield_required)) {
                $checked_required='checked="checked"';
            }
            if(!empty($detailallow_zero) && in_array($_detailfieldname[$i],$detailallow_zero)) {
                $checked_zero='checked="checked"';
            }
            if(!empty($detailallow_minus) && in_array($_detailfieldname[$i],$detailallow_minus)) {
                $checked_minus='checked="checked"';
            }
            if(!empty($detailchk_duplicate) && in_array($_detailfieldname[$i],$detailchk_duplicate)) {
                $checked_duplicate='checked="checked"';
            }
            if(!empty($detailis_disabled) && in_array($_detailfieldname[$i],$detailis_disabled)) {
                $is_disabled='checked="checked"';
            }
         } //generator options if ends
         if(isset($detailfields_labels[$i]))
            $field_label=$detailfields_labels[$i];
        if($field_label=="")
            $field_label=ucwords(str_replace("_"," ",$_detailfieldname[$i]));

         $_strgenerateflds_detail.='<tr style="'.$style.'">';
         $_strgenerateflds_detail.='<td>'.ucwords(str_replace("_"," ",$_detailfieldname[$i])).'
         <input type="hidden" name="detailfield_name[]" value="'.$_detailfieldname[$i].'" />
         <input type="hidden" name="detaildata_type[]" value="'.$_meta["data_type"].'" />
         <input type="hidden" name="detaillength[]" value="'.$_meta["length"].'" />
         <input type="hidden" name="detailprecision[]" value="'.$_meta["precision"].'" />
         <input type="hidden" name="detailscale[]" value="'.$_meta["scale"].'" />
         </td>';
        $ddetailDropdownFieldsId='detaildropdown_fields'.$i;
         $_strgenerateflds_detail.='<td>
                <select name="detailfield_type[]" onchange="showDropdownFields(this,\''.$ddetailDropdownFieldsId.'\');">
                        <option value="">Select</option>
                        <option  value="text" '.$text.'>Text</option>
                        <option value="number" '.$number.'>Number</option>
                        <option  value="textarea" '.$textarea.'>Paragraph</option>
                        <option value="select" '.$select.'>Dropdown</option>
                         <option value="checkbox" '.$checkbox.'>Checkbox</option>
                         <option value="radio" '.$radio.'>Radio Buttons</option>
                        <option value="email" '.$email.'>Email</option>                                    
                        <option value="date" '.$date.'>Date</option>
                        <option value="datetime-local" '.$datetime_local.'>Datetime</option>
                        <option value="hidden" '.$hidden.'>Hidden</option>
                        <option value="password" '.$password.'>Password</option>
                </select>
                <div id="'.$ddetailDropdownFieldsId.'" class="'.$dropdowncls.'">
                <input type="text" name="detaildropdown_table[]" placeholder="table name" value="'.$detaildropdown_table_val.'" /><br>
                <input type="text" name="detaillabel_column[]" placeholder="label column" value="'.$detaillabel_column_val.'" /><br>
                <input type="text" name="detailvalue_column[]" placeholder="value column" value="'.$detailvalue_column_val.'" /><br>
                <input type="text" name="detailwhere_condition[]" placeholder="where condition" value="'.$detailwhere_condition_val.'" />
            </div>
          </td>';
         $_strgenerateflds_detail.='<td><input type="text" name="detailfield_label[]"  value="'.$field_label.'" /></td>';
         $_strgenerateflds_detail.='<td><input type="checkbox" name="detailfield_display[]" value="'.$_detailfieldname[$i].'" '.$checked_display.'></td>';
         $_strgenerateflds_detail.='<td><input type="checkbox" name="detailfield_required[]" value="'.$_detailfieldname[$i].'" '.$checked_required.'></td>';
         $_strgenerateflds_detail.='<td><input type="checkbox" name="detailallow_zero[]" value="'.$_detailfieldname[$i].'" '.$checked_zero.'></td>';
         $_strgenerateflds_detail.='<td><input type="checkbox" name="detailallow_minus[]" value="'.$_detailfieldname[$i].'" '.$checked_minus.'></td>';
        $_strgenerateflds_detail.='<td ><input type="checkbox" name="detailis_disabled[]" value="'.$_detailfieldname[$i].'" '.$is_disabled.'></td>';
         $_strgenerateflds_detail.='<td class="hidden"><input type="checkbox" name="detailchk_duplicate[]" value="'.$_detailfieldname[$i].'" '.$checked_duplicate.'></td>';

         $_strgenerateflds_detail.='</tr>';
         $i++;
    } //endforeach
            $_strgenerateflds_detail.='</tbody>
            </table>';
     $tbl_hidden.='<input type="hidden" name="table_name_detail" value="'.$_detailtablename.'" />';
    $tbl_hidden.='<input type="hidden" name="detailfield_no" value="'.($i-1).'" />';
}
?>