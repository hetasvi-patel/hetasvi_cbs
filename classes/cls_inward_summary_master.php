<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_inwardmaster
{
    public function pageSearch()
    {
        global $_dbh;
        global $database_name;
        $params = "";
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

        try {
            $fromDateTime = DateTime::createFromFormat('d/m/Y', $fromDate);
            $toDateTime = DateTime::createFromFormat('d/m/Y', $toDate);
            if ($fromDateTime) {
                $fromDateSql = $fromDateTime->format('Y-m-d');
            } else {
                $fromDateSql = date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
            }
            if ($toDateTime) {
                $toDateSql = $toDateTime->format('Y-m-d 23:59:59');
            } else {
                $toDateSql = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $defaultToDate)));
            }
        } catch (Exception $e) {
            error_log("Date parsing error: " . $e->getMessage());
            $fromDateSql = date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
            $toDateSql = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $defaultToDate)));
        }

        if (strtotime($toDateSql) < strtotime($fromDateSql)) {
            $toDateSql = $fromDateSql . ' 23:59:59';
            $toDate = $fromDate;
        }

        if (COMPANY_ID != ADMIN_COMPANY_ID) {
            $where_condition = " t.company_id = :company_id AND t.company_year_id = :company_year_id AND t.inward_date BETWEEN :from_date AND :to_date";
        } else {
            $where_condition = " t.company_year_id = :company_year_id AND t.inward_date BETWEEN :from_date AND :to_date";
        }

        $select_columns = "t.inward_no, t.inward_date, t3.customer AS customer, t4.customer AS broker, " .
            "d.lot_no, itm.item_name AS item, d.marko, d.inward_wt, d.location, sd.Label AS storage_duration, " .
            "d.rent_per_storage_duration, " .
            "sd_rent.Label AS rent_per, " .
            "d.inward_qty, pu.packing_unit_name AS unit, d.remark, t.transporter, t.vehicle_no, " .
            "t.driver_name, t.driver_mobile_no, city.city_name AS customer_city, state.state_name AS customer_state";
        $from_clause = "tbl_inward_master t " .
            "INNER JOIN tbl_customer_master t3 ON t.customer = t3.customer_id " .
            "INNER JOIN tbl_customer_master t4 ON t.broker = t4.customer_id " .
            "INNER JOIN tbl_inward_detail d ON d.inward_id = t.inward_id " .
            "LEFT JOIN tbl_item_master itm ON d.item = itm.item_id " .
            "LEFT JOIN tbl_packing_unit_master pu ON d.packing_unit = pu.packing_unit_id " .
            "LEFT JOIN view_storage_duration sd ON d.storage_duration = sd.id " .
            "LEFT JOIN view_rent_type sd_rent ON d.rent_per = sd_rent.id " .
            "LEFT JOIN tbl_city_master city ON t3.city_id = city.city_id " .
            "LEFT JOIN tbl_state_master state ON t3.state_id = state.state_id";
        $select_columns = trim(preg_replace('/\s+/', ' ', $select_columns));
        $from_clause = trim(preg_replace('/\s+/', ' ', $from_clause));
        $where_condition = trim(preg_replace('/\s+/', ' ', $where_condition));
        $sql = "SELECT $select_columns FROM $from_clause WHERE $where_condition";
        error_log("SQL Query: $sql");
        error_log("Parameters: " . print_r($params, true));
        $stmt = $_dbh->prepare($sql);
        $params = [
            ':company_year_id' => $companyYearId,
            ':from_date' => $fromDateSql,
            ':to_date' => $toDateSql
        ];
        if (COMPANY_ID != ADMIN_COMPANY_ID) {
            $params[':company_id'] = COMPANY_ID;
            error_log("Company ID: " . COMPANY_ID);
        }

        // Fetch data for dropdowns
        $customers = [];
        $brokers = [];
        $items = [];
        $storage_durations = [];
        $customerSql = "SELECT DISTINCT customer FROM tbl_customer_master WHERE customer_id IN (SELECT DISTINCT customer FROM tbl_inward_master WHERE company_year_id = :company_year_id)";
        $customerStmt = $_dbh->prepare($customerSql);
        $customerStmt->execute([':company_year_id' => $companyYearId]);
        while ($row = $customerStmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = $row['customer'];
        }
        $brokerSql = "SELECT DISTINCT customer FROM tbl_customer_master WHERE customer_id IN (SELECT DISTINCT broker FROM tbl_inward_master WHERE company_year_id = :company_year_id)";
        $brokerStmt = $_dbh->prepare($brokerSql);
        $brokerStmt->execute([':company_year_id' => $companyYearId]);
        while ($row = $brokerStmt->fetch(PDO::FETCH_ASSOC)) {
            $brokers[] = $row['customer'];
        }
        $itemSql = "SELECT DISTINCT item_name FROM tbl_item_master WHERE item_id IN (SELECT DISTINCT item FROM tbl_inward_detail WHERE inward_id IN (SELECT inward_id FROM tbl_inward_master WHERE company_year_id = :company_year_id))";
        $itemStmt = $_dbh->prepare($itemSql);
        $itemStmt->execute([':company_year_id' => $companyYearId]);
        while ($row = $itemStmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $row['item_name'];
        }
        $storageDurationSql = "SELECT DISTINCT Label FROM view_storage_duration WHERE id IN (SELECT DISTINCT storage_duration FROM tbl_inward_detail WHERE inward_id IN (SELECT inward_id FROM tbl_inward_master WHERE company_year_id = :company_year_id))";
        $storageDurationStmt = $_dbh->prepare($storageDurationSql);
        $storageDurationStmt->execute([':company_year_id' => $companyYearId]);
        while ($row = $storageDurationStmt->fetch(PDO::FETCH_ASSOC)) {
            $storage_durations[] = $row['Label'];
        }

        // Assign the correct DataTable column index for each filter dropdown
        $filter_fields = [
            'Customer' => ['dropdown-filter', 2],
            'Broker' => ['dropdown-filter', 3],
            'Item' => ['dropdown-filter', 5],
            'Storage Duration' => ['dropdown-filter', 9]
        ];

        render_date_filter($fromDate, $toDate, 'from-date', 'to-date', 'btn-date-search', 'date-filters');
        $fields_per_row = 8;
        $k = 0;
        echo '<div class="container-fluid px-2" id="search-filters">';
        foreach ($filter_fields as $label => [$class, $colIndex]) {
            if ($k % $fields_per_row == 0) {
                if ($k > 0) echo "</div>";
                echo '<div class="row gx-2 gy-2 mb-1">';
            }
            echo "<div class='col'>";
            $options = [];
            if ($label == 'Customer') {
                $options = $customers;
            } elseif ($label == 'Broker') {
                $options = $brokers;
            } elseif ($label == 'Item') {
                $options = $items;
            } elseif ($label == 'Storage Duration') {
                $options = $storage_durations;
            }
            echo "<select class='form-select $class' data-index='$colIndex'>";
            echo "<option value=''>Select $label</option>";
            foreach ($options as $option) {
                echo "<option value='" . htmlspecialchars($option) . "'>" . htmlspecialchars($option) . "</option>";
            }
            echo "</select>";
            echo "</div>";
            $k++;
        }
        // Fill up the remaining boxes to ensure 7 per row visually
        $remaining = $fields_per_row - ($k % $fields_per_row);
        if ($remaining < $fields_per_row) {
            for ($i = 0; $i < $remaining; $i++) {
                echo "<div class='col'></div>";
            }
            echo "</div>";
        } else {
            echo "</div>";
        }
        echo "</div>";

        // Table headers remain unchanged
        echo "<table id='searchMaster' class='ui celled table display'>
              <thead>
                  <tr>";
        $headers = [
            'Inward No', 'Inward Date', 'Customer', 'Broker', 'Lot No', 'Item', 'Marko', 'Inward Wt (kg)', 'Location', 'Storage Duration', 'Rent', 'Per', 'Inward Qty', 'Unit', 'Remark', 'Transporter', 'Vehicle No', 'Driver Name', 'Driver Mobile No'
        ];
        foreach ($headers as $index => $header) {
            $classes = 'printout-col';
            if (in_array($header, ['Broker', 'Rent', 'Per', 'Remark', 'Transporter', 'Driver Mobile No'])) {
                $classes .= ' no-print';
            }
            echo "<th class='$classes'>$header</th>";
        }
        echo "</tr>
              </thead>
              <tbody>";

        $_grid = "";
        $j = 0;
        $customerInfoMap = [];
        try {
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();
            while ($_rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $j++;
                $_grid .= "<tr>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["inward_no"]) . "</td>";
                $_grid .= "<td class='printout-col'>" . (!empty($_rs["inward_date"]) ? date("d/m/Y", strtotime($_rs["inward_date"])) : "") . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["customer"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["broker"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["lot_no"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["item"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["marko"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["inward_wt"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["location"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["storage_duration"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["rent_per_storage_duration"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["rent_per"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["inward_qty"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["unit"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["remark"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["transporter"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["vehicle_no"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col'>" . htmlspecialchars($_rs["driver_name"] ?? '') . "</td>";
                $_grid .= "<td class='printout-col no-print'>" . htmlspecialchars($_rs["driver_mobile_no"] ?? '') . "</td>";
                $_grid .= "</tr>\n";
                $customerInfoMap[$j] = [
                    'name' => $_rs["customer"],
                    'city' => $_rs["customer_city"] ?? '',
                    'state' => $_rs["customer_state"] ?? '',
                ];
            }
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            echo "<tr><td colspan='" . count($headers) . "'>Error fetching data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            $_grid .= "</tbody></table>";
            echo $_grid;
            return;
        }
        $_grid .= "</tbody></table>";
        echo $_grid;

        echo "<script>var inwardCustomerInfo = " . json_encode($customerInfoMap) . ";</script>";
    }
}

$_bll = new bll_inwardmaster();
?>