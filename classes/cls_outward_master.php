<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");
include("cls_outward_detail.php");
class mdl_outwardmaster 
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
        global $tbl_outward_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_outward_master);
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

                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    
}

class bll_outwardmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_outwardmaster(); 
        $this->_dal =new dal_outwardmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
        $_bllitem= new bll_outwarddetail();
        if($this->_mdl->_transactionmode!="D")
        {
            if(!empty($this->_mdl->_array_itemdetail)) {
                    for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            foreach($detailrow as $name=>$value) {
                                if($value==="")
                                        $value=null;
                                $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->outward_id = $this->_mdl->_outward_id;
                        $_bllitem->dbTransaction();
                    }
            }
                if(!empty($this->_mdl->_array_itemdelete)) {
                for($iterator= $this->_mdl->_array_itemdelete->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            foreach($detailrow as $name=>$value) {
                                $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->outward_id = $this->_mdl->_outward_id;
                        $_bllitem->dbTransaction();
                    }
                }
        }
    /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_outward_master.php");
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
        $sql="CALL csms_search_detail(
    't.*, 
     t4.customer as customer, 
     t13.contact_person_name as outward_order_by, 
     c1.company_name as company_id, 
     u1.person_name as created_by, 
     u2.person_name as modified_by',
    'tbl_outward_master t 
        LEFT JOIN tbl_customer_master t4 ON t.customer = t4.customer_id
        LEFT JOIN tbl_contact_person_detail t13 ON t.outward_order_by = t13.contact_person_id 
        LEFT JOIN tbl_company_master c1 ON t.company_id = c1.company_id
        LEFT JOIN tbl_user_master u1 ON t.created_by = u1.user_id
        LEFT JOIN tbl_user_master u2 ON t.modified_by = u2.user_id',
    '".$company_query."'
)";
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
        $_grid.="<form  method=\"post\" action=\"frm_outward_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form>";
        }
        if($canDelete) { 
        $_grid.="<form  method=\"post\" action=\"classes/cls_outward_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
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
    $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";$company_id=COMPANY_ID;

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
    if(COMPANY_ID==ADMIN_COMPANY_ID) {
        $company_id= "NULL"; 
    }

   /* \Add this on 29/09/2025 by HETASVI */
    $scope_field_name = "NULL";
    $scope_field_value = "NULL";
    if (isset($_POST["company_year_id"]) && $_POST["company_year_id"] != "") {
        $scope_field_name = "'company_year_id'";
        $scope_field_value = "'".$_POST["company_year_id"]."'";
    }
      /* \END this on 29/09/2025 by HETASVI */

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
        echo 0;
        exit;
    }
    echo 0;
    exit;
}
    /* ADDED BY BHUMITA ON 18/08/2025 */
    function getContactPersons() {
        global $_dbh;
        $customer_id = intval($_REQUEST['customer_id']);
        $options = '<option value="">Select</option>';
        if ($customer_id > 0) {
            try {
                $stmt = $_dbh->prepare("SELECT contact_person_id, contact_person_name FROM tbl_contact_person_detail WHERE customer_id = ? ORDER BY contact_person_name");
                $stmt->execute([$customer_id]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $id = htmlspecialchars($row['contact_person_id']);
                        $name = htmlspecialchars($row['contact_person_name']);
                        $options .= "<option value=\"$id\">$name</option>";
                    }
                } else {
                    $options = '<option value="">No contact persons found</option>';
                }
            } catch (PDOException $e) {
                $options = '<option value="">Error loading contact persons</option>';
                error_log("Database error: " . $e->getMessage());
            }
        }
        echo $options;
        exit;
    }
    /* \ADDED BY BHUMITA ON 18/08/2025 */
    public function getForm($transactionmode="I",$popup=false,$label_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1", $field_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2") {
        $output=""; $hidden_str="";
        /* ADDED BY BHUMITA ON 19/08/2025 */
        global $next_outward_sequence, $outward_no_formatted;
        $next_outward_sequence = isset($next_outward_sequence) ? $next_outward_sequence : 1;
        $outward_no_formatted = isset($outward_no_formatted) ? $outward_no_formatted : '';
        /* \ADDED BY BHUMITA ON 19/08/2025 */
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

            /* ADDED BY BHUMITA ON 19/08/2025 */
            $output .= '
                <div class="row mb-2 align-items-center">
                    <label for="outward_sequence" class="' . $label_layout_classes . '">Outward No</label>
                    <div class="' . $field_layout_classes . '">
                        <div class="row g-2">
                            <div class="col-6">'.
                            addNumber("outward_sequence", $next_outward_sequence,"required","","form-control duplicate","duplicate","min='1'","","Outward Sequence")
                                .'<div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">'.
                            addInput("text","outward_no",$outward_no_formatted,"required","disabled","form-control duplicate","duplicate","",$this->_mdl->generator_fields_labels[2])
                                .'
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>';
            /* \ADDED BY BHUMITA ON 19/08/2025 */
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                /* ADDED BY BHUMITA ON 30/07/2025 */
                if ($fieldname == "outward_sequence" || $fieldname == "outward_no") {
                    continue;
                }
                /* \ADDED BY BHUMITA ON 30/07/2025 */
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
                                            $field_str.=addHidden($fieldname,0);
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
                                /* ADDED BY BHUMITA ON 19/08/2025 */
                                if($fieldname=="customer"){
                                    $field_str .= '
                                    <div class="d-flex align-items-center gap-2" style="width:100%;">
                                        <div style="flex:1;min-width:120px;max-width:100%;">';
                                    $disabled_str = ($transactionmode=="U") ? "disabled" : "";//ADDED BY HETANSHREE CUSTOMER EDIT MODE DISABLE
                                }
                                /* \ADDED BY BHUMITA ON 19/08/2025 */
                                if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                    $field_str.=getDropdown($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $disabled_str).$error_container;
                                }
                                //ADDED BY HETANSHREE CUSTOMER EDIT MODE DISABLE
                                if ($fieldname=="customer" && $transactionmode=="U") {
                                $field_str .= '<input type="hidden" name="customer" value="' . htmlspecialchars($selected_val) . '">';
                                }
                                /* ADDED BY BHUMITA ON 19/08/2025 */
                                if($fieldname=="customer") {
                                    $field_str .= '</div>
                                       <button type="button" class="btn btn-info w-auto" id="btn_inward">Select Inward</button>
                                    </div>';
                                } 
                                /* \ADDED BY BHUMITA ON 19/08/2025 */   
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
                        $output.='<div class="row mb-2 align-items-center">';
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

               /* MODIFIED BY BHUMTIA ON 19/08/2025 */
                $output.="<!-- detail table content--> 
                        <div class=\"box-body\">
                            <div class=\"box-detail\">";
                                    $_blldetail = new bll_outwarddetail();
                                     $detailHtml = $_blldetail->pageSearch();
                                    if($detailHtml)
                                        $output.=$detailHtml; 
                                $output.="
                        </div>
                    </div>
                    <!-- /.box-body detail table content -->";
                /* \MODIFIED BY BHUMTIA ON 19/08/2025 */
                /* MODIFIED BY MANSI AFTER DETAIL DESIGN*/
               if (!empty($field_array)) {
                    $output .= '<div class="box-body">';
                    // First row
                    $output .= '<div class="form-group mb-1">';
                    $output .= '<div class="row">';
                    for ($j = 0; $j < 2; $j++) {
                        $label_text_raw = isset($lbl_array[$j]) ? trim(strip_tags($lbl_array[$j])) : '';
                        $label_text = htmlspecialchars($label_text_raw, ENT_QUOTES, 'UTF-8');
                        $field = isset($field_array[$j]) ? $field_array[$j] : '';
                        $output .= '
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label text-truncate" title="' . $label_text . '">' . $label_text . '</label>
                                    <div class="col-7">' . $field . '</div>
                                </div>
                            </div>';
                    }
                    $output .= '</div></div>';
                    $output .= '
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[2], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[2] . '</label>
                                        <div class="col-7">' . $field_array[2] . '</div>
                                    </div>
                                </div>
                                 <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[3], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[3] . '</label>
                                        <div class="col-7">' . $field_array[3] . '</div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[4], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[4] . '</label>
                                        <div class="col-7">' . $field_array[4] . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    $output .= '
                        <div class="form-group mt-1">';
                    $output .= '
                            <div class="row">
                                <!-- Left column -->
                                <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[13], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[13] . '</label>
                                        <div class="col-7">' . $field_array[13] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[14], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[14] . '</label>
                                        <div class="col-7">' . $field_array[14] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[12], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[12] . '</label>
                                        <div class="col-7">' . $field_array[12] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[15], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[15] . '</label>
                                        <div class="col-7">' . $field_array[15] . '</div>
                                    </div>
                                </div>

                                <!-- Middle column -->
                                <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[5], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[5] . '</label>
                                        <div class="col-7">' . $field_array[5] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[6], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[6] . '</label>
                                        <div class="col-7">
                                            <div class="row gx-2 align-items-center">
                                                <div class="col">' . $field_array[6] . '</div>
                                                <div class="col-auto text-center px-1">:</div>
                                                <div class="col">' . $field_array[7] . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[8], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[8] . '</label>
                                        <div class="col-7">
                                            <div class="row gx-2 align-items-center">
                                                <div class="col">' . $field_array[8] . '</div>
                                                <div class="col-auto text-center px-1">:</div>
                                                <div class="col">' . $field_array[9] . '</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right column (totals etc.) -->
                                <div class="col-12 col-lg-4">
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[10], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[10] . '</label>
                                        <div class="col-7">' . $field_array[10] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[11], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[11] . '</label>
                                        <div class="col-7">' . $field_array[11] . '</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[16], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[16] . '</label>
                                        <div class="col-7">' . $field_array[16] . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>';

                    $output .= '</div>'; // /.box-body
                }
        } // if ends
        return $output;
    } // function getForm ends
}
 class dal_outwardmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        try {
            if($_mdl->_outward_id=="") {
                $_mdl->_outward_id=0;
            }
            $_dbh->exec("set @p0 = ".$_mdl->_outward_id);
            $_pre=$_dbh->prepare("CALL outward_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            
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
                   $_mdl->_outward_id=$insertedId;
                }
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    if($_mdl->_outward_id)
                        $return_id=$_mdl->_outward_id;
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
            }      catch (PDOException $e) {
                global $tbl_outward_master;
                $ajax=0;
                if($_mdl->_ajaxAdd==1) {
                    $ajax=1;
                    $_mdl->_ajaxAdd=0;
                }
                errorHandling($e,"outward_id",$tbl_outward_master,$ajax, $_mdl->_outward_id);
                
        }
        /*  \ADDED BY HETANSHREE FOREIGNKEY DELETE ERROR MESSAGE*/
        
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_outward_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL outward_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["outward_id"]);
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
$_bll=new bll_outwardmaster();
/*** FOR DETAIL ***/
$_blldetail=new bll_outwarddetail();
/*** /FOR DETAIL ***/
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

            // FILE FIELD HANDLING (unchanged aside from minor variable safety)
            if($_bll->_mdl->generator_fields_types[$i]=="file") {
                $upload_dir = UPLOAD_DIR ."outward_master/";
                $upload_path = UPLOAD_PATH ."outward_master/";
                $url_fieldname = $fieldname . "_url";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                if (!empty($_FILES[$fieldname]["name"]) && $_FILES[$fieldname]["error"] == UPLOAD_ERR_OK) {
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

                        while (file_exists($abs_path)) {
                            $counter++;
                            $new_file_name = $base_name . "_" . $counter . "." . $file_ext;
                            $abs_path = $upload_path . $new_file_name;
                            $rel_path = $upload_dir . $new_file_name;
                        }

                        if ($tmode == "U" && isset($_REQUEST[$url_fieldname]) && $_REQUEST[$url_fieldname]!="" && file_exists(BASE_PATH.$_REQUEST[$url_fieldname])) {
                            @unlink(BASE_PATH.$_REQUEST[$url_fieldname]);
                        }
                        if (move_uploaded_file($_FILES[$fieldname]["tmp_name"], $abs_path)) {
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
            /*MODIFIED BY MANSI*/
            $originalField = null;
            if (isset($_bll->_mdl->{"_".$fieldname})) {
                $originalField = $_bll->_mdl->{"_".$fieldname};
            }
            if(isset($_REQUEST[$fieldname]) && $_REQUEST[$fieldname] !== '') {
                $field = $_REQUEST[$fieldname];

                // Normalize date/datetime safely
                if($_bll->_mdl->generator_fields_types[$i]=="datetime-local"
                    || $_bll->_mdl->generator_fields_types[$i]=="datetime"
                    || $_bll->_mdl->generator_fields_types[$i]=="timestamp") {

                    if($field !== null && $field !== '') {
                        $ts = strtotime($field);
                        if($ts !== false) {
                            $field = date("Y-m-d H:i:s",$ts);
                        } else {
                            // leave as original or null
                            $field = $originalField;
                        }
                    } else {
                        $field = $originalField;
                    }

                } else if($_bll->_mdl->generator_fields_types[$i]=="date") {
                    if($field !== null && $field !== '') {
                        $ts = strtotime($field);
                        if($ts !== false) {
                            $field = date("Y-m-d",$ts);
                        } else {
                            $field = $originalField;
                        }
                    } else {
                        $field = $originalField;
                    }

                } else if(is_array($field) 
                        && ($_bll->_mdl->generator_fields_types[$i]=="checkbox" 
                            || $_bll->_mdl->generator_fields_types[$i]=="radio")
                        && !empty($_bll->_mdl->generator_dropdown_table)
                        && !empty($_bll->_mdl->generator_label_column)
                        && !empty($_bll->_mdl->generator_value_column)) {
                    $field = implode(",", $field);
                }

                $field = (is_string($field)) ? trim($field) : $field;
            }
            // Special fallback for disabled fields in Update mode
            else if($tmode=="U" && in_array($fieldname, ['outward_sequence','outward_no','outward_date'])) {
                $field = $originalField;
            }
            else if($fieldname=="customer" && $tmode=="U") {
                $field = $_bll->_mdl->_customer;
            }
            else {
                $field = null;
            }

            $_bll->_mdl->{"_".$fieldname} = $field;
        }
    }
    $_bll->_mdl->_transactionmode =$tmode;
  
            /*** FOR DETAIL ***/
            $_bll->_mdl->_array_itemdetail=array();
            $_bll->_mdl->_array_itemdelete=array();
            if(isset($_REQUEST["detail_records"])) {
                $detail_records=json_decode($_REQUEST["detail_records"],true);
                if(!empty($detail_records)) {
                    $arrayobject = new ArrayObject($detail_records);
                        $_bll->_mdl->_array_itemdetail=$arrayobject;
                }
            }
            if(isset($_REQUEST["deleted_records"])) {
                $deleted_records=json_decode($_REQUEST["deleted_records"],true);
                if(!empty($deleted_records)) {
                    $deleteobject = new ArrayObject($deleted_records);
                        $_bll->_mdl->_array_itemdelete=$deleteobject;
                }
            }
            /*** \FOR DETAIL ***/
        $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
