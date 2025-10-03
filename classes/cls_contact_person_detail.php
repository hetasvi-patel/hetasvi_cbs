<?php
 /* \ADDED BY Hetasvi ON 23/09/2025 */
global $tbl_contact_person_detail;
$tbl_contact_person_detail = "tbl_contact_person_detail";
/* \ADDED BY Hetasvi ON 23/09/2025 */
    class mdl_contactpersondetail 
{                        
public $contact_person_id;     
                  
    public $customer_id;     
                  
    public $contact_person_name;     
                  
    public $mobile;     
                  
    public $email;     
                  
    public $is_whatsapp;     
                  
    public $is_email;     
                  
    public $detailtransactionmode;
}

class bll_contactpersondetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_contactpersondetail(); 
        $this->_dal =new dal_contactpersondetail();
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
         $_grid.="<th> Contact Person </th>";
                          $_grid.="<th> Mobile No. </th>";
                          $_grid.="<th> Email ID </th>";
                          $_grid.="<th> WhatsApp </th>";
                          $_grid.="<th> Email </th>";
                         $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="customer_id";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms_search_detail('t.contact_person_name, t.mobile, t.email, t.is_whatsapp, t.is_email, t.contact_person_id','tbl_contact_person_detail t',' and t.".$main_id_name."=".$main_id."')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="contact_person_id";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            
                $_grid.="
                <td data-label=\"customer_id\" style=\"display:none\">&nbsp;</td>"; 
           
                    $value=$_rs['contact_person_name'];
                    $text_align="left";
                     $data_value="";
            
                $_grid.="
                <td class=\"\" data-value=\"".$data_value."\" data-label=\"contact_person_name\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['mobile'];
                    $text_align="left";
                     $data_value="";
            
                $_grid.="
                <td class=\"\" data-value=\"".$data_value."\" data-label=\"mobile\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['email'];
                    $text_align="left";
                     $data_value="";
            
                $_grid.="
                <td class=\"\" data-value=\"".$data_value."\" data-label=\"email\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['is_whatsapp'];
                    $text_align="left";
                     $data_value="";
            
                    if($value) {
                        $data_value=$value;
                        $value="<img src='dist/images/right_icon.png' style=\"height:15px; width:auto;\">";
                        $text_align="left";
                    }
                    else {
                        $data_value="";
                        $value="";
                    }   
                
                $_grid.="
                <td class=\"different\" data-value=\"".$data_value."\" data-label=\"is_whatsapp\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['is_email'];
                    $text_align="left";
                     $data_value="";
            
                    if($value) {
                        $data_value=$value;
                        $value="<img src='dist/images/right_icon.png' style=\"height:15px; width:auto;\">";
                        $text_align="left";
                    }
                    else {
                        $data_value="";
                        $value="";
                    }   
                
                $_grid.="
                <td class=\"different\" data-value=\"".$data_value."\" data-label=\"is_email\" align=\"".$text_align."\"> ".$value." </td>"; 
           $_grid.= "</tr>\n";
        $i++;
        }
        if($i==0) {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"6\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="</tr>";
        }
    } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"6\">No records available.</td>";
            $_grid.="<td style=\"display:none\">&nbsp;</td>";
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
 class dal_contactpersondetail                         
{
    
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        global $tbl_contact_person_detail;
        try{
             /* \ADDED BY Hetasvi ON 23/09/2025 */
            if($_mdl->contact_person_id=="") {
                $_mdl->contact_person_id=0; 
            }
             /* \ADDED BY Hetasvi ON 23/09/2025 */
        $_dbh->exec("set @p0 = ".$_mdl->contact_person_id);
        $_pre=$_dbh->prepare("CALL contact_person_detail_transaction (@p0,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->customer_id,PDO::PARAM_INT);
        $_pre->bindParam(2,$_mdl->contact_person_name,);
        $_pre->bindParam(3,$_mdl->mobile,);
        $_pre->bindParam(4,$_mdl->email,);
        $_pre->bindParam(5,$_mdl->is_whatsapp,PDO::PARAM_INT);
        $_pre->bindParam(6,$_mdl->is_email,PDO::PARAM_INT);
        $_pre->bindParam(7,$_mdl->detailtransactionmode);
        $_pre->execute();
           /* \ADDED BY Hetasvi ON 23/09/2025 */
            if($_mdl->detailtransactionmode=="I") {
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                $insertedId = $result->fetchColumn();
                $_mdl->contact_person_id=$insertedId;  
            }
            
            if($_mdl->detailtransactionmode=="D") {
                $_SESSION["sess_message"]="Record Deleted Successfully."; 
            } else if($_mdl->detailtransactionmode=="U") {
                $_SESSION["sess_message"]="Record Updated Successfully."; 
                $_SESSION["sess_message"]="Record Saved Successfully.";   
            }
            $_SESSION["sess_message_cls"]="success";
            $_SESSION["sess_message_title"]="Success!";
            $_SESSION["sess_message_icon"]="success";
        } catch (PDOException $e) {
            $ajax=0;
            errorHandling($e,"contact_person_id", $tbl_contact_person_detail, $ajax, $_mdl->contact_person_id);  
        }
         /* \ADDED BY Hetasvi ON 23/09/2025 */
    }
}