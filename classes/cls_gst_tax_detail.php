<?php
    class mdl_gsttaxdetail 
{                        
public $gst_tax_id;     
                  
    public $hsn_code_id;     
                  
    public $tax_type;     
                  
    public $tax;     
                  
    public $effective_date;     
                  
    public $remark;     
                  
    public $detailtransactionmode;
}

class bll_gsttaxdetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_gsttaxdetail(); 
        $this->_dal =new dal_gsttaxdetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
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
         $_grid.="<th> Tax Type </th>";
                          $_grid.="<th> Tax % </th>";
                          $_grid.="<th> Effective Date </th>";
                          $_grid.="<th> Remark </th>";
                         $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="hsn_code_id";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms_search_detail('t2.value as tax_type_name, t.tax_type,t.tax, t.effective_date, t.remark, t.gst_tax_id','tbl_gst_tax_detail t LEFT JOIN view_tax_type t2 ON t.tax_type=t2.id',' and t.".$main_id_name."=".$main_id."')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="gst_tax_id";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            
                $_grid.="
                <td data-label=\"hsn_code_id\" style=\"display:none\">&nbsp;</td>"; 
           
                    $value=$_rs['tax_type'];
                    $text_align="left";
                    $data_value="";
            
                    $data_value=$value;
                    $value=$_rs['tax_type_name'];
                
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"tax_type\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['tax'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"tax\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['effective_date'];
                    $text_align="left";
                    $data_value="";
            
                    $data_value=$value;
                    $value=date("d/m/Y",strtotime($value));
                
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"effective_date\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['remark'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"remark\" align=\"".$text_align."\"> ".$value." </td>"; 
            $_grid.= "</tr>\n";
            $i++;
            }
            if($i==0) {
                $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
                $_grid.="<td colspan=\"5\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="</tr>";
            }
        } else {
                $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
                $_grid.="<td colspan=\"5\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="<td style=\"display:none\">&nbsp;</td>";
                        $_grid.="</tr>";
        }
        $_grid.="</tbody>
        </table> ";
        return $_grid; 
    }   
}
 class dal_gsttaxdetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        
        try{
            $_dbh->exec("set @p0 = ".$_mdl->gst_tax_id);
            $_pre=$_dbh->prepare("CALL gst_tax_detail_transaction (@p0,?,?,?,?,?,?) ");
            $_pre->bindParam(1,$_mdl->hsn_code_id,PDO::PARAM_INT);
            $_pre->bindParam(2,$_mdl->tax_type,);
            $_pre->bindParam(3,$_mdl->tax,);
            $_pre->bindParam(4,$_mdl->effective_date,);
            $_pre->bindParam(5,$_mdl->remark,);
            $_pre->bindParam(6,$_mdl->detailtransactionmode);
            $_pre->execute();
        }
        catch (PDOException $e) {
           $err_info = $e->errorInfo;
            if (is_array($err_info) && $err_info[1] == 1452) {
                $msg=$err_info[2];
                $msg=explode(":",$msg);
                $msg=$msg[0];
                $_SESSION["sess_message"] = $msg;

            } else {
                $_SESSION["sess_message"] = addslashes($e->getMessage());
            }
            $_SESSION["sess_message_cls"] = "danger";
            $_SESSION["sess_message_title"] = "Error!";
            $_SESSION["sess_message_icon"] = "error";
        }
        
    }
}