<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_outwardmaster
{
    public function pageSearch()
    {
        global $_dbh;
        global $database_name;

        $defaultFromDate = "";
        $defaultToDate = "";

        $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

        if ($companyYearId) {
            $stmtYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
            $stmtYear->execute([$companyYearId]);
            $rowYear = $stmtYear->fetch(PDO::FETCH_ASSOC);
            if ($rowYear) {
                $defaultFromDate = date('d/m/Y', strtotime($rowYear["start_date"]));
                $defaultToDate = date('d/m/Y', strtotime($rowYear["end_date"]));
            }
        }

        if (empty($defaultFromDate) || empty($defaultToDate)) {
            $currentYear = date('Y');
            if (date('m') < 4) {
                $currentYear--;
            }
            $defaultFromDate = "01/04/$currentYear";
            $defaultToDate = "31/03/" . ($currentYear + 1);
        }

        $fromDate = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : $defaultFromDate;
        $toDate = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : $defaultToDate;

        $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate)) ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate))) : date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
        $toDateSql = !empty($toDate) && strtotime(str_replace('/', '-', $toDate)) ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate))) : date('Y-m-d', strtotime(str_replace('/', '-', $defaultToDate)));

        // Corrected where condition block
        if (COMPANY_ID != ADMIN_COMPANY_ID) {
            $where_condition = " AND t.company_id = " . COMPANY_ID . " AND t.company_year_id = " . COMPANY_YEAR_ID;
        } else {
            $where_condition = "";
        }
        $where_condition .= " AND t.outward_date >= '{$fromDateSql}' AND t.outward_date <= '{$toDateSql}' ";

        $select_columns = "t.outward_no, t.outward_date, t4.customer AS customer, t5.customer AS broker, " .
            "t11.item_name AS item, t9.marko, t1.out_qty AS outward_qty, t12.packing_unit_name AS unit, t1.out_wt AS outward_wt, " .
            "t.delivery_to, t.driver_name, t.driver_mob_no, t.transporter, t.vehicle_no, t8.inward_no, " .
            "t8.inward_date, t9.lot_no, t9.inward_qty AS stock_qty, t9.location, t9.inward_qty, t9.inward_wt";

        $from_clause = "tbl_outward_master t " .
            "INNER JOIN tbl_customer_master t4 ON t.customer = t4.customer_id " .
            "INNER JOIN tbl_outward_detail t1 ON t.outward_id = t1.outward_id " .
            "LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id " .
            "LEFT JOIN tbl_inward_master t8 ON t9.inward_id = t8.inward_id " .
            "LEFT JOIN tbl_customer_master t5 ON t8.broker = t5.customer_id " .
            "LEFT JOIN tbl_item_master t11 ON t9.item = t11.item_id " .
            "LEFT JOIN tbl_packing_unit_master t12 ON t9.packing_unit = t12.packing_unit_id";

        $sql = "CALL csms_search_detail('" .
            str_replace("'", "''", $select_columns) . "', '" .
            str_replace("'", "''", $from_clause) . "', '" .
            str_replace("'", "''", $where_condition) . "')";

        // Fetch data for dropdowns and table from the same query result
        $customers = [];
        $brokers = [];
        $items = [];
        $tableData = [];

        try {
            $stmt = $_dbh->query($sql);
            while ($_rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Collect unique values for dropdowns
                if (!empty($_rs["customer"]) && !in_array($_rs["customer"], $customers)) {
                    $customers[] = $_rs["customer"];
                }
                if (!empty($_rs["broker"]) && !in_array($_rs["broker"], $brokers)) {
                    $brokers[] = $_rs["broker"];
                }
                if (!empty($_rs["item"]) && !in_array($_rs["item"], $items)) {
                    $items[] = $_rs["item"];
                }
                // Store data for table
                $tableData[] = $_rs;
            }
            // Close the cursor to free the result set
            $stmt->closeCursor();

            // Sort arrays for better usability
            sort($customers);
            sort($brokers);
            sort($items);
        } catch (PDOException $e) {
            echo "<div>Error fetching data: " . htmlspecialchars($e->getMessage()) . "</div>";
            return;
        }

        // Only 3 dropdown filters: Customer, Broker, Item
        $filter_fields = [
            'Customer' => 'dropdown-filter',
            'Broker' => 'dropdown-filter',
            'Item' => 'dropdown-filter'
        ];

        // Date filter inputs (optional: keep if you want date search UI above dropdowns)
        render_date_filter($fromDate, $toDate, 'from-date', 'to-date', 'btn-date-search', 'date-filters');

        // Render 8 boxes in a single row - 3 dropdowns, 5 empty columns
        echo '<div class="container-fluid px-2" id="search-filters">';
        echo '<div class="row gx-2 gy-2 mb-1">';
        $dropdown_column_indexes = [ 'Customer' => 2, 'Broker' => 3, 'Item' => 4 ];
        $dropdown_labels = array_keys($filter_fields);
        for ($i = 0; $i < 8; $i++) {
            echo '<div class="col">';
            if ($i < 3) {
                $label = $dropdown_labels[$i];
                $index = $dropdown_column_indexes[$label];
                echo "<select class=\"form-select dropdown-filter\" data-index=\"{$index}\">";
                echo "<option value=\"\">{$label}s</option>";
                $options = [];
                if ($label === 'Customer') $options = $customers;
                if ($label === 'Broker') $options = $brokers;
                if ($label === 'Item') $options = $items;
                foreach ($options as $opt) {
                    echo "<option value=\"" . htmlspecialchars($opt) . "\">" . htmlspecialchars($opt) . "</option>";
                }
                echo "</select>";
            } else {
                // Empty column for spacing/alignment
                echo '&nbsp;';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';

        // Table headers (ALL columns as before)
        $table_headers = [
            'Outward No', 'Outward Date', 'Customer', 'Broker', 'Item', 'Marko', 'Outward Qty', 'Unit', 'Outward Wt (kg)',
            'Deliver To', 'Driver Name', 'Driver Mob.No', 'Transporter', 'Vehicle No', 'Inward No', 'Inward Date',
            'Lot No', 'Stock Qty', 'Location', 'Inward Qty', 'Inward Wt(kg)'
        ];

        echo "
        <table id=\"searchMaster\" class=\"ui celled table display\">
        <thead><tr>";
        foreach ($table_headers as $header) {
            echo "<th>{$header}</th>";
        }
        echo "</tr></thead><tbody>";

        $_grid = "";
        $j = 0;

        try {
            // Use the pre-fetched table data instead of re-querying
            foreach ($tableData as $_rs) {
                $j++;
                $_grid .= "<tr>";

                $_grid .= "<td>" . htmlspecialchars($_rs["outward_no"] ?? "") . "</td>";
                $timestamp = $_rs["outward_date"] && strtotime($_rs["outward_date"]) !== false
                    ? strtotime($_rs["outward_date"])
                    : 0;
                $_grid .= "<td data-order=\"" . $timestamp . "\">" .
                    ($timestamp ? date("d/m/Y", $timestamp) : "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["customer"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["broker"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["item"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["marko"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["outward_qty"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["unit"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["outward_wt"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["delivery_to"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["driver_name"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["driver_mob_no"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["transporter"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["vehicle_no"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_no"] ?? "") . "</td>";
                $inward_timestamp = $_rs["inward_date"] && strtotime($_rs["inward_date"]) !== false
                    ? strtotime($_rs["inward_date"])
                    : 0;
                $_grid .= "<td data-order=\"" . $inward_timestamp . "\">" .
                    ($inward_timestamp ? date("d/m/Y", $inward_timestamp) : "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["lot_no"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["stock_qty"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["location"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_qty"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($_rs["inward_wt"] ?? "") . "</td>";

                $_grid .= "</tr>\n";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan=\"" . count($table_headers) . "\">Error rendering table: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            $_grid .= "</tbody></table>";
            echo $_grid;
            return;
        }

        $_grid .= "</tbody></table>";
        echo $_grid;
    }
}

$_bll = new bll_outwardmaster();