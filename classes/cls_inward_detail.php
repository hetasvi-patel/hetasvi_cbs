<?php
class mdl_inwarddetail 
{                        
    public $inward_detail_id;     
    public $inward_id;     
    public $lot_no;     
    public $storage_duration;     
    public $rent_per;     
    public $item;     
    public $rent_per_month;     
    public $gst_type;     
    public $rent_per_storage_duration;     
    public $marko;     
    public $remark;     
    public $packing_unit;     
    public $inward_qty;     
    public $inward_wt;     
    public $avg_wt_per_bag;     
    public $location;     
    public $moisture;     
    public $seasonal_start_date;     
    public $seasonal_end_date;     
    public $unloading_charge;     
    public $detailtransactionmode;
    /*Added BY Hetasvi */
    public $chamber; 
    public $floor;
    public $rack;
    public $outwarded; 
    /*Added BY Hetasvi */
}

class bll_inwarddetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_inwarddetail(); 
        $this->_dal =new dal_inwarddetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
    }

    /* FUNCTION MODIFIED BY BHUMITA ON 13/08/2025 */
    public function pageSearch()
    {
        global $_dbh;
        $_grid="";
        $_grid="
        <table  id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
        <thead id=\"tableHead\">
            <tr>
            <th>Action</th>";
        $_grid .= "<th>Lot No</th>";
        $_grid .= "<th>Item</th>";
        $_grid .= "<th>Tax Type</th>";
        $_grid .= "<th>Marko</th>";
        $_grid .= "<th>Packing Unit</th>";
        $_grid .= "<th>Inward Qty.</th>";
        $_grid .= "<th>Inward Weight(Kg)</th>";
        $_grid .= "<th>Avg. Wt./Packing Unit (Kg) </th>";
        $_grid .= "<th>Location</th>";
        $_grid .= "<th>Storage Duration</th>";
        $_grid .= "<th>Rent Per</th>";
        $_grid .= "<th>Rent</th>";
        $_grid .= "<th>Unloading Charge</th>";
        $_grid .= "<th>Remark </th>";
        $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="inward_id";
        if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
        if($main_id) {
            $sql="CALL csms_search_detail(
        't.*, t3.item_name, t.item, t6.packing_unit_name, t.packing_unit, t7.value as gst_type_value, t8.value as storage_duration_value,
        (SELECT SUM(od.out_qty) FROM tbl_outward_detail od WHERE od.inward_detail_id = t.inward_detail_id) AS total_out_qty',
        'tbl_inward_detail t 
         INNER JOIN tbl_item_master t3 ON t.item = t3.item_id 
         INNER JOIN tbl_packing_unit_master t6 ON t.packing_unit = t6.packing_unit_id
         LEFT JOIN view_item_gst_type t7 ON t.gst_type = t7.id
         LEFT JOIN view_storage_duration t8 ON t.storage_duration = t8.id',
        ' and t." . $main_id_name . " = " . $main_id . "')";//MODIFIED QUERY MANSI
            $stmt = $_dbh->query($sql, PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="inward_detail_id";
                $detail_id=$_rs[$detail_id_label];
                /*DRASHTI MODIFIED BY MANSI */
                $total_out_qty = floatval($_rs["total_out_qty"]) ?: 0;
                $inward_qty = floatval($_rs['inward_qty']) ?: 0;
                $is_outwarded = ($total_out_qty > 0) ? "1" : "0";
                $is_fully_outwarded = ($inward_qty > 0 && $total_out_qty >= $inward_qty) ? "1" : "0";/* ADD BY HETANSHREE */
                /*Added by Hetasvi */
                $original_inward_qty = floatval($_rs['inward_qty']) ?: 0;
                $outward_qty_used = floatval($_rs["total_out_qty"]) ?: 0;

                /* ADDED BY HETANSHREE DISABLE FEILDS INWARD_QTY,INWARD_WT AND REMARK */
                $sql_invoice = "SELECT SUM(invoice_qty) FROM tbl_rent_invoice_detail WHERE inward_id = :inward_id AND item = :item";
                $stmt_invoice = $_dbh->prepare($sql_invoice);
                $stmt_invoice->execute([
                    ':inward_id' => $_rs['inward_id'],
                    ':item' => $_rs['item']
                ]);
                $total_invoice_qty = $stmt_invoice->fetchColumn() ?: 0;
                $stmt_invoice->closeCursor();
                $is_any_invoiced = ($total_invoice_qty > 0) ? "1" : "0";
                $is_fully_invoiced = ($total_invoice_qty > 0 && $total_invoice_qty >= $total_out_qty) ? "1" : "0";
                
                 /*Added by Hetasvi */
                $_grid .= "<tr data-label=\"$detail_id_label\" data-id=\"$detail_id\" data-outwarded=\"$is_outwarded\" data-original-inward-qty=\"$original_inward_qty\" data-outward-used=\"$outward_qty_used\" data-fully-outwarded=\"$is_fully_outwarded\" data-fully-invoiced=\"$is_fully_invoiced\" data-any-invoiced=\"$is_any_invoiced\" id=\"row$i\">";/*DRASHTI MODIFIED BY Hetanshree,Hetasvi */
                $_grid .= "
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"$detail_id\" data-index=\"$i\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"$detail_id\" data-index=\"$i\""
                    . ($is_outwarded == "1" ? " disabled" : "") .
                    ">Delete</button>
                </td>";
                
                $value=$_rs['lot_no'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"lot_no\" align=\"".$text_align."\"> ".$value." </td>"; 

                $value=$_rs['item'];
                $text_align="left";
                $data_value=$value;
                $value=$_rs['item_name'];
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"item\" align=\"".$text_align."\"> ".$value." </td>"; 
                
                $value=$_rs['gst_type'];
                $text_align="left";
                $data_value=$value;
                $value=$_rs['gst_type_value'];
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"gst_type\" align=\"".$text_align."\"> ".$value." </td>"; 

                $value=$_rs['marko'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"marko\" align=\"".$text_align."\"> ".$value." </td>"; 

                $value=$_rs['packing_unit'];
                $text_align="left";
                $data_value=$value;
                $value=$_rs['packing_unit_name'];           
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"packing_unit\" align=\"".$text_align."\"> ".$value." </td>";

                $value=$_rs['inward_qty'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"inward_qty\" align=\"".$text_align."\"> ".$value." </td>"; 
                
                $value=$_rs['inward_wt'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"inward_wt\" align=\"".$text_align."\"> ".$value." </td>";

                $value=$_rs['avg_wt_per_bag'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"avg_wt_per_bag\" align=\"".$text_align."\"> ".$value." </td>";

                $value=$_rs['location'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"location\" align=\"".$text_align."\"> ".$value." </td>"; 

                $value=$_rs['storage_duration'];
                $text_align="left";
                $data_value=$value;
                $value=$_rs['storage_duration_value'];
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"storage_duration\" align=\"".$text_align."\"> ".$value." </td>";
           
                $value=$_rs['rent_per'];
                $text_align="left";
                $data_value=$value;
                if ($value == '1') {
                    $value = 'Quantity';
                } elseif ($value == '2') {
                    $value = 'Kg';
                }
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"rent_per\" align=\"".$text_align."\"> ".$value." </td>"; 

                $value=$_rs['rent_per_storage_duration'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"rent_per_storage_duration\" align=\"".$text_align."\">".$value."</td>"; 
           
                $value=$_rs['unloading_charge'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"unloading_charge\" align=\"".$text_align."\"> ".$value." </td>";
           
                $value=$_rs['remark'];
                $text_align="left";
                $data_value="";
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"remark\" align=\"".$text_align."\"> ".$value." </td>"; 
                 

                // Hidden fields
                $_grid .= "<td data-value=\"\" data-label=\"inward_id\" style=\"display:none\">" . ($_rs['inward_id'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"inward_detail_id\" style=\"display:none\">" . ($_rs['inward_detail_id'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"seasonal_start_date\" style=\"display:none\">" . ($_rs['seasonal_start_date'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"seasonal_end_date\" style=\"display:none\">" . ($_rs['seasonal_end_date'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"rent_per_month\" style=\"display:none\">" . ($_rs['rent_per_month'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"moisture\" style=\"display: none;\">" . ($_rs['moisture'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"chamber\" style=\"display: none;\">" . ($_rs['chamber'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"floor\" style=\"display: none;\">" . ($_rs['floor'] ?? '') . "</td>";
                $_grid .= "<td data-value=\"\" data-label=\"rack\" style=\"display: none;\">" . ($_rs['rack'] ?? '') . "</td>";
                $_grid.= "</tr>\n";
                $i++;
            }
            if($i==0) {
                $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
                $_grid.="<td colspan=\"15\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                $_grid.="</tr>";
            }
        } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"15\">No records available.</td>";
            $_grid.="</tr>";
        }
        $_grid.="</tbody>
        </table> ";
        return $_grid; 
    }
    /* \FUNCTION MODIFIED BY BHUMITA ON 13/08/2025 */   
}
class dal_inwarddetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        try{
            $_dbh->exec("set @p0 = ".((!empty($_mdl->inward_detail_id)) ? $_mdl->inward_detail_id : 0));   // Modified By Hetasvi 
            $_pre=$_dbh->prepare("CALL inward_detail_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            $_pre->bindParam(1,$_mdl->inward_id,);
            $_pre->bindParam(2,$_mdl->lot_no,);
            $_pre->bindParam(3,$_mdl->storage_duration,PDO::PARAM_INT);
            $_pre->bindParam(4,$_mdl->rent_per,PDO::PARAM_INT);
            $_pre->bindParam(5,$_mdl->item,PDO::PARAM_INT);
            $_pre->bindParam(6,$_mdl->rent_per_month,);
            $_pre->bindParam(7,$_mdl->gst_type,PDO::PARAM_INT);
            $_pre->bindParam(8,$_mdl->rent_per_storage_duration,);
            $_pre->bindParam(9,$_mdl->marko,);
            $_pre->bindParam(10,$_mdl->remark,);
            $_pre->bindParam(11,$_mdl->packing_unit,PDO::PARAM_INT);
            $_pre->bindParam(12,$_mdl->inward_qty,PDO::PARAM_INT);
            $_pre->bindParam(13,$_mdl->inward_wt,);
            $_pre->bindParam(14,$_mdl->avg_wt_per_bag,);
            $_pre->bindParam(15,$_mdl->location,);
            $_pre->bindParam(16,$_mdl->moisture,);
            $_pre->bindParam(17,$_mdl->seasonal_start_date,);
            $_pre->bindParam(18,$_mdl->seasonal_end_date,);
            $_pre->bindParam(19,$_mdl->unloading_charge,);
            $_pre->bindParam(20,$_mdl->chamber,);
            $_pre->bindParam(21,$_mdl->floor,);
            $_pre->bindParam(22,$_mdl->rack,);
            $_pre->bindParam(23,$_mdl->detailtransactionmode);
            $_pre->execute();
        }  catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
        
    }
}