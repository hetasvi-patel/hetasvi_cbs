<?php
include_once(__DIR__ . "/../config/connection.php");

class bll_yearly_stock_report_master {
    public function pageSearch() {
        global $_dbh;
        global $database_name;

        // --- Date logic: always use company year if set ---
        $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

        $defaultFromDate = '';
        $defaultToDate = '';
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

        // REMOVE: $fromDate and $toDate logic from POST, always use default
        $fromDate = $defaultFromDate;
        $toDate = $defaultToDate;
        $itemSearch = $_POST['item_search'] ?? '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 0;

        // Convert to SQL format
        $fromDateSql = date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)));
        $toDateSql = date('Y-m-d', strtotime(str_replace('/', '-', $toDate)));

        // Item dropdown - all items
        $itemArr = [];
        $stmtItems = $_dbh->query("SELECT item_id, item_name FROM tbl_item_master ORDER BY item_name");
        while ($rowItem = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
            $itemArr[] = $rowItem;
        }

        // Filter items
        $itemIds = [];
        $selectedPage = $page;
        if ($itemSearch && $itemSearch !== 'ALL') {
            foreach ($itemArr as $k => $it) {
                if ($it['item_id'] == $itemSearch) {
                    $itemIds = [$it['item_id']];
                    $selectedPage = $k;
                    break;
                }
            }
        } else {
            foreach ($itemArr as $item) {
                $itemIds[] = $item['item_id'];
            }
        }

        // --- Prepare months (April..March) but according to fromDate/toDate ---
        $startMonth = (int)date('m', strtotime($fromDateSql));
        $startYear = (int)date('Y', strtotime($fromDateSql));
        $endMonth = (int)date('m', strtotime($toDateSql));
        $endYear = (int)date('Y', strtotime($toDateSql));

        $months = [];
        $curMonth = $startMonth;
        $curYear = $startYear;
        while ($curYear < $endYear || ($curYear == $endYear && $curMonth <= $endMonth)) {
            $months[] = [
                'month' => $curMonth,
                'year' => $curYear,
                'label' => date('F', mktime(0,0,0,$curMonth,1)) . " - $curYear"
            ];
            $curMonth++;
            if ($curMonth > 12) { $curMonth = 1; $curYear++; }
        }

        // --- Fetch and process data per item ---
        $results = [];
        foreach ($itemIds as $itemId) {
            // Get item name
            $itemName = '';
            foreach ($itemArr as $it) {
                if ($it['item_id'] == $itemId) $itemName = $it['item_name'];
            }

            // Find previous year end-date (< current year start-date)
            $stmtPrevYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE end_date < ? ORDER BY end_date DESC LIMIT 1");
            $stmtPrevYear->execute([$fromDateSql]);
            $prevYearRow = $stmtPrevYear->fetch(PDO::FETCH_ASSOC);

            if ($prevYearRow) {
                $prevYearStartDate = $prevYearRow['start_date'];
                $prevYearEndDate = $prevYearRow['end_date'];

                // Get all inwards and outwards for previous year
                $stmtPrevInward = $_dbh->prepare("
                    SELECT SUM(id.inward_qty) as qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    WHERE id.item = ? AND im.inward_date BETWEEN ? AND ?
                ");
                $stmtPrevInward->execute([$itemId, $prevYearStartDate, $prevYearEndDate]);
                $prevInwardQty = floatval($stmtPrevInward->fetchColumn());

                $stmtPrevOutward = $_dbh->prepare("
                    SELECT SUM(od.out_qty) as qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    LEFT JOIN tbl_outward_detail od ON od.inward_detail_id = id.inward_detail_id
                    LEFT JOIN tbl_outward_master om ON om.outward_id = od.outward_id
                    WHERE id.item = ? AND om.outward_date BETWEEN ? AND ?
                ");
                $stmtPrevOutward->execute([$itemId, $prevYearStartDate, $prevYearEndDate]);
                $prevOutwardQty = floatval($stmtPrevOutward->fetchColumn());

                // Get opening qty for previous year
                $stmtPrevOpening = $_dbh->prepare("
                    SELECT SUM(id.inward_qty) AS in_qty, SUM(od.out_qty) AS out_qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    LEFT JOIN tbl_outward_detail od ON od.inward_detail_id = id.inward_detail_id
                    LEFT JOIN tbl_outward_master om ON om.outward_id = od.outward_id
                    WHERE id.item = ? AND im.inward_date < ? AND (om.outward_date IS NULL OR om.outward_date < ?)
                ");
                $stmtPrevOpening->execute([$itemId, $prevYearStartDate, $prevYearStartDate]);
                $rowPrevOpening = $stmtPrevOpening->fetch(PDO::FETCH_ASSOC);
                $prevOpeningQty = floatval($rowPrevOpening['in_qty']) - floatval($rowPrevOpening['out_qty']);

                // Previous year closing/balance qty
                $openingQty = $prevOpeningQty + $prevInwardQty - $prevOutwardQty;
            } else {
                // First year: all inwards - outwards before fromDate
                $stmtOp = $_dbh->prepare("
                    SELECT SUM(id.inward_qty) AS in_qty, SUM(od.out_qty) AS out_qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    LEFT JOIN tbl_outward_detail od ON od.inward_detail_id = id.inward_detail_id
                    LEFT JOIN tbl_outward_master om ON om.outward_id = od.outward_id
                    WHERE id.item = ? 
                    AND im.inward_date < ?
                    AND (om.outward_date IS NULL OR om.outward_date < ?)
                ");
                $stmtOp->execute([$itemId, $fromDateSql, $fromDateSql]);
                $rowOp = $stmtOp->fetch(PDO::FETCH_ASSOC);
                $openingQty = floatval($rowOp['in_qty']) - floatval($rowOp['out_qty']);
            }

            $monthly = [];
            $runningBalance = $openingQty;

            foreach ($months as $m) {
                $monthStart = date('Y-m-01', strtotime("{$m['year']}-{$m['month']}-01"));
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                if ($monthStart < $fromDateSql) $monthStart = $fromDateSql;
                if ($monthEnd > $toDateSql) $monthEnd = $toDateSql;

                // Inward qty for this month
                $stmtIn = $_dbh->prepare("
                    SELECT SUM(id.inward_qty) as qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    WHERE id.item = ? AND im.inward_date BETWEEN ? AND ?
                ");
                $stmtIn->execute([$itemId, $monthStart, $monthEnd]);
                $inwardQty = floatval($stmtIn->fetchColumn());

                // Outward qty for this month
                $stmtOut = $_dbh->prepare("
                    SELECT SUM(od.out_qty) as qty
                    FROM tbl_inward_detail id
                    LEFT JOIN tbl_inward_master im ON im.inward_id = id.inward_id
                    LEFT JOIN tbl_outward_detail od ON od.inward_detail_id = id.inward_detail_id
                    LEFT JOIN tbl_outward_master om ON om.outward_id = od.outward_id
                    WHERE id.item = ? AND om.outward_date BETWEEN ? AND ?
                ");
                $stmtOut->execute([$itemId, $monthStart, $monthEnd]);
                $outwardQty = floatval($stmtOut->fetchColumn());

                // Balance calculation
                $row = [
                    'label' => $m['label'],
                    'opening' => $runningBalance,
                    'inward' => $inwardQty,
                    'outward' => $outwardQty,
                    'balance' => $runningBalance + $inwardQty - $outwardQty
                ];
                $monthly[] = $row;
                $runningBalance = $row['balance'];
            }

            $results[] = [
                'item_id' => $itemId,
                'item_name' => $itemName,
                'monthly' => $monthly
            ];
        }

        // Pagination logic
        $totalItems = count($itemArr);
        $curPage = $selectedPage;
        if ($curPage < 0) $curPage = 0;
        if ($curPage >= $totalItems) $curPage = $totalItems - 1;

        // Render Form and Table
        ?>
        <form method="post" id="yearly-stock-search-form" autocomplete="off" class="no-print" style="margin-bottom:0;">
            <div class="container-fluid px-2" style="margin-bottom:8px;">
                <div class="row gx-2 gy-2 mb-1">
                    <?php
                    echo '<div class="col" style="display:flex;align-items:center;">';
                    echo '<label style="margin-right:8px;min-width:38px;">Item</label>';
                    echo '<select class="form-select input-sm" name="item_search" id="item-search-dd" style="width:auto;min-width:190px;" title="Item">';
                    echo '<option value="ALL"' . (($itemSearch == 'ALL' || $itemSearch == '') ? ' selected' : '') . '>ALL</option>';
                    foreach ($itemArr as $item) {
                        echo '<option value="' . htmlspecialchars($item['item_id']) . '"' . ($itemSearch == $item['item_id'] ? " selected" : "") . '>' . htmlspecialchars($item['item_name']) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '<div class="col-auto" style="display:flex;align-items:center;gap:8px;">';
                    echo '<button type="button" class="btn btn-default" id="print-btn">Print</button>';
                    echo '<button type="button" class="btn btn-default" id="pdf-btn">PDF</button>';
                    echo '<button type="button" class="btn btn-default" id="excel-btn">Excel</button>';
                    echo '</div>';
                    ?>
                </div>
            </div>
            <input type="hidden" name="page" id="item-page" value="<?php echo $curPage; ?>" />
        </form>
        <?php

        // Only show one item per page
        if ($curPage >= 0 && $curPage < count($itemArr)) {
            $itemRow = $results[0]; // results contains only the selected item
            $curDate = date('d/m/Y');
            $fromDateLabel = htmlspecialchars($fromDate);
            $toDateLabel = htmlspecialchars($toDate);

            echo "<div class='stock-report-section'>";
            echo "<div style='display:flex;justify-content:space-between;font-size:13px;'><div>$curDate</div><div><b>Cold Storage</b></div><div style='text-align:right;'>Page ".($curPage+1)." of ".count($itemArr)."</div></div>";
            echo "<div class='statement-title'>YEARLY STOCK REPORT </div>";
            echo "<table class='stock-report-table'>";
            echo "<tr><th>Month</th><th>Opening Qty.</th><th>Inward Qty.</th><th>Outward Qty.</th><th>Balance Qty.</th></tr>";
            echo "<tr><td colspan='5' class='item-head'><a href='#' style='color:#0047b3;text-decoration:underline;cursor:pointer;'>".htmlspecialchars($itemRow['item_name'])."</a></td></tr>";
            foreach ($itemRow['monthly'] as $m) {
                echo "<tr>
                    <td style='text-align:center;'>".htmlspecialchars($m['label'])."</td>
                    <td style='text-align:center;'>".(floatval($m['opening'])>0?number_format($m['opening'],2):'--')."</td>
                    <td style='text-align:center;'>".(floatval($m['inward'])>0?number_format($m['inward'],2):'--')."</td>
                    <td style='text-align:center;'>".(floatval($m['outward'])>0?number_format($m['outward'],2):'--')."</td>
                    <td style='text-align:center;'>".(floatval($m['balance'])>0?number_format($m['balance'],2):'--')."</td>
                </tr>";
            }
            echo "</table>";

            // Add pagination buttons below the table with right alignment (CORRECTED)
            echo "<div class='row'>
                    <div class='col text-end' style='margin-top:10px;'> <!-- Changed to text-end for right alignment -->
                        <button class='btn btn-primary' id='prev-item' " . ($curPage <= 0 ? 'disabled' : '') . ">&lt; Previous</button>
                        <button class='btn btn-primary' id='next-item' " . ($curPage >= count($itemArr) - 1 ? 'disabled' : '') . ">Next &gt;</button>
                        <span style='font-size:13px; margin-left:10px;'>Page " . ($curPage + 1) . " of " . count($itemArr) . "</span>
                    </div>
                </div>";
            echo "</div>";
        }
        ?>
        <style>
        .stock-report-section { margin-bottom:10px; }
        .stock-report-table th, .stock-report-table td { padding:2px 7px; }
        .stock-report-table th { border-bottom:1px solid #000; text-align: center;}
        .stock-report-table tr:not(:last-child) td { border-bottom:1px dotted #bbb;}
        .stock-report-table { width:100%; border-collapse:collapse; font-size:15px;}
        .statement-title { text-align:center;font-weight:bold;font-size:17px;margin:0 0 3px 0;}
        .item-head { font-size:16px;font-weight:bold;text-align:center;text-decoration:underline;color:#0047b3;margin:0 0 6px 0;}
        .btn-primary {
            padding: 5px 15px;
            font-size: 14px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .text-end {
            text-align: right !important;
        }
        @media print {
            .no-print, #prev-item, #next-item { display:none !important; }
            .stock-report-section { page-break-after:always; }
        }
        </style>
        <?php
    }
}
$_bll_yearly = new bll_yearly_stock_report_master();
?>