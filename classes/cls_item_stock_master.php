<?php
include_once(__DIR__ . "/../config/connection.php");

class bll_inwardmaster
{
    public function pageSearch()
    {
        global $_dbh;

        // SQL remains unchanged
        $sql = "SELECT  
    id.lot_no, 
    itm.item_name AS item,          
    id.marko, 
    unit.packing_unit_name AS unit,         
    id.inward_qty,
    id.inward_wt,
    id.inward_wt AS stock_wt,
    id.inward_qty AS stock_qty,
    id.location, 
    vsd.value AS storage_duration,              
    id.rent_per_month AS rent, 
    vrt.Label AS per,                           
    im.inward_date, 
    TRIM(im.inward_no) AS inward_no,  
    c.customer AS customer,    
    b.customer AS broker
FROM tbl_inward_detail id
JOIN tbl_inward_master im ON id.inward_id = im.inward_id
LEFT JOIN tbl_customer_master c ON im.customer = c.customer_id
LEFT JOIN tbl_customer_master b ON im.broker = b.customer_id AND b.customer_type = 2
LEFT JOIN tbl_item_master itm ON id.item = itm.item_id
LEFT JOIN tbl_packing_unit_master unit ON id.packing_unit = unit.packing_unit_id
LEFT JOIN view_storage_duration vsd ON id.storage_duration = vsd.id
LEFT JOIN view_rent_type vrt ON id.rent_per = vrt.id";

        // REMOVE FILTER_FIELDS AND FILTER INPUTS
        // Table headers (manually define headers)
        echo "<table id=\"searchMaster\" class=\"ui celled table display\">";
        echo "<thead><tr>";
        $headers = [
            'Inward No.', 'Inward Date', 'Customer', 'Broker', 'Lot No.',
            'Item', 'Marko', 'Unit', 'Inward Qty.', 'Inward Wt. (Kg.)',
            'Stock Qty.', 'Stock Wt. (Kg.)', 'Location', 'Storage Duration',
            'Rent', 'Per'
        ];
        foreach ($headers as $header) {
            echo "<th>{$header}</th>";
        }
        echo "</tr></thead><tbody>";

        // Table body
        $_grid = "";
        $j = 0;
        try {
            $stmt = $_dbh->query($sql);
            foreach ($stmt as $_rs) {
                $j++;
                $_grid .= "<tr>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_no"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars(isset($_rs["inward_date"]) ? date("d/m/Y", strtotime($_rs["inward_date"])) : '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["customer"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["broker"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["lot_no"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["item"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["marko"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["unit"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_qty"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_wt"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["stock_qty"] ?? '0') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["stock_wt"] ?? '0') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["location"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["storage_duration"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["rent"] ?? '') . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["per"] ?? '') . "</td>";
                $_grid .= "</tr>\n";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan=\"" . count($headers) . "\">Error fetching data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            $_grid .= "</tbody></table>";
            echo $_grid;
            return;
        }

        if ($j == 0) {
            $_grid .= "<tr><td colspan=\"" . count($headers) . "\">No records available.</td></tr>";
        }

        $_grid .= "</tbody></table>";
        echo $_grid;
    }
}

// Instantiate for use
$_bll = new bll_inwardmaster();
?>