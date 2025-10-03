<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_inwardmaster
{
    public function pageSearch()
    {
        global $_dbh;

        $currentDay = date('d');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $defaultFromDate = ""; // From date blank
        $defaultToDate = date('d/m/Y');
        $statusFilter = isset($_REQUEST['status_filter']) ? $_REQUEST['status_filter'] : 'all';
        $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

        if ($companyYearId) {
            if (!isset($_SESSION['company_year_start']) || !isset($_SESSION['company_year_end']) || $_SESSION['company_year_id'] != $companyYearId) {
                $stmtYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
                $stmtYear->execute([$companyYearId]);
                $rowYear = $stmtYear->fetch(PDO::FETCH_ASSOC);
                if ($rowYear) {
                    $_SESSION['company_year_start'] = $rowYear['start_date'];
                    $_SESSION['company_year_end'] = $rowYear['end_date'];
                    $_SESSION['company_year_id'] = $companyYearId;
                }
            }
            $yearStart = isset($_SESSION['company_year_start']) ? date('Y', strtotime($_SESSION['company_year_start'])) : $currentYear;
            $defaultToDate = "$currentDay/$currentMonth/$yearStart"; // Always use start year
        }

        $fromDate = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : $defaultFromDate;
        $toDate = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : $defaultToDate;

        $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate)) ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate))) : date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
        $toDateSql = !empty($toDate) && strtotime(str_replace('/', '-', $toDate)) ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate))) : date('Y-m-d', strtotime(str_replace('/', '-', $defaultToDate)));

        // Fetch customers for dropdown
        $customerSql = "SELECT DISTINCT c.customer_id, c.customer
                       FROM tbl_customer_master c
                       INNER JOIN tbl_inward_master im ON c.customer_id = im.customer 
                       WHERE c.customer_type = 1";
        $stmtCustomers = $_dbh->query($customerSql);
        $customers = $stmtCustomers->fetchAll(PDO::FETCH_ASSOC);

        // Fetch brokers for dropdown
        $brokerSql = "SELECT DISTINCT b.customer_id, b.customer
                      FROM tbl_customer_master b
                      INNER JOIN tbl_inward_master im ON b.customer_id = im.broker
                      WHERE b.customer_type = 2";
        $stmtBrokers = $_dbh->query($brokerSql);
        $brokers = $stmtBrokers->fetchAll(PDO::FETCH_ASSOC);

        // Fetch items for dropdown
        $itemSql = "SELECT DISTINCT itm.item_id, itm.item_name 
                    FROM tbl_item_master itm
                    INNER JOIN tbl_inward_detail idtl ON itm.item_id = idtl.item";
        $stmtItems = $_dbh->query($itemSql);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Fetch units for dropdown
        $unitSql = "SELECT DISTINCT packing_unit_id, packing_unit_name 
                    FROM tbl_packing_unit_master";
        $stmtUnits = $_dbh->query($unitSql);
        $units = $stmtUnits->fetchAll(PDO::FETCH_ASSOC);

        // Base SQL query (all fields)
        $sql = "SELECT
            im.inward_id,
            im.inward_no,
            im.inward_date,
            c_cust.customer AS customer,
            c_bro.customer AS broker,
            idtl.lot_no,
            itm.item_name AS item,
            idtl.marko,
            punit.packing_unit_name AS unit,
            idtl.inward_qty,
            IFNULL(odtl.total_out_qty, 0) AS out_qty,
            (idtl.inward_qty - IFNULL(odtl.total_out_qty, 0)) AS pending_qty,
            idtl.inward_wt,
            IFNULL(odtl.total_out_wt, 0) AS out_wt,
            (idtl.inward_wt - IFNULL(odtl.total_out_wt, 0)) AS pending_wt,
            idtl.gst_type,
            idtl.avg_wt_per_bag,
            idtl.location,
            idtl.moisture,
            idtl.storage_duration,
            idtl.rent_per_month,
            idtl.rent_per_storage_duration,
            idtl.seasonal_start_date,
            idtl.seasonal_end_date,
            idtl.rent_per,
            idtl.unloading_charge,
            idtl.remark
        FROM tbl_inward_detail idtl
        INNER JOIN tbl_inward_master im ON idtl.inward_id = im.inward_id
        LEFT JOIN tbl_customer_master c_cust ON im.customer = c_cust.customer_id AND c_cust.customer_type = 1
        LEFT JOIN tbl_customer_master c_bro ON im.broker = c_bro.customer_id AND c_bro.customer_type = 2
        LEFT JOIN tbl_item_master itm ON idtl.item = itm.item_id
        LEFT JOIN tbl_packing_unit_master punit ON idtl.packing_unit = punit.packing_unit_id
        LEFT JOIN (
            SELECT
                inward_detail_id,
                SUM(out_qty) AS total_out_qty,
                SUM(out_wt) AS total_out_wt
            FROM tbl_outward_detail
            GROUP BY inward_detail_id
        ) odtl ON idtl.inward_detail_id = odtl.inward_detail_id";

        $where_conditions = [];
        $params = [];

        if (defined('COMPANY_ID')) {
            $where_conditions[] = "im.company_id = ?";
            $params[] = COMPANY_ID;
        }
        if (defined('COMPANY_YEAR_ID')) {
            $where_conditions[] = "im.company_year_id = ?";
            $params[] = COMPANY_YEAR_ID;
        }

        $where_conditions[] = "im.inward_date >= ?";
        $where_conditions[] = "im.inward_date <= ?";
        $params[] = $fromDateSql;
        $params[] = $toDateSql;

        if ($statusFilter === 'pending') {
            $where_conditions[] = "(idtl.inward_qty - IFNULL(odtl.total_out_qty, 0)) > 0";
        } elseif ($statusFilter === 'clear') {
            $where_conditions[] = "(idtl.inward_qty - IFNULL(odtl.total_out_qty, 0)) = 0";
        }

        $where_clause = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";
        $sql .= $where_clause . " ORDER BY im.inward_date DESC, im.inward_no DESC, idtl.lot_no";

        // Only filter fields: Customer, Broker, Item Name, Unit
        $filter_fields = [
            'Customer' => 'customer-filter',
            'Broker' => 'broker-filter',
            'Item Name' => 'item-filter',
            'Unit' => 'unit-filter'
        ];

        echo '<div class="row gx-2 gy-1 align-items-center">';
        echo '<div class="col-auto">';
        // Date filter
        render_date_filter($fromDate, $toDate, 'from-date', 'to-date', 'btn-date-search', 'date-filters');
        echo '</div>';
        echo '<div class="col-auto">';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="status_filter" id="status_all" value="all" ' . ($statusFilter === 'all' ? 'checked' : '') . '>';
        echo '<label class="form-check-label" for="status_all">All</label>';
        echo '</div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="status_filter" id="status_pending" value="pending" ' . ($statusFilter === 'pending' ? 'checked' : '') . '>';
        echo '<label class="form-check-label" for="status_pending">Pending</label>';
        echo '</div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="status_filter" id="status_clear" value="clear" ' . ($statusFilter === 'clear' ? 'checked' : '') . '>';
        echo '<label class="form-check-label" for="status_clear">Clear</label>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="container-fluid px-2" id="search-filters">';
        $fields_per_row = 8;
        $field_keys = array_keys($filter_fields);
        $total_fields = count($field_keys);
        $rows = ceil($total_fields / $fields_per_row);
        $k = 0;
        for ($row = 0; $row < $rows; $row++) {
            echo '<div class="row gx-2 gy-1 mb-1">';
            for ($col = 0; $col < $fields_per_row; $col++) {
                $i = $row * $fields_per_row + $col;
                if ($i >= $total_fields) break;
                $label = $field_keys[$i];
                $class = $filter_fields[$label];
                echo '<div class="col">';
                if ($label === 'Customer') {
                    echo "<select class=\"form-select customer-filter\" data-index=\"{$k}\" autocomplete=\"off\">";
                    echo "<option value=\"\">Search {$label}</option>";
                    foreach ($customers as $customer) {
                        echo "<option value=\"" . htmlspecialchars($customer['customer']) . "\">" . htmlspecialchars($customer['customer']) . "</option>";
                    }
                    echo "</select>";
                } elseif ($label === 'Broker') {
                    echo "<select class=\"form-select broker-filter\" data-index=\"{$k}\" autocomplete=\"off\">";
                    echo "<option value=\"\">Search {$label}</option>";
                    foreach ($brokers as $broker) {
                        echo "<option value=\"" . htmlspecialchars($broker['customer']) . "\">" . htmlspecialchars($broker['customer']) . "</option>";
                    }
                    echo "</select>";
                } elseif ($label === 'Item Name') {
                    echo "<select class=\"form-select item-filter\" data-index=\"{$k}\" autocomplete=\"off\">";
                    echo "<option value=\"\">Search {$label}</option>";
                    foreach ($items as $item) {
                        echo "<option value=\"" . htmlspecialchars($item['item_name']) . "\">" . htmlspecialchars($item['item_name']) . "</option>";
                    }
                    echo "</select>";
                } elseif ($label === 'Unit') {
                    echo "<select class=\"form-select unit-filter\" data-index=\"{$k}\" autocomplete=\"off\">";
                    echo "<option value=\"\">Search {$label}</option>";
                    foreach ($units as $unit) {
                        echo "<option value=\"" . htmlspecialchars($unit['packing_unit_name']) . "\">" . htmlspecialchars($unit['packing_unit_name']) . "</option>";
                    }
                    echo "</select>";
                } else {
                    echo "<input type=\"text\" class=\"form-control {$class}\" placeholder=\"Search {$label}\" data-index=\"{$k}\" />";
                }
                echo '</div>';
                $k++;
            }
        $remaining = $fields_per_row - ($k % $fields_per_row);
        if ($remaining < $fields_per_row) {
            for ($i = 0; $i < $remaining; $i++) {
                echo "<div class='col'></div>";
            }
            echo "</div>";
        } else {
            echo "</div>";
        }
        }
        echo '</div>';

        // Table with all fields (grid)
        echo '<table id="searchMaster" class="ui celled table display">';
        echo '<thead><tr>';
        echo '<th><label><input type="checkbox" id="select-all" > Select</label></th>';
        echo '<th>Customer</th>';
        echo '<th>In. Date</th>';
        echo '<th>Broker</th>';
        echo '<th>Inward No.</th>';
        echo '<th>Item Name</th>';
        echo '<th>Unit</th>';
        echo '<th>Lot No.</th>';
        echo '<th>Marko</th>';
        echo '<th>In. Qty.</th>';
        echo '<th>Out. Qty.</th>';
        echo '<th>Pend. Qty.</th>';
        echo '<th>In. Wt.(kg)</th>';
        echo '<th>Out. Wt.(kg)</th>';
        echo '<th>Pen. Wt.(kg)</th>';
        // Add more headers if you want all fields
        echo '</tr></thead><tbody>';

        $_grid = "";
        $tot_in_qty = $tot_out_qty = $tot_pending_qty = $tot_in_wt = $tot_out_wt = $tot_pending_wt = 0;
        try {
            $stmt = $_dbh->prepare($sql);
            $stmt->execute($params);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inward_timestamp = $row["inward_date"] && strtotime($row["inward_date"]) !== false ? strtotime($row["inward_date"]) : 0;
                $_grid .= "<tr>";
                $_grid .= "<td><input type=\"checkbox\" class=\"row-select\" value=\"{$row['inward_id']}\" /></td>";
                $_grid .= "<td>" . htmlspecialchars($row["customer"] ?? "") . "</td>";
                $_grid .= "<td data-order=\"" . $inward_timestamp . "\">" . ($inward_timestamp ? date("d/m/Y", $inward_timestamp) : "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["broker"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["inward_no"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["item"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["unit"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["lot_no"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["marko"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["inward_qty"] ?? "") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["out_qty"] ?? "0") . "</td>";
                $_grid .= "<td>" . htmlspecialchars($row["pending_qty"] ?? "0") . "</td>";
                $_grid .= "<td>" . htmlspecialchars(number_format($row["inward_wt"] ?? 0, 2)) . "</td>";
                $_grid .= "<td>" . htmlspecialchars(number_format($row["out_wt"] ?? 0, 2)) . "</td>";
                $_grid .= "<td>" . htmlspecialchars(number_format($row["pending_wt"] ?? 0, 2)) . "</td>";
                $_grid .= "</tr>\n";
                
                // Calculate totals
                $tot_in_qty += floatval($row["inward_qty"]);
                $tot_out_qty += floatval($row["out_qty"]);
                $tot_pending_qty += floatval($row["pending_qty"]);
                $tot_in_wt += floatval($row["inward_wt"]);
                $tot_out_wt += floatval($row["out_wt"]);
                $tot_pending_wt += floatval($row["pending_wt"]);
            }
        }  catch (PDOException $e) {
            echo "<tr><td colspan=\"20\">Error fetching data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            $_grid .= "</tbody></table>";
            echo $_grid;
            return;
        }

         $_grid .= "</tbody>";
        // Totals row
        $_grid .= '<tfoot><tr>';
        $_grid .= '<th colspan="8" class="text-end">Total</th>';
        $_grid .= '<th>' . number_format($tot_in_qty, 2) . '</th>';
        $_grid .= '<th>' . number_format($tot_out_qty, 2) . '</th>';
        $_grid .= '<th>' . number_format($tot_pending_qty, 2) . '</th>';
        $_grid .= '<th></th>';
        $_grid .= '<th>' . number_format($tot_in_wt, 2) . '</th>';
        $_grid .= '<th>' . number_format($tot_out_wt, 2) . '</th>';
        $_grid .= '<th>' . number_format($tot_pending_wt, 2) . '</th>';
        $_grid .= '</tr></tfoot></table>';
        echo $_grid;
    }
 public function pagePreview()
    {
        global $_dbh;

        $ids = isset($_GET['ids']) && $_GET['ids'] != "" ? explode(",", $_GET['ids']) : [];
        $where_id = "";
        if (count($ids) > 0) {
            $ids_safe = array_map('intval', $ids);
            $where_id = "AND idtl.inward_id IN (" . implode(",", $ids_safe) . ")";
        }
        $year_filter = "";
        if (defined('COMPANY_ID')) {
            $year_filter .= " AND im.company_id = " . intval(COMPANY_ID);
        }
        if (defined('COMPANY_YEAR_ID')) {
            $year_filter .= " AND im.company_year_id = " . intval(COMPANY_YEAR_ID);
        }

        $printDate = date('d/m/Y');
        $companyTitle = "Cold Storage";
        $financialYearStart = isset($_SESSION['company_year_start']) ? date('Y', strtotime($_SESSION['company_year_start'])) : date('Y');
        $summaryTitle = "PARTY WISE INWARD STATUS ON " . date('d-m-', strtotime('30-07-' . $financialYearStart)) . $financialYearStart;

        $sql = "
        SELECT
            im.inward_id,
            im.inward_no,
            im.inward_date,
            c_cust.customer AS customer,
            c_bro.customer AS broker,
            idtl.lot_no,
            itm.item_name,
            idtl.marko,
            punit.packing_unit_name AS unit,
            idtl.inward_qty,
            IFNULL(odtl.total_out_qty,0) AS out_qty,
            (idtl.inward_qty - IFNULL(odtl.total_out_qty,0)) AS pending_qty,
            idtl.inward_wt,
            IFNULL(odtl.total_out_wt,0) AS out_wt,
            (idtl.inward_wt - IFNULL(odtl.total_out_wt,0)) AS pending_wt,
            idtl.gst_type,
            idtl.avg_wt_per_bag,
            idtl.location,
            idtl.moisture,
            idtl.storage_duration,
            idtl.rent_per_month,
            idtl.rent_per_storage_duration,
            idtl.seasonal_start_date,
            idtl.seasonal_end_date,
            idtl.rent_per,
            idtl.unloading_charge,
            idtl.remark
        FROM tbl_inward_detail idtl
        INNER JOIN tbl_inward_master im ON idtl.inward_id = im.inward_id
        LEFT JOIN tbl_customer_master c_cust ON im.customer = c_cust.customer_id AND c_cust.customer_type = 1
        LEFT JOIN tbl_customer_master c_bro ON im.broker = c_bro.customer_id AND c_bro.customer_type = 2
        LEFT JOIN tbl_item_master itm ON idtl.item = itm.item_id
        LEFT JOIN tbl_packing_unit_master punit ON idtl.packing_unit = punit.packing_unit_id
        LEFT JOIN (
            SELECT
                inward_detail_id,
                SUM(out_qty) AS total_out_qty,
                SUM(out_wt) AS total_out_wt
            FROM tbl_outward_detail
            GROUP BY inward_detail_id
        ) odtl ON idtl.inward_detail_id = odtl.inward_detail_id
        WHERE 1=1 $where_id $year_filter
        ORDER BY c_cust.customer, im.inward_date, im.inward_no, idtl.lot_no
        ";

        $data = [];
        $stmt = $_dbh->query($sql);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $grouped = [];
        foreach ($data as $row) {
            $customer = $row['customer'] ?: 'Unknown Party';
            $grouped[$customer][] = $row;
        }

        // Calculate grand totals
        $gt_inward = $gt_outward = $gt_pending = $gt_inwt = $gt_outwt = $gt_pendingwt = 0;
        foreach ($data as $row) {
            $gt_inward += floatval($row['inward_qty']);
            $gt_outward += floatval($row['out_qty']);
            $gt_pending += floatval($row['pending_qty']);
            $gt_inwt += floatval($row['inward_wt']);
            $gt_outwt += floatval($row['out_wt']);
            $gt_pendingwt += floatval($row['pending_wt']);
        }

        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<title>' . htmlspecialchars($summaryTitle) . '</title>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>';
        echo '</head>';
        echo '<body class="pwib-body">';
        echo '<div class="pwib-button-container">';
        echo '<button onclick="doPrint()">Print</button>';
        echo '<button onclick="doPDF()">PDF</button>';
        echo '<button onclick="doClose()">Close</button>';
        echo '</div>';
        echo '<div id="pwib-preview-content">';
        echo '<div class="pwib-header-container">';
        echo '<div class="pwib-header-left">' . htmlspecialchars($printDate) . '</div>';
        echo '<div class="pwib-header-center">' . htmlspecialchars($companyTitle) . '</div>';
        echo '<div class="pwib-header-right">Page 1 of 1</div>';
        echo '</div>';
        echo '<hr class="pwib-title-divider">';
        echo '<div class="pwib-report-title">' . htmlspecialchars($summaryTitle) . '</div>';
        echo '<hr class="pwib-title-divider">';
        echo '<table class="pwib-report-table" id="pwib-report-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>In. Date</th>';
        echo '<th>In. No.</th>';
        echo '<th>Lot No.</th>';
        echo '<th>Item Name</th>';
        echo '<th>Unit</th>';
        echo '<th>Variety</th>';
        echo '<th>Inward</th>';
        echo '<th>Outward</th>';
        echo '<th>Pending</th>';
        echo '<th>Inward WL</th>';
        echo '<th>Out. WL</th>';
        echo '<th>Pend. WL</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach($grouped as $party => $rows){
            $p_inward = $p_outward = $p_pending = $p_inwt = $p_outwt = $p_pendingwt = 0;
            echo '<tr>';
            echo '<td class="pwib-party-header">' . htmlspecialchars($party) . '</td>';
            echo '<td colspan="11"></td>';
            echo '</tr>';
            foreach($rows as $row){
                $p_inward += floatval($row['inward_qty']);
                $p_outward += floatval($row['out_qty']);
                $p_pending += floatval($row['pending_qty']);
                $p_inwt += floatval($row['inward_wt']);
                $p_outwt += floatval($row['out_wt']);
                $p_pendingwt += floatval($row['pending_wt']);
                echo '<tr>';
                echo '<td>' . ($row['inward_date'] ? date('d/m/y', strtotime($row['inward_date'])) : '') . '</td>';
                echo '<td>' . htmlspecialchars($row['inward_no']) . '</td>';
                echo '<td>' . htmlspecialchars($row['lot_no']) . '</td>';
                echo '<td>' . htmlspecialchars($row['item_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['unit']) . '</td>';
                echo '<td>' . htmlspecialchars($row['marko']) . '</td>';
                echo '<td>' . htmlspecialchars($row['inward_qty']) . '</td>';
                echo '<td>' . htmlspecialchars($row['out_qty']) . '</td>';
                echo '<td>' . number_format($row['pending_qty'], 2) . '</td>';
                echo '<td>' . number_format($row['inward_wt'], 2) . '</td>';
                echo '<td>' . number_format($row['out_wt'], 2) . '</td>';
                echo '<td>' . number_format($row['pending_wt'], 2) . '</td>';
                echo '</tr>';
            }
            // Party total
            echo '<tr class="pwib-party-total">';
            echo '<td colspan="6" class="pwib-party">Party Wise Total :</td>';
            echo '<td>' . $p_inward . '</td>';
            echo '<td>' . $p_outward . '</td>';
            echo '<td>' . number_format($p_pending, 2) . '</td>';
            echo '<td>' . number_format($p_inwt, 2) . '</td>';
            echo '<td>' . number_format($p_outwt, 2) . '</td>';
            echo '<td>' . number_format($p_pendingwt, 2) . '</td>';
            echo '</tr>';
        }
        // Grand total
        echo '<tr class="pwib-grand-total">';
        echo '<td colspan="6" class="pwib-party">Grand Total :</td>';
        echo '<td>' . $gt_inward . '</td>';
        echo '<td>' . $gt_outward . '</td>';
        echo '<td>' . number_format($gt_pending, 2) . '</td>';
        echo '<td>' . number_format($gt_inwt, 2) . '</td>';
        echo '<td>' . number_format($gt_outwt, 2) . '</td>';
        echo '<td>' . number_format($gt_pendingwt, 2) . '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        ?>
        <script>
            function doPrint() { window.print(); }
            function getFormattedFileName() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                return dd + '-' + mm + '-' + yyyy + '_inward_summary.pdf';
            }
            function doPDF() {
                var container = document.getElementById('pwib-preview-content');
                html2canvas(container).then(function(canvas) {
                    var imgData = canvas.toDataURL('image/png');
                    var pdf = new window.jspdf.jsPDF('l', 'mm', 'a4');
                    var pageWidth = pdf.internal.pageSize.getWidth();
                    var pageHeight = pdf.internal.pageSize.getHeight();
                    var imgWidth = pageWidth - 20;
                    var imgHeight = canvas.height * imgWidth / canvas.width;
                    var position = 10;
                    var leftMargin = 10;
                    var topMargin = 10;
                    var pageContentHeight = pageHeight - 2 * topMargin;
                    var pageCount = Math.ceil(imgHeight / pageContentHeight);
                    var sourceX = 0;
                    var sourceY = 0;
                    var sourceWidth = canvas.width;
                    var sourceHeight = (pageContentHeight * canvas.width) / imgWidth;
                    for (var i = 0; i < pageCount; i++) {
                        var tempCanvas = document.createElement("canvas");
                        tempCanvas.width = canvas.width;
                        tempCanvas.height = Math.min(sourceHeight, canvas.height - sourceY);
                        var ctx = tempCanvas.getContext("2d");
                        ctx.drawImage(
                            canvas,
                            sourceX, sourceY, sourceWidth, tempCanvas.height,
                            0, 0, sourceWidth, tempCanvas.height
                        );
                        var pageImgData = tempCanvas.toDataURL('image/png');
                        var pageImgHeight = (tempCanvas.height * imgWidth) / tempCanvas.width;
                        if (i > 0) pdf.addPage('a4', 'l');
                        pdf.addImage(pageImgData, 'PNG', leftMargin, topMargin, imgWidth, pageImgHeight);
                        sourceY += tempCanvas.height;
                    }
                    pdf.save(getFormattedFileName());
                });
            }
            function doClose() { window.close(); }
        </script>
        <?php
        echo '</body>';
        echo '</html>';
        exit;
    }
}
$_bll = new bll_inwardmaster();
?>