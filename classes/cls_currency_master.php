<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");
class mdl_currencymaster 
{   
    public $generator_table_layout;
    public $generator_fields_names;
    public $generator_fields_types;
    public $generator_field_scale;
    public $generator_dropdown_table;
    public $generator_label_column;
    public $generator_value_column;
    public $generator_where_condition;
    public $generator_default_value;
    public $generator_fields_labels;
    public $generator_field_display;
    public $generator_field_required;
    public $generator_allow_zero;
    public $generator_allow_minus;
    public $generator_chk_duplicate;
    public $generator_field_data_type;
    public $generator_field_is_disabled;
    public $generator_after_detail;
    protected $fields = [];

    public function __get($name) {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }
    public function __construct() {
        global $_dbh;
        global $tbl_generator_master;
        global $tbl_currency_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_currency_master);
        $select->execute();
        $row = $select->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $generator_options = json_decode($row["generator_options"]);
            if ($generator_options) {
                $this->generator_table_layout=$generator_options->table_layout;
                $this->generator_fields_names=$generator_options->field_name;
                $this->generator_fields_types=$generator_options->field_type;
                $this->generator_field_scale=$generator_options->field_scale;
                $this->generator_dropdown_table=$generator_options->dropdown_table;
                $this->generator_label_column=$generator_options->label_column;
                $this->generator_value_column=$generator_options->value_column;
                $this->generator_where_condition=$generator_options->where_condition;
                $this->generator_default_value=$generator_options->default_value;
                $this->generator_fields_labels=$generator_options->field_label;
                $this->generator_field_display=$generator_options->field_display;
                $this->generator_field_required=$generator_options->field_required;
                $this->generator_allow_zero=$generator_options->allow_zero;
                $this->generator_allow_minus=$generator_options->allow_minus;
                $this->generator_chk_duplicate=$generator_options->chk_duplicate;
                $this->generator_field_data_type=$generator_options->field_data_type;
                $this->generator_field_is_disabled=$generator_options->is_disabled;
                $this->generator_after_detail=$generator_options->after_detail;
            }
        }
    }

}

class bll_currencymaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_currencymaster(); 
        $this->_dal =new dal_currencymaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_currency_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_currency_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_currency_master.php");
       }

    }
 
    public function fillModel()
    {
        $this->_dal->fillModel($this->_mdl);
    
    }
     public function pageSearch()
    {
        global $_dbh;
        global $canUpdate;
        global $canDelete;
        $company_query=str_replace("company_id","t.company_id",COMPANY_QUERY);
        $sql="CAll csms_search_detail('t.*, u1.person_name as created_by, u2.person_name as modified_by, c1.company_name as company_id','tbl_currency_master t LEFT JOIN tbl_user_master u1  ON t.created_by=u1.user_id  LEFT JOIN tbl_user_master u2  ON t.modified_by=u2.user_id  LEFT JOIN tbl_company_master c1  ON t.company_id=c1.company_id ','".$company_query."')";
        echo "<!-- Filter row -->
                <div class=\"row gx-2 gy-1 align-items-center\" id=\"search-filters\">";
                $k=0;$hstr="";$url_fieldname="";
                foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
                {
                    $extracls="";
                    if(!in_array($fieldname,$this->_mdl->generator_field_display)) {
                        continue;
                    }
                    if($fieldname=="company_id") {
                        if(COMPANY_ID==ADMIN_COMPANY_ID) {
                            $k++;
                            $hstr.="<div class=\"col-auto\">";
                            $hstr.="<input type=\"text\" class=\"form-control \" placeholder=\"Search ".$this->_mdl->generator_fields_labels[$i]."\" data-index=\"".$k."\" />";
                            $hstr.="</div>";
                        }
                        continue;
                    }
                    if(!in_array($fieldname,$this->_mdl->generator_field_display) || $fieldname==$url_fieldname) {
                        continue;
                    }
                    $k++;
                    if($this->_mdl->generator_fields_types[$i]=="file") {
                        $url_fieldname=$fieldname."_url";
                        continue;
                    }
                    if($this->_mdl->generator_field_data_type[$i]=="datetime" || $this->_mdl->generator_field_data_type[$i]=="date" || $this->_mdl->generator_field_data_type[$i]=="timestamp") {
                        $extracls.="date-filter";
                    }
                    if($this->_mdl->generator_fields_labels[$i]!="") 
                        $label=$this->_mdl->generator_fields_labels[$i];
                    else
                        $label=ucwords(str_replace("_"," ",$fieldname));
                    $hstr.="<div class=\"col-auto\">";
                    $hstr.="<input type=\"text\" class=\"form-control ".$extracls."\" placeholder=\"Search ".$label."\" data-index=\"".$k."\" />";
                    $hstr.="</div>";
                }
                echo $hstr;echo "</div>";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>";
            if($canUpdate || $canDelete) {
                echo "<th>Action</th>";
            }
            $hstr="";$url_fieldname="";
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                if(!in_array($fieldname,$this->_mdl->generator_field_display)) {
                    continue;
                }
                if($this->_mdl->generator_fields_labels[$i]!="") 
                    $label=$this->_mdl->generator_fields_labels[$i];
                else
                    $label=ucwords(str_replace("_"," ",$fieldname));
                if($fieldname=="company_id") {
                    if(COMPANY_ID==ADMIN_COMPANY_ID) {
                        $hstr.= "<th> ".$label." </th>"; 
                    }
                    continue;
                }
                if($this->_mdl->generator_fields_types[$i]=="file") {
                    $url_fieldname=$fieldname."_url";
                }
                if(!in_array($fieldname,$this->_mdl->generator_field_display) || $fieldname==$url_fieldname) {
                    continue;
                }
                $hstr.="<th>";
                $hstr.=$label;
                $hstr.="</th>";
            }
        echo $hstr;
               echo "</tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>";
        if($canUpdate || $canDelete) {
        $_grid.="<td data-label=\"Action\">";
        }
        if($canUpdate) {
        $_grid.="<form  method=\"post\" action=\"frm_currency_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"currency_id\" value=\"".$_rs["currency_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form>";
        }
        if($canDelete) { 
        $_grid.="<form  method=\"post\" action=\"classes/cls_currency_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"currency_id\" value=\"".$_rs["currency_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>";
        }
        if($canUpdate || $canDelete) {
        $_grid.="</td>";
        }
        $url_fieldname="";
        foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if(!in_array($fieldname,$this->_mdl->generator_field_display) || $fieldname==$url_fieldname) {
                continue;
            }
            if($this->_mdl->generator_fields_labels[$i]!="") 
                $label=$this->_mdl->generator_fields_labels[$i];
            else
                $label=ucwords(str_replace("_"," ",$fieldname));
            if($fieldname=="company_id") {
                if(COMPANY_ID==ADMIN_COMPANY_ID) {
                    $_grid.= "<td data-label=\"".$label."\"> ".$_rs[$fieldname]." </td>"; 
                }
                continue;
            }
            if($this->_mdl->generator_field_data_type[$i]=="date" || $this->_mdl->generator_field_data_type[$i]=="datetime-local" || $this->_mdl->generator_field_data_type[$i]=="datetime" || $this->_mdl->generator_field_data_type[$i]=="timestamp") {
                $fieldvalue=date("d/m/Y",strtotime($_rs[$fieldname]));
                if($this->_mdl->generator_field_data_type[$i]!="date") {
                    $fieldvalue.="<br><small> ".date("h:i:s a",strtotime($_rs[$fieldname]))."</small>";
                }
            } 
            else if($this->_mdl->generator_fields_types[$i]=="file") {
                $url_fieldname=$fieldname."_url";
                if(!empty($_rs[$url_fieldname])) {
                    $fieldvalue="<img src=\"".BASE_URL.$_rs[$url_fieldname]."\" style=\"max-width:100px; max-height:100px;\" alt=\"File\" />";
                }
            } else if($this->_mdl->generator_field_data_type[$i]=="bit") {
                $fieldvalue=($_rs[$fieldname]==1) ? "Yes" : "No";
            } else {
                $fieldvalue=$_rs[$fieldname];
            }
            $_grid.="<td data-label=\"".$label."\">";
            $_grid.=$fieldvalue;
            $_grid.="</td>";
        }
        $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td>No records available.</td>";
                foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
                {
                    if(!in_array($fieldname,$this->_mdl->generator_field_display)) {
                        continue;
                    }
                    if($fieldname=="company_id") {
                        if(COMPANY_ID==ADMIN_COMPANY_ID) {
                            $_grid.= "<td style=\"display:none\">&nbsp;</td>"; 
                        }
                        continue;
                    }
                    $_grid.="<td style=\"display:none\">&nbsp;</td>";
                }
                $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    public function checkDuplicate() {
        global $_dbh;
        $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";$scope_field_name="NULL";$scope_field_value="NULL";$company_id=COMPANY_ID;
        if(isset($_POST["column_name"]))
            $column_name=$_POST["column_name"];
        if(isset($_POST["column_value"]))
            $column_value=$_POST["column_value"];
        if(isset($_POST["id_name"]))
            $id_name=$_POST["id_name"];
        if(isset($_POST["id_value"]))
            $id_value=$_POST["id_value"];
        if(isset($_POST["table_name"]))
            $table_name=$_POST["table_name"];
        if(isset($_POST["scope_field_name"]) && $_POST["scope_field_name"]!="")
            $scope_field_name="'".$_POST["scope_field_name"]."'";
        if(isset($_POST["scope_field_value"]) && $_POST["scope_field_value"]!="")
            $scope_field_value="'".$_POST["scope_field_value"]."'";
        if(COMPANY_ID==ADMIN_COMPANY_ID) {
            $company_id= "NULL"; 
        }
        try {
            $sql="CAll csms_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',".$company_id.",@is_duplicate,".$scope_field_name.",".$scope_field_value.")";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_duplicate = $result->fetchColumn();
            echo $is_duplicate;
            exit;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
    public function getForm($transactionmode="I",$popup=false,$label_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1", $field_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2") {
        $output=""; $hidden_str="";
         if(isset($this->_mdl->generator_table_layout))
            $table_layout=$this->_mdl->generator_table_layout;
        else
            $table_layout="vertical";
        if(is_array($this->_mdl->generator_fields_names) && !empty($this->_mdl->generator_fields_names)){
            if($table_layout=="horizontal") {
                $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
            } else {
                $label_layout_classes=$label_classes." col-form-label";
                $field_layout_classes=$field_classes;
            }
            $output.='<div class="box-body">
                <div class="form-group row gy-2">';
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";$cls_field_name="_".$fieldname;$is_disabled=0;$disabled_str="";$img_str="";

                if(!empty($this->_mdl->generator_field_required) && in_array($fieldname,$this->_mdl->generator_field_required)) {
                    $required=1;
                }
                if(!empty($this->_mdl->generator_field_is_disabled) && in_array($fieldname,$this->_mdl->generator_field_is_disabled)) {
                    $is_disabled=1;
                }
               
                if($this->_mdl->generator_fields_labels[$i]) {
                    $lbl_str='<label for="'.$fieldname.'" class="'.$label_layout_classes.'">'.$this->_mdl->generator_fields_labels[$i].'';
                        if($table_layout=="vertical") {
                            $field_layout_classes=$field_classes;
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
                if(!empty($this->_mdl->generator_chk_duplicate) && in_array($fieldname,$this->_mdl->generator_chk_duplicate)) {
                    $error_container='<div class="invalid-feedback"></div>';
                    $duplicate_str="duplicate";
                    $lbl_str.="*";
                }
                if($is_disabled) {
                    $disabled_str="disabled";
                }
                if($this->_mdl->generator_fields_types[$i]=="email") {
                    $error_container='<div class="invalid-feedback"></div>';
                }
                $lbl_str.="</label>";
                switch($this->_mdl->generator_fields_types[$i]) {
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
                            $table=explode("_",$fieldname);
                            $field_name=$table[0]."_name";
                            $fields=$fieldname.", ".$table[0]."_name";
                            $tablename="tbl_".$table[0]."_master";
                            $selected_val="";
                            if($this->_mdl->$cls_field_name) {
                                $selected_val=$this->_mdl->$cls_field_name;
                            } else if($this->_mdl->generator_default_value[$i]){
                                $selected_val=$this->_mdl->generator_default_value[$i];
                            }
                            if(!empty($this->_mdl->generator_where_condition[$i]))
                                $where_condition_val=$this->_mdl->generator_where_condition[$i];
                            else {
                                $where_condition_val=null;
                            }
                            if($this->_mdl->generator_fields_types[$i]=="checkbox" || $this->_mdl->generator_fields_types[$i]=="radio") {
                                    $cls.=$required_str;
                                    if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                        $flag=1;
                                        $field_str.=getChecboxRadios($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $this->_mdl->generator_fields_types[$i],$disabled_str).$error_container;
                                    }
                                    else{
                                            if($transactionmode=="U" && $this->_mdl->$cls_field_name==1) {
                                                $chk_str="checked='checked'";
                                            }
                                            $value="1";
                                            $field_str.=addHidden($fieldname,0,"chk");
                                    }
                            } else {
                                $cls.="form-control ".$required_str." ".$duplicate_str;
                                $chk_str="";
                                    if(isset($this->_mdl)) {
                                        $value=$this->_mdl->$cls_field_name; 
                                }
                            }
                            if(!empty($value) && ($this->_mdl->generator_fields_types[$i]=="date" || $this->_mdl->generator_fields_types[$i]=="datetime-local" || $this->_mdl->generator_fields_types[$i]=="datetime" || $this->_mdl->generator_fields_types[$i]=="timestamp")) {
                                $value=date("Y-m-d",strtotime($value));
                            }
                            if($this->_mdl->generator_fields_types[$i]=="number") {
                                $step="";$max_str="";$disabled_value="";
                                if(!empty($this->_mdl->generator_field_scale[$i]) && $this->_mdl->generator_field_scale[$i]>0) {
                                    for($k=1;$k<$this->_mdl->generator_field_scale[$i];$k++) {
                                        $step.=0;
                                    }
                                    $step="0.".$step."1";
                                } else {
                                    $step=1;
                                }
                                $step_str='step="'.$step.'"';
                                $min=1; 
                                if(!empty($this->_mdl->generator_allow_zero) && in_array($fieldname,$this->_mdl->generator_allow_zero)) 
                                    $min=0;
                                if(!empty($this->_mdl->generator_allow_minus) && in_array($fieldname,$this->_mdl->generator_allow_minus)) 
                                $min="";

                                $min_str='min="'.$min.'"';
                                $field_str.=addNumber($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str,$this->_mdl->generator_fields_labels[$i],$disabled_value,$max_str).$error_container;
                            }
                            else if($this->_mdl->generator_fields_types[$i]=="select") {
                                $cls="form-select ".$required_str." ".$duplicate_str;
                                
                                if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                    $field_str.=getDropdown($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $disabled_str).$error_container;
                                }
                            } else {
                                if($flag==0) {
                                    if($this->_mdl->generator_fields_types[$i]=="file") {
                                        $value="";$img_str="<br>";
                                        $url_fieldname="_".$fieldname."_url";
                                        if ($transactionmode == "U") {
                                            if ($this->_mdl->$url_fieldname && file_exists(BASE_PATH.$this->_mdl->$url_fieldname)) {
                                                $img_str .= '<img src="' .BASE_URL . $this->_mdl->$url_fieldname. '" alt="'.$this->_mdl->generator_fields_labels[$i].'" style="max-width: 200px; max-height: 200px;">';
                                            } 
                                        } 
                                    }
                                    $field_str.=addInput($this->_mdl->generator_fields_types[$i],$fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$chk_str,$this->_mdl->generator_fields_labels[$i]).$error_container.$img_str;
                                }
                            }
                        break;
                    case "hidden":
                        $lbl_str="";
                        if($this->_mdl->generator_field_data_type[$i]=="int" || $this->_mdl->generator_field_data_type[$i]=="bigint"  || $this->_mdl->generator_field_data_type[$i]=="tinyint" || $this->_mdl->generator_field_data_type[$i]=="decimal")
                            $hiddenvalue=0;
                        else
                            $hiddenvalue="";
                        if($fieldname=="created_by") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=USER_ID;
                            }
                        } else if($fieldname=="created_date") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=date("Y-m-d H:i:s");
                            }
                        } else if($fieldname=="modified_by") {
                            $hiddenvalue=USER_ID; 
                        } else if($fieldname=="modified_date") {
                            $hiddenvalue=date("Y-m-d H:i:s");
                        }
                        else if($fieldname=="company_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_ID;
                            }
                        }
                            
                        else if($fieldname=="company_year_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_YEAR_ID;
                            }
                        }
                            else {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } 
                        }
                        $hidden_str.=addHidden($fieldname,$hiddenvalue);
                    
                        break;
                    case "textarea":
                        $cls.="form-control ".$required_str." ".$duplicate_str;
                        $value="";
                        if(isset($this->_mdl)){
                                $value=$this->_mdl->$cls_field_name;
                            }
                        $field_str.=addTextArea($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,3,$this->_mdl->generator_fields_labels[$i]).$error_container;
                        break;
                    default:
                        break;
                } //switch ends
                 if(empty($this->_mdl->generator_after_detail) || (!empty($this->_mdl->generator_after_detail) && !in_array($fieldname,$this->_mdl->generator_after_detail))) {
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='<div class="row mb-3 align-items-center">';
                    }
                    $output.=$lbl_str;
                    if($field_str) {
                    $output.='<div class="'.$field_layout_classes.'">';
                    $output.=$field_str;
                    $output.='</div>';
                    }
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='</div>';
                    }
                } else {
                    $lbl_array[]=$lbl_str;
                    $field_array[]=$field_str;
                }
            } // foreach ends
            $output.="</div><!-- /.row -->";
            $output.=$hidden_str;
               $output.="</div> <!-- /.box-body -->";
        
            if(!empty($field_array)) {
                $output.='<div class="box-body">
                <div class="form-group row gy-2">';
                 for($j=0;$j<count($field_array);$j++) {
                    if($table_layout=="vertical") {
                        $output.='<div class="row mb-3 align-items-center">';
                    }
                    $output.=$lbl_array[$j];
                    if($field_array[$j]) {
                        $output.='<div class="col-8 col-sm-4 col-md-3 col-lg-2">';
                        $output.=$field_array[$j];
                        $output.='</div>';
                    }
                    if($table_layout=="vertical") {
                        $output.='</div>';
                    }
                 } // for loop ends
                 $output.="</div><!-- /.row -->
              </div> <!-- /.box-body -->";
            }
        } // if ends
        return $output;
    } // function getForm ends
}
 class dal_currencymaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        try {
            if($_mdl->_currency_id=="") {
                $_mdl->_currency_id=0;
            }
            $_dbh->exec("set @p0 = ".$_mdl->_currency_id);
            $_pre=$_dbh->prepare("CALL currency_master_transaction (@p0,?,?,?,?,?,?,?,?,?) ");
            
                if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                    foreach($_mdl->generator_fields_names as $i=>$fieldname)
                    {
                        if($i==0)
                            continue;
                        if($fieldname=="company_id") {
                            if(COMPANY_ID==ADMIN_COMPANY_ID) {
                                $field=$_mdl->{"_".$fieldname};
                            } else {
                                $field=COMPANY_ID;
                            }  
                        } else {
                            $field=$_mdl->{"_".$fieldname};
                        }
                        $_pre->bindValue($i,$field);
                    }
                }
                $_pre->bindValue($i+1,$_mdl->_transactionmode);
                $_pre->execute();
                if($_mdl->_transactionmode=="I") {
                    $result = $_dbh->query("SELECT @p0 AS inserted_id");
                    $insertedId = $result->fetchColumn();
                    $_mdl->_currency_id=$insertedId;
                }
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    if($_mdl->_currency_id)
                        $return_id=$_mdl->_currency_id;
                    else 
                        $return_id=0;
                    echo $return_id;
                    exit;
                }
                if($_mdl->_transactionmode=="D") {
                    $_SESSION["sess_message"]="Record Deleted Successfully.";
                }
                else if($_mdl->_transactionmode=="U") {
                    $_SESSION["sess_message"]="Record Updated Successfully.";
                }
                else {
                    $_SESSION["sess_message"]="Record Saved Successfully.";  
                }
                $_SESSION["sess_message_cls"]="success";
                $_SESSION["sess_message_title"]="Success!";
                $_SESSION["sess_message_icon"]="success";
            } catch (PDOException $e) {
                global $tbl_currency_master;
                $ajax=0;
                if($_mdl->_ajaxAdd==1) {
                    $ajax=1;
                    $_mdl->_ajaxAdd=0;
                }
                errorHandling($e,"currency_id",$tbl_currency_master, $ajax);
                
            }
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL currency_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["currency_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {
            if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                foreach($_mdl->generator_fields_names as $i=>$fieldname)
                {
                    $_mdl->{"_".$fieldname}=$_rs[0][$fieldname];
                }
            }
            $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_currencymaster();
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
    if(isset($_REQUEST["transactionmode"]))
    $tmode=$_REQUEST["transactionmode"];
    else
        $tmode="I";
    
    if(isset($_POST["ajaxAdd"]) && $_POST["ajaxAdd"]==1) {
        $_bll->_mdl->_ajaxAdd=1;
    }
    if(is_array($_bll->_mdl->generator_fields_names) && !empty($_bll->_mdl->generator_fields_names)){
        $url_fieldname="";
        foreach($_bll->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if($fieldname==$url_fieldname) {
                continue;
            }
            if($_bll->_mdl->generator_fields_types[$i]=="file") {
                $upload_dir = UPLOAD_DIR ."currency_master/";
                $upload_path = UPLOAD_PATH ."currency_master/";
                $url_fieldname = $fieldname . "_url";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $new_logo_uploaded = false;
                if ($_FILES[$fieldname]["name"]!="" && $_FILES[$fieldname]["error"] == UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES[$fieldname]["name"]);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_ext = ["jpg", "jpeg", "png", "gif"];

                    if (in_array($file_ext, $allowed_ext)) {
                        $file_name = preg_replace("/[^A-Za-z0-9._-]/", "", $file_name);
                        $base_name = pathinfo($file_name, PATHINFO_FILENAME);
                        $counter = 0;
                        $new_file_name = $file_name;
                        $abs_path = $upload_path . $new_file_name;
                        $rel_path = $upload_dir . $new_file_name;

                        while (file_exists($relative_path)) {
                            $counter++;
                            $new_file_name = $base_name . "_" . $counter . "." . $file_ext;
                            $abs_path = $upload_path . $new_file_name;
                            $rel_path = $upload_dir . $new_file_name;
                        }

                        if ($tmode == "U" && $_REQUEST[$url_fieldname]!="" && file_exists(BASE_PATH.$_REQUEST[$url_fieldname])) {
                            unlink(BASE_PATH.$_REQUEST[$url_fieldname]);
                        }
                        $tmpName = $_FILES[$fieldname]["tmp_name"];
                        if (move_uploaded_file($tmpName, $abs_path)) {
                            $imageData = file_get_contents($abs_path);
                            $_bll->_mdl->{"_".$fieldname} = $imageData;
                            $_bll->_mdl->{"_".$url_fieldname}= $rel_path;
                            continue;
                        } else {
                            $_SESSION["sess_message"]="Failed to upload file.";
                            $_SESSION["sess_message_cls"]="danger";
                        }
                    } else {
                        $_SESSION["sess_message"]="Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                        $_SESSION["sess_message_cls"]="danger";
                    }
                }
            } 
            if(isset($_REQUEST[$fieldname]) && !empty($_REQUEST[$fieldname])) {
                $field=$_REQUEST[$fieldname];
                if($_bll->_mdl->generator_fields_types[$i]=="datetime-local" || $_bll->_mdl->generator_fields_types[$i]=="datetime" || $_bll->_mdl->generator_fields_types[$i]=="timestamp") {
                    $field=date("Y-m-d H:i:s",strtotime($field));
                } else if($_bll->_mdl->generator_fields_types[$i]=="date") {
                    $field=date("Y-m-d",strtotime($field));
                }else if(is_array($field) && ($_bll->_mdl->generator_fields_types[$i]=="checkbox" || $_bll->_mdl->generator_fields_types[$i]=="radio") && $_bll->_mdl->generator_dropdown_table!="" && $_bll->_mdl->generator_label_column!="" && $_bll->_mdl->generator_value_column!="") {
                    $field = implode(",", $field);
                }
                else if(is_array($field) && $_bll->_mdl->generator_fields_types[$i]=="select") {
                    $field = implode(",", $field);
                }
                if(!is_array($field))
                    $field=trim($field);
            }    
            else {
                $field=null;
            } 

            $_bll->_mdl->{"_".$fieldname}=$field;
        }
    }
    $_bll->_mdl->_transactionmode =$tmode;
 $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
