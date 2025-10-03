<?php
include_once(__DIR__ . "/../config/connection.php");

class mdl_itempreservationchargeslist
{
    public $item_preservation_price_list_id;
    public $customer_wise_item_preservation_price_list_id;
    public $item_id;
    public $customer_id;
    public $rent_per_kg_month;
    public $rent_per_kg_season;
    public $created_date;
    public $created_by;
    public $modified_date;
    public $modified_by;
    public $company_id;
    public $company_year_id;
    public $transactionmode;
    public $array_itemdetail;
    public $array_itemdelete;
}

class bll_itempreservationchargeslist
{
    public $_mdl;
    public function __construct()
    {
        $this->_mdl = new mdl_itempreservationchargeslist();
    }

    // Get all packing units for the grid
    public function getAllPackingUnits()
    {
        global $_dbh, $tbl_packing_unit_master;
        $sql = "SELECT * FROM $tbl_packing_unit_master WHERE status = 1 ORDER BY packing_unit_name";
        return $_dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch details for item preservation charges (like detail pageSearch from detail BLL)
    public function getDetailsByMasterId($master_id, $filter_type = 'item_wise')
    {
        global $_dbh, $tbl_item_preservation_price_list_detail, $tbl_customer_wise_item_preservation_price_list_detail;

        if ($filter_type == 'item_wise') {
            $sql = "SELECT * FROM $tbl_item_preservation_price_list_detail WHERE item_preservation_price_list_id = ?";
        } else {
            $sql = "SELECT * FROM $tbl_customer_wise_item_preservation_price_list_detail WHERE customer_wise_item_preservation_price_list_id = ?";
        }
        $stmt = $_dbh->prepare($sql);
        $stmt->execute([$master_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pageSearch()
    {
        global $_dbh, $tbl_item_preservation_price_list_master, $tbl_customer_wise_item_preservation_price_list_master;
        global $tbl_item_master, $tbl_customer_master, $tbl_user_master;

        $filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'item_wise';
        $company_id = COMPANY_ID;
        $company_year_id = COMPANY_YEAR_ID;

        // Build grid header
        $_grid = '
        <div class="box-body">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="margin-right: 20px;">
                    <input type="radio" name="filter_type" value="item_wise" ' . ($filter_type == 'item_wise' ? 'checked' : '') . ' onchange="reloadGrid()">
                    Item Wise
                </label>
                <label>
                    <input type="radio" name="filter_type" value="customer_wise" ' . ($filter_type == 'customer_wise' ? 'checked' : '') . ' onchange="reloadGrid()">
                    Customer Wise
                </label>
            </div>
            <table id="searchMaster" class="ui celled table display">
                <thead>
                    <tr>';
        if ($filter_type == 'customer_wise') {
            $_grid .= '<th>Customer</th>';
        }
        $_grid .= '
                        <th>Item</th>
                        <th>Rent / Kg./Month</th>
                        <th>Rent / Kg./Season</th>
                        <th>Packing Unit Name</th>
                        <th>Rent/Month/Qty</th>
                        <th>Season Rent/Month/Qty</th>
                    </tr>
                </thead>
                <tbody>';

        $j = 0;
        try {
            $packing_units = $this->getAllPackingUnits();

            if ($filter_type == 'item_wise') {
                // Get item-wise masters
                $sql = "SELECT t.item_preservation_price_list_id, t.item_id, t.rent_per_kg_month, t.rent_per_kg_season, im.item_name
                        FROM $tbl_item_preservation_price_list_master t
                        LEFT JOIN $tbl_item_master im ON t.item_id = im.item_id
                        WHERE t.company_id = $company_id AND t.company_year_id = $company_year_id";
                $masters = $_dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                foreach ($masters as $_rs) {
                    $details = $this->getDetailsByMasterId($_rs['item_preservation_price_list_id'], 'item_wise');
                    $details_by_unit = [];
                    foreach ($details as $d) {
                        $details_by_unit[$d['packing_unit_id']] = $d;
                    }
                    foreach ($packing_units as $unit) {
                        $j++;
                        $_grid .= "<tr>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['item_name']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['rent_per_kg_month']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['rent_per_kg_season']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($unit['packing_unit_name']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars(isset($details_by_unit[$unit['packing_unit_id']]) ? $details_by_unit[$unit['packing_unit_id']]['rent_per_qty_month'] : '0.00') . "</td>";
                        $_grid .= "<td>" . htmlspecialchars(isset($details_by_unit[$unit['packing_unit_id']]) ? $details_by_unit[$unit['packing_unit_id']]['rent_per_qty_season'] : '0.00') . "</td>";
                        $_grid .= "</tr>";
                    }
                }

                if ($j == 0) {
                    $_grid .= '<tr><td colspan="6">No records available.</td></tr>';
                }
            } else {
                // customer_wise
                $sql = "SELECT t.customer_wise_item_preservation_price_list_id, t.customer_id, t.item_id, t.rent_per_kg_month, t.rent_per_kg_season, c.customer, im.item_name
                        FROM $tbl_customer_wise_item_preservation_price_list_master t
                        INNER JOIN $tbl_customer_master c ON t.customer_id = c.customer_id
                        INNER JOIN $tbl_item_master im ON t.item_id = im.item_id
                        WHERE t.company_id = $company_id AND t.company_year_id = $company_year_id";
                $masters = $_dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                foreach ($masters as $_rs) {
                    $details = $this->getDetailsByMasterId($_rs['customer_wise_item_preservation_price_list_id'], 'customer_wise');
                    $details_by_unit = [];
                    foreach ($details as $d) {
                        $details_by_unit[$d['packing_unit_id']] = $d;
                    }
                    foreach ($packing_units as $unit) {
                        $j++;
                        $_grid .= "<tr>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['customer']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['item_name']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['rent_per_kg_month']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($_rs['rent_per_kg_season']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars($unit['packing_unit_name']) . "</td>";
                        $_grid .= "<td>" . htmlspecialchars(isset($details_by_unit[$unit['packing_unit_id']]) ? $details_by_unit[$unit['packing_unit_id']]['rent_per_qty_month'] : '0.00') . "</td>";
                        $_grid .= "<td>" . htmlspecialchars(isset($details_by_unit[$unit['packing_unit_id']]) ? $details_by_unit[$unit['packing_unit_id']]['rent_per_qty_season'] : '0.00') . "</td>";
                        $_grid .= "</tr>";
                    }
                }

                if ($j == 0) {
                    $_grid .= '<tr><td colspan="7">No records available.</td></tr>';
                }
            }
        } catch (PDOException $e) {
            error_log("Error in pageSearch: " . $e->getMessage());
            $_grid .= $filter_type == 'customer_wise'
                ? '<tr><td colspan="7">Error fetching data.</td></tr>'
                : '<tr><td colspan="6">Error fetching data.</td></tr>';
        }

        $_grid .= "</tbody></table></div>";
        echo $_grid;
    }
}

// Instantiate BLL and handle direct action calls
$_bll = new bll_itempreservationchargeslist();

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    if ($action === 'fetch_units' && isset($_POST['item_id'])) {
        $_bll->fetchUnits();
        exit;
    }
    $_bll->$action();
}
?>