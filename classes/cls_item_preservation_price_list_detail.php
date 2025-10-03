<?php
include_once(__DIR__ . "/../config/connection.php"); // ADDED BY BHUMITA ON 29/07/2025
class mdl_itempreservationpricelistdetail 
{                        
public $item_preservation_price_list_detail_id;     
                  
    public $item_preservation_price_list_id;     
                  
    public $packing_unit_id;     
                  
    public $rent_per_qty_month;     
                  
    public $rent_per_qty_season;     
                  
    public $detailtransactionmode;
}

class bll_itempreservationpricelistdetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_itempreservationpricelistdetail(); 
        $this->_dal =new dal_itempreservationpricelistdetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
    }
    /* FUNCTION MODIFIED BY BHUMITA ON 29/07/2025 */
     public function pageSearch()
    {
        global $_dbh, $tbl_item_preservation_price_list_master, $tbl_item_preservation_price_list_detail, $tbl_packing_unit_master;
        $_grid="";
        $_grid="
        <div class=\"table-responsive\" style=\"width: 100%; display: block;\">
        <table  id=\"searchDetail\" class=\"table table-bordered table-striped text-center align-middle\">
        <thead id=\"tableHead\">
            <tr>";
         $_grid.="<th> Unit </th>";
        $_grid.="<th> Rent/Month/Qty. </th>";
        $_grid.="<th> Season Rent/Month/Qty. </th>";
        $_grid.="</tr>
        </thead>";
        $_grid.="<tbody id=\"tableBody\">";
        $i=0;
        $result=array();
        $company_query=str_replace("company_id","um.company_id",COMPANY_QUERY);
        $status_query=str_replace("`status`","um.status",STATUS_QUERY);
        $company_query2=str_replace("company_id","pm.company_id",COMPANY_QUERY);
        $master_id = isset($_POST['master_id']) ? intval($_POST['master_id']) : 0;

        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        $sql = "SELECT 
                um.packing_unit_id, 
                um.packing_unit_name, 
                COALESCE(d.rent_per_qty_month, '0.00') AS rent_per_qty_month, 
                COALESCE(d.rent_per_qty_season, '0.00') AS rent_per_qty_season,
                d.item_preservation_price_list_detail_id
            FROM {$tbl_packing_unit_master} um
            LEFT JOIN (
                SELECT 
                    pd.packing_unit_id, 
                    pd.rent_per_qty_month, 
                    pd.rent_per_qty_season,
                    pd.item_preservation_price_list_detail_id
                FROM {$tbl_item_preservation_price_list_detail} pd
                INNER JOIN {$tbl_item_preservation_price_list_master} pm
                    ON pd.item_preservation_price_list_id = pm.item_preservation_price_list_id
                WHERE pd.item_preservation_price_list_id = :master_id
                {$company_query2}
            ) d ON um.packing_unit_id = d.packing_unit_id
            WHERE 1=1".$status_query.$company_query; 
        $stmt = $_dbh->prepare($sql);
        $stmt->bindParam(':master_id', $master_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
       
        if (!empty($result)) {
            foreach ($result as $_rs) {
                $_grid .= "
                <tr data-id=\"{$_rs['packing_unit_id']}\" data-detail-id=\"" . ($_rs['item_preservation_price_list_detail_id'] ?? '') . "\">
                    <td style=\"background-color: #f0f0f0;\">{$_rs['packing_unit_name']}</td>
                    <td contenteditable=\"true\" class=\"editable rent-monthly\" 
                        data-field=\"rent_per_qty_month\" data-original=\"{$_rs['rent_per_qty_month']}\">
                        {$_rs['rent_per_qty_month']}
                    </td>
                    <td contenteditable=\"true\" class=\"editable rent-seasonal\" 
                        data-field=\"rent_per_qty_season\" data-original=\"{$_rs['rent_per_qty_season']}\">
                        {$_rs['rent_per_qty_season']}
                    </td>
                </tr>";
            }
        } else {
            $_grid .= "<tr id=\"norecords\" class=\"norecords\"><td colspan=\"3\">No records found</td></tr>";
        }
        
        $_grid.="</tbody>
        </table> 
        </div>";
        return $_grid; 
    }   
    /* \FUNCTION MODIFIED BY BHUMITA ON 29/07/2025 */
    /* FUNCTION ADDED BY BHUMITA ON 29/07/2025 */
    function fetchUnits() {
        echo $this->pageSearch();
        exit;
    }
    /* \FUNCTION ADDED BY BHUMITA ON 29/07/2025 */
}
 class dal_itempreservationpricelistdetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        $_dbh->exec("set @p0 = ".$_mdl->item_preservation_price_list_detail_id);
        $_pre=$_dbh->prepare("CALL item_preservation_price_list_detail_transaction (@p0,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->item_preservation_price_list_id,PDO::PARAM_INT);
        $_pre->bindParam(2,$_mdl->packing_unit_id,PDO::PARAM_INT);
        $_pre->bindParam(3,$_mdl->rent_per_qty_month,);
        $_pre->bindParam(4,$_mdl->rent_per_qty_season,);
        $_pre->bindParam(5,$_mdl->detailtransactionmode);
        $_pre->execute();
        
    }
}
/* ADDED BY BHUMITA ON 29/07/2025 */
if(isset($_REQUEST["actionDetail"]))
{
    $action=$_REQUEST["actionDetail"];
    $_blldetail=new bll_itempreservationpricelistdetail();
    $_blldetail->$action();
}
/* \ADDED BY BHUMITA ON 29/07/2025 */