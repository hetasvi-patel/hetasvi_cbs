<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_lot_statement_master {
    public function pageSearch() {
        global $_dbh;

        // --- Date logic ---
        $defaultFromDate = "";
        $defaultToDate = "";
        $companyYearId = defined('COMPANY_YEAR_ID') ? COMPANY_YEAR_ID : (isset($_SESSION['company_year_id']) ? $_SESSION['company_year_id'] : null);

        if ($companyYearId) {
            $stmtYear = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
            $stmtYear->execute([$companyYearId]);
            $rowYear = $stmtYear->fetch(PDO::FETCH_ASSOC);
            if ($rowYear) {
                $startDate = strtotime($rowYear["start_date"]);
                $month = date('m', $startDate);
                $day = date('d', $startDate);
                $defaultFromDate = "{$day}/{$month}/2018";
                $defaultToDate = date('d/m/Y', strtotime($rowYear["end_date"]));
            }
        }
        if (empty($defaultFromDate) || empty($defaultToDate)) {
            $currentYear = date('Y');
            if (date('m') < 4) $currentYear--;
            $defaultFromDate = "01/04/2018";
            $defaultToDate = "31/03/" . ($currentYear + 1);
        }

        // Only POST: set values, else (GET/refresh) reset all to default
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fromDate = $_POST['from_date'] ?? $defaultFromDate;
            $toDate = $_POST['to_date'] ?? $defaultToDate;
            $partySearch = $_POST['party_search'] ?? '';
            $lotSearch = $_POST['lot_search'] ?? '';
            $itemSearch = $_POST['item_search'] ?? '';
            $brokerSearch = $_POST['broker_search'] ?? '';
        } else {
            $fromDate = $defaultFromDate;
            $toDate = $defaultToDate;
            $partySearch = '';
            $lotSearch = '';
            $itemSearch = '';
            $brokerSearch = '';
        }

        // Convert to SQL format
        $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate)))
            : date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
        $toDateSql = !empty($toDate) && strtotime(str_replace('/', '-', $toDate))
            ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate)))
            : date('Y-m-d', strtotime(str_replace('/', '-', $defaultToDate)));

        // --- SQL ---
        $sql = "SELECT 
            im.inward_no, 
            im.inward_date, 
            cm1.customer AS broker, 
            id.lot_no, 
            itm.item_name AS item_name, 
            id.marko, 
            id.inward_qty, 
            id.inward_wt AS inward_weight, 
            pum.packing_unit_name AS unit_name, 
            om.outward_no, 
            om.outward_date, 
            od.out_qty AS outward_qty, 
            od.out_wt AS outward_weight,
            om.vehicle_no, 
            cm2.customer AS delivery_to
            FROM tbl_inward_master im
            LEFT JOIN tbl_inward_detail id ON im.inward_id = id.inward_id
            LEFT JOIN tbl_item_master itm ON id.item = itm.item_id
            LEFT JOIN tbl_packing_unit_master pum ON id.packing_unit = pum.packing_unit_id
            LEFT JOIN tbl_customer_master cm1 ON im.broker = cm1.customer_id
            LEFT JOIN tbl_outward_detail od ON od.inward_detail_id = id.inward_detail_id
            LEFT JOIN tbl_outward_master om ON om.outward_id = od.outward_id
            LEFT JOIN tbl_customer_master cm2 ON om.delivery_to = cm2.customer_id
            WHERE im.company_id = :company_id
            AND im.inward_date BETWEEN :from_date AND :to_date";

        $params = [
            ':company_id' => COMPANY_ID,
            ':from_date' => $fromDateSql,
            ':to_date' => $toDateSql
        ];

        // Add dropdown filters to SQL
        if ($partySearch) {
            $sql .= " AND cm1.customer = :party_search";
            $params[':party_search'] = $partySearch;
        }
        if ($lotSearch) {
            $sql .= " AND id.lot_no = :lot_search";
            $params[':lot_search'] = $lotSearch;
        }
        if ($itemSearch) {
            $sql .= " AND itm.item_name = :item_search";
            $params[':item_search'] = $itemSearch;
        }
        if ($brokerSearch) {
            $sql .= " AND cm1.customer = :broker_search";
            $params[':broker_search'] = $brokerSearch;
        }

        $sql .= " ORDER BY im.inward_no, id.lot_no, om.outward_no";

        try {
            $stmt = $_dbh->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- Grouping for Designer View ---
            $groupedData = [];
            $partyArr = [];
            $lotArr = [];
            $itemArr = [];
            $brokerArr = [];
            $allLotArr = [];
            $stmtLots = $_dbh->query("SELECT DISTINCT lot_no FROM tbl_inward_detail WHERE lot_no IS NOT NULL AND lot_no <> '' ORDER BY lot_no");
            while ($rowLot = $stmtLots->fetch(PDO::FETCH_ASSOC)) {
                $allLotArr[] = $rowLot['lot_no'];
            }
            foreach ($rows as $row) {
                $inwardNo = $row['inward_no'] ?? '';
                $lotNo = $row['lot_no'] ?? '';
                $itemName = $row['item_name'] ?? '';
                $broker = $row['broker'] ?? '';
                $party = $row['broker'] ?? '';

                if ($party != '' && !in_array($party, $partyArr)) $partyArr[] = $party;
                if ($lotNo != '' && !in_array($lotNo, $lotArr)) $lotArr[] = $lotNo;
                if ($itemName != '' && !in_array($itemName, $itemArr)) $itemArr[] = $itemName;
                if ($broker != '' && !in_array($broker, $brokerArr)) $brokerArr[] = $broker;

                if (!isset($groupedData[$inwardNo])) {
                    $groupedData[$inwardNo] = [
                        'inward_date' => $row['inward_date'] ?? '',
                        'broker'      => $row['broker'] ?? '',
                        'party'       => $row['broker'] ?? '', 
                        'lots'        => []
                    ];
                }
                if (!isset($groupedData[$inwardNo]['lots'][$lotNo])) {
                    $groupedData[$inwardNo]['lots'][$lotNo] = [
                        'item_name'   => $row['item_name'] ?? '',
                        'variety'     => $row['marko'] ?? '',
                        'inward_qty'  => $row['inward_qty'] ?? '',
                        'inward_weight' => $row['inward_weight'] ?? '',
                        'unit'        => $row['unit_name'] ?? '',
                        'outwards'    => []
                    ];
                }
                $groupedData[$inwardNo]['lots'][$lotNo]['outwards'][] = [
                    'out_no'      => $row['outward_no'] ?? '',
                    'out_date'    => $row['outward_date'] ?? '',
                    'out_qty'     => $row['outward_qty'] ?? '',
                    'out_weight'  => $row['outward_weight'] ?? '',
                    'veh_no'      => $row['vehicle_no'] ?? '',
                    'del_to'      => $row['delivery_to'] ?? ''
                ];
            }

            // --- Top Panel: Date filters and 4 Dropdown Filters (NO LABELS) ---
            ?>
  <!-- Top Panel: Date filters first (NO LABELS) -->
<!-- Top Panel: Date filters with inline labels -->
<form method="post" id="lot-statement-search-form" autocomplete="off" class="no-print">
    <div class="row gx-2 gy-1 align-items-center" id="dropdown-filters" style="margin-bottom:12px;">
        <div class="col-auto d-flex align-items-center">
            <label for="search-lot-date-from" style="margin-right: 5px; white-space: nowrap;">From</label>
            <input type="text" class="form-control date-filter datepicker" id="search-lot-date-from" name="from_date" placeholder="From Date" value="<?php echo htmlspecialchars($fromDate ?? ''); ?>" autocomplete="off" style="width:120px;" />
        </div>
        <div class="col-auto d-flex align-items-center">
            <label for="search-lot-date-to" style="margin-right: 5px; white-space: nowrap;">To</label>
            <input type="text" class="form-control date-filter datepicker" id="search-lot-date-to" name="to_date" placeholder="To Date" value="<?php echo htmlspecialchars($toDate ?? ''); ?>" autocomplete="off" style="width:120px;" />
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary" id="search-date-btn">Search</button>
        </div>
    </div>

    <!-- Dropdown Filters below date fields (NO LABELS) -->
    <div class="row gx-2 gy-1 align-items-center" style="margin-bottom:12px;">
        <div class="col-auto">
            <select class="form-select input-sm" id="party-filter" name="party_search" style="min-width:170px;" title="Party" autocomplete="off">
                <option value="">All Parties</option>
                <?php foreach ($partyArr as $party) {
                    echo '<option value="' . htmlspecialchars($party) . '"'.($partySearch==$party?" selected":"").'>' . htmlspecialchars($party) . '</option>';
                } ?>
            </select>
        </div>
        <div class="col-auto">
            <select class="form-select input-sm" id="lot-filter" name="lot_search" style="min-width:170px;" title="Lot No" autocomplete="off">
                <option value="">All Lot Nos</option>
                <?php foreach ($allLotArr as $lot) {
                    echo '<option value="' . htmlspecialchars($lot) . '"'.($lotSearch==$lot?" selected":"").'>' . htmlspecialchars($lot) . '</option>';
                } ?>
            </select>
        </div>
        <div class="col-auto">
            <select class="form-select input-sm" id="item-filter" name="item_search" style="min-width:170px;" title="Item" autocomplete="off">
                <option value="">All Items</option>
                <?php foreach ($itemArr as $item) {
                    echo '<option value="' . htmlspecialchars($item) . '"'.($itemSearch==$item?" selected":"").'>' . htmlspecialchars($item) . '</option>';
                } ?>
            </select>
        </div>
        <div class="col-auto">
            <select class="form-select input-sm" id="broker-filter" name="broker_search" style="min-width:170px;" title="Broker" autocomplete="off">
                <option value="">All Brokers</option>
                <?php foreach ($brokerArr as $broker) {
                    echo '<option value="' . htmlspecialchars($broker) . '"'.($brokerSearch==$broker?" selected":"").'>' . htmlspecialchars($broker) . '</option>';
                } ?>
            </select>
        </div>
    </div>

    <!-- Export Buttons below filters -->
    <div class="row mt-2 no-print">
        <div class="col-auto">
            <button id="btn-print" class="btn btn-default">Print</button>
            <button id="btn-pdf" class="btn btn-default">PDF</button>
            <button id="btn-excel" class="btn btn-default">Excel</button>
        </div>
    </div>
</form>
      

            <!-- Designer Grouped View (below the panel, always visible) -->
            <style>
            .lot-statement-section { page-break-inside: avoid; margin-bottom: 30px; }
            .lot-statement-header { font-size: 15px; margin-bottom: 2px; }
            .lot-statement-header span { display: inline-block; min-width: 230px; margin-right: 20px; }
            .lot-statement-table { border-collapse: collapse; width: 100%; margin-bottom: 4px; font-size: 15px; }
            .lot-statement-table th, .lot-statement-table td { padding: 2px 5px; }
            .lot-totals { font-weight: bold; }
            .statement-title { text-align: center; font-weight: bold; font-size: 17px; margin: 6px 0; }
            .top-bar { width:100%;display:flex;justify-content:space-between;align-items:center; font-size:13px; }
            hr.hr1 { border:0;border-top:2px solid #000;margin:0 0 2px 0; }
            hr.hr2 { border:0;border-top:5px solid #000;margin:2px 0 6px 0; }
            .hr-outqty { width: 100%; border: none; border-top: 1px solid #000; margin: 0; }
            .outqty-cell { padding: 0; text-align: center; }
            @media print {
                .lot-statement-section { page-break-inside: avoid; }
                .no-print { display: none; }
            }
                @media print {
  /* Hide unwanted elements */
  .no-print { display: none !important; }

  /* Allow natural page breaks everywhere */
  body, html, .content-wrapper, .container-fluid, .box-body,
  .lot-statement-section, .lot-statement-table, table, tr, td, th {
    page-break-inside: auto !important;
    page-break-before: auto !important;
    page-break-after: auto !important;
    overflow: visible !important;
  }

  /* Remove any forced avoid rules */
  .lot-statement-section, .lot-statement-table, table, tr, td, th {
    page-break-inside: auto !important;
  }

  /* Optional: Reduce margin/padding for title and section headers for print */
  .statement-title, .top-bar, .lot-statement-header {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    padding: 0 !important;
  }

  /* Remove extra margin below sections for compact print */
  .lot-statement-section {
    margin-bottom: 10px !important;
  }

  /* Fix odd table breaks (sometimes helps) */
  table {
    width: 100% !important;
  }
}
                
                
            </style>
            <?php
             echo "<div class='lot-statement-content'>";
            $curDate = date('d/m/Y');
            echo "<div class='top-bar'><div>$curDate</div><div><b>Inter Continental Enterprise (Cold Storage) Pvt. Ltd.</b></div><div>Page 1</div></div>";
            echo "<div class='statement-title'>LOT STATEMENT FROM $fromDate TO $toDate</div>";
            echo "<hr class='hr1'>";

            if (empty($groupedData)) {
                echo "<div>No records found</div>";
            } else {
                foreach ($groupedData as $inwardNo => $header) {
                    echo "<div class='lot-statement-section'>";
                    echo "<div class='lot-statement-header'>
                        <span>Inward No. : <b>" . htmlspecialchars($inwardNo) . "</b></span>
                        <span>Inward Date : " . ($header['inward_date'] ? date('d/m/Y', strtotime($header['inward_date'])) : '') . "</span><br>
                        <span>Party : <b>" . htmlspecialchars($header['party']) . "</b></span>
                        <span>Broker : <b>" . htmlspecialchars($header['broker']) . "</b></span>
                    </div>";
                    // Plain hr below Party/Broker
                    echo "<hr>";
                    echo "<table class='lot-statement-table'>";
                    echo "<tr>
                        <th>Lot No.</th>
                        <th>Item Name</th>
                        <th>Variety</th>
                        <th>In. Qty.</th>
                        <th>In. Wt.</th>
                        <th>Unit</th>
                        <th>Out No.</th>
                        <th>Out Date</th>
                        <th>Out Qty.</th>
                        <th>Out Wt.</th>
                        <th>Veh. No.</th>
                        <th>Del. To</th>
                    </tr>";
                    // Plain hr below header row
                    echo "<tr style='height:7px;'><td colspan='12' style='padding:0;'><hr></td></tr>";
                    foreach ($header['lots'] as $lotNo => $lot) {
                        $rowspan = count($lot['outwards']) ?: 1;
                        $first = true;
                        $totalOutQty = 0; // Calculate total out qty
                        foreach ($lot['outwards'] as $out) {
                            $totalOutQty += floatval($out['out_qty'] ?? 0);
                            echo "<tr>";
                            if ($first) {
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lotNo ?? '') . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lot['item_name'] ?? '') . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lot['variety'] ?? '') . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lot['inward_qty'] ?? '') . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lot['inward_weight'] ?? '') . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . htmlspecialchars($lot['unit'] ?? '') . '</td>';
                                $first = false;
                            }
                            echo '<td>' . htmlspecialchars($out['out_no'] ?? '') . '</td>';
                            echo '<td>' . ($out['out_date'] ? date('d/m/Y', strtotime($out['out_date'])) : '') . '</td>';
                            echo '<td>' . htmlspecialchars($out['out_qty'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($out['out_weight'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($out['veh_no'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($out['del_to'] ?? '') . '</td>';
                            echo '</tr>';
                        }
                        $inwardQty = floatval($lot['inward_qty'] ?? 0);
                        $stockQty = max(0, $inwardQty - $totalOutQty);
                        echo "<tr>
                            <td colspan='8'></td>
                            <td class='outqty-cell'><hr class='hr-outqty' style='width:100%;max-width:120px;'></td>
                            <td colspan='3'></td>
                        </tr>";
                        echo "<tr class='lot-totals'><td colspan='8'></td>
                              <td>Total Out Qty.: " . htmlspecialchars(number_format($totalOutQty, 2)) . "</td>
                              <td colspan='3'></td></tr>";
                        echo "<tr class='lot-totals'><td colspan='8'></td>
                              <td>Stock Qty.: " . htmlspecialchars(number_format($stockQty, 2)) . "</td>
                              <td colspan='3'></td></tr>";
                        echo "<tr><td colspan='12' style='padding:0;'><hr></td></tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                    
                }
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Database Error: {$e->getMessage()}</div>";
        }
    }
}
$_bll = new bll_lot_statement_master();
?>