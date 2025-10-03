<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_rentinvoicemaster                           
{   
public function pageSearch()
{
    global $_dbh;

    // 1. Financial year date defaults
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

    // 2. Request filters
    $fromDate = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : $defaultFromDate;
    $toDate   = isset($_REQUEST['to_date'])   ? $_REQUEST['to_date']   : $defaultToDate;
    $invoiceType = isset($_REQUEST['invoice_type']) ? $_REQUEST['invoice_type'] : '';
    $invoiceFor = isset($_REQUEST['invoice_for']) ? $_REQUEST['invoice_for'] : '';
    $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';
    $item = isset($_REQUEST['item']) ? $_REQUEST['item'] : '';
    $storageDuration = isset($_REQUEST['storage_duration']) ? $_REQUEST['storage_duration'] : '';
    $per = isset($_REQUEST['per']) ? $_REQUEST['per'] : '';

    $fromDateSql = !empty($fromDate) && strtotime(str_replace('/', '-', $fromDate)) ? date('Y-m-d', strtotime(str_replace('/', '-', $fromDate))) : date('Y-m-d', strtotime(str_replace('/', '-', $defaultFromDate)));
    $toDateSql   = !empty($toDate)   && strtotime(str_replace('/', '-', $toDate))   ? date('Y-m-d', strtotime(str_replace('/', '-', $toDate)))   : date('Y-m-d', strtotime(str_replace('/', '-', $defaultToDate)));

    // 3. Dropdown data
    // Invoice Type (from view)
    $invoiceTypes = [];
    $stmtTypes = $_dbh->query("SELECT id, value FROM view_item_gst_type ORDER BY value");
    while ($row = $stmtTypes->fetch(PDO::FETCH_ASSOC)) {
        $invoiceTypes[$row['id']] = $row['value'];
    }
    // Invoice For (from view)
    $invoiceForArr = [];
    $stmtInvoiceFor = $_dbh->query("SELECT id, value FROM view_invoice_for ORDER BY value");
    while ($row = $stmtInvoiceFor->fetch(PDO::FETCH_ASSOC)) {
        $invoiceForArr[$row['id']] = $row['value'];
    }
    // Per/Rent Type (from view)
    $perTypes = [];
    $stmtPer = $_dbh->query("SELECT id, Label FROM view_rent_type ORDER BY value");
    while ($row = $stmtPer->fetch(PDO::FETCH_ASSOC)) {
        $perTypes[$row['id']] = $row['Label'];
    }
    // Storage Duration (from view)
    $storageDurations = [];
    $stmtSD = $_dbh->query("SELECT id, Label FROM view_storage_duration ORDER BY id");
    while ($row = $stmtSD->fetch(PDO::FETCH_ASSOC)) {
        $storageDurations[$row['id']] = $row['Label'];
    }
    // Customers
    $customers = [];
    $stmtCustomers = $_dbh->prepare("SELECT customer_id, customer FROM tbl_customer_master WHERE company_id = :company_id ORDER BY customer");
    $stmtCustomers->execute(['company_id' => COMPANY_ID]);
    while ($row = $stmtCustomers->fetch(PDO::FETCH_ASSOC)) $customers[] = $row;
    // Items
    $items = [];
    $stmtItems = $_dbh->query("SELECT item_id, item_name FROM tbl_item_master ORDER BY item_name");
    while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) $items[] = $row;

    // 4. Filter Row
    echo '<form method="post" id="invoice-summary-form">';

// Row 1: From Date, To Date, Search Button
echo '<div class="container-fluid px-2" style="margin-bottom:8px;">';
echo '<div class="row gx-2 gy-1 align-items-center">';
echo '<div class="col-auto"><label for="from_date" class="form-label mb-0">From</label></div>';
echo '<div class="col-auto"><input type="text" class="form-control date-filter" id="from_date" name="from_date" placeholder="From Date" value="' . htmlspecialchars($fromDate ?? '') . '"></div>';
echo '<div class="col-auto"><label for="to_date" class="form-label mb-0">To</label></div>';
echo '<div class="col-auto"><input type="text" class="form-control date-filter" id="to_date" name="to_date" placeholder="To Date" value="' . htmlspecialchars($toDate ?? '') . '"></div>';
echo '<div class="col-auto"><button type="submit" class="btn btn-primary">Search</button></div>';
echo '</div>';

// Row 2: All Dropdowns (put them in a new row below)
echo '<div class="row gx-2 gy-1 align-items-center mt-2">';
// Invoice Type
echo '<div class="col"><select class="form-select" name="invoice_type"><option value="">Select Invoice Type</option>';
foreach ($invoiceTypes as $id => $value) echo '<option value="'.$id.'"'.($invoiceType==$id?' selected':'').'>'.htmlspecialchars($value ?? '').'</option>';
echo '</select></div>';
// Invoice For
echo '<div class="col"><select class="form-select" name="invoice_for"><option value="">Select Invoice For</option>';
foreach ($invoiceForArr as $id => $value) echo '<option value="'.$id.'"'.($invoiceFor==$id?' selected':'').'>'.htmlspecialchars($value ?? '').'</option>';
echo '</select></div>';
// Customer
echo '<div class="col"><select class="form-select" name="customer"><option value="">Select Customer</option>';
foreach ($customers as $cust) echo '<option value="'.$cust['customer_id'].'"'.($customer==$cust['customer_id']?' selected':'').'>'.htmlspecialchars($cust['customer'] ?? '').'</option>';
echo '</select></div>';
// Item
echo '<div class="col"><select class="form-select" name="item"><option value="">Select Item</option>';
foreach ($items as $itm) echo '<option value="'.$itm['item_id'].'"'.($item==$itm['item_id']?' selected':'').'>'.htmlspecialchars($itm['item_name'] ?? '').'</option>';
echo '</select></div>';
// Storage Duration
echo '<div class="col"><select class="form-select" name="storage_duration"><option value="">Select Storage Duration</option>';
foreach ($storageDurations as $id => $label) echo '<option value="'.htmlspecialchars($id).'"'.($storageDuration==$id?' selected':'').'>'.htmlspecialchars($label).'</option>';
echo '</select></div>';
// Per
echo '<div class="col"><select class="form-select" name="per"><option value="">Select Per</option>';
foreach ($perTypes as $id => $label) echo '<option value="'.$id.'"'.($per==$id?' selected':'').'>'.htmlspecialchars($label ?? '').'</option>';
echo '</select></div>';
echo '</div>'; // end row 2

echo '</div>'; // end container-fluid
echo '</form>';

    // 5. Main SQL
    $sql = "SELECT t.invoice_no, t.invoice_date, v.value AS invoice_type_value, vf.value AS invoice_for_value, c.customer,
              d.inward_no, d.lot_no, d.inward_date, im.item_name AS item, d.marko,
              d.invoice_qty AS qty, d.wt_per_kg, d.rent_per_storage_duration AS rent, per_v.Label AS per_label,
              sd.Label AS storage_duration_label,
              d.charges_from AS bill_from, d.charges_to AS bill_to,
              CONCAT(IFNULL(d.actual_month,0),'M ',IFNULL(d.actual_day,0),'D') AS actual_storage,
              CONCAT(IFNULL(d.invoice_day,0),' Days') AS bill_for,
              d.status, d.invoice_amount AS amount
       FROM tbl_rent_invoice_master t
       INNER JOIN tbl_customer_master c ON t.customer = c.customer_id
       INNER JOIN tbl_rent_invoice_detail d ON t.rent_invoice_id = d.rent_invoice_id
       LEFT JOIN tbl_item_master im ON d.item = im.item_id
       LEFT JOIN view_item_gst_type v ON t.invoice_type = v.id
       LEFT JOIN view_invoice_for vf ON t.invoice_for = vf.id
       LEFT JOIN view_rent_type per_v ON d.rent_per = per_v.id
       LEFT JOIN view_storage_duration sd ON d.storage_duration = sd.id
       WHERE t.company_id = :company_id
         AND t.invoice_date BETWEEN :from_date AND :to_date";
    $params = [
        'company_id' => COMPANY_ID,
        'from_date' => $fromDateSql,
        'to_date' => $toDateSql
    ];
    if ($invoiceType != '') {
        $sql .= " AND t.invoice_type = :invoice_type";
        $params['invoice_type'] = $invoiceType;
    }
    if ($invoiceFor != '') {
        $sql .= " AND t.invoice_for = :invoice_for";
        $params['invoice_for'] = $invoiceFor;
    }
    if ($customer != '') {
        $sql .= " AND t.customer = :customer";
        $params['customer'] = $customer;
    }
    if ($item != '') {
        $sql .= " AND d.item = :item";
        $params['item'] = $item;
    }
    if ($storageDuration != '') {
        $sql .= " AND d.storage_duration = :storage_duration";
        $params['storage_duration'] = $storageDuration;
    }
    if ($per != '') {
        $sql .= " AND d.rent_per = :per";
        $params['per'] = $per;
    }
    $sql .= " ORDER BY t.invoice_date DESC, t.invoice_no DESC";

    // 6. Table Headers
         $headers = [
            'Invoice No.', 'Invoice Date', 'Invoice Type', 'Invoice for', 'Customer', 'Inward No.',
            'Lot No.', 'Inward Date', 'Item', 'Marko', 'Qty.', 'Weight (Kg.)', 'Rent', 'Per',
            'Storage Duration', 'Bill From', 'Bill To', 'Actual Storage', 'Bill for(Storage time)', 'Status', 'Amount'
        ];
        $colCount = count($headers);

        // 7. Table
        echo '<div class="table-responsive">';
        echo '<table id="searchMaster" class="table table-bordered table-striped table-hover display">';
        echo '<thead><tr>';
        foreach ($headers as $header) echo '<th>' . htmlspecialchars($header ?? '') . '</th>';
        echo '</tr></thead><tbody>';

        try {
            $stmt = $_dbh->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                echo '<tr>' . str_repeat('<td></td>', $colCount) . '</tr>';
            } else {
                foreach ($rows as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['invoice_no'] ?? '') . '</td>';
                    echo '<td>' . (!empty($row["invoice_date"]) ? date("d/m/Y", strtotime($row["invoice_date"])) : '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['invoice_type_value'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['invoice_for_value'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['customer'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['inward_no'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['lot_no'] ?? '') . '</td>';
                    echo '<td>' . (!empty($row["inward_date"]) ? date("d/m/Y", strtotime($row["inward_date"])) : '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['item'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['marko'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['qty'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['wt_per_kg'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['rent'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['per_label'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['storage_duration_label'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['bill_from'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['bill_to'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['actual_storage'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['bill_for'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['status'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row['amount'] ?? '') . '</td>';
                    echo '</tr>';
                }
            }
        } catch (PDOException $e) {
            echo '<tr>' . str_repeat('<td></td>', $colCount) . '</tr>';
        }

        echo '</tbody></table></div>';
    ?>
    <?php
}
}

$_bll=new bll_rentinvoicemaster();
?>