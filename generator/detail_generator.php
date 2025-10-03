<?php
 $_strgeneratebll_detail='<?php  
include_once(__DIR__ . "/../config/connection.php");
';
$_classmdl_detail= "class ".$_mdl_detail;
$_strgeneratebll_detail.="<?php
    ".$_classmdl_detail." 
{                        
"; 
    $_strgeneratebll_detail.=$_definevar_detail;
    $detailtransactionmode="detailtransactionmode";
    $_strgeneratebll_detail.="public $".$detailtransactionmode.";";
      
$_strgeneratebll_detail.="
}

";
//starting of bllmaster class 
$_strgeneratebll_detail.='class '.$_bll_detail."                           
{   
    ";            
                $_strgeneratebll_detail.='public $_mdl;
    ';
                $_strgeneratebll_detail.='public $_dal;

    ';      $_strgeneratebll_detail.='public function __construct()    
    {
        $this->_mdl =new '.$_mdl_detail.'();'.
    ' 
        $this->_dal =new '.$_dal_detail.'();
    }

    ';
    $_strgeneratebll_detail.='public function dbTransaction()
    {
        ';
               
               $_strgeneratebll_detail.='$this->_dal->dbTransaction($this->_mdl);
               
       ';
$_strgeneratebll_detail.='
    }
     public function pageSearch()
    {
        global $_dbh;
        $_grid="";
        $_grid="
        <table  id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
        <thead id=\"tableHead\">
            <tr>
            <th>Action</th>";
        ';
               for($i=1;$i<=$_detailfieldno;$i++)
                {
                     if(in_array($_detailfieldname[$i],$_detailfield_display)) {
                         $_strgeneratebll_detail.=' $_grid.="<th> '.$_detailfield_label[$i].' </th>";
                         '; 
                     }
                }
             
               $_strgeneratebll_detail.='$_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="'.str_replace('$_',"",$_var[0]).'";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms_search_detail(\''.$_detailfield_display_str.'\',\''.$tbl_detail.'\',\' and t.".$main_id_name."=".$main_id."\')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="'.$_detailfieldname[0].'";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            ';
    for($i=1;$i<=$_detailfieldno;$i++)
    {     
        if(in_array($_detailfieldname[$i],$_detailfield_display)) {
            $_strgeneratebll_detail.='
                    $value=$_rs[\''.$detailfetch_fields[$i].'\'];
                    $text_align="left";
                    $data_value="";
            ';
            $class="";
            if($_detailtype[$i]=="date" || $_detailtype[$i]=="datetime-local" || $_detailtype[$i]=="datetime" || $_detailtype[$i]=="timestamp") {
                $_strgeneratebll_detail.='
                    $data_value=$value;
                    $value=date("d/m/Y",strtotime($value));
                ';
                if($_detailtype[$i]!="date") {
                    $_strgeneratebll_detail.='$value.="<br><small> ".date("h:i:s a",strtotime($value))."</small>";   
                    ';
                }
            } 
            if($_detailtype[$i]=='bit') {
                 $_strgeneratebll_detail.='
                    if($value) {
                        $data_value=$value;
                        $value="<img src=\'dist/images/right_icon.png\' style=\"height:15px; width:auto;\">";
                        $text_align="left";
                    }
                    else {
                        $data_value="";
                        $value="";
                    }   
                ';
            } 
            if($_detailfield_type[$i]=="select" && $_detaildropdown_table[$i]!="" && $_detaillabel_column[$i]!="" && $_detailvalue_column[$i]!="") {
                 $_strgeneratebll_detail.='
                    $data_value=$value;
                    $value=$_rs[\''.$detailfetch_fields[$i].'_name\'];
                ';
            }
            $_strgeneratebll_detail.='
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"'.$detailfetch_fields[$i].'\" align=\"".$text_align."\"> ".$value." </td>"; 
           ';
        } else {
            $_strgeneratebll_detail.='
                $_grid.="
                <td data-label=\"'.$_detailfieldname[$i].'\" style=\"display:none\">&nbsp;</td>"; 
           ';
        }
    }

    $_strgeneratebll_detail.='$_grid.= "</tr>\n";
        $i++;
        }
        if($i==0) {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"'.$_detailfieldno.'\">No records available.</td>";';

            for($i=1;$i<=$_detailfieldno;$i++) {
                if(in_array($_detailfieldname[$i],$_detailfield_display)) {
                     $_strgeneratebll_detail.='$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     ';
                } 
            }
            $_strgeneratebll_detail.='$_grid.="</tr>";
        }
    } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"'.$_detailfieldno.'\">No records available.</td>";
            ';

            for($i=1;$i<=$_detailfieldno;$i++) {
                if(in_array($_detailfieldname[$i],$_detailfield_display)) {
                     $_strgeneratebll_detail.='$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     ';
                }
            }
            $_strgeneratebll_detail.='$_grid.="</tr>";
    }
        $_grid.="</tbody>
        </table> ";
        return $_grid; 
    }   
}
 ';

//ending of bllmaster class  & starting of dalmaster class
$_strgeneratebll_detail.='class '.$_dal_detail.'                         
{
    ';    
            $_strgeneratebll_detail.='public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        try {
        ';
                for($i=0;$i<=$_detailfieldno;$i++)
                {
                    if($i==0) {
                        $_strgeneratebll_detail.='$_dbh->exec("set @p0 = ".$_mdl->'.str_replace('$',"",$_detailvar[$i]).');';
                    }else {
                        $_detailquestion.="?,";
                    }
                }
               $_strgeneratebll_detail.='
        $_pre=$_dbh->prepare("CALL '.str_replace("tbl_","",$_detailtablename).'_transaction (@p0,'.$_detailquestion.'?) ");
        ';   
             for($i=0;$i<=$_detailfieldno;$i++)
                {
                 
                    if($i>0) {
                        if($_detailtype[$i]=='bit' || $_detailtype[$i]=='int') {
                            $third_arg=", PDO::PARAM_INT";
                        } else {
                            $third_arg="";
                        }
                         $_strgeneratebll_detail.='$_pre->bindParam('.$i.',$_mdl->'.str_replace('$',"",$_detailvar[$i]).$third_arg.');
        '; 
                        
                    }
                }
        $_strgeneratebll_detail.='$_pre->bindParam('.$i.',$_mdl->detailtransactionmode);
        '; 
               $_strgeneratebll_detail.='$_pre->execute();
        ';
            
               $_strgeneratebll_detail.='
            } catch (PDOException $e) {
                echo $e->getMessage();
                exit;
            }
    }
}';      

$handle = fopen("../classes/".str_replace("tbl_","cls_",$_detailtablename).".php", "w");
fwrite($handle,$_strgeneratebll_detail);
