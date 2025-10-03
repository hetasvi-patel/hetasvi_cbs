<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_inward_outward_summary_master
{
    public function renderFilters($fromDate, $toDate) {
        global $_dbh;
      $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

// Correction: If still blank, fetch default from DB
if (!$companyYearId) {
    $stmt = $_dbh->query("SELECT company_year_id FROM tbl_company_year_master ORDER BY start_date DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $companyYearId = $row ? $row['company_year_id'] : null;
    if ($companyYearId) {
        $_SESSION['company_year_id'] = $companyYearId;
    }
}

        // Default dates logic: month always same, year from company year
        if ($companyYearId) {
            $stmtYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
            $stmtYear->execute([$companyYearId]);
            $rowYear = $stmtYear->fetch(PDO::FETCH_ASSOC);
            if ($rowYear) {
                $fromYear = date('Y', strtotime($rowYear["start_date"]));
                $toYear = date('Y', strtotime($rowYear["end_date"]));
                $defaultFromDate = "01/04/$fromYear";
                $defaultToDate = "31/03/$toYear";
            } else {
                $defaultFromDate = "01/04/" . date('Y');
                $defaultToDate = "31/03/" . (date('Y') + 1);
            }
        } else {
            $defaultFromDate = "01/04/" . date('Y');
            $defaultToDate = "31/03/" . (date('Y') + 1);
        }

        // Use request or default
        $fromDate = !empty($fromDate) ? $fromDate : $defaultFromDate;
        $toDate = !empty($toDate) ? $toDate : $defaultToDate;

        // Prepare filters according to selected date range and company_year_id
        $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)))
            : '';
        $toDateSql = !empty($toDate) && strtotime(str_replace('/', '-', $toDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate)))
            : '';

        // WHERE clauses
        $whereInward = " WHERE t.company_year_id = :company_year_id";
        $whereOutward = " WHERE t.company_year_id = :company_year_id";
        $params = [':company_year_id' => $companyYearId];

        if ($fromDateSql && $toDateSql) {
            $whereInward .= " AND t.inward_date BETWEEN :from_date AND :to_date";
            $whereOutward .= " AND t.outward_date BETWEEN :from_date AND :to_date";
            $params[':from_date'] = $fromDateSql;
            $params[':to_date'] = $toDateSql;
        }

        // Get customers from inward/outward for given company_year_id and date
        $customerSql = "
            SELECT DISTINCT cm.customer
            FROM tbl_customer_master cm
            WHERE cm.customer_id IN (
                SELECT t.customer FROM tbl_inward_master t $whereInward
                UNION
                SELECT t.customer FROM tbl_outward_master t $whereOutward
            )
            ORDER BY cm.customer
        ";
        $stmt = $_dbh->prepare($customerSql);
        $stmt->execute($params);
        $customers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = $row['customer'];
        }

        // Get items from inward/outward for given company_year_id and date
        $itemSql = "
            SELECT DISTINCT im.item_name
            FROM tbl_item_master im
            WHERE im.item_id IN (
                SELECT d.item FROM tbl_inward_detail d
                    INNER JOIN tbl_inward_master t ON d.inward_id = t.inward_id $whereInward
                UNION
                SELECT d.item FROM tbl_inward_detail d
                    INNER JOIN tbl_outward_detail od ON d.inward_detail_id = od.inward_detail_id
                    INNER JOIN tbl_outward_master t ON od.outward_id = t.outward_id $whereOutward
            )
            ORDER BY im.item_name
        ";
        $stmt = $_dbh->prepare($itemSql);
        $stmt->execute($params);
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $row['item_name'];
        }

        // Render filters in a single row, with customer/item dropdowns after the search button
        echo '<div class="row gx-2 gy-1 align-items-center mb-2" id="date-filters">';
        echo '<div class="col-auto d-flex align-items-center">
            <label for="from-date" class="form-label mb-0 me-2">From</label>
            <input type="text" class="form-control date-filter datepicker" id="from-date" name="from-date" placeholder="From Date" value="'.htmlspecialchars($fromDate ?? '').'" autocomplete="off" style="width:120px;" />
          </div>';
        echo '<div class="col-auto d-flex align-items-center">
            <label for="to-date" class="form-label mb-0 me-2">To</label>
            <input type="text" class="form-control date-filter datepicker" id="to-date" name="to-date" placeholder="To Date" value="'.htmlspecialchars($toDate ?? '').'" autocomplete="off" style="width:120px;" />
          </div>';
        echo '<div class="col-auto">
            <button type="button" class="btn btn-primary" id="btn-date-search">Search</button>
          </div>';

        // Customer dropdown
        echo '<div class="col-auto">';
        echo '<select class="form-select input-sm search-field" data-index="customer" id="customer-search" style="min-width:170px;">';
        echo '<option value="">All Customers</option>';
        foreach ($customers as $cname) {
            $selected = (isset($_REQUEST['customer_search']) && $_REQUEST['customer_search'] === $cname) ? 'selected' : '';
            echo '<option value="'.htmlspecialchars($cname).'" '.$selected.'>'.htmlspecialchars($cname).'</option>';
        }
        echo '</select>';
        echo '</div>';

        // Item dropdown
        echo '<div class="col-auto">';
        echo '<select class="form-select input-sm search-field" data-index="item" id="item-search" style="min-width:170px;">';
        echo '<option value="">All Items</option>';
        foreach ($items as $iname) {
            $selected = (isset($_REQUEST['item_search']) && $_REQUEST['item_search'] === $iname) ? 'selected' : '';
            echo '<option value="'.htmlspecialchars($iname).'" '.$selected.'>'.htmlspecialchars($iname).'</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '</div>';
    }

    public function ajaxTable() {
        $this->pageSearch(true);
    }

    public function pageSearch($onlyTable = false)
    {
        global $_dbh;
        $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

        // Default dates
        if ($companyYearId) {
            $stmtYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
            $stmtYear->execute([$companyYearId]);
            $rowYear = $stmtYear->fetch(PDO::FETCH_ASSOC);
            if ($rowYear) {
                $defaultFromDate = "01/04/" . date('Y', strtotime($rowYear["start_date"]));
                $defaultToDate = "31/03/" . date('Y', strtotime($rowYear["end_date"]));
            } else {
                $defaultFromDate = "01/04/" . date('Y');
                $defaultToDate = "31/03/" . (date('Y') + 1);
            }
        } else {
            $defaultFromDate = "01/04/" . date('Y');
            $defaultToDate = "31/03/" . (date('Y') + 1);
        }
        $fromDate = $_REQUEST['from_date'] ?? $defaultFromDate;
        $toDate = $_REQUEST['to_date'] ?? $defaultToDate;

        $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)))
            : date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
        $toDateSql = !empty($toDate) && strtotime(str_replace('/', '-', $toDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate)))
            : date('Y-m-d', strtotime(str_replace('/', '-', $defaultToDate)));

        // Get customer and item filters from request
        $customerFilter = $_REQUEST['customer_search'] ?? '';
        $itemFilter = $_REQUEST['item_search'] ?? '';

        // WHERE clauses with company_year_id
        $inward_where = " t.company_year_id = :company_year_id AND t.inward_date BETWEEN :from_date AND :to_date";
        $outward_where = " t.company_year_id = :company_year_id AND t.outward_date BETWEEN :from_date AND :to_date";

        if ($customerFilter) {
            $inward_where .= " AND t3.customer = :customer_search";
        }
        if ($itemFilter) {
            $inward_where .= " AND itm.item_name = :item_search";
        }

        $inward_sql = "SELECT t.inward_date, t.inward_no, t3.customer AS customer, itm.item_name AS item, d.inward_qty, d.inward_id
            FROM tbl_inward_master t
            INNER JOIN tbl_customer_master t3 ON t.customer = t3.customer_id
            INNER JOIN tbl_inward_detail d ON d.inward_id = t.inward_id
            LEFT JOIN tbl_item_master itm ON d.item = itm.item_id
            WHERE $inward_where
            ORDER BY t.inward_date, t.inward_no";

        $inward_params = [
            ':company_year_id' => $companyYearId,
            ':from_date' => $fromDateSql,
            ':to_date' => $toDateSql
        ];
        if ($customerFilter) {
            $inward_params[':customer_search'] = $customerFilter;
        }
        if ($itemFilter) {
            $inward_params[':item_search'] = $itemFilter;
        }

        if ($customerFilter) {
            $outward_where .= " AND t4.customer = :customer_search";
        }
        if ($itemFilter) {
            $outward_where .= " AND t11.item_name = :item_search";
        }

        $outward_sql = "SELECT t.outward_date, t.outward_no, t4.customer AS customer, t1.out_qty AS outward_qty,
                        t11.item_name AS item, t1.inward_detail_id, t8.inward_no, t8.inward_date
            FROM tbl_outward_master t
            INNER JOIN tbl_customer_master t4 ON t.customer = t4.customer_id
            INNER JOIN tbl_outward_detail t1 ON t.outward_id = t1.outward_id
            LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id
            LEFT JOIN tbl_inward_master t8 ON t9.inward_id = t8.inward_id
            LEFT JOIN tbl_item_master t11 ON t9.item = t11.item_id
            WHERE $outward_where
            ORDER BY t.outward_date, t.outward_no";

        $outward_params = [
            ':company_year_id' => $companyYearId,
            ':from_date' => $fromDateSql,
            ':to_date' => $toDateSql
        ];
        if ($customerFilter) {
            $outward_params[':customer_search'] = $customerFilter;
        }
        if ($itemFilter) {
            $outward_params[':item_search'] = $itemFilter;
        }

        $inward_data = [];
        $total_inward_qty = 0;
        try {
            $stmt = $_dbh->prepare($inward_sql);
            $stmt->execute($inward_params);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inward_data[] = $row;
                $total_inward_qty += (float)$row['inward_qty'];
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error fetching inward data: " . htmlspecialchars($e->getMessage() ?? '') . "</div>";
            return;
        }

        $outward_data = [];
        $total_outward_qty = 0;
        try {
            $stmt = $_dbh->prepare($outward_sql);
            $stmt->execute($outward_params);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $outward_data[] = $row;
                $total_outward_qty += (float)$row['outward_qty'];
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error fetching outward data: " . htmlspecialchars($e->getMessage() ?? '') . "</div>";
            return;
        }

        if (!$onlyTable) {
            $this->renderFilters($fromDate, $toDate);
        }

        // Table rendering
        $maxRows = max(count($inward_data), count($outward_data));
        echo '<div class="table-responsive"><table class="table table-bordered" id="inwardOutwardSummary">';
        echo '<thead>
            <tr>
                <th colspan="5" style="text-align:center; border-right:2px solid #000;">Inward Summary</th>
                <th colspan="7" style="text-align:center;">Outward Summary</th>
            </tr>
            <tr>
                <th>Inward Date</th>
                <th>Inward No</th>
                <th>Customer</th>
                <th>Item</th>
                <th class="text-right">Inward Qty</th>
                <th style="border-left:2px solid #000;">Outward Date</th>
                <th>Outward No</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Inward No</th>
                <th>Inward Date</th>
                <th class="text-right">Outward Qty</th>
            </tr>
        </thead>';
        echo '<tbody>';
        for ($i = 0; $i < $maxRows; $i++) {
            echo '<tr>';
            if (isset($inward_data[$i]) && is_array($inward_data[$i])) {
                $inward = $inward_data[$i];
                echo '<td>' . (isset($inward['inward_date']) && $inward['inward_date'] ? htmlspecialchars(date("d/m/Y", strtotime($inward['inward_date']))) : '') . '</td>';
                echo '<td>' . htmlspecialchars($inward['inward_no'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($inward['customer'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($inward['item'] ?? '') . '</td>';
                echo '<td class="text-right">' . htmlspecialchars($inward['inward_qty'] ?? '') . '</td>';
            } else {
                echo str_repeat('<td></td>', 5);
            }
            if (isset($outward_data[$i]) && is_array($outward_data[$i])) {
                $outward = $outward_data[$i];
                echo '<td style="border-left:2px solid #000;">' . (isset($outward['outward_date']) && $outward['outward_date'] ? htmlspecialchars(date("d/m/Y", strtotime($outward['outward_date']))) : '') . '</td>';
                echo '<td>' . htmlspecialchars($outward['outward_no'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($outward['customer'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($outward['item'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($outward['inward_no'] ?? '') . '</td>';
                echo '<td>' . (isset($outward['inward_date']) && $outward['inward_date'] ? htmlspecialchars(date("d/m/Y", strtotime($outward['inward_date']))) : '') . '</td>';
                echo '<td class="text-right">' . htmlspecialchars($outward['outward_qty'] ?? '') . '</td>';
            } else {
                echo '<td style="border-left:2px solid #000;"></td>' . str_repeat('<td></td>', 6);
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr style="font-weight:bold;">';
        echo '<td colspan="4" style="text-align:right;">Total:</td>';
        echo '<td class="text-right">' . htmlspecialchars(number_format($total_inward_qty, 2) ?? '') . '</td>';
        echo '<td style="border-left:2px solid #000;"></td>';
        echo '<td colspan="5" style="text-align:right;">Total:</td>';
        echo '<td class="text-right">' . htmlspecialchars(number_format($total_outward_qty, 2) ?? '') . '</td>';
        echo '</tr>';
        echo '</tfoot>';
        echo '</table></div>';
    }
}
$_bll = new bll_inward_outward_summary_master();