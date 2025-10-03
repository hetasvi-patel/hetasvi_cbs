<?php
include("../config/connection.php");

class generate
{
    public $_fieldname;
    public $_var;
    public $_prvar; 
    public $_mdl;
    public $_bll;
    public $_dal;
    public $_classmdl;
    public $_classbll; 
    public $_classdal;
    public $_class; 
    public $_tablename;
    public $_strgeneratebll;
    public $_generatesearch;
    public $_generatetransaction;
    public $_prctype;
    public $_prvinsert;
    public $_prvinsertvalues;
    public $_prvupdate;
    public $_fieldno;
  //  public $_check;
    public function classes()
    {
        global $_dbh;
        global $database_name;
        $_checktype=""; //master_detail   //if table structure is master detail
        //$_tablename="tbl_customer_master"; // master table name
        $_detailtablename=""; //  detail table name
        
         $_strgeneratedetailtbls="";
        $_strgeneratetbls='<!DOCTYPE html>
        <html>
                <head>
                    <title>Generate Form</title>
                    <style>
                        .hidden {display:none;}
                    </style>
                    <script>
                        function showDropdownFields(obj,displayContainer) {
                            if(obj.value=="select" || obj.value=="checkbox" || obj.value=="radio") {
                                document.getElementById(displayContainer).classList.remove("hidden");
                            } else {
                                 document.getElementById(displayContainer).classList.add("hidden");
                            }
                        }
                    </script>
                </head>
                <body>
                    <form id="frm" name="frm"  method="post" >';
        
            // query to fetch all column names and its data type and character length etc. 
            $_sql="SELECT TABLE_NAME as `name` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='".$database_name."'";
            $_pre=$_dbh->prepare($_sql);
            //$_fieldno=$_pre->columnCount();
            $_pre->execute();

            $master_selected="";$master_detail_selected="";
            if(isset($_POST["selectType"])) {
                $_checktype=$_POST["selectType"];
                if($_POST["selectType"]=="master")
                    $master_selected='selected="selected"';
                if($_POST["selectType"]=="master_detail")
                    $master_detail_selected='selected="selected"';
            }

            $_strgeneratetbls.='
                        <div style="text-align:center; margin-top:40px;">
                         <select name="selectType" onchange="document.getElementById(\'frm\').submit();">
                                <option value="master" '.$master_selected.'>Master</option>
                                <option value="master_detail" '.$master_detail_selected.'>Master-Detail</option>
                            </select>
                            <select name="selectTables" onchange="document.getElementById(\'frm\').submit();">
                                <option value="">Select</option>';

            foreach($_pre->fetchAll(PDO::FETCH_ASSOC) as $_meta)
            {      
                 $_tablename=$_meta["name"];
                 $_tablename_arr=explode("_",$_tablename);
                 $count=count($_tablename_arr);
                $sel="";$sel_detail="";
                 if(isset($_POST["selectTables"]) && $_POST["selectTables"]==$_tablename) {
                     $sel='selected="selected"';
                 }
                 if(isset($_POST["selectDetailTables"]) && $_POST["selectDetailTables"]==$_tablename) {
                     $sel_detail='selected="selected"';
                 }
                 if($_tablename_arr[$count-1]=='master')
                    $_strgeneratetbls.="<option value='".$_tablename."' ".$sel.">".$_tablename."</option>";
                else
                    $_strgeneratedetailtbls.="<option value='".$_tablename."' ".$sel_detail.">".$_tablename."</option>";
            }

            $_strgeneratetbls.='
                            </select>';
            if(isset($_POST["selectType"]) && $_POST["selectType"]=="master_detail") {
                $_strgeneratetbls.='<select name="selectDetailTables" onchange="document.getElementById(\'frm\').submit();">
                                    <option value="">Select</option>';
                $_strgeneratetbls.=$_strgeneratedetailtbls;
                $_strgeneratetbls.='</select>';
            }
            $_strgeneratetbls.='</div>';
            $tbl_hidden="";
            $_strgenerateflds="";
             if(isset($_POST["selectTables"]) && $_POST["selectTables"]!="") {
               $_tablename=$_POST["selectTables"];
                include("generator_main_table.php");
            } //main table ends
            $_strgenerateflds_detail="";
                 $i=0;
             if(isset($_POST["selectDetailTables"]) && $_POST["selectDetailTables"]!="") {
                 $_detailtablename=$_POST["selectDetailTables"];
                 include("generator_detail_table.php");
             } // detail table ends
            $_strgeneratetbls.=$_strgenerateflds;
            if($_strgenerateflds_detail!="") {
                $_strgeneratetbls.=$_strgenerateflds_detail;
            }
              if(isset($_POST["selectTables"]) && $_POST["selectTables"]!="") {
                 $_strgeneratetbls.='
                  <div style="width:90%; margin:10px auto 0 auto;" s>Id, created date, created by, modified date, modified by => HIDDEN. Join fields => DROPDOWN.</div>';
              }
                  $_strgeneratetbls.='
                  <div style="text-align:center; margin-top:30px;">
                  '.$tbl_hidden.'
                  <input type="hidden" name="checktype" value="'.$_checktype.'" />
                    <input type="submit" name="submit_fields" value="Submit" style="padding:10px; cursor:pointer;" />
                    </div>';
        $_strgeneratetbls.="
                </form>
            </body>
        </html>";
       echo $_strgeneratetbls;
        $options=array();
      if(isset($_POST["submit_fields"])) {
          /*echo "<pre>";
            foreach($_POST as $name => $value) {
                    print_r($name);
                    echo "<br>";
            }
          echo "</pre>";*/
          $table_layout=$_POST["table_layout"];
           $_checktype=$_POST["checktype"];
            $_fieldno=$_POST["field_no"];
            $_tablename=$_POST["table_name"];
            if(isset($_POST["table_name_detail"]))
                $_detailtablename=$_POST["table_name_detail"];
          if(isset($_POST["detailfield_no"]))
                $_detailfieldno=$_POST["detailfield_no"];
            $_str= str_replace("_","",$_tablename);         //remove _
            $_class=str_replace("tbl","",$_str);            //remove tbl and gives classname
            $_mdl= str_replace("tbl","mdl_",$_str);         //three classname
            $_bll= str_replace("tbl","bll_",$_str);  
            $_dal= str_replace("tbl","dal_",$_str);  
           /**** FOR DETAIL ***/
          if($_detailtablename!="") {
            $detailtable_layout=$_POST["table_layout"];
        $_str_detail= str_replace("_","",$_detailtablename);       
        $_class_detail=str_replace("tbl","",$_str_detail);       
        $_mdl_detail= str_replace("tbl","mdl_",$_str_detail); 
        $_bll_detail= str_replace("tbl","bll_",$_str_detail);  
        $_dal_detail= str_replace("tbl","dal_",$_str_detail);  
          }
           /**** \FOR DETAIL ***/
            $_classmdl= "class ".$_mdl;
            $_strgeneratebll='<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");
';
         /**** FOR DETAIL ***/
          if($_detailtablename!="") {
    $_strgeneratebll.='include("'.str_replace("tbl_","cls_",$_detailtablename).'.php"); 
        ';
          }
           /**** \FOR DETAIL ***/
    $_strgeneratebll.=
$_classmdl.' 
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
        global $'.$_tablename.';
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $'.$_tablename.');
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
'; 
            $_prvinsert=""; $_prctype=""; $_prvinsertvalues=""; $_question=""; $_strgeneratesrh=""; $_prvalias=""; $_generatetransaction=""; $_prvupdate="";
            $_fieldname=array(); $_type=array(); $_typelen=array(); $_precision=array(); $_scale=array(); $_field_type=array(); $_field_label=array(); $_field_display=array(); $_field_required=array(); $_allow_zero=array(); $_allow_minus=array(); $_chk_duplicate=array();$_is_disabled=array(); $_after_detail=array();  $tbl=$_tablename." t";$fetch_fields=array();$_dropdown_table=array();$_label_column=array();$_value_column=array();$_where_condition=array();$_default_value=array();
        $joinflag=0;
;            for($i=0;$i<=$_fieldno;$i++) {
                $_fieldname[$i]=$_POST["field_name"][$i];
                $_type[$i]=$_POST["data_type"][$i];
                $_typelen[$i]=$_POST["length"][$i];
                $_precision[$i]=$_POST["precision"][$i];
                $_scale[$i]=$_POST["scale"][$i];
                $_field_type[$i]=$_POST["field_type"][$i];
                $_dropdown_table[$i]=$_POST["dropdown_table"][$i];
                $_label_column[$i]=$_POST["label_column"][$i];
                $_value_column[$i]=$_POST["value_column"][$i];
                $_where_condition[$i]=$_POST["where_condition"][$i];
                $_default_value[$i]=$_POST["default_value"][$i];
                $_field_label[$i]=$_POST["field_label"][$i];
                
                if(isset($_POST["field_display"][$i])) {
                    $_field_display[$i]=$_POST["field_display"][$i];
                }
                //$_field_required[$i]="";
                if(isset($_POST["field_required"][$i])) {
                    $_field_required[$i]=$_POST["field_required"][$i];
                }
                
                //$_allow_zero[$i]="";
                if(isset($_POST["allow_zero"][$i])) {
                    $_allow_zero[$i]=$_POST["allow_zero"][$i];
                }
                //$_allow_minus[$i]="";
                if(isset($_POST["allow_minus"][$i])) {
                    $_allow_minus[$i]=$_POST["allow_minus"][$i];
                }
                //$_chk_duplicate[$i]="";
                if(isset($_POST["chk_duplicate"][$i])) {
                    $_chk_duplicate[$i]=$_POST["chk_duplicate"][$i];
                }
                if(isset($_POST["is_disabled"][$i])) {
                    $_is_disabled[$i]=$_POST["is_disabled"][$i];
                }
                if(isset($_POST["after_detail"][$i])) {
                    $_after_detail[$i]=$_POST["after_detail"][$i];
                }
                if($_type[$i]=="int" || $_type[$i]=="bigint")
                {
                    $_typelen[$i]="";
                }
                else if($_type[$i]=="numeric" || $_type[$i]=="decimal")
                {
                    $_typelen[$i]=$_precision[$i].','.$_scale[$i];
                }
                if($_typelen[$i]!="") 
                {
                   
                    $_typelen[$i]="(".$_typelen[$i].")";
                }
            
                $_str= $_fieldname[$i];       // removes _ in field name
                $_varstr[$_str]= str_replace($_fieldname[$i],'$_'.$_fieldname[$i],$_fieldname[$i]);  //variable name in to the string array for further use 
                $_var[$i]= str_replace($_str,'$_'.$_str,$_str);
                //$_prvar[$i]= str_replace($_str,'pr_'.$_str,$_str);
               $_prvinsert.=$_fieldname[$i].',
                        ';
                if($i==0) 
                    $_inout="INOUT";
                else 
                    $_inout="IN";
                $_prctype.=$_inout." p_".$_fieldname[$i].' '.'     '.$_type[$i].$_typelen[$i].',
        ' ;
                   // $_strgeneratebll.='public '.$_varstr[$_str].';          
    //';    
                $_prvinsertvalues.='p_'.$_fieldname[$i].',
                        '; 
            } // main table for loop ends
            
            $_field_display_str="t.*"; // for search display
           for($i=0;$i<=$_fieldno;$i++) {
                if(in_array($_fieldname[$i],$_field_display)) {
                    
                    if(($_field_type[$i]=="select" || $_field_type[$i]=="radio" || $_field_type[$i]=="checkbox")  && $_dropdown_table[$i]!="" && $_label_column[$i]!="" && $_value_column[$i]!="") {
                        // $tbl1=explode("_",$_fieldname[$i]);
                        
                         $tbl.=" LEFT JOIN ".$_dropdown_table[$i]." t".$i." ON t.".$_fieldname[$i]."=t".$i.".".$_value_column[$i];
                         //$_field_display_str.=str_replace("t.".$_fieldname[$i].", ","",$_field_display_str);
                         $_field_display_str.=", t".$i.".".$_label_column[$i]." as ".$_fieldname[$i];
                         $fetch_fields[$i]=$_fieldname[$i];
                    }
                    elseif($_fieldname[$i]=="company_id") {
                        
                        $_field_display_str.=", c1.company_name as ".$_fieldname[$i];
                        $tbl.=" LEFT JOIN tbl_company_master c1  ON t.".$_fieldname[$i]."=c1.company_id ";
                        $fetch_fields[$i]=$_fieldname[$i];
                    } 
                    elseif($_fieldname[$i]=="created_by") {
                        
                        $_field_display_str.=", u1.person_name as ".$_fieldname[$i];
                        $tbl.=" LEFT JOIN tbl_user_master u1  ON t.".$_fieldname[$i]."=u1.user_id ";
                        $fetch_fields[$i]=$_fieldname[$i];
                    } 
                    elseif($_fieldname[$i]=="modified_by") {
                       
                        $_field_display_str.=", u2.person_name as ".$_fieldname[$i];
                        $tbl.=" LEFT JOIN tbl_user_master u2  ON t.".$_fieldname[$i]."=u2.user_id ";
                        $fetch_fields[$i]=$_fieldname[$i];
                    } else {
                        $fetch_fields[$i]=$_fieldname[$i];
                    }
                }
           }
          /**** FOR DETAIL ***/
          if($_detailtablename!="") {
              $_definevar_detail="";$_detailprvinsert=""; $_detailprctype=""; $_detailprvinsertvalues=""; $_detailquestion=""; $_detailstrgeneratesrh=""; $_detailprvalias=""; $_detailgeneratetransaction=""; $_detailprvupdate=""; $_detailfield_display_str="t.*";
                $_detailfieldname=array(); $_detailtype=array(); $_detailtypelen=array(); $_detailprecision=array(); $_detailscale=array(); $_detailfield_type=array(); $_detailfield_label=array(); $_detailfield_display=array(); $_detailfield_required=array(); $_detailallow_zero=array(); $_detailallow_minus=array(); $_detailchk_duplicate=array();$_detailis_disabled=array(); $tbl_detail=$_detailtablename." t";$detailfetch_fields=array();$_detaildropdown_table=array();$_detailvalue_column=array();$_detaillabel_column=array();$_detailwhere_condition=array();
              for($i=0;$i<=$_detailfieldno;$i++) {
                    $_detailfieldname[$i]=$_POST["detailfield_name"][$i];
                    $_detailtype[$i]=$_POST["detaildata_type"][$i];
                    $_detailtypelen[$i]=$_POST["detaillength"][$i];
                    $_detailprecision[$i]=$_POST["detailprecision"][$i];
                    $_detailscale[$i]=$_POST["detailscale"][$i];
                    $_detailfield_type[$i]=$_POST["detailfield_type"][$i];
                   $_detaildropdown_table[$i]=$_POST["detaildropdown_table"][$i];
                   $_detailvalue_column[$i]=$_POST["detailvalue_column"][$i];
                   $_detaillabel_column[$i]=$_POST["detaillabel_column"][$i];
                  $_detailwhere_condition[$i]=$_POST["detailwhere_condition"][$i];
                    $_detailfield_label[$i]=$_POST["detailfield_label"][$i];
                    if(isset($_POST["detailfield_display"][$i])) {
                        $_detailfield_display[$i]=$_POST["detailfield_display"][$i];
                    }
                    if(in_array($_detailfieldname[$i],$_detailfield_display)) {
                        if(($_detailfield_type[$i]=="select" || $_detailfield_type[$i]=="radio" || $_detailfield_type[$i]=="checkbox")  && $_detaildropdown_table[$i]!="" && $_detaillabel_column[$i]!="" && $_detailvalue_column[$i]!="") {
                            // $tbl1=explode("_",$_fieldname[$i]);
                            
                            $tbl_detail.=" LEFT JOIN ".$_detaildropdown_table[$i]." t".$i." ON t.".$_detailfieldname[$i]."=t".$i.".".$_detailvalue_column[$i];
                            //$_field_display_str.=str_replace("t.".$_fieldname[$i].", ","",$_field_display_str);
                            $_detailfield_display_str.=", t".$i.".".$_detaillabel_column[$i]." as ".$_detailfieldname[$i]."_name, t.".$_detailfieldname[$i];
                            $detailfetch_fields[$i]=$_detailfieldname[$i];
                        }
                        else {
                            $detailfetch_fields[$i]=$_detailfieldname[$i];
                        }
                    }
                    
                    
                    //$_detailfield_required[$i]="";
                    if(isset($_POST["detailfield_required"][$i])) {
                        $_detailfield_required[$i]=$_POST["detailfield_required"][$i];
                    }
                    //$_detailallow_zero[$i]="";
                    if(isset($_POST["detailallow_zero"][$i])) {
                        $_detailallow_zero[$i]=$_POST["detailallow_zero"][$i];
                    }
                    //$_detailallow_minus[$i]="";
                    if(isset($_POST["detailallow_minus"][$i])) {
                        $_detailallow_minus[$i]=$_POST["detailallow_minus"][$i];
                    }
                    //$_detailchk_duplicate[$i]="";
                    if(isset($_POST["detailchk_duplicate"][$i])) {
                        $_detailchk_duplicate[$i]=$_POST["detailchk_duplicate"][$i];
                    }
                    if(isset($_POST["detailis_disabled"][$i])) {
                        $_detailis_disabled[$i]=$_POST["detailis_disabled"][$i];
                    }            
                    if($_detailtype[$i]=="int" || $_detailtype[$i]=="bigint")
                    {
                        $_detailtypelen[$i]="";
                    }
                    if($_detailtype[$i]=="numeric" || $_detailtype[$i]=="decimal")
                    {
                        $_detailtypelen[$i]=$_detailprecision[$i].','.$_detailscale[$i];
                    }
                    if($_detailtypelen[$i]!="") 
                    {
                    
                        $_detailtypelen[$i]="(".$_detailtypelen[$i].")";
                    }
                    $_detailstr= $_detailfieldname[$i];       // removes _ in field name
                    $_detailvarstr[$_detailstr]= str_replace($_detailfieldname[$i],'$'.$_detailfieldname[$i],$_detailfieldname[$i]);  //variable name in to the string array for further use 
                    $_detailvar[$i]= str_replace($_detailstr,'$'.$_detailstr,$_detailstr);
                    //$_prvar[$i]= str_replace($_str,'pr_'.$_str,$_str);
                   $_detailprvinsert.=$_detailfieldname[$i].',
                            ';
                    if($i==0) 
                        $_detailinout="INOUT";
                    else 
                        $_detailinout="IN";
                    $_detailprctype.=$_detailinout." p_".$_detailfieldname[$i].' '.'     '.$_detailtype[$i].$_detailtypelen[$i].',
            ' ;
                  $_definevar_detail.='public '.$_detailvarstr[$_detailstr].';     
                  
    ';     
                    $_detailprvinsertvalues.='p_'.$_detailfieldname[$i].',
                            '; 
                } // detail table for loop ends
          }
          /**** \FOR DETAIL ***/
           if($_field_display_str=="") {
                $_field_display_str="*";
            } 
          /*** FOR DETAIL **/
          if($_detailtablename!="") {
              if($_detailfield_display_str=="") {
                $_detailfield_display_str="*";
            } 
          }
          /**** \FOR DETAIL ***/
       
            $options=array(
                "table_layout"=>$table_layout,
                "field_name"=>$_fieldname,
                "field_type"=>$_field_type,
                "field_scale"=>$_scale,
                "dropdown_table"=>$_dropdown_table,
                "value_column"=>$_value_column,
                "label_column"=>$_label_column,
                "where_condition"=>$_where_condition,
                "default_value"=>$_default_value,
                "field_label"=>$_field_label,
                "field_display"=>$_field_display,
                "field_required"=>$_field_required,
                "allow_zero"=>$_allow_zero,
                "allow_minus"=>$_allow_minus,
                "chk_duplicate"=>$_chk_duplicate,
                "is_disabled"=>$_is_disabled,
                "after_detail"=>$_after_detail,
                "field_data_type"=>$_type,
            );
            $options=json_encode($options);
          
            if(!$row)
            {
                $stmt = $_dbh->prepare("INSERT INTO `tbl_generator_master` (table_name,generator_options)  VALUES (:value1,:value2)");
                $stmt->execute(array(':value1'=>$_tablename,':value2'=>$options));
            } else {
                $stmt = $_dbh->prepare("UPDATE `tbl_generator_master` SET table_name = :value1, generator_options = :value2 WHERE generator_id = :value3");
                $stmt->execute(array(':value1'=>$_tablename,':value2'=>$options,':value3'=>$row['generator_id']));
            }
          
          /*** FOR DETAIL **/
          if($_detailtablename!="") {
            $options_detail=array(
                    "table_layout"=>$detailtable_layout,
                    "field_name"=>$_detailfieldname,
                    "field_type"=>$_detailfield_type,
                    "field_scale"=>$_detailscale,
                    "dropdown_table"=>$_detaildropdown_table,
                    "value_column"=>$_detailvalue_column,
                    "label_column"=>$_detaillabel_column,
                    "where_condition"=>$_detailwhere_condition,
                    "field_label"=>$_detailfield_label,
                    "field_display"=>$_detailfield_display,
                    "field_required"=>$_detailfield_required,
                    "allow_zero"=>$_detailallow_zero,
                    "allow_minus"=>$_detailallow_minus,
                    "chk_duplicate"=>$_detailchk_duplicate,
                    "is_disabled"=>$_detailis_disabled,
                    "field_data_type"=>$_detailtype,
                );
                $options_detail=json_encode($options_detail);
           
               if(!$rowdetail)
                {
                    $stmt = $_dbh->prepare("INSERT INTO `tbl_generator_master` (table_name,generator_options)  VALUES (:value1,:value2)");
                    $stmt->execute(array(':value1'=>$_detailtablename,':value2'=>$options_detail));
                } else {
                    $stmt = $_dbh->prepare("UPDATE `tbl_generator_master` SET table_name = :value1, generator_options = :value2 WHERE generator_id = :value3");
                    $stmt->execute(array(':value1'=>$_detailtablename,':value2'=>$options_detail,':value3'=>$rowdetail['generator_id']));
                }
          }
            /*** \FOR DETAIL **/
            
            $_str=$_class;
            $_len=strlen($_str);
            $_check= substr($_str,$_len-6);
           if($_check=="master")                        //  if master then arraylist
            {  
                //$_strgeneratebll.='public $_transactionmode;'.'
   // ';
               
                if($_detailtablename!=""){
                    $_strgeneratebll.='
                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    ';
                }
                /*if($_checktype=="master_detail")
                {
                    $_strgeneratebll.='public $_array_itemdetail;

    '; 
                    $_strgeneratebll.="public function ".$_mdl."()
    {
            ";
                    $_strgeneratebll.='$this->_array_itemdetail = new  ArrayObject();
    }
';              } */
               $_strgeneratebll.="
}

";
             // end of mdlmaster class               
               
               //starting of bllmaster class 
                $_strgeneratebll.='class '.$_bll."                           
{   
    ";            
                $_strgeneratebll.='public $_mdl;
    ';
                $_strgeneratebll.='public $_dal;

    ';      $_strgeneratebll.='public function __construct()    
    {
        $this->_mdl =new '.$_mdl.'();'.
    ' 
        $this->_dal =new '.$_dal.'();
    }

    ';
               $_strgeneratebll.='public function dbTransaction()
    {
        ';
               /*if($_checktype=="master_detail")
                {
            $_strgeneratebll.='$_bllitem= new '.str_replace("master","detail",$_bll).'();'.'
   ';
            $_strgeneratebll.=' 
        if($this->_mdl->_transactionmode=="D")
        {
            for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
            {
                $_bllitem->_mdl =$iterator->current();
                $_bllitem->dbTransaction();
            }
        }
    ';
                }*/
               $_strgeneratebll.='$this->_dal->dbTransaction($this->_mdl);
               
       ';
        if($_detailtablename!="") {
               $_strgeneratebll.='/** FOR DETAIL **/
               
        $_bllitem= new '.$_bll_detail.'();
        if($this->_mdl->_'.$_fieldname[0].' > 0 ) {
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
                            $_bllitem->_mdl->'.$_fieldname[0].' = $this->_mdl->_'.$_fieldname[0].';
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
                        $_bllitem->_mdl->'.$_fieldname[0].' = $this->_mdl->_'.$_fieldname[0].';
                        $_bllitem->dbTransaction();
                    }
                }
            }
        }
        /** \FOR DETAIL **/
        ';
        }
        /* if($_checktype=="master_detail")
        {
$_strgeneratebll.='
        if($this->_mdl->_transactionmode!="D")
        {
            for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
            {
                    $_bllitem->_mdl =$iterator->current();
                    $_bllitem->_mdl->'.str_replace("$","",$_var[0]).' = '.'$this->_mdl->'.str_replace("$","",$_var[0]).';'.'
                    $_bllitem->dbTransaction();
            }
        }
';
        }*/
            $_strgeneratebll.='
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../'.str_replace("tbl_","srh_",$_tablename).'.php");
       }
       ';
                        $_strgeneratebll.='if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../'.str_replace("tbl_","srh_",$_tablename).'.php");
       }
       ';
                        $_strgeneratebll.='if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../'.str_replace("tbl_","frm_",$_tablename).'.php'.'");
       }

    }
 ';  
               $_strgeneratebll.='
    public function fillModel()
    {
        $this->_dal->fillModel($this->_mdl);
    ';
    /*if($_checktype=="master_detail")
     { 
$_strgeneratebll.='    $_pre=$_dbh->prepare("CALL '.str_replace("tbl_","",$_detailtablename).'_fillmodel'.' (?) '.'");
';
$_strgeneratebll.='$_pre->bindParam(1,$_REQUEST["'.$_detailfieldname[0].'"]);
$_pre->execute();
$_rs=$_pre->fetchAll(); 

';

    for($i=0;$i<=$_detailfieldno;$i++)
    {
        $_strgeneratebll.='$_mdl->'.str_replace('$',"",$_var[$i]).'=$_rs[0]["'.$_detailfieldname[$i].'"];
';  
    }
$_strgeneratebll.='$_mdl->_transactionmode =$_REQUEST["detailtransactionmode"];';
     } */
               $_strgeneratebll.='
    }
    public function pageSearch()
    {
        global $_dbh;
        global $canUpdate;
        global $canDelete;
        $company_query=str_replace("company_id","t.company_id",COMPANY_QUERY);
        $sql="CAll csms_search_detail(\''.$_field_display_str.'\',\''.$tbl.'\',\'".$company_query."\')";
        echo "<!-- Filter row -->
                <div class=\"row gx-2 gy-1 align-items-center\" id=\"search-filters\">";';
                $_strgeneratebll.='
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
                echo $hstr;';
        $_strgeneratebll.='echo "</div>";
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
        ';
               $_strgeneratebll.='echo $hstr;
               echo "</tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        ';
               $_strgeneratebll.='
        $_grid.="<tr>";
        if($canUpdate || $canDelete) {
        $_grid.="<td data-label=\"Action\">";
        }
        if($canUpdate) {
        $_grid.="<form  method=\"post\" action=\"frm_'.str_replace("tbl_","",$_tablename).'.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"'.$_fieldname[0].'\" value=\"".$_rs["'.$_fieldname[0].'"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form>";
        }
        if($canDelete) { 
        $_grid.="<form  method=\"post\" action=\"classes/cls_'.str_replace("tbl_","",$_tablename).'.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"'.$_fieldname[0].'\" value=\"".$_rs["'.$_fieldname[0].'"]."\" />
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
        ';
            $_strgeneratebll.='$_grid.= "</tr>\n";
           
            ';
               $_strgeneratebll.='
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
                ';
                $_strgeneratebll.='$_grid.="</tr>";
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
            $scope_field_name="\'".$_POST["scope_field_name"]."\'";
        if(isset($_POST["scope_field_value"]) && $_POST["scope_field_value"]!="")
            $scope_field_value="\'".$_POST["scope_field_value"]."\'";
        if(COMPANY_ID==ADMIN_COMPANY_ID) {
            $company_id= "NULL"; 
        }
        try {
            $sql="CAll csms_check_duplicate(\'".$column_name."\',\'".$column_value."\',\'".$id_name."\',\'".$id_value."\',\'".$table_name."\',".$company_id.",@is_duplicate,".$scope_field_name.",".$scope_field_value.")";
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
            $output.=\'<div class="box-body">
                <div class="form-group row gy-2">\';
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
                    $lbl_str=\'<label for="\'.$fieldname.\'" class="\'.$label_layout_classes.\'">\'.$this->_mdl->generator_fields_labels[$i].\'\';
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
                    $error_container=\'<div class="invalid-feedback"></div>\';
                    $lbl_str.="*";
                }
                if(!empty($this->_mdl->generator_chk_duplicate) && in_array($fieldname,$this->_mdl->generator_chk_duplicate)) {
                    $error_container=\'<div class="invalid-feedback"></div>\';
                    $duplicate_str="duplicate";
                    $lbl_str.="*";
                }
                if($is_disabled) {
                    $disabled_str="disabled";
                }
                if($this->_mdl->generator_fields_types[$i]=="email") {
                    $error_container=\'<div class="invalid-feedback"></div>\';
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
                                                $chk_str="checked=\'checked\'";
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
                                $step_str=\'step="\'.$step.\'"\';
                                $min=1; 
                                if(!empty($this->_mdl->generator_allow_zero) && in_array($fieldname,$this->_mdl->generator_allow_zero)) 
                                    $min=0;
                                if(!empty($this->_mdl->generator_allow_minus) && in_array($fieldname,$this->_mdl->generator_allow_minus)) 
                                $min="";

                                $min_str=\'min="\'.$min.\'"\';
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
                                                $img_str .= \'<img src="\' .BASE_URL . $this->_mdl->$url_fieldname. \'" alt="\'.$this->_mdl->generator_fields_labels[$i].\'" style="max-width: 200px; max-height: 200px;">\';
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
                        }';
                        if($_tablename!="tbl_company_master") {
                            $_strgeneratebll.='
                        else if($fieldname=="company_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_ID;
                            }
                        }
                            ';
                        }
                        if($_tablename!="tbl_company_year_master") {
                            $_strgeneratebll.='
                        else if($fieldname=="company_year_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_YEAR_ID;
                            }
                        }
                            ';
                        }
                    $_strgeneratebll.='else {
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
                        $output.=\'<div class="row mb-3 align-items-center">\';
                    }
                    $output.=$lbl_str;
                    if($field_str) {
                    $output.=\'<div class="\'.$field_layout_classes.\'">\';
                    $output.=$field_str;
                    $output.=\'</div>\';
                    }
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.=\'</div>\';
                    }
                } else {
                    $lbl_array[]=$lbl_str;
                    $field_array[]=$field_str;
                }
            } // foreach ends
            $output.="</div><!-- /.row -->";
            $output.=$hidden_str;
               $output.="</div> <!-- /.box-body -->";
        ';
        if($_detailtablename!="") {
            $_strgeneratebll.='
                $output.="<!-- detail table content--> 
                        <div class=\"box-body\">
                            <div class=\"box-detail\">";';
                                    $_str= str_replace("_","",$_detailtablename);         
                                    $_childbll= str_replace("tbl","bll_",$_str); 
                                     $_strgeneratebll.='
                                     $_blldetail = new '.$_childbll.'();
                                     $detailHtml = $_blldetail->pageSearch();
                                    if($detailHtml)
                                        $output.=$detailHtml; 
                                $output.="<button type=\"button\" name=\"detailBtn\" id=\"detailBtn\" class=\"btn btn-primary add\" data-bs-toggle=\"modal\" data-bs-target=\"#modalDialog\"  onclick=\"openModal()\">Add Detail Record</button>
                        </div>
                    </div>
                    <!-- /.box-body detail table content -->";
            ';
        }
        $_strgeneratebll.='
            if(!empty($field_array)) {
                $output.=\'<div class="box-body">
                <div class="form-group row gy-2">\';
                 for($j=0;$j<count($field_array);$j++) {
                    if($table_layout=="vertical") {
                        $output.=\'<div class="row mb-3 align-items-center">\';
                    }
                    $output.=$lbl_array[$j];
                    if($field_array[$j]) {
                        $output.=\'<div class="col-8 col-sm-4 col-md-3 col-lg-2">\';
                        $output.=$field_array[$j];
                        $output.=\'</div>\';
                    }
                    if($table_layout=="vertical") {
                        $output.=\'</div>\';
                    }
                 } // for loop ends
                 $output.="</div><!-- /.row -->
              </div> <!-- /.box-body -->";
            }
        } // if ends
        return $output;
    } // function getForm ends
}
 ';
           //ending of bllmaster class  & starting of dalmaster class
            $_strgeneratebll.='class '.$_dal.'                         
{
    ';    
            $_strgeneratebll.='public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        ';
                $id=str_replace('$',"",$_var[0]);
                 
                for($i=0;$i<=$_fieldno;$i++)
                {
                    if($i>0) {
                        $_question.="?,";
                    }
                }
                $_strgeneratebll.='
        try {
            if($_mdl->'.$id.'=="") {
                $_mdl->'.$id.'=0;
            }
            $_dbh->exec("set @p0 = ".$_mdl->'.$id.');';
                $_strgeneratebll.='
            $_pre=$_dbh->prepare("CALL '.str_replace("tbl_","",$_tablename).'_transaction (@p0,'.$_question.'?) ");
            
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
                    $_mdl->'.$id.'=$insertedId;
                }
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    if($_mdl->'.$id.')
                        $return_id=$_mdl->'.$id.';
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
                global $'.$_tablename.';
                $ajax=0;
                if($_mdl->_ajaxAdd==1) {
                    $ajax=1;
                    $_mdl->_ajaxAdd=0;
                }
                errorHandling($e,"'.$_fieldname[0].'",$'.$_tablename.', $ajax);
                
            }
        ';  
            
        if($_detailtablename!="") {
           $_strgeneratebll.='
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_'.$_fieldname[0].'=$insertedId;
            }
            /*** /FOR DETAIL ***/
    ';
        }
             /*  if($_checktype=="master_detail")
                {
        $_strgeneratebll.='if($_REQUEST["transactionmode"]!="D")       
        {
            $maxid=$_pre->fetchAll();
            $_mdl->'.str_replace("$","",$_var[0]).'=$maxid[0]["'.str_replace('$_',"",$_var[0]).'"];
        }
        ';
                }*/
        $_strgeneratebll.='
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL '.str_replace("tbl_","",$_tablename).'_fillmodel'.' (?) '.'");
        ';
        $_strgeneratebll.='$_pre->bindParam(1,$_REQUEST["'.$_fieldname[0].'"]);
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
}';      

$_strgeneratebll.='
$_bll=new '.$_bll.'();
';
if($_detailtablename!="") {
$_str= str_replace("_","",$_detailtablename);         
$_childbll= str_replace("tbl","bll_",$_str);             
$_strgeneratebll.='
/*** FOR DETAIL ***/
$_blldetail=new '.$_childbll.'();
/*** /FOR DETAIL ***/
';
}
               
 $_strgeneratebll.='if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
';
/*if($_checktype=="master_detail")
{ 
$_strgeneratebll.='$_childbll = new '.$_childbll.'();

';
}*/
$_strgeneratebll.='if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
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
                $upload_dir = UPLOAD_DIR ."'.str_replace("tbl_","",$_tablename).'/";
                $upload_path = UPLOAD_PATH ."'.str_replace("tbl_","",$_tablename).'/";
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
 ';
          
        
    if($_detailtablename!="") {
            $_strgeneratebll.=' 
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
        ';
        }
        $_strgeneratebll.='$_bll->dbTransaction();
}

'; 
        $_strgeneratebll.='if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
';
               /***** FOR DETAIL ****/
               if($_detailtablename!="") {
                    //include("detail_generator.php");
               }
               /***** \FOR DETAIL *****/
               
                $handle = fopen("../classes/".str_replace("tbl_","cls_",$_tablename).".php", "w");
                fwrite($handle,$_strgeneratebll);
                
               //include("frm_generator_detail.php");
               //include("srh_generator.php");
               //include("stored_procedures.php");
               
                /***** FOR DETAIL ****/
               if($_detailtablename!="") {
                //include("stored_procedures_detail.php");
               }
               /***** \FOR DETAIL *****/
        
           } // end of master condition
      } // post submit fields ends
    } // classes ends
} // generate ends

$_generate = new generate();
$_generate->classes();

?> 

