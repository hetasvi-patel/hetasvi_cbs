<?php
include_once(__DIR__ . "/../config/connection.php");
class mdl_rentinvoicedetail 
{                        
public $rent_invoice_detail_id;     
                  
    public $rent_invoice_id;     
                  
    public $description;     
                  
    public $qty;     
                  
    public $manual_unit;     
                  
    public $weight;     
                  
    public $rate_per_unit;     
                  
    public $manual_rent_per;     
                  
    public $amount;     
                  
    public $remark; 
    
    public $inward_id;
                  
    public $inward_no;     
                  
    public $inward_date;     
                  
    public $lot_no;     
                  
    public $item;     
                  
    public $marko;     
                  
    public $invoice_qty;     
                  
    public $unit;     
                  
    public $wt_per_kg;     
                  
    public $storage_duration;     
                  
    public $rent_per_storage_duration;     
                  
    public $rent_per;     
                  
    public $outward_date;     
                  
    public $charges_from;     
                  
    public $charges_to;     
                  
    public $actual_month;     
                  
    public $actual_day;     
                  
    public $invoice_for;     
                  
    public $invoice_day;     
                  
    public $invoice_amount;     
                  
    public $status;
    
    public $outward_detail_id; 
                  
    public $detailtransactionmode;
}

class bll_rentinvoicedetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_rentinvoicedetail(); 
        $this->_dal =new dal_rentinvoicedetail();
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
         $_grid.="<th> Description </th>";
                          $_grid.="<th> Qty </th>";
                          $_grid.="<th> Unit </th>";
                          $_grid.="<th> Rate/Unit </th>";
                          $_grid.="<th> Amount </th>";
                          $_grid.="<th> Remark </th>";
                         $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="rent_invoice_id";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms_search_detail('t.description, t.qty, t.manual_unit, t.rate_per_unit, t.amount, t.remark, t.rent_invoice_detail_id','tbl_rent_invoice_detail t',' and t.".$main_id_name."=".$main_id."')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="rent_invoice_detail_id";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            
                $_grid.="
                <td data-label=\"rent_invoice_id\" style=\"display:none\">&nbsp;</td>"; 
           
                    $value=$_rs['description'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"description\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['qty'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"qty\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['manual_unit'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"manual_unit\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                $_grid.="
                <td data-label=\"weight\" style=\"display:none\">&nbsp;</td>"; 
           
                    $value=$_rs['rate_per_unit'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"rate_per_unit\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                $_grid.="
                <td data-label=\"manual_rent_per\" style=\"display:none\">&nbsp;</td>"; 
           
                    $value=$_rs['amount'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"amount\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                    $value=$_rs['remark'];
                    $text_align="left";
                    $data_value="";
            
                $_grid.="
                <td data-value=\"".$data_value."\" data-label=\"remark\" align=\"".$text_align."\"> ".$value." </td>"; 
           
                $_grid.="
                <td data-label=\"inward_no\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"inward_date\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"lot_no\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"item\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"marko\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"invoice_qty\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"unit\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"wt_per_kg\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"storage_duration\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"rent_per_storage_duration\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"rent_per\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"outward_date\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"charges_from\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"charges_to\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"actual_month\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"actual_day\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"invoice_for\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"invoice_day\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"invoice_amount\" style=\"display:none\">&nbsp;</td>"; 
           
                $_grid.="
                <td data-label=\"status\" style=\"display:none\">&nbsp;</td>"; 
           $_grid.= "</tr>\n";
        $i++;
        }
        if($i==0) {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"29\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="</tr>";
        }
    } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"29\">No records available.</td>";
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
        return $_grid; 
    }
    /* ADDED BY BHUMITA ON 23/08/2025 */
    function format_duration($value, $unit) {
        return $value . ' ' . ($value == 1 ? $unit : $unit . 's');
    }
   // FIXED: outward_detail_id was missing in SELECT
        function generateInvoice() {
        global $_dbh;
        global $tbl_customer_master, $tbl_inward_master, $tbl_inward_detail, $tbl_outward_master, $tbl_outward_detail, $tbl_rent_invoice_master, $tbl_rent_invoice_detail, $tbl_item_master, $view_rent_type, $view_storage_duration, $tbl_packing_unit_master;
        try {
            $lot_no = $_POST['lot_no'] ?? null;
            $customer = $_POST['customer'] ?? null;
            $invoice_for = $_POST['invoice_for'] ?? null;
            $invoice_type = $_POST['invoice_type'] ?? null;
            $where = [];
            $params = [];

            if ($customer) { $where[] = "im.customer = ?"; $params[] = $customer; }
            if ($lot_no) {
                if (is_array($lot_no) && !empty($lot_no)) {
                    $placeholders = implode(',', array_fill(0, count($lot_no), '?'));
                    $where[] = "i.lot_no IN ($placeholders)";
                    $params = array_merge($params, $lot_no);
                } else {
                    $where[] = "i.lot_no = ?";
                    $params[] = $lot_no;
                }
            }
            if ($invoice_for == 4) { $where[] = "i.storage_duration = ?"; $params[] = 9; }
            if ($invoice_type) {
                if ($invoice_type == 2) { $where[] = "i.gst_type = ?"; $params[] = 1; }
                elseif ($invoice_type == 3) { $where[] = "i.gst_type = ?"; $params[] = 2; }
                elseif ($invoice_type == 1) { $where[] = "i.gst_type = ?"; $params[] = 3; }
            }
            $filter = (!empty($where) ? ' AND ' . implode(' AND ', $where) : '');

            $stock_calc = "(i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0))";
            $stock_filter = "";
            if ($invoice_for == 2) { $stock_filter = "AND $stock_calc > 0 AND $stock_calc < i.inward_qty"; }
            else if ($invoice_for == 3) { $stock_filter = "AND $stock_calc = 0"; }

            // OUTWARD rows — EXCLUDE ONLY already-invoiced outward_detail_id
           $sql_outward = "
                SELECT
                    i.inward_id AS inward_id,
                    im.inward_no AS in_no,
                    DATE_FORMAT(im.inward_date, '%d/%m/%Y') AS in_date,
                    im.inward_date AS inward_date_db,
                    i.lot_no,
                    itm.item_id AS item_id,
                    itm.item_name AS item,
                    um.packing_unit_id AS unit_id,
                    um.packing_unit_name AS unit_name,
                    i.marko,
                    o.out_qty AS qty,
                    o.out_qty AS out_qty,
                    o.out_qty AS invoice_qty,
                    (o.out_qty * i.avg_wt_per_bag) AS weight,
                    i.avg_wt_per_bag,
                    vsd.id AS storage_duration_id,
                    vsd.value AS storage_duration_name,
                    i.rent_per_storage_duration,
                    i.rent_per_month,
                    vrp.id AS rent_per_id,
                    vrp.value AS rent_per,
                    DATE_FORMAT(om.outward_date, '%d/%m/%Y') AS out_date,
                    om.outward_date AS outward_date_db,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN DATE_FORMAT(i.seasonal_start_date, '%d/%m/%Y')
                      ELSE DATE_FORMAT(im.inward_date, '%d/%m/%Y')
                    END AS charges_from,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN i.seasonal_start_date
                      ELSE im.inward_date
                    END AS charges_from_db,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN DATE_FORMAT(i.seasonal_end_date, '%d/%m/%Y')
                      ELSE DATE_FORMAT(calculate_charges_to(im.inward_date, om.outward_date, vsd.id), '%d/%m/%Y')
                    END AS charges_to,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN i.seasonal_end_date
                      ELSE calculate_charges_to(im.inward_date, om.outward_date, vsd.id)
                    END AS charges_to_db,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN
                        JSON_EXTRACT(seasonal_calculate_actual_duration(im.inward_date, om.outward_date, i.seasonal_start_date, i.seasonal_end_date), '$.season_days')
                      ELSE
                        JSON_EXTRACT(calculate_actual_duration(im.inward_date, om.outward_date, vsd.id), '$.actual_months')
                    END AS act_month,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN
                        JSON_EXTRACT(seasonal_calculate_actual_duration(im.inward_date, om.outward_date, i.seasonal_start_date, i.seasonal_end_date), '$.extra_days')
                      ELSE
                        JSON_EXTRACT(calculate_actual_duration(im.inward_date, om.outward_date, vsd.id), '$.actual_days')
                    END AS act_day,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN
                        JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, om.outward_date, i.seasonal_start_date, i.seasonal_end_date), '$.invoice_days')
                      ELSE
                        JSON_EXTRACT(calculate_invoice_duration(im.inward_date, om.outward_date, vsd.id), '$.invoice_days')
                    END AS invoice_for,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN
                        JSON_UNQUOTE(JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, om.outward_date, i.seasonal_start_date, i.seasonal_end_date), '$.invoice_for_text'))
                      ELSE
                        JSON_UNQUOTE(JSON_EXTRACT(calculate_invoice_duration(im.inward_date, om.outward_date, vsd.id), '$.invoice_for_text'))
                    END AS invoice_for_text,
                    CASE
                      WHEN vsd.value = 'seasonal' THEN
                        JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, om.outward_date, i.seasonal_start_date, i.seasonal_end_date), '$.invoice_days')
                      ELSE
                        JSON_EXTRACT(calculate_invoice_duration(im.inward_date, om.outward_date, vsd.id), '$.invoice_days')
                    END AS invoice_days,
                    NULL AS amount,
                    'Outward' AS status,
                    o.outward_detail_id AS outward_detail_id,
                    itm.item_gst AS gst_status
                FROM $tbl_inward_detail i
                JOIN $tbl_inward_master im ON i.inward_id = im.inward_id
                LEFT JOIN $tbl_item_master itm ON i.item = itm.item_id
                LEFT JOIN $view_rent_type vrp ON i.rent_per = vrp.id
                LEFT JOIN $view_storage_duration vsd ON i.storage_duration = vsd.id
                LEFT JOIN $tbl_packing_unit_master um ON i.packing_unit = um.packing_unit_id
                JOIN $tbl_outward_detail o ON o.inward_detail_id = i.inward_detail_id
                JOIN $tbl_outward_master om ON o.outward_id = om.outward_id
                WHERE o.out_qty > 0
                $filter
                $stock_filter
                AND NOT EXISTS (
                    SELECT 1
                    FROM $tbl_rent_invoice_detail rid
                    WHERE rid.outward_detail_id = o.outward_detail_id
                )
            ";

            $results = [];
            if ($invoice_for == 2 || $invoice_for == 3) {
                $stmt = $_dbh->prepare($sql_outward);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $sql_stock = "
                    SELECT
                        i.inward_id AS inward_id,
                        im.inward_no AS in_no,
                        DATE_FORMAT(im.inward_date, '%d/%m/%Y') AS in_date,
                        im.inward_date AS inward_date_db,
                        i.lot_no,
                        itm.item_id AS item_id,
                        itm.item_name AS item,
                        um.packing_unit_id AS unit_id,
                        um.packing_unit_name AS unit_name,
                        i.marko,
                        (i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0)) AS qty,
                        NULL AS out_qty,
                        (i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0)) AS invoice_qty,
                        ((i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0)) * i.avg_wt_per_bag) AS weight,
                        i.avg_wt_per_bag,
                        vsd.id AS storage_duration_id,
                        vsd.value AS storage_duration_name,
                        i.rent_per_storage_duration,
                        i.rent_per_month,
                        vrp.id AS rent_per_id,
                        vrp.value AS rent_per,
                        NULL AS out_date,
                        NULL AS outward_date_db,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN DATE_FORMAT(i.seasonal_start_date, '%d/%m/%Y')
                          ELSE DATE_FORMAT(im.inward_date, '%d/%m/%Y')
                        END AS charges_from,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN i.seasonal_start_date
                          ELSE im.inward_date
                        END AS charges_from_db,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN DATE_FORMAT(i.seasonal_end_date, '%d/%m/%Y')
                          ELSE DATE_FORMAT(calculate_charges_to(im.inward_date, CURDATE(), vsd.id), '%d/%m/%Y')
                        END AS charges_to,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN i.seasonal_end_date
                          ELSE calculate_charges_to(im.inward_date, CURDATE(), vsd.id)
                        END AS charges_to_db,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN
                            JSON_EXTRACT(seasonal_calculate_actual_duration(im.inward_date, CURDATE(), i.seasonal_start_date, i.seasonal_end_date), '$.seasonal_days')
                          ELSE
                            JSON_EXTRACT(calculate_actual_duration(im.inward_date, CURDATE(), vsd.id), '$.actual_months')
                        END AS act_month,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN
                            JSON_EXTRACT(seasonal_calculate_actual_duration(im.inward_date, CURDATE(), i.seasonal_start_date, i.seasonal_end_date), '$.extra_days')
                          ELSE
                            JSON_EXTRACT(calculate_actual_duration(im.inward_date, CURDATE(), vsd.id), '$.actual_days')
                        END AS act_day,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN
                            JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, CURDATE(), i.seasonal_start_date, i.seasonal_end_date), '$.invoice_days')
                          ELSE
                            JSON_EXTRACT(calculate_invoice_duration(im.inward_date, CURDATE(), vsd.id), '$.invoice_days')
                        END AS invoice_for,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN
                            JSON_UNQUOTE(JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, CURDATE(), i.seasonal_start_date, i.seasonal_end_date), '$.invoice_for_text'))
                          ELSE
                            JSON_UNQUOTE(JSON_EXTRACT(calculate_invoice_duration(im.inward_date, CURDATE(), vsd.id), '$.invoice_for_text'))
                        END AS invoice_for_text,
                        CASE
                          WHEN vsd.value = 'seasonal' THEN
                            JSON_EXTRACT(seasonal_calculate_invoice_duration(im.inward_date, CURDATE(), i.seasonal_start_date, i.seasonal_end_date), '$.invoice_days')
                          ELSE
                            JSON_EXTRACT(calculate_invoice_duration(im.inward_date, CURDATE(), vsd.id), '$.invoice_days')
                        END AS invoice_days,
                        NULL AS amount,
                        'Stock' AS status,
                        NULL AS outward_detail_id,
                        itm.item_gst AS gst_status
                    FROM $tbl_inward_detail i
                    JOIN $tbl_inward_master im ON i.inward_id = im.inward_id
                    LEFT JOIN $tbl_item_master itm ON i.item = itm.item_id
                    LEFT JOIN $view_rent_type vrp ON i.rent_per = vrp.id
                    LEFT JOIN $view_storage_duration vsd ON i.storage_duration = vsd.id
                    LEFT JOIN $tbl_packing_unit_master um ON i.packing_unit = um.packing_unit_id
                    WHERE (i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0)) > 0
                    $filter
                    AND NOT EXISTS (
                        SELECT 1
                        FROM $tbl_rent_invoice_detail rid
                        WHERE rid.inward_id = i.inward_id
                          AND rid.outward_detail_id IS NULL
                    )
                ";

                $final_sql = "($sql_outward) UNION ALL ($sql_stock)";
                $all_params = array_merge($params, $params);

                $stmt = $_dbh->prepare($final_sql);
                $stmt->execute($all_params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        } catch (Exception $e) {
            error_log("Error in generate_details: " . $e->getMessage() . ", Inputs: " . json_encode($_POST));
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
    /* \ADDED BY BHUMITA ON 23/08/2025 */
}
 class dal_rentinvoicedetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
       
        /* ADDED BY BHUMITA ON 25/08/2025 */
        foreach($_mdl as $name=>$value) {
            if($name=="rent_invoice_detail_id" && $_mdl->detailtransactionmode=="I")
            {
                $_mdl->rent_invoice_detail_id=0;
            } else if($value=="") {
                 $_mdl->$name=null;
            }
        }
        /* \ADDED BY BHUMITA ON 25/08/2025 */

        try {
            $_dbh->exec("set @p0 = ".$_mdl->rent_invoice_detail_id);
            $_pre=$_dbh->prepare("CALL rent_invoice_detail_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            $_pre->bindParam(1,$_mdl->rent_invoice_id);
            $_pre->bindParam(2,$_mdl->description);
            $_pre->bindParam(3,$_mdl->qty,PDO::PARAM_INT);
            $_pre->bindParam(4,$_mdl->manual_unit);
            $_pre->bindParam(5,$_mdl->weight);
            $_pre->bindParam(6,$_mdl->rate_per_unit);
            $_pre->bindParam(7,$_mdl->manual_rent_per,PDO::PARAM_INT);
            $_pre->bindParam(8,$_mdl->amount);
            $_pre->bindParam(9,$_mdl->remark);
            $_pre->bindParam(10,$_mdl->inward_id,PDO::PARAM_INT);
            $_pre->bindParam(11,$_mdl->inward_no);
            $_pre->bindParam(12,$_mdl->inward_date);
            $_pre->bindParam(13,$_mdl->lot_no);
            $_pre->bindParam(14,$_mdl->item,PDO::PARAM_INT);
            $_pre->bindParam(15,$_mdl->marko);
            $_pre->bindParam(16,$_mdl->invoice_qty,PDO::PARAM_INT);
            $_pre->bindParam(17,$_mdl->unit,PDO::PARAM_INT);
            $_pre->bindParam(18,$_mdl->wt_per_kg);
            $_pre->bindParam(19,$_mdl->storage_duration,PDO::PARAM_INT);
            $_pre->bindParam(20,$_mdl->rent_per_storage_duration);
            $_pre->bindParam(21,$_mdl->rent_per,PDO::PARAM_INT);
            $_pre->bindParam(22,$_mdl->outward_date,);
            $_pre->bindParam(23,$_mdl->charges_from,);
            $_pre->bindParam(24,$_mdl->charges_to,);
            $_pre->bindParam(25,$_mdl->actual_month,PDO::PARAM_INT);
            $_pre->bindParam(26,$_mdl->actual_day,PDO::PARAM_INT);
            $_pre->bindParam(27,$_mdl->invoice_for,PDO::PARAM_INT);
            $_pre->bindParam(28,$_mdl->invoice_day,PDO::PARAM_INT);
            $_pre->bindParam(29,$_mdl->invoice_amount);
            $_pre->bindParam(30,$_mdl->status);
            $_pre->bindParam(31,$_mdl->outward_detail_id,PDO::PARAM_INT);
            $_pre->bindParam(32,$_mdl->detailtransactionmode);
            $_pre->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
        
    }
}
/* ADDED BY BHUMTIA ON 23/08/2025 */
$_blldetail=new bll_rentinvoicedetail();
if(isset($_REQUEST["action_detail"]))
{
    $action_detail=$_REQUEST["action_detail"];
    $_blldetail->$action_detail();
}
/* \ADDED BY BHUMTIA ON 23/08/2025 */