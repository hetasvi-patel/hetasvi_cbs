<?php session_start();     
include_once(__DIR__ . "/../config/connection.php");
include("cls_customer_contact_detail.php"); 
        class mdl_customermaster 
{                        
public $_customer_id;          
    public $_customer_name;          
    public $_customer_type;          
    public $_address;          
    public $_district_name;          
    public $_city_id;          
    public $_state_id;          
    public $_country_id;          
    public $_pincode;          
    public $_phone_no;          
    public $_email_id;          
    public $_webaddress;          
    public $_gst_no;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_transactionmode;
    
                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    
}

class bll_customermaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_customermaster(); 
        $this->_dal =new dal_customermaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
                  $_bllitem= new bll_customercontactdetail();
                    if($this->_mdl->_transactionmode!="D")
                    {
                        if(!empty($this->_mdl->_array_itemdetail)) {
                             for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                             {
                                     $detailrow=$iterator->current();
                                    if(is_array($detailrow)) {
                                        foreach($detailrow as $name=>$value) {
                                            $_bllitem->_mdl->{$name}=$value;
                                        }
                                    }
                                    $_bllitem->_mdl->customer_id = $this->_mdl->_customer_id;
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
                                    $_bllitem->_mdl->customer_id = $this->_mdl->_customer_id;
                                    $_bllitem->dbTransaction();
                             }
                         }
                  }
               /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_customer_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_customer_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_customer_master.php");
       }

    }
 
    public function fillModel()
    {
        global $_dbh;
        $this->_dal->fillModel($this->_mdl);
    
    
    }
     public function pageSearch()
    {
        global $_dbh;
        
        $sql="CAll hetasvi_search('t.customer_name, t.district_name, t2.customer_type, t.country_id, t.gst_no, t5.city_name, t.created_date, t.modified_date, u2.person_name as modified_by, t.customer_id','tbl_customer_master t INNER JOIN customer_view t2 ON t.customer_type=t2.customer_type INNER JOIN tbl_city_master t5 ON t.city_id=t5.city_id LEFT JOIN tbl_user_master u2  ON t.modified_by=u2.user_id ')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Customer Name <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Customer Name\" /></th> 
                         <th> Customer Type <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Customer Type\" /></th> 
                         <th> District Name <br><input type=\"text\" data-index=\"4\" placeholder=\"Search District Name\" /></th> 
                         <th> City Name <br><input type=\"text\" data-index=\"5\" placeholder=\"Search City Name\" /></th> 
                         <th> Country Name <br><input type=\"text\" data-index=\"7\" placeholder=\"Search Country Name\" /></th> 
                         <th> Gst No <br><input type=\"text\" data-index=\"12\" placeholder=\"Search Gst No\" /></th> 
                         <th> Created Date <br><input type=\"text\" data-index=\"13\" placeholder=\"Search Created Date\" /></th> 
                         <th> Modified Date <br><input type=\"text\" data-index=\"15\" placeholder=\"Search Modified Date\" /></th> 
                         <th> Modified By <br><input type=\"text\" data-index=\"16\" placeholder=\"Search Modified By\" /></th> 
                         </tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>
          <td> 
                    <i class='fa fa-edit update' style='cursor: pointer;' onclick='enableEdit(this)'></i>
                    <i class='fa fa-save save d-none' style='cursor: pointer;' onclick='saveEdit(this)'></i>
                    <input type='hidden' class='customer_id' value='{$_rs["customer_id"]}' />

                    <form  method=\"post\" action=\"classes/cls_customer_master.php\" style=\"display:inline;\">
                        <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
                        <input type=\"hidden\" name=\"customer_id\" value=\"".$_rs["customer_id"]."\" />
                        <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
                    </form>
                </td>";
                       $fieldvalue=$_rs["customer_name"];
                            $_grid.= "
                            <td class='editable'> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["customer_type"];
                            $_grid.= "
                            <td class='editable'> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["district_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["city_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["country_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["gst_no"];
                            $_grid.= "
                            <td class='editable'> ".$fieldvalue." </td>"; 
                       
                             if(!empty($_rs["created_date"])) {
                             $fieldvalue=date("d/m/Y",strtotime($_rs["created_date"]));
                             $fieldvalue.="<small> ".date("h:i:s a",strtotime($_rs["created_date"]))."</small>";
                             }
                             
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       
                             if(!empty($_rs["modified_date"])) {
                             $fieldvalue=date("d/m/Y",strtotime($_rs["modified_date"]));
                             $fieldvalue.="<small> ".date("h:i:s a",strtotime($_rs["modified_date"]))."</small>";
                             }
                             
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["modified_by"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"16\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    function checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name) {
        global $_dbh;
        try {
            $sql="CAll hetasvi_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_default = $result->fetchColumn();
            return $is_default;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            return 0;
        }
        return 0;
    }
}
 class dal_customermaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_customer_id);
        $_pre=$_dbh->prepare("CALL customer_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_customer_name);
        $_pre->bindParam(2,$_mdl->_customer_type);
        $_pre->bindParam(3,$_mdl->_address);
        $_pre->bindParam(4,$_mdl->_district_name);
        $_pre->bindParam(5,$_mdl->_city_id);
        $_pre->bindParam(6,$_mdl->_state_id);
        $_pre->bindParam(7,$_mdl->_country_id);
        $_pre->bindParam(8,$_mdl->_pincode);
        $_pre->bindParam(9,$_mdl->_phone_no);
        $_pre->bindParam(10,$_mdl->_email_id);
        $_pre->bindParam(11,$_mdl->_webaddress);
        $_pre->bindParam(12,$_mdl->_gst_no);
        $_pre->bindParam(13,$_mdl->_created_date);
        $_pre->bindParam(14,$_mdl->_created_by);
        $_pre->bindParam(15,$_mdl->_modified_date);
        $_pre->bindParam(16,$_mdl->_modified_by);
        $_pre->bindParam(17,$_mdl->_transactionmode);
        $_pre->execute();
        
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_customer_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL customer_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["customer_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_customer_id=$_rs[0]["customer_id"];
        $_mdl->_customer_name=$_rs[0]["customer_name"];
        $_mdl->_customer_type=$_rs[0]["customer_type"];
        $_mdl->_address=$_rs[0]["address"];
        $_mdl->_district_name=$_rs[0]["district_name"];
        $_mdl->_city_id=$_rs[0]["city_id"];
        $_mdl->_state_id=$_rs[0]["state_id"];
        $_mdl->_country_id=$_rs[0]["country_id"];
        $_mdl->_pincode=$_rs[0]["pincode"];
        $_mdl->_phone_no=$_rs[0]["phone_no"];
        $_mdl->_email_id=$_rs[0]["email_id"];
        $_mdl->_webaddress=$_rs[0]["webaddress"];
        $_mdl->_gst_no=$_rs[0]["gst_no"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_customermaster();


    /*** FOR DETAIL ***/
    $_blldetail=new bll_customercontactdetail();
    /*** /FOR DETAIL ***/
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["type"]) && $_POST["type"]=="ajax") {
    $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";
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
    echo $_bll->checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name);
    exit;
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
 
            
            if(isset($_REQUEST["customer_id"]) && !empty($_REQUEST["customer_id"]))
                $field=trim($_REQUEST["customer_id"]);
            else {
                    $field=0;
           }
            $_bll->_mdl->_customer_id=$field;

            
            if(isset($_REQUEST["customer_name"]) && !empty($_REQUEST["customer_name"]))
                $field=trim($_REQUEST["customer_name"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_customer_name=$field;

            
            if(isset($_REQUEST["customer_type"]) && !empty($_REQUEST["customer_type"]))
                $field=trim($_REQUEST["customer_type"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_customer_type=$field;

            
            if(isset($_REQUEST["address"]) && !empty($_REQUEST["address"]))
                $field=trim($_REQUEST["address"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_address=$field;

            
            if(isset($_REQUEST["district_name"]) && !empty($_REQUEST["district_name"]))
                $field=trim($_REQUEST["district_name"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_district_name=$field;

            
            if(isset($_REQUEST["city_id"]) && !empty($_REQUEST["city_id"]))
                $field=trim($_REQUEST["city_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_city_id=$field;

            
            if(isset($_REQUEST["state_id"]) && !empty($_REQUEST["state_id"]))
                $field=trim($_REQUEST["state_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_state_id=$field;

            
            if(isset($_REQUEST["country_id"]) && !empty($_REQUEST["country_id"]))
                $field=trim($_REQUEST["country_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_country_id=$field;

            
            if(isset($_REQUEST["pincode"]) && !empty($_REQUEST["pincode"]))
                $field=trim($_REQUEST["pincode"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_pincode=$field;

            
            if(isset($_REQUEST["phone_no"]) && !empty($_REQUEST["phone_no"]))
                $field=trim($_REQUEST["phone_no"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_phone_no=$field;

            
            if(isset($_REQUEST["email_id"]) && !empty($_REQUEST["email_id"]))
                $field=trim($_REQUEST["email_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_email_id=$field;

            
            if(isset($_REQUEST["webaddress"]) && !empty($_REQUEST["webaddress"]))
                $field=trim($_REQUEST["webaddress"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_webaddress=$field;

            
            if(isset($_REQUEST["gst_no"]) && !empty($_REQUEST["gst_no"]))
                $field=trim($_REQUEST["gst_no"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_gst_no=$field;

            
            if(isset($_REQUEST["created_date"]) && !empty($_REQUEST["created_date"]))
                $field=trim($_REQUEST["created_date"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_created_date=$field;

            
            if(isset($_REQUEST["created_by"]) && !empty($_REQUEST["created_by"]))
                $field=trim($_REQUEST["created_by"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_created_by=$field;

            
            if(isset($_REQUEST["modified_date"]) && !empty($_REQUEST["modified_date"]))
                $field=trim($_REQUEST["modified_date"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_modified_date=$field;

            
            if(isset($_REQUEST["modified_by"]) && !empty($_REQUEST["modified_by"]))
                $field=trim($_REQUEST["modified_by"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_modified_by=$field;

            if(isset($_REQUEST["transactionmode"]))
                $tmode=$_REQUEST["transactionmode"];
            else
                $tmode="I";
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
?>
<script>

document.addEventListener('DOMContentLoaded', () => {
    window.enableEdit = function(icon) {
        const row = icon.closest('tr');
        const saveIcon = row.querySelector('.save');
        icon.classList.add('d-none');
        saveIcon.classList.remove('d-none');

        row.querySelectorAll('td.editable').forEach(cell => {
            const text = cell.innerText.trim();
            cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${text}">`;
        });
    };
window.saveEdit = function(icon) {
    const row = icon.closest('tr');
    const editIcon = row.querySelector('.update');
    icon.classList.add('d-none');
    editIcon.classList.remove('d-none');

    const customerId = row.querySelector('.customer_id').value;

    const updatedValues = Array.from(row.querySelectorAll('td.editable input')).map(input => input.value);

    const [newCustomerName, newCustomerType, newGstNo] = updatedValues; 

    const modifiedDate = new Date().toISOString().slice(0, 19).replace('T', ' '); 
    const modifiedBy = <?php echo json_encode($_SESSION["user_id"] ?? null); ?>;  

    fetch('classes/cls_customer_master.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `transactionmode=U&customer_id=${customerId}&customer_name=${encodeURIComponent(newCustomerName)}&customer_type=${encodeURIComponent(newCustomerType)}&gst_no=${encodeURIComponent(newGstNo)}&modified_date=${encodeURIComponent(modifiedDate)}&modified_by=${modifiedBy}&masterHidden=save`
    })
    .then(response => response.text())
    .then(result => {
 
        row.querySelector('td.editable').innerHTML = newCustomerName;
        row.querySelector('td.editable + td').innerHTML = newCustomerType;
        row.querySelector('td.editable + td + td').innerHTML = newGstNo;
    })
    .catch(() => {
        alert("Error saving data");
    });
};
  
});

</script>
