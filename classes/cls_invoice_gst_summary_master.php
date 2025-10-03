<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_rentinvoicemaster {
    public function pageSearch() {
        global $_dbh;
        global $canUpdate;
        global $canDelete;

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

        // SQL with invoice_type from view_invoice_type
        $sql = "SELECT 
            (SELECT v.value FROM view_invoice_type v WHERE v.id = t.invoice_type) as invoice_type,
            t.invoice_no, t.invoice_date, t7.customer as val7,
            (SELECT t8.customer FROM tbl_inward_master ti
                LEFT JOIN tbl_customer_master t8 ON ti.broker = t8.customer_id
                WHERE ti.company_id = t.company_id LIMIT 1) as broker_name,
            (SELECT t8.gstin FROM tbl_inward_master ti
                LEFT JOIN tbl_customer_master t8 ON ti.broker = t8.customer_id
                WHERE ti.company_id = t.company_id LIMIT 1) as customer_gstin,
            tc.city_name as customer_city,
            ts.state_name as customer_state,
            t.hsn_code,
            t.basic_amount,
            t.other_expense1,
            (t.basic_amount + t.other_expense1) as value_of_goods,
            t.sgst,
            t.sgst_amount,
            t.cgst,
            t.cgst_amount,
            t.igst,
            t.igst_amount,
            t.net_amount, t.rent_invoice_id
            FROM tbl_rent_invoice_master t
            INNER JOIN tbl_customer_master t7 ON t.customer = t7.customer_id
            LEFT JOIN tbl_city_master tc ON t7.city_id = tc.city_id
            LEFT JOIN tbl_state_master ts ON t7.state_id = ts.state_id
            WHERE t.company_id = :company_id
            AND t.invoice_date BETWEEN :from_date AND :to_date";

        try {
            $stmt = $_dbh->prepare($sql);
            $stmt->execute([
                'company_id' => COMPANY_ID,
                'from_date' => $fromDateSql,
                'to_date' => $toDateSql
            ]);

            $headers = [
                'Invoice Type',
                'Invoice No',
                'Invoice Date',
                'Customer',
                'Broker',
                'GSTIN',  
                'City',
                'State',
                'HSN Code',
                'Basic Amount',
                'Other Expense',
                'Value of Goods',
                'SGST%',
                'SGST Amount',
                'CGST%',
                'CGST Amount',
                'IGST%',
                'IGST Amount',
                'Net Amount'
            ];

            // Only render date filter
            render_date_filter($fromDate, $toDate, 'search-invoice-date-from', 'search-invoice-date-to', 'search-date-btn', 'invoice-gst-summary-date-filter');

            echo '<div class="row gx-2 gy-1 align-items-center invoice-gst-summary-search-row" id="search-filters">';
            // ONLY date filter fields
            echo '</div>';

            echo '<table id="searchMaster" class="invoice-gst-summary-table ui celled table display">
                <thead><tr>';
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($stmt as $_rs) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($_rs["invoice_type"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["invoice_no"]) . '</td>';
                echo '<td>' . (empty($_rs["invoice_date"]) ? '' : date("d/m/Y", strtotime($_rs["invoice_date"]))) . '</td>';
                echo '<td>' . htmlspecialchars($_rs["val7"]) . '</td>';
                echo '<td>' . htmlspecialchars($_rs["broker_name"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["customer_gstin"] ?? 'N/A') . '</td>';  
                echo '<td>' . htmlspecialchars($_rs["customer_city"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["customer_state"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["hsn_code"] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["basic_amount"]) . '</td>';
                echo '<td>' . htmlspecialchars($_rs["other_expense1"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["value_of_goods"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["sgst"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["sgst_amount"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["cgst"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["cgst_amount"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["igst"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["igst_amount"] ?? '0.00') . '</td>';
                echo '<td>' . htmlspecialchars($_rs["net_amount"]) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">An error occurred while fetching data. Please try again later.</div>';
            error_log("Database Error in pageSearch(): " . $e->getMessage());
        }
    }
}

$_bll = new bll_rentinvoicemaster();
?>