<?php
     $_tablename=$_POST["selectTables"];
     $_sql="SELECT COLUMN_NAME as `field_name`, DATA_TYPE as `data_type`, CHARACTER_MAXIMUM_LENGTH as `length`, NUMERIC_PRECISION as `precision`, NUMERIC_SCALE as `scale` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$database_name."' AND `TABLE_NAME`='".$_tablename."' ORDER BY `ORDINAL_POSITION`";
      $_pre=$_dbh->prepare($_sql);
      //$_fieldno=$_pre->columnCount();
      $_pre->execute();
      $i=0;
     $select = $_dbh->prepare('SELECT generator_options,generator_id FROM tbl_generator_master WHERE table_name = ?');
    $select->bindParam(1, $_tablename);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
     $generator_options_arr=array();
     $table_layout="horizontal";
    if(isset($_POST["submit_fields"])) {
        if(isset($_POST["table_layout"]))
            $table_layout=$_POST["table_layout"];
        if(isset($_POST["field_type"]))
            $fields_types=$_POST["field_type"];
        if(isset($_POST["dropdown_table"]))
            $dropdown_table=$_POST["dropdown_table"];
        if(isset($_POST["label_column"]))
            $label_column=$_POST["label_column"];
        if(isset($_POST["value_column"]))
            $value_column=$_POST["value_column"];
        if(isset($_POST["where_condition"]))
            $where_condition=$_POST["where_condition"];
        if(isset($_POST["default_value"]))
            $where_condition=$_POST["default_value"];
        if(isset($_POST["field_label"]))
            $fields_labels=$_POST["field_label"];
        if(isset($_POST["field_display"]))
            $field_display=$_POST["field_display"];
        if(isset($_POST["field_required"]))
            $field_required=$_POST["field_required"];
        if(isset($_POST["allow_zero"]))
            $allow_zero=$_POST["allow_zero"];
         if(isset($_POST["allow_minus"]))
            $allow_minus=$_POST["allow_minus"];
         if(isset($_POST["chk_duplicate"]))
            $chk_duplicate=$_POST["chk_duplicate"];
        if(isset($_POST["is_disabled"]))
            $is_disabled=$_POST["is_disabled"];
        if(isset($_POST["after_detail"]))
            $after_detail=$_POST["after_detail"];
    } else {
        // fetch generator option from generator table
             if($row) {
                 $generator_options=$row["generator_options"];
                 $generator_options_arr=json_decode($generator_options);
                 if(isset($generator_options_arr->table_layout))
                        $table_layout=$generator_options_arr->table_layout;
                 if(isset($generator_options_arr->field_type))
                        $fields_types=$generator_options_arr->field_type;
                   if(isset($generator_options_arr->dropdown_table))
                        $dropdown_table=$generator_options_arr->dropdown_table;
                  if(isset($generator_options_arr->label_column))
                        $label_column=$generator_options_arr->label_column;
                 if(isset($generator_options_arr->value_column))
                    $value_column=$generator_options_arr->value_column;
                 if(isset($generator_options_arr->where_condition))
                        $where_condition=$generator_options_arr->where_condition;
                if(isset($generator_options_arr->default_value))
                        $default_value=$generator_options_arr->default_value;
                 if(isset($generator_options_arr->field_label))
                    $fields_labels=$generator_options_arr->field_label;
                if(isset($generator_options_arr->field_display))
                    $field_display=$generator_options_arr->field_display;
                  if(isset($generator_options_arr->field_required))
                    $field_required=$generator_options_arr->field_required;
                 if(isset($generator_options_arr->allow_zero))
                    $allow_zero=$generator_options_arr->allow_zero;
                  if(isset($generator_options_arr->allow_minus))
                    $allow_minus=$generator_options_arr->allow_minus;
                  if(isset($generator_options_arr->chk_duplicate))
                        $chk_duplicate=$generator_options_arr->chk_duplicate;
                 if(isset($generator_options_arr->is_disabled))
                        $is_disabled=$generator_options_arr->is_disabled;
                  if(isset($generator_options_arr->after_detail))
                    $after_detail=$generator_options_arr->after_detail;
             }
    }
    $table_layout_horizontal="";$table_layout_vertical="";
    if($table_layout=="horizontal") {
        $table_layout_horizontal='selected="selected"';
    } else {
        $table_layout_vertical='selected="selected"';
    }
    $_strgenerateflds.='<h1 style="width:90%; margin:0 auto;">'.$_tablename.'</h1>';
    $_strgenerateflds.='<div style="width:90%; margin:20px auto;"><strong>Table Layout:</strong> <select name="table_layout" id="table_layout"><option '.$table_layout_horizontal.' value="horizontal">Horizontal</option><option value="vertical" '.$table_layout_vertical.'>Vertical</option></select></div>';
     $_strgenerateflds.='<table border="1" width="90%" cellpadding="7" cellspacing="0" style="margin:20px auto 0 auto;">';
     $_strgenerateflds.='<thead>';
     $_strgenerateflds.='<tr>
        <td>Field Name</td>
        <td>Field Type</td>
        <td>Field Label</td>
        <td>Show on Search Page?</td>
        <td>Required?</td>
        <td>Allow Zero?</td>
        <td>Allow Minus?</td>
        <td>Check Duplicate?</td>
        <td>Is Disabled?</td>
        <td>After Detail?</td>
     </tr>';
     $_strgenerateflds.='</thead>
     <tbody>';
     $hidden='selected="selected"';
     foreach($_pre->fetchAll(PDO::FETCH_ASSOC) as $_meta)
    {

         $_fieldname[$i]=$_meta["field_name"];
         $field_label="";$text="";$number="";$textarea="";$select="";$checkbox="";$radio="";$email="";$date="";$datetime_local="";$file="";$checked_display="";$checked_required="";$checked_zero="";$checked_minus="";$checked_duplicate="";$style="";$checked_disabled="";$checked_after_detail="";$dropdown_table_val="";$label_column_val="";$value_column_val="";$where_condition_val="";$default_val="";$dropdowncls="hidden";
        if(!empty($generator_options_arr) || isset($_POST["submit_fields"])) {
           $hidden="";
             switch($fields_types[$i]) {
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
                case "file" : 
                    $file='selected="selected"';
                     break;
                default:
                      $hidden='selected="selected"';
                     break;
            }
            if(!empty($dropdown_table[$i])) {
                $dropdown_table_val=$dropdown_table[$i];
                $dropdowncls="";
            }
            if(!empty($label_column[$i]))
                $label_column_val=$label_column[$i];
           if(!empty($value_column[$i]))
                $value_column_val=$value_column[$i];
           if(!empty($where_condition[$i]))
                $where_condition_val=$where_condition[$i];
            if(!empty($default_value[$i]))
                $default_val=$default_value[$i];
         
            if(!empty($field_display) && in_array($_fieldname[$i],$field_display)) {
                $checked_display='checked="checked"';
            }
            if(!empty($field_required) && in_array($_fieldname[$i],$field_required)) {
                $checked_required='checked="checked"';
            }
            if(!empty($allow_zero) && in_array($_fieldname[$i],$allow_zero)) {
                $checked_zero='checked="checked"';
            }
            if(!empty($allow_minus) && in_array($_fieldname[$i],$allow_minus)) {
                $checked_minus='checked="checked"';
            }
            if(!empty($chk_duplicate) && in_array($_fieldname[$i],$chk_duplicate)) {
                $checked_duplicate='checked="checked"';
            }
            if(!empty($is_disabled) && in_array($_fieldname[$i],$is_disabled)) {
                $checked_disabled='checked="checked"';
            }
            if(!empty($after_detail) && in_array($_fieldname[$i],$after_detail)) {
                $checked_after_detail='checked="checked"';
            }
         } //generator options if ends
         if(isset($fields_labels[$i]))
            $field_label=$fields_labels[$i];
        if($field_label=="")
            $field_label=ucwords(str_replace("_"," ",$_fieldname[$i]));
        
         $_strgenerateflds.='<tr style="'.$style.'">';
         $_strgenerateflds.='<td>'.ucwords(str_replace("_"," ",$_fieldname[$i])).'
         <input type="hidden" name="field_name[]" value="'.$_fieldname[$i].'" />
         <input type="hidden" name="data_type[]" value="'.$_meta["data_type"].'" />
         <input type="hidden" name="length[]" value="'.$_meta["length"].'" />
         <input type="hidden" name="precision[]" value="'.$_meta["precision"].'" />
         <input type="hidden" name="scale[]" value="'.$_meta["scale"].'" />
         </td>';
         $dropdownFieldsId='dropdown_fields'.$i;
         $_strgenerateflds.='<td>
                <select name="field_type[]" onchange="showDropdownFields(this,\''.$dropdownFieldsId.'\');">
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
                        <option value="file" '.$file.'>File</option>
                </select>
                <div id="'.$dropdownFieldsId.'" class="'.$dropdowncls.'">
                    <input type="text" name="dropdown_table[]" placeholder="table name" value="'.$dropdown_table_val.'" /><br>
                    <input type="text" name="label_column[]" placeholder="label column" value="'.$label_column_val.'" /><br>
                    <input type="text" name="value_column[]" placeholder="value column" value="'.$value_column_val.'" /><br>
                    <input type="text" name="where_condition[]" placeholder="where condition" value="'.$where_condition_val.'" /><br>
                    <input type="text" name="default_value[]" placeholder="default value" value="'.$default_val.'" />
                </div>
          </td>';
         $_strgenerateflds.='<td><input type="text" name="field_label[]"  value="'.$field_label.'" /></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="field_display[]" value="'.$_fieldname[$i].'" '.$checked_display.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="field_required[]" value="'.$_fieldname[$i].'" '.$checked_required.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="allow_zero[]" value="'.$_fieldname[$i].'" '.$checked_zero.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="allow_minus[]" value="'.$_fieldname[$i].'" '.$checked_minus.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="chk_duplicate[]" value="'.$_fieldname[$i].'" '.$checked_duplicate.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="is_disabled[]" value="'.$_fieldname[$i].'" '.$checked_disabled.'></td>';
         $_strgenerateflds.='<td><input type="checkbox" name="after_detail[]" value="'.$_fieldname[$i].'" '.$checked_after_detail.'></td>';
         $_strgenerateflds.='</tr>';
         $i++;
    } //endforeach
    $_strgenerateflds.='</tbody>
    </table>';
      $tbl_hidden.='<input type="hidden" name="table_name" value="'.$_tablename.'" />';
    $tbl_hidden.='<input type="hidden" name="field_no" value="'.($i-1).'" />';
?>