<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_inwardmaster
{
    public function pageSearch()
    {
        global $_dbh;
        global $database_name;

        $currentDate = date('d/m/Y');
        $tillDate = isset($_REQUEST['till_date']) ? $_REQUEST['till_date'] : $currentDate;
        $selectedCustomer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';

        try {
            $tillDateTime = DateTime::createFromFormat('d/m/Y', $tillDate);
            if ($tillDateTime) {
                $tillDateSql = $tillDateTime->format('Y-m-d 23:59:59');
            } else {
                $tillDateSql = date('Y-m-d 23:59:59');
            }
        } catch (Exception $e) {
            error_log("Date parsing error: " . $e->getMessage());
            $tillDateSql = date('Y-m-d 23:59:59');
        }

        $customerOptions = '';
        try {
            $customerStmt = $_dbh->query(
                "SELECT DISTINCT c.customer_id, c.customer
                 FROM tbl_rent_invoice_master m 
                 INNER JOIN tbl_customer_master c ON m.customer = c.customer_id 
                 WHERE c.customer IS NOT NULL AND c.customer <> '' 
                 ORDER BY c.customer ASC"
            );
            while($row = $customerStmt->fetch(PDO::FETCH_ASSOC)) {
                $custId = htmlspecialchars($row['customer_id']);
                $custName = htmlspecialchars($row['customer']);
                $selected = ($selectedCustomer === $custId) ? "selected" : "";
                $customerOptions .= "<option value=\"{$custId}\" {$selected}>{$custName}</option>";
            }
        } catch (PDOException $e) {
            error_log("Customer fetch error: " . $e->getMessage());
        }

        $from_clause = "
            tbl_rent_invoice_detail d
            LEFT JOIN tbl_rent_invoice_master m ON d.rent_invoice_id = m.rent_invoice_id
            LEFT JOIN tbl_customer_master c ON m.customer = c.customer_id
            LEFT JOIN tbl_item_master i ON d.item = i.item_id
        ";

        $where_condition = "(d.inward_date <= :till_date OR d.inward_date IS NULL)";
        if (!empty($selectedCustomer)) {
            $where_condition .= " AND m.customer = :customer";
        }

        $select_columns = "
      d.inward_no,
    c.customer AS customer,
    d.lot_no,
    i.item_name AS item,
    d.manual_unit,
    d.unit,
    d.qty,
    d.weight,
    d.invoice_qty,
    d.unit,
    d.invoice_for,
    d.invoice_day,
    d.wt_per_kg,
    d.invoice_amount,
    d.amount,
    m.invoice_date AS master_invoice_date,
    d.marko,
    d.rent_per_storage_duration,
    d.inward_date,
    d.actual_month,
    d.actual_day,
    d.status
        ";

        $select_columns = trim(preg_replace('/\s+/', ' ', $select_columns));
        $from_clause = trim(preg_replace('/\s+/', ' ', $from_clause));
        $where_condition = trim(preg_replace('/\s+/', ' ', $where_condition));

        $sql = "SELECT $select_columns FROM $from_clause WHERE $where_condition";
        error_log("SQL Query: $sql");

        $params = [
            ':till_date' => $tillDateSql
        ];
        if (!empty($selectedCustomer)) {
            $params[':customer'] = $selectedCustomer;
        }

        $filter_fields = [
            'Item' => '',  
            'Marko' => '',    
            'In. No.' => '',  
            'Lot No.' => ''   
        ];

        
        echo "<form method='post' id='mainFilterForm'>
                <div class=\"row gx-2 gy-1 align-items-center\" style=\"margin-bottom:3px;\">
                    <div class=\"row gx-2 gy-1 align-items-center\" id=\"till-date-filter\">";

        //Till Date
        echo '<div class="col-auto">';
        echo '<input type="text" class="form-control date-filter" placeholder="Till Date" id="till_date" name="till_date" value="' . htmlspecialchars($tillDate) . '" />';
        echo '</div>';

        //Customer dropdown
        echo '<div class="col-auto">';
        echo '<select class="form-select" id="customer_filter" name="customer" style="width:179px; max-width:100%;">';
        echo '<option value="">All Customers</option>';
        echo $customerOptions;
        echo '</select>';
        echo '</div>';

        //Total Amount 
        echo '<div class="col-auto d-flex align-items-center">';
        echo '<label for="total_amount" style="font-weight:600; font-size:14px; margin-right:5px;">Total Amount:</label>';
        echo '<input type="text" class="form-control" id="total_amount" name="total_amount" readonly style="width:89px; max-width:100%;" />';
        echo '</div>';


        for ($i = 0; $i < 5; $i++) {
            echo '<div class="col">&nbsp;</div>';
        }
        echo "</div></div>";
        // --- Other search filters ---
        echo '<div class="row gx-2 gy-1 align-items-center" id="search-filters">';
        $column_map = [
            'Item' => 3,      
            'Marko' => 11,    
            'In. No.' => 0,
            'Lot No.' => 2,   
        ];
        foreach ($filter_fields as $label => $class) {
            $colIndex = $column_map[$label];
            echo "<div class=\"col-auto\">
                    <input type=\"text\" class=\"form-control {$class}\" placeholder=\"Search {$label}\" data-index=\"{$colIndex}\" />
                  </div>";
        }
        echo "</div></form>";

        // --- TABLE ---
        echo "<table id=\"searchMaster\" class=\"ui celled table display\">
        <thead><tr>";
        $full_headers = [
            'Inward No','Customer','Lot No', 'Item', 'Qty', 'Unit', 'Weight', 'Amount', 'Valuation Date', 'Valuation Month',
            'Valuation Day', 'Marko', 'Rent', 'Inward Date', 'Actual Month', 'Actual Day', 'Status'
        ];
        foreach ($full_headers as $i => $header) {
            // Hide Customer column 
            if ($i == 1) {
                echo '<th style="display:none;">' . $header . '</th>';
            } else {
                echo '<th>' . $header . '</th>';
            }
        }
        echo "</tr></thead><tbody>";

        $_grid = "";
        $j = 0;
        try {
            $stmt = $_dbh->prepare($sql);
            $stmt->execute($params);
            while ($_rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $j++;
                $_grid .= "<tr>";
                $valuation_month = '';
                $valuation_day = '';
                if (!empty($_rs["master_invoice_date"]) && !empty($_rs["rent_per_storage_duration"])) {
                    if (!empty($_rs["inward_date"])) {
                        $start = new DateTime($_rs["inward_date"]);
                        $end = new DateTime($_rs["master_invoice_date"]);
                        $interval = $start->diff($end);
                        $total_days = $interval->days + 1; 
                        $valuation_month = floor($total_days / $_rs["rent_per_storage_duration"]);
                        $valuation_day = $total_days % $_rs["rent_per_storage_duration"];
                    }
                }
                if (empty($_rs["inward_no"])) {
                    $_grid .= "<td></td>"; 
                    $_grid .= '<td style="display:none;">' . htmlspecialchars($_rs["customer"] ?? '') . '</td>'; // Customer hidden
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["qty"] ?? '') . "</td>";
                    $_grid .= "<td>" . htmlspecialchars($_rs["unit"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["weight"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["amount"] ?? '') . "</td>";
                    $_grid .= "<td>" . (!empty($_rs["master_invoice_date"]) ? date("d/m/Y", strtotime($_rs["master_invoice_date"])) : "") . "</td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td></td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["status"] ?? '') . "</td>"; 
                } else {
                    $_grid .= "<td>" . htmlspecialchars($_rs["inward_no"] ?? '') ."</td>";
                    $_grid .= '<td style="display:none;">' . htmlspecialchars($_rs["customer"] ?? '') . '</td>'; // Customer hidden
                    $_grid .= "<td>" . htmlspecialchars($_rs["lot_no"] ?? '') . "</td>";
                    $_grid .= "<td>" . htmlspecialchars($_rs["item"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["invoice_qty"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["manual_unit"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["wt_per_kg"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["invoice_amount"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . (!empty($_rs["master_invoice_date"]) ? date("d/m/Y", strtotime($_rs["master_invoice_date"])) : "") . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["invoice_for"] ?? '') . "</td>";
                    $_grid .= "<td>" . htmlspecialchars($_rs["invoice_day"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["marko"] ?? '') . "</td>";
                    $_grid .= "<td>" . htmlspecialchars($_rs["rent_per_storage_duration"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . (!empty($_rs["inward_date"]) ? date("d/m/Y", strtotime($_rs["inward_date"])) : "") . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["actual_month"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["actual_day"] ?? '') . "</td>"; 
                    $_grid .= "<td>" . htmlspecialchars($_rs["status"] ?? '') . "</td>";
                }
                $_grid .= "</tr>\n";
            }
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            $_grid .= "<tr>";
            foreach ($full_headers as $i => $header) {
                if ($i == 1) {
                    $_grid .= '<td style="display:none;"></td>';
                } else {
                    $_grid .= "<td></td>";
                }
            }
            $_grid .= "</tr>";
            $_grid .= "</tbody></table>";
            echo $_grid;
            return;
        }
        if ($j == 0) {
            $_grid .= "<tr>";
            foreach ($full_headers as $i => $header) {
                if ($i == 1) {
                    $_grid .= '<td style="display:none;"></td>';
                } else {
                    $_grid .= "<td></td>";
                }
            }
            $_grid .= "</tr>";
        }
        $_grid .= "</tbody></table>";
        echo $_grid;
    }
}
$_bll = new bll_inwardmaster();
?>