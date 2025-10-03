<?php
include_once(__DIR__ . "/../config/connection.php");

class bll_rentinvoicemaster
{
    public function getItemList()
    {
        global $_dbh;
        $sql = "SELECT DISTINCT t11.item_name 
                FROM tbl_outward_detail t1
                LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id
                LEFT JOIN tbl_item_master t11 ON t9.item = t11.item_id
                WHERE t11.item_name IS NOT NULL
                ORDER BY t11.item_name";
        $stmt = $_dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUnitList()
    {
        global $_dbh;
        $sql = "SELECT DISTINCT t12.packing_unit_name 
                FROM tbl_outward_detail t1
                LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id
                LEFT JOIN tbl_packing_unit_master t12 ON t9.packing_unit = t12.packing_unit_id
                WHERE t12.packing_unit_name IS NOT NULL
                ORDER BY t12.packing_unit_name";
        $stmt = $_dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function pageSearch()
    {
        global $_dbh;

        $tillDate = (!empty($_REQUEST['till_date']))
            ? $_REQUEST['till_date']
            : date('d/m/Y');

        if (!empty($_REQUEST['till_date']) && strtotime(str_replace('/', '-', $_REQUEST['till_date']))) {
            $tillDateSql = date('Y-m-d', strtotime(str_replace('/', '-', $_REQUEST['till_date'])));
        } else {
            $tillDateSql = date('Y-m-d');
        }

        if (COMPANY_ID != ADMIN_COMPANY_ID) {
            $where_condition = " AND t.company_id = " . (int)COMPANY_ID;
        } else {
            $where_condition = "";
        }
        $where_condition .= " AND t.outward_date <= '" . $tillDateSql . "'";

        $select_columns = trim(preg_replace('/\s+/', ' ',
            "t11.item_name AS item_name, t12.packing_unit_name AS unit_name,
             t9.inward_qty AS stock_qty, t9.inward_wt AS stock_weight, t11.market_rate,
             (t9.inward_wt * t11.market_rate) AS stock_valuation"
        ));
        $from_clause = trim(preg_replace('/\s+/', ' ',
            "tbl_outward_master t
             INNER JOIN tbl_outward_detail t1 ON t.outward_id = t1.outward_id
             LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id
             LEFT JOIN tbl_item_master t11 ON t9.item = t11.item_id
             LEFT JOIN tbl_packing_unit_master t12 ON t9.packing_unit = t12.packing_unit_id"
        ));

        $sql = "CALL csms_search_detail(:cols, :fromClause, :whereCond)";
        $stmt = $_dbh->prepare($sql);
        $stmt->bindValue(':cols', $select_columns, PDO::PARAM_STR);
        $stmt->bindValue(':fromClause', $from_clause, PDO::PARAM_STR);
        $stmt->bindValue(':whereCond', $where_condition, PDO::PARAM_STR);

        $itemList = $this->getItemList();
        $unitList = $this->getUnitList();

        // 8-box row: 1) Date, 2) Item dropdown, 3) Unit dropdown, 4-8) empty
        echo '<!-- Filter row -->
        <div class="container-fluid px-2" style="margin-bottom:8px;">
          <div class="row gx-2 gy-1 align-items-center" id="search-filters">';

        // 1st box: Date
        echo '<div class="col" style="min-width:170px;">';
        echo '<input type="text" class="form-control date-filter" placeholder="Till Date" id="till_date" value="' . htmlspecialchars($tillDate) . '" />';
        echo '</div>';

        // 2nd box: Item Name dropdown
        echo '<div class="col" style="min-width:170px;">';
        echo '<select class="form-select" id="item_filter" data-index="0">';
        echo '<option value="">All Items</option>';
        foreach ($itemList as $iname) {
            echo '<option value="' . htmlspecialchars($iname) . '">' . htmlspecialchars($iname) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        // 3rd box: Unit Name dropdown
        echo '<div class="col" style="min-width:170px;">';
        echo '<select class="form-select" id="unit_filter" data-index="1">';
        echo '<option value="">All Units</option>';
        foreach ($unitList as $uname) {
            echo '<option value="' . htmlspecialchars($uname) . '">' . htmlspecialchars($uname) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        // 4th to 8th box: empty for alignment
        for ($i = 0; $i < 5; $i++) {
            echo '<div class="col">&nbsp;</div>';
        }

        echo '</div></div>';

        // Table
        echo '<table id="searchMaster" class="ui celled table display">
        <thead><tr>
            <th>Item Name</th>
            <th>Unit Name</th>
            <th>Stock Qty</th>
            <th>Stock Weight</th>
            <th>Market Rate</th>
            <th>Stock Valuation (Rs.)</th>
        </tr></thead><tbody>';

        $j = 0;
        try {
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $j++;
                echo "<tr>
                        <td>" . htmlspecialchars($row["item_name"]) . "</td>
                        <td>" . htmlspecialchars($row["unit_name"]) . "</td>
                        <td>" . htmlspecialchars($row["stock_qty"]) . "</td>
                        <td>" . htmlspecialchars($row["stock_weight"]) . "</td>
                        <td>" . htmlspecialchars($row["market_rate"]) . "</td>
                        <td>" . htmlspecialchars($row["stock_valuation"]) . "</td>
                      </tr>";
            }
        } catch (PDOException $e) {
            echo '<tr><td colspan="6">Error fetching data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            echo '</tbody></table>';
            return;
        }
        if ($j === 0) {
            echo '<tr><td colspan="6">No records available.</td></tr>';
        }
        echo '</tbody></table>';
    }
}

$_bll = new bll_rentinvoicemaster();