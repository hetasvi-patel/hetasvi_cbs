<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");
include("cls_rent_invoice_detail.php"); 
class mdl_rentinvoicemaster 
{   
    public $generator_table_layout;
    public $generator_fields_names;
    public $generator_fields_types;
    public $generator_field_scale;
    public $generator_dropdown_table;
    public $generator_label_column;
    public $generator_value_column;
    public $generator_where_condition;
    public $generator_default_value;
    public $generator_fields_labels;
    public $generator_field_display;
    public $generator_field_required;
    public $generator_allow_zero;
    public $generator_allow_minus;
    public $generator_chk_duplicate;
    public $generator_field_data_type;
    public $generator_field_is_disabled;
    public $generator_after_detail;
    protected $fields = [];

    public function __get($name) {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }
    public function __construct() {
        global $_dbh;
        global $tbl_generator_master;
        global $tbl_rent_invoice_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_rent_invoice_master);
        $select->execute();
        $row = $select->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $generator_options = json_decode($row["generator_options"]);
            if ($generator_options) {
                $this->generator_table_layout=$generator_options->table_layout;
                $this->generator_fields_names=$generator_options->field_name;
                $this->generator_fields_types=$generator_options->field_type;
                $this->generator_field_scale=$generator_options->field_scale;
                $this->generator_dropdown_table=$generator_options->dropdown_table;
                $this->generator_label_column=$generator_options->label_column;
                $this->generator_value_column=$generator_options->value_column;
                $this->generator_where_condition=$generator_options->where_condition;
                $this->generator_default_value=$generator_options->default_value;
                $this->generator_fields_labels=$generator_options->field_label;
                $this->generator_field_display=$generator_options->field_display;
                $this->generator_field_required=$generator_options->field_required;
                $this->generator_allow_zero=$generator_options->allow_zero;
                $this->generator_allow_minus=$generator_options->allow_minus;
                $this->generator_chk_duplicate=$generator_options->chk_duplicate;
                $this->generator_field_data_type=$generator_options->field_data_type;
                $this->generator_field_is_disabled=$generator_options->is_disabled;
                $this->generator_after_detail=$generator_options->after_detail;
            }
        }
    }

    /** FOR DETAIL **/
    public $_array_itemdetail;
        public $_array_itemdelete;
    /** \FOR DETAIL **/
                    
}

class bll_rentinvoicemaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_rentinvoicemaster(); 
        $this->_dal =new dal_rentinvoicemaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
        $_bllitem= new bll_rentinvoicedetail();
        if($this->_mdl->_transactionmode!="D")
        {
            
            if($this->_mdl->_rent_invoice_id >0 && !empty($this->_mdl->_array_itemdetail)) {
                    for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            
                            foreach($detailrow as $name=>$value) {
                                if($name!="storage_duration_id" && $name!="storage_duration_name" && $name!="gst_status") // added by BHUMITA 
                                    $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->rent_invoice_id = $this->_mdl->_rent_invoice_id;
                        $_bllitem->dbTransaction();
                    }
            }
            if(!empty($this->_mdl->_array_itemdelete)) {
                for($iterator= $this->_mdl->_array_itemdelete->getIterator();$iterator->valid();$iterator->next())
                {
                        $detailrow=$iterator->current();
                    if(is_array($detailrow)) {
                        foreach($detailrow as $name=>$value) {
                            $_bllitem->_mdl->{$name}=$value;
                        }
                    }
                    $_bllitem->_mdl->rent_invoice_id = $this->_mdl->_rent_invoice_id;
                    $_bllitem->dbTransaction();
                }
            }
        }
    /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_rent_invoice_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_rent_invoice_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_rent_invoice_master.php");
       }

    }
 
    public function fillModel()
    {
        $this->_dal->fillModel($this->_mdl);
    
    }
    public function pageSearch()
    {
        global $_dbh;
        global $canUpdate;
        global $canDelete;
        $company_query=str_replace("company_id","t.company_id",COMPANY_QUERY);
        
        $sql = "CALL csms_search_detail(
    't.*,
     t1.value as invoice_type,
     t6.value as debit_cash,
     t7.customer as customer,
     ct.city_name as customer_city,
     t9.hsn_code_name,
     t9.description as hsn_code,
     t17.value as tax_amount,
     u1.person_name as created_by,
     u2.person_name as modified_by',
    'tbl_rent_invoice_master t 
        LEFT JOIN view_invoice_type t1 ON t.invoice_type = t1.id 
        LEFT JOIN view_debit_cash t6 ON t.debit_cash = t6.id 
        LEFT JOIN tbl_customer_master t7 ON t.customer = t7.customer_id
        LEFT JOIN tbl_city_master ct ON t7.city_id = ct.city_id
        LEFT JOIN tbl_hsn_code_master t9 ON t.hsn_code = t9.hsn_code_id 
        LEFT JOIN view_tax_amount t17 ON t.tax_amount = t17.id
        LEFT JOIN tbl_user_master u1 ON t.created_by = u1.user_id
        LEFT JOIN tbl_user_master u2 ON t.modified_by = u2.user_id',
    '".$company_query."'
)";
        echo "<!-- Filter row -->
              <div class=\"row gx-2 gy-1 align-items-center\" id=\"search-filters\">";
        $k=0;$hstr="";$url_fieldname="";
        foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if(!in_array($fieldname,$this->_mdl->generator_field_display)) {
                continue;
            }
            if($fieldname=="company_id") {
                if(COMPANY_ID==ADMIN_COMPANY_ID) {
                    $k++;
                    $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));
                    $hstr.="<div class=\"col-auto\">";
                    $hstr.="<input type=\"text\" class=\"form-control\" placeholder=\"Search ".$label."\" data-index=\"".$k."\" />";
                    $hstr.="</div>";
                }
                continue;
            }
            if($fieldname==$url_fieldname) {
                continue;
            }
            if($this->_mdl->generator_fields_types[$i]=="file") {
                $url_fieldname=$fieldname."_url";
                continue;
            }
            $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));
            $extracls="";
            if($this->_mdl->generator_field_data_type[$i]=="datetime"
                || $this->_mdl->generator_field_data_type[$i]=="date"
                || $this->_mdl->generator_field_data_type[$i]=="timestamp") {
                $extracls.="date-filter";
            }

            if($fieldname=="customer") {
                // Customer filter
                $k++;
                $hstr.="<div class=\"col-auto\">";
                $hstr.="<input type=\"text\" class=\"form-control\" placeholder=\"Search ".$label."\" data-index=\"".$k."\" />";
                $hstr.="</div>";
                // City filter just after customer
                $k++;
                $hstr.="<div class=\"col-auto\">";
                $hstr.="<input type=\"text\" class=\"form-control\" placeholder=\"Search City\" data-index=\"".$k."\" />";
                $hstr.="</div>";
                continue;
            }

            $k++;
            $hstr.="<div class=\"col-auto\">";
            $hstr.="<input type=\"text\" class=\"form-control ".$extracls."\" placeholder=\"Search ".$label."\" data-index=\"".$k."\" />";
            $hstr.="</div>";
        }
        echo $hstr;
        echo "</div>";
        echo "<table id=\"searchMaster\" class=\"ui celled table display\">
              <thead>
              <tr>";
        if($canUpdate || $canDelete) {
            echo "<th>Action</th>";
        }
        $hstr="";$url_fieldname="";
        foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if(!in_array($fieldname,$this->_mdl->generator_field_display)) {
                continue;
            }
            if($fieldname=="company_id") {
                if(COMPANY_ID==ADMIN_COMPANY_ID) {
                    $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));
                    $hstr.="<th>".$label."</th>";
                }
                continue;
            }
            if($fieldname==$url_fieldname) {
                continue;
            }
            if($this->_mdl->generator_fields_types[$i]=="file") {
                $url_fieldname=$fieldname."_url";
            }
            $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));

            if($fieldname=="customer") {
                $hstr.="<th>".$label."</th>";
                $hstr.="<th>City</th>";
                continue;
            }

            $hstr.="<th>".$label."</th>";
        }
        echo $hstr;
        echo "</tr></thead><tbody>";

        $_grid=""; $j=0;
        foreach($_dbh->query($sql) as $_rs) {
            $j++;
            $_grid.="<tr>";
            if($canUpdate || $canDelete) {
                $_grid.="<td data-label=\"Action\">";
                if($canUpdate) {
                    $_grid.="<form method=\"post\" action=\"frm_rent_invoice_master.php\" style=\"display:inline; margin-rigth:5px;\">
                                <i class=\"fa fa-edit update\" style=\"cursor:pointer;\"></i>
                                <input type=\"hidden\" name=\"rent_invoice_id\" value=\"".$_rs["rent_invoice_id"]."\" />
                                <input type=\"hidden\" name=\"transactionmode\" value=\"U\" />
                             </form>";
                }
                if($canDelete) {
                    $_grid.="<form method=\"post\" action=\"classes/cls_rent_invoice_master.php\" style=\"display:inline;\">
                                <i class=\"fa fa-trash delete\" style=\"cursor:pointer;\"></i>
                                <input type=\"hidden\" name=\"rent_invoice_id\" value=\"".$_rs["rent_invoice_id"]."\" />
                                <input type=\"hidden\" name=\"transactionmode\" value=\"D\" />
                             </form>";
                }
                $_grid.="</td>";
            }

            $url_fieldname="";
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                if(!in_array($fieldname,$this->_mdl->generator_field_display) || $fieldname==$url_fieldname) {
                    continue;
                }
                if($fieldname=="company_id") {
                    if(COMPANY_ID==ADMIN_COMPANY_ID) {
                        $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));
                        $_grid.="<td data-label=\"".$label."\">".$_rs[$fieldname]."</td>";
                    }
                    continue;
                }
                if($this->_mdl->generator_fields_types[$i]=="file") {
                    $url_fieldname=$fieldname."_url";
                }

                $label = $this->_mdl->generator_fields_labels[$i] ?: ucwords(str_replace("_"," ",$fieldname));

                if($this->_mdl->generator_field_data_type[$i]=="date"
                   || $this->_mdl->generator_field_data_type[$i]=="datetime-local"
                   || $this->_mdl->generator_field_data_type[$i]=="datetime"
                   || $this->_mdl->generator_field_data_type[$i]=="timestamp") {
                    $fieldvalue = $_rs[$fieldname] ? date("d/m/Y",strtotime($_rs[$fieldname])) : "";
                    if($this->_mdl->generator_field_data_type[$i]!="date" && $_rs[$fieldname]) {
                        $fieldvalue.="<br><small>".date("h:i:s a",strtotime($_rs[$fieldname]))."</small>";
                    }
                } elseif($this->_mdl->generator_fields_types[$i]=="file") {
                    if(!empty($_rs[$url_fieldname])) {
                        $fieldvalue="<img src=\"".BASE_URL.$_rs[$url_fieldname]."\" style=\"max-width:100px; max-height:100px;\" alt=\"File\" />";
                    } else {
                        $fieldvalue="";
                    }
                } elseif($this->_mdl->generator_field_data_type[$i]=="bit") {
                    $fieldvalue=($_rs[$fieldname]==1) ? "Yes" : "No";
                } else {
                    $fieldvalue=$_rs[$fieldname];
                }

                $_grid.="<td data-label=\"".$label."\">".$fieldvalue."</td>";

                if($fieldname=="customer") {
                    $cityVal = isset($_rs['customer_city']) ? htmlspecialchars($_rs['customer_city']) : '';
                    $_grid.="<td data-label=\"City\">".$cityVal."</td>";
                }
            }
            $_grid.="</tr>\n";
        }

        if($j==0) {
            $_grid.="<tr>";
            $_grid.="<td>No records available.</td>";
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                if(!in_array($fieldname,$this->_mdl->generator_field_display)) continue;
                if($fieldname=="company_id" && COMPANY_ID!=ADMIN_COMPANY_ID) continue;

                // After customer, add one more blank cell for City
                if($fieldname=="customer") {
                    $_grid.="<td style=\"display:none\">&nbsp;</td>"; // customer placeholder
                    $_grid.="<td style=\"display:none\">&nbsp;</td>"; // city placeholder
                    continue;
                }
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
            }
            $_grid.="</tr>";
        }

        $_grid.="</tbody></table>";
        echo $_grid;
    }
    public function checkDuplicate() {
        global $_dbh;
        $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";$scope_field_name="NULL";$scope_field_value="NULL";$company_id=COMPANY_ID;
        if(isset($_POST["column_name"]))
            $column_name=$_POST["column_name"];
        if(isset($_POST["column_value"]))
            $column_value=$_POST["column_value"];
        if(isset($_POST["id_name"]))
            $id_name=$_POST["id_name"];
        if(isset($_POST["id_value"]))
            $id_value=$_POST["id_value"];
        if(isset($_POST["table_name"]))
            $table_name=$_POST["table_name"];
        if(isset($_POST["scope_field_name"]) && $_POST["scope_field_name"]!="")
            $scope_field_name="'".$_POST["scope_field_name"]."'";
        if(isset($_POST["scope_field_value"]) && $_POST["scope_field_value"]!="")
            $scope_field_value="'".$_POST["scope_field_value"]."'";
        if(COMPANY_ID==ADMIN_COMPANY_ID) {
            $company_id= "NULL"; 
        }
        /* \Add this on 29/09/2025 by HETASVI */
        $scope_field_name = "NULL";
        $scope_field_value = "NULL";
        if (isset($_POST["company_year_id"]) && $_POST["company_year_id"] != "") {
            $scope_field_name = "'company_year_id'";
            $scope_field_value = "'".$_POST["company_year_id"]."'";
        }
      /* \END this on 29/09/2025 by HETASVI */
        try {
            $sql="CAll csms_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',".$company_id.",@is_duplicate,".$scope_field_name.",".$scope_field_value.")";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_duplicate = $result->fetchColumn();
            echo $is_duplicate;
            exit;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
    /* ADDED BY BHUMITA ON 22/08/2025 */
    public function fetchLots() {
        global $_dbh, $tbl_inward_master;
        $customer_id=0;$invoice_type='';
        if(isset($_REQUEST['customer_id']))
            $customer_id = intval($_REQUEST['customer_id']);
        if(isset($_REQUEST['invoice_type']))
            $invoice_type = $_REQUEST['invoice_type'];
        $gst_type = null;
        if($customer_id > 0 && $invoice_type!="") {
            switch ($invoice_type) {
                case '1':
                    $gst_type = 3;
                    break;
                case '2':
                    $gst_type = 1;
                    break;
                case '3':
                    $gst_type = 2;
                    break;
                default:
                    $gst_type =0;
            }
            if($gst_type===0) {
                //echo "Invalid GST Type";
                echo json_encode(['error' =>"Invalid GST Type"]);
                exit;
            }
            try {
                $stmt = $_dbh->prepare("
                    SELECT i.lot_no
                    FROM tbl_inward_detail i
                    INNER JOIN ".$tbl_inward_master." m ON i.inward_id = m.inward_id
                    WHERE m.customer = ? AND i.gst_type = ?
                    GROUP BY i.lot_no
                    ORDER BY i.lot_no
                ");
                $stmt->execute([$customer_id, $gst_type]);
                $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
               echo json_encode($lots);
                exit;
            } catch (Exception $e) {
                //echo $e->getMessage();
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }  
        exit;
    }
    function fetchCustomerState() {
        global $_dbh, $tbl_customer_master, $tbl_state_master;
        if (isset($_REQUEST["customer_id"])) {
            $customerId = $_REQUEST["customer_id"];
            try {
                $stmt = $_dbh->prepare("SELECT state_name FROM ".$tbl_customer_master." cm INNER JOIN ".$tbl_state_master." sm ON cm.state_id=sm.state_id WHERE customer_id = ?");
                $stmt->execute([$customerId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row && $row['state_name']!="") {
                    echo json_encode([
                        'success' => true,
                        'state_name' => $row['state_name']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Customer state not found']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        exit;
    }
    function fetchHSNTaxRates() {
        global $_dbh, $view_tax_type, $tbl_gst_tax_detail;
        if (isset($_REQUEST["hsn_code_id"])) {
            $hsnCodeId = intval($_REQUEST["hsn_code_id"]);
            try {
                if($hsnCodeId > 0) {
                    $sgst=0.00; $cgst=0.00;$igst=0.00;
                    $where=" AND tax_type=1 AND effective_date <= CURDATE() AND hsn_code_id = {$hsnCodeId} ORDER BY effective_date DESC LIMIT 1";
                    //echo "CALL csms_search_detail('tax as sgst','".$tbl_gst_tax_detail."','".$where."')";
                    $stmt = $_dbh->prepare("CALL csms_search_detail('tax as sgst','".$tbl_gst_tax_detail."','".$where."')");
                    $stmt->execute();
                    $sgst = $stmt->fetchColumn();
                    $stmt->closeCursor();

                    $where=" AND tax_type=2 AND effective_date <= CURDATE() AND hsn_code_id = {$hsnCodeId} ORDER BY effective_date DESC LIMIT 1";
                    $stmt = $_dbh->prepare("CALL csms_search_detail('tax as cgst','".$tbl_gst_tax_detail."','".$where."')");
                    $stmt->execute();
                    $cgst = $stmt->fetchColumn();
                    $stmt->closeCursor();

                    $where=" AND tax_type=3 AND effective_date <= CURDATE() AND hsn_code_id = {$hsnCodeId} ORDER BY effective_date DESC LIMIT 1";
                    $stmt = $_dbh->prepare("CALL csms_search_detail('tax as igst','".$tbl_gst_tax_detail."','".$where."')");
                    $stmt->execute();
                    $igst = $stmt->fetchColumn();
                    $stmt->closeCursor();

                    echo json_encode([
                        'success' => true,
                        'sgst' => $sgst,
                        'cgst' => $cgst,
                        'igst' => $igst
                    ]);
                }else {
                    echo json_encode(['success' => false, 'error' => 'HSN code not found']);
                }  
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
    }
    public function getInvoiceGrid($transactionmode) {
        global $_dbh, $tbl_rent_invoice_detail,$view_storage_duration,$tbl_packing_unit_master,$view_rent_type,$tbl_item_master,$generatedDetails;
        $generatedDetails = [];
        $output="";
        $output .= <<<HTML
            <div class="my-3" id="generate-btn-wrap" style="margin-top: 1rem !important; margin-bottom: 1rem !important;">
                <button type="button" class="btn btn-primary mb-3" name="generate" id="generate" style="margin-top:inherit;">Generate Invoice</button>
                
                <div id="generatedInvoiceGrid" class="mt-4" style="display:none;">
                    <table id="searchGeneratedDetail" class="table table-bordered table-striped" style="width:100%; font-size:14px;">
                        <thead>
                            <tr>
                                <th>In. No.</th>
                                <th>In. Date</th>
                                <th>Lot No.</th>
                                <th>Item</th>
                                <th>marko</th>
                                <th>Qty.</th>
                                <th>Unit</th>
                                <th>Weight (Kg.)</th>
                                <th>Storage Duration</th>
                                <th>Rent/Month</th>
                                <th>Per</th>
                                <th>Out. Date</th>
                                <th>Charges From</th>
                                <th>Charges To</th>
                                <th>Act. Month</th>
                                <th>Act. Days</th>
                                <th>Invoice For</th>
                                <th>Invoice Days</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="generatedInvoiceTableBody">
            HTML;
            if ($transactionmode == "U") {
                 try {
                    $stmt = $_dbh->prepare("
                        SELECT 
                            rid.*, 
                            vsd.id AS storage_duration_id,
                            vsd.value AS storage_duration,
                            pum.packing_unit_name,
                            vrt.id AS rent_per_id,
                            vrt.value AS rent_per,
                            tim.item_name AS item_name_value
                        FROM ".$tbl_rent_invoice_detail." rid
                        LEFT JOIN ".$view_storage_duration." vsd ON rid.storage_duration = vsd.id
                        LEFT JOIN ".$tbl_packing_unit_master." pum ON rid.unit = pum.packing_unit_id
                        LEFT JOIN ".$view_rent_type." vrt ON rid.rent_per = vrt.id
                        LEFT JOIN ".$tbl_item_master." tim ON rid.item = tim.item_id
                        WHERE rid.rent_invoice_id = ? AND rid.inward_no IS NOT NULL
                    ");
                    $stmt->execute([$this->_mdl->_rent_invoice_id]);
                    $generatedDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error loading generated details: " . $e->getMessage();
                }
                if (empty($generatedDetails)) {
                    $output .= '<tr>
                        <td colspan="21" style="text-align:center;">No records available.</td>
                    </tr>';
                } else {
                    $index = 0; 
                    foreach ($generatedDetails as $detail) {
                        $output .= '<tr>
                            <td data-label="Invoice No">'.htmlspecialchars($detail['inward_no'] ?? '').'</td>
                            <td data-label="Invoice Date">'.(isset($detail['inward_date']) && $detail['inward_date'] !== null ? htmlspecialchars($detail['inward_date']) : '').'</td>
                            <td data-label="Lot No">'.htmlspecialchars($detail['lot_no'] ?? '').'</td>
                            <td data-label="Item">'.htmlspecialchars($detail['item_name_value'] ?? $detail['item'] ?? '').'</td>
                            <td data-label="Marko">'.htmlspecialchars($detail['marko'] ?? '').'</td>
                            <td data-label="Quantity">'.htmlspecialchars($detail['invoice_qty'] ?? '').'</td>
                            <td data-label="Unit">'.htmlspecialchars($detail['packing_unit_name'] ?? '').'</td>
                            <td data-label="Weight (Kg)">'.htmlspecialchars($detail['wt_per_kg'] ?? '').'</td>
                            <td data-label="Storage Duration">'.htmlspecialchars($detail['storage_duration'] ?? '').'</td>
                            <td data-label="Rent" class="rent-editable" data-index="'.$index.'" contenteditable="true">'.htmlspecialchars($detail['rent_per_storage_duration'] ?? '').'</td>
                            <td data-label="Per">'.htmlspecialchars($detail['rent_per'] ?? '').'</td>
                            <td data-label="Out Date">'.(isset($detail['outward_date']) && $detail['outward_date'] !== null ? htmlspecialchars($detail['outward_date']) : '').'</td>
                            <td data-label="Charges From">'.(isset($detail['charges_from']) && $detail['charges_from'] !== null ? htmlspecialchars($detail['charges_from']) : '').'</td>
                            <td data-label="Charges To">'.(isset($detail['charges_to']) && $detail['charges_to'] !== null ? htmlspecialchars($detail['charges_to']) : '').'</td>
                            <td data-label="Actual Month">'.htmlspecialchars($detail['actual_month'] ?? '').'</td>
                            <td data-label="Actual Day">'.htmlspecialchars($detail['actual_day'] ?? '').'</td>
                            <td data-label="Invoice For" class="invoice-for">'.htmlspecialchars($detail['invoice_for'] ?? '').'</td>
                            <td data-label="Invoice Day" class="invoice-day">'.htmlspecialchars($detail['invoice_day'] ?? '').'</td>
                            <td data-label="Amount" class="amount">'.number_format($detail['invoice_amount'] ?? 0, 2).'</td>
                            <td data-label="Status">'.htmlspecialchars($detail['status'] ?? '').'</td>
                            </tr>';
                    } // foreach ends
                } //generatedDetails else ends
            } else {
                $output .= '<tr>
                    <td colspan="21" style="text-align:center;">No records available.</td>
                </tr>';
            }
             $output .= <<<HTML
                        </tbody></table>
                </div>
                <div id="generated-invoice-details" style="display:none;"></div></div>
            HTML;
            return $output;
    }
    public function getDisabledOnEditFields() {
        return $disabledFields = ["invoice_type", "invoice_no", "invoice_sequence", "invoice_date", "billing_till_date",
        "debit_cash", "customer", "invoice_for", "hsn_code", "tax_amount"
        ];
    }
    function generateInvoiceNumber() {
        global $tbl_rent_invoice_master, $next_invoice_sequence, $invoice_no_formatted;
        $rent_invoice_id = isset($_REQUEST['rent_invoice_id']) ? intval($_REQUEST['rent_invoice_id']) : 0;
        $invoice_type = isset($_REQUEST['invoice_type']) ? $_REQUEST['invoice_type'] : 'null';
        if($invoice_type == 'null' || $invoice_type == '') {
            echo json_encode(['error' => 'Invoice type is required']);
            exit;
        }
         $sequence_data = getNextSequenceAndNo(
            $tbl_rent_invoice_master,
            'invoice_sequence',
            'invoice_no',
            'invoice_date',
            COMPANY_YEAR_ID,
            $rent_invoice_id,
            'rent_invoice_id',
            $invoice_type,
            'invoice_type'
        );
        $next_invoice_sequence = $sequence_data['next_sequence'];
        $invoice_no_formatted = $sequence_data['formatted_no'];
        echo json_encode([
            'invoice_sequence' => $next_invoice_sequence,
            'invoice_no' => $invoice_no_formatted
        ]);
        exit;
    }
    /* ADDED BY MANSI ON 23/09/2025: Return only customers who currently have eligible lots for selected Invoice For and Invoice Type */
    public function fetchEligibleCustomers() {
        global $_dbh, 
               $tbl_inward_detail, 
               $tbl_inward_master, 
               $tbl_outward_detail, 
               $tbl_outward_master, 
               $tbl_rent_invoice_detail, 
               $tbl_customer_master;

        $invoice_for  = isset($_REQUEST['invoice_for']) ? trim($_REQUEST['invoice_for']) : '';
        $invoice_type = isset($_REQUEST['invoice_type']) ? trim($_REQUEST['invoice_type']) : '';

        try {
            $gst_type = null;
            if ($invoice_type !== '') {
                switch ($invoice_type) {
                    case '1': $gst_type = 3; break;   // Regular → gst_type=3 (તમારી mapping મુજબ)
                    case '2': $gst_type = 1; break;   // Tax Invoice
                    case '3': $gst_type = 2; break;   // Bill of Supply
                }
            }

            $where = [];
            $params = [];

            if ($gst_type !== null) {
                $where[] = "i.gst_type = ?";
                $params[] = $gst_type;
            }

            $companyScope = "";
            if (defined('COMPANY_ID') && defined('ADMIN_COMPANY_ID') && COMPANY_ID != ADMIN_COMPANY_ID) {
                $companyScope = " AND im.company_id = " . intval(COMPANY_ID);
            }
            // Seasonal (invoice_for = 4) → restrict to storage_duration = 9 (Seasonal)
            if ($invoice_for === '4') { $where[]  = "i.storage_duration = ?"; $params[] = 9; }
            $filter = (!empty($where) ? ' AND ' . implode(' AND ', $where) : '');
            $stock_calc = "(i.inward_qty - IFNULL((SELECT SUM(o2.out_qty) FROM $tbl_outward_detail o2 WHERE o2.inward_detail_id = i.inward_detail_id), 0))";
            $stock_filter = "";
            if ($invoice_for === '2') {        
                $stock_filter = "AND $stock_calc > 0 AND $stock_calc < i.inward_qty";
            } elseif ($invoice_for === '3') {  
                $stock_filter = "AND $stock_calc = 0";
            }

            $sql_outward = "
                SELECT DISTINCT cm.customer_id, cm.customer
                FROM $tbl_inward_detail i
                JOIN $tbl_inward_master im ON i.inward_id = im.inward_id
                JOIN $tbl_customer_master cm ON im.customer = cm.customer_id
                JOIN $tbl_outward_detail o ON o.inward_detail_id = i.inward_detail_id
                JOIN $tbl_outward_master om ON o.outward_id = om.outward_id
                WHERE o.out_qty > 0
                  $companyScope
                  $filter
                  $stock_filter
                  AND NOT EXISTS (
                      SELECT 1 FROM $tbl_rent_invoice_detail rid
                      WHERE rid.outward_detail_id = o.outward_detail_id
                  )
            ";

            $sql_stock = "
                SELECT DISTINCT cm.customer_id, cm.customer
                FROM $tbl_inward_detail i
                JOIN $tbl_inward_master im ON i.inward_id = im.inward_id
                JOIN $tbl_customer_master cm ON im.customer = cm.customer_id
                WHERE $stock_calc > 0
                  $companyScope
                  $filter
                  AND NOT EXISTS (
                      SELECT 1 FROM $tbl_rent_invoice_detail rid
                      WHERE rid.inward_id = i.inward_id
                        AND rid.outward_detail_id IS NULL
                  )
            ";

            if ($invoice_for === '2' || $invoice_for === '3') {
                $final_sql = $sql_outward . " ORDER BY cm.customer";
                $all_params = $params;
            } else {
                $final_sql = "($sql_outward) UNION ($sql_stock) ORDER BY customer";
                $all_params = array_merge($params, $params);
            }

            $stmt = $_dbh->prepare($final_sql);
            $stmt->execute($all_params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($rows);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
    /* \ADDED BY MANSI ON 23/09/2025 */
    /* \ADDED BY BHUMITA ON 22/08/2025 */
    public function getForm($transactionmode="I",$popup=false,$label_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1", $field_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2") {
        /* ADDED BY BHUMITA ON 22/08/2025 */
        global $next_invoice_sequence, $invoice_no_formatted;
        $next_invoice_sequence = isset($next_invoice_sequence) ? $next_invoice_sequence : 1;
        $invoice_no_formatted = isset($invoice_no_formatted) ? $invoice_no_formatted : '';
        /* \ADDED BY BHUMITA ON 22/08/2025 */
        $output=""; $hidden_str="";
         if(isset($this->_mdl->generator_table_layout))
            $table_layout=$this->_mdl->generator_table_layout;
        else
            $table_layout="vertical";
        if(is_array($this->_mdl->generator_fields_names) && !empty($this->_mdl->generator_fields_names)){
            if($table_layout=="horizontal") {
                $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
            } else {
                $label_layout_classes=$label_classes." col-form-label";
                $field_layout_classes=$field_classes;
            }
            $output.='<div class="box-body">
                <div class="form-group row gy-2">';
            
            /* ADDED BY BHUMITA ON 22/08/2025 */
           $selected_invoice_type = $this->_mdl->_invoice_type ?? '2'; 
           $output .= '
               <div class="row align-items-center mb-2">
                   <label class="' . $label_layout_classes . '">Invoice Type</label>
                   <div class="col-12 col-sm-9 col-md-7 col-lg-6">
                       <div class="d-flex flex-nowrap gap-3 align-items-center">
                           <div class="form-check form-check-inline m-0">'
                               . addInput("radio", "invoice_type", "1", ($selected_invoice_type == "1" ? "checked" : ""), "", "form-check-input", "", "", "Regular", "") . '
                               <label class="form-check-label" for="invoice_type1">Regular</label>
                           </div>
                           <div class="form-check form-check-inline m-0">'
                               . addInput("radio", "invoice_type", "2", ($selected_invoice_type == "2" ? "checked" : ""), "", "form-check-input", "", "", "Tax Invoice", "") . '
                               <label class="form-check-label" for="invoice_type2">Tax Invoice</label>
                           </div>
                           <div class="form-check form-check-inline m-0">'
                               . addInput("radio", "invoice_type", "3", ($selected_invoice_type == "3" ? "checked" : ""), "", "form-check-input", "", "", "Bill of Supply", "") . '
                               <label class="form-check-label" for="invoice_type3">Bill of Supply</label>
                           </div>
                       </div>
                   </div>
               </div>
           ';
            $output .= '
                <div class="row align-items-center">
                    <label for="rent_invoice_sequence" class="' . $label_layout_classes . '">Invoice No</label>
                    <div class="' . $field_layout_classes . '">
                        <div class="row g-2 ml-0">
                            <div class="col-6">'.
                            addNumber("invoice_sequence", $next_invoice_sequence,"required","","form-control duplicate","duplicate","min='1'","","Invoice Sequence")
                                .'<div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">'.
                            addInput("text","invoice_no",$invoice_no_formatted,"required","disabled","form-control duplicate","duplicate","",$this->_mdl->generator_fields_labels[2])
                                .'
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>';
            /* \ADDED BY BHUMITA ON 22/08/2025 */

            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                /* ADDED BY BHUMITA ON 22/08/2025 */
                if (in_array($fieldname, ['invoice_sequence', 'invoice_no', 'invoice_type'])) {
                    continue;
                }
                /* \ADDED BY BHUMITA ON 22/08/2025 */
                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";$cls_field_name="_".$fieldname;$is_disabled=0;$disabled_str="";$img_str="";

                if(!empty($this->_mdl->generator_field_required) && in_array($fieldname,$this->_mdl->generator_field_required)) {
                    $required=1;
                }
                if(!empty($this->_mdl->generator_field_is_disabled) && in_array($fieldname,$this->_mdl->generator_field_is_disabled)) {
                    $is_disabled=1;
                }
               
                if($this->_mdl->generator_fields_labels[$i]) {
                    $lbl_str='<label for="'.$fieldname.'" class="'.$label_layout_classes.'">'.$this->_mdl->generator_fields_labels[$i].'';
                        if($table_layout=="vertical") {
                            $field_layout_classes=$field_classes;
                        } 
                } else {
                    if($table_layout=="vertical") {
                        $field_layout_classes="col-12";
                    } 
                }   
                if($required) {
                    $required_str="required";
                    $error_container='<div class="invalid-feedback"></div>';
                    $lbl_str.="*";
                }
                if(!empty($this->_mdl->generator_chk_duplicate) && in_array($fieldname,$this->_mdl->generator_chk_duplicate)) {
                    $error_container='<div class="invalid-feedback"></div>';
                    $duplicate_str="duplicate";
                    $lbl_str.="*";
                }
                if($is_disabled) {
                    $disabled_str="disabled";
                }
                if($this->_mdl->generator_fields_types[$i]=="email") {
                    $error_container='<div class="invalid-feedback"></div>';
                }
                $lbl_str.="</label>";

                /* ADDED BY BHUMITA ON 22/08/2025 */
                if ($fieldname == "billing_till_date" || $fieldname == "grace_days") {
                    $error_container = '<div class="invalid-feedback"></div>';
                }
                if ($fieldname == "lot_no") {
                    $output .= '<label for="lot_no" class="col-4 col-sm-2 col-md-1 col-lg-1 form-label">Lot No</label>';
                    $output .= '<div class="col-8 col-sm-4 col-md-3 col-lg-2">';
                    if ($transactionmode == "U" && $this->_mdl->_invoice_for !== '5') {
                        $output .= addInput("text","lot_no_display",htmlspecialchars($this->_mdl->_lot_no ?? ''),"",
                            "readonly disabled","form-control","","","Lot No");
                        $output .= addHidden("lot_no", htmlspecialchars($this->_mdl->_lot_no ?? ''));
                    } else {
                        $output .= '<div id="lotNoMultiselect" class="multiselect position-relative">
                            <div id="lotNoSelectLabel" class="selectBox" tabindex="0">
                                <select id="lot_no" name="lot_no" class="lot-no form-control form-select required" style="width: 100%;" required>
                                    <option>Select Lot No</option>
                                </select>
                                <div class="overSelect"></div>
                            </div>
                            <div id="lotNoSelectOptions" class="shadow"></div>
                        </div>';
                    }
                    $output .= '</div>';
                    $show_generate_btn = true;
                    continue;
                }
                /* \ADDED BY BHUMITA ON 22/08/2025 */
                switch($this->_mdl->generator_fields_types[$i]) {
                    case "text":
                    case "email":
                    case "file":
                    case "date":
                    case "datetime-local":
                    case "radio":
                    case "checkbox":
                    case "number":
                    case "select":
                        $value="";$field_str="";$cls="";$flag=0;
                            $table=explode("_",$fieldname);
                            $field_name=$table[0]."_name";
                            $fields=$fieldname.", ".$table[0]."_name";
                            $tablename="tbl_".$table[0]."_master";
                            $selected_val="";
                            if($this->_mdl->$cls_field_name) {
                                $selected_val=$this->_mdl->$cls_field_name;
                            } else if($this->_mdl->generator_default_value[$i]){
                                $selected_val=$this->_mdl->generator_default_value[$i];
                            }
                            if(!empty($this->_mdl->generator_where_condition[$i]))
                                $where_condition_val=$this->_mdl->generator_where_condition[$i];
                            else {
                                $where_condition_val=null;
                            }
                            if($this->_mdl->generator_fields_types[$i]=="checkbox" || $this->_mdl->generator_fields_types[$i]=="radio") {
                                    $cls.=$required_str;
                                    if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                        $flag=1;
                                        $field_str.=getChecboxRadios($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $this->_mdl->generator_fields_types[$i],$disabled_str).$error_container;
                                    }
                                    else{
                                            if($transactionmode=="U" && $this->_mdl->$cls_field_name==1) {
                                                $chk_str="checked='checked'";
                                            }
                                            $value="1";
                                            $field_str.=addHidden($fieldname,0);
                                    }
                            } else {
                                $cls.="form-control ".$required_str." ".$duplicate_str;
                                $chk_str="";
                                    if(isset($this->_mdl)) {
                                        $value=$this->_mdl->$cls_field_name; 
                                }
                            }
                            if(!empty($value) && ($this->_mdl->generator_fields_types[$i]=="date" || $this->_mdl->generator_fields_types[$i]=="datetime-local" || $this->_mdl->generator_fields_types[$i]=="datetime" || $this->_mdl->generator_fields_types[$i]=="timestamp")) {
                                $value=date("Y-m-d",strtotime($value));
                            }
                            if($this->_mdl->generator_fields_types[$i]=="number") {
                                $step="";$max_str="";$disabled_value="";
                                if(!empty($this->_mdl->generator_field_scale[$i]) && $this->_mdl->generator_field_scale[$i]>0) {
                                    for($k=1;$k<$this->_mdl->generator_field_scale[$i];$k++) {
                                        $step.=0;
                                    }
                                    $step="0.".$step."1";
                                } else {
                                    $step=1;
                                }
                                $step_str='step="'.$step.'"';
                                $min=1; 
                                if(!empty($this->_mdl->generator_allow_zero) && in_array($fieldname,$this->_mdl->generator_allow_zero)) 
                                    $min=0;
                                if(!empty($this->_mdl->generator_allow_minus) && in_array($fieldname,$this->_mdl->generator_allow_minus)) 
                                $min="";

                                $min_str='min="'.$min.'"';
                                if($value!="" && $value!=null) {
                                    $value=number_format((float)$value,$this->_mdl->generator_field_scale[$i],'.','');
                                }
                                $field_str.=addNumber($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str,$this->_mdl->generator_fields_labels[$i],$disabled_value,$max_str).$error_container;
                            }
                            else if($this->_mdl->generator_fields_types[$i]=="select") {
                                $cls="form-select ".$required_str." ".$duplicate_str;
                                
                                if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                    /* ADDED BY BHUMITA ON 23/08/2025 */
                                    $data1=""; $data_value1="";
                                    if($fieldname=="hsn_code"){
                                        $data1="unique_id";
                                        $data_value1="unique_id";
                                    }
                                    /* \ADDED BY BHUMITA ON 23/08/2025 */
                                    $field_str.=getDropdown($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $disabled_str,$data1,$data_value1).$error_container; // Last 2 arguments added by BHUMITA on 23/08/2025
                                }
                            } else {
                                if($flag==0) {
                                    if($this->_mdl->generator_fields_types[$i]=="file") {
                                        $value="";$img_str="<br>";
                                        $url_fieldname="_".$fieldname."_url";
                                        if ($transactionmode == "U") {
                                            if ($this->_mdl->$url_fieldname && file_exists(BASE_PATH.$this->_mdl->$url_fieldname)) {
                                                $img_str .= '<img src="' .BASE_URL . $this->_mdl->$url_fieldname. '" alt="'.$this->_mdl->generator_fields_labels[$i].'" style="max-width: 200px; max-height: 200px;">';
                                            } 
                                        } 
                                    }
                                    $field_str.=addInput($this->_mdl->generator_fields_types[$i],$fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$chk_str,$this->_mdl->generator_fields_labels[$i]).$error_container.$img_str;
                                }
                            }
                        break;
                    case "hidden":
                        $lbl_str="";
                        if($this->_mdl->generator_field_data_type[$i]=="int" || $this->_mdl->generator_field_data_type[$i]=="bigint"  || $this->_mdl->generator_field_data_type[$i]=="tinyint" || $this->_mdl->generator_field_data_type[$i]=="decimal")
                            $hiddenvalue=0;
                        else
                            $hiddenvalue="";
                        if($fieldname=="created_by") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=USER_ID;
                            }
                        } else if($fieldname=="created_date") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=date("Y-m-d H:i:s");
                            }
                        } else if($fieldname=="modified_by") {
                            $hiddenvalue=USER_ID; 
                        } else if($fieldname=="modified_date") {
                            $hiddenvalue=date("Y-m-d H:i:s");
                        }
                        else if($fieldname=="company_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_ID;
                            }
                        }
                            
                        else if($fieldname=="company_year_id") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=COMPANY_YEAR_ID;
                            }
                        }
                            else {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } 
                        }
                        $hidden_str.=addHidden($fieldname,$hiddenvalue);
                    
                        break;
                    case "textarea":
                        $cls.="form-control ".$required_str." ".$duplicate_str;
                        $value="";
                        if(isset($this->_mdl)){
                                $value=$this->_mdl->$cls_field_name;
                            }
                        $field_str.=addTextArea($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,3,$this->_mdl->generator_fields_labels[$i]).$error_container;
                        break;
                    default:
                        break;
                } //switch ends
                 if(empty($this->_mdl->generator_after_detail) || (!empty($this->_mdl->generator_after_detail) && !in_array($fieldname,$this->_mdl->generator_after_detail))) {
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='<div class="row mb-3 align-items-center">';
                    }
                    $output.=$lbl_str;
                    if($field_str) {
                    $output.='<div class="'.$field_layout_classes.'">';
                    $output.=$field_str;
                    $output.='</div>';
                    }
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='</div>';
                    }
                } else {
                    $lbl_array[]=$this->_mdl->generator_fields_labels[$i]; // Changed by BHUMITA ON 22/08/2025
                    $field_array[]=$field_str;
                }
            } // foreach ends
            $output.="</div><!-- /.row -->";
            $output.=$hidden_str;
            $output.=$this->getInvoiceGrid($transactionmode); // ADDED BY BHUMTIA ON 22/08/2025
            $output.="</div> <!-- /.box-body -->";
        
            $output.="<!-- detail table content--> 
                    <div class=\"box-body\" id=\"manual-invoice-details\" style=\"display:none;\"> // id and style ADDED BY BHUMITA ON 22/08/2025
                        <div class=\"box-detail\">"; 
                                    $_blldetail = new bll_rentinvoicedetail();
                                    $detailHtml = $_blldetail->pageSearch();
                                if($detailHtml)
                                    $output.=$detailHtml; 
                            $output.="<button type=\"button\" name=\"detailBtn\" id=\"detailBtn\" class=\"btn btn-primary add\" data-bs-toggle=\"modal\" data-bs-target=\"#modalDialog\"  onclick=\"openModal()\">Add Detail Record</button>
                    </div>
                </div>
                <!-- /.box-body detail table content -->";
            
            /* MODIFIED BY BHUMITA ON 22/08/2025 */
            if(!empty($field_array)) {
                $output.='<div class="box-body">
                <div class="form-group row">';
                 $output.='
                 <div class="col-12 col-lg-4 col-md-8 col-sm-12">';
                 // Basic Amount
                 $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-xl-3 col-form-label text-truncate">'.$lbl_array[0].'</label>
                    <div class="col-8 col-lg-8 col-sm-4">'.$field_array[0].'</div>
                </div>';

                // Other expense fields
                $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-xl-3 col-form-label text-truncate">'.$lbl_array[3].'</label>
                    <div class="col-8 col-lg-8 col-sm-4">
                        <div class="input-group">'.$field_array[3].'
                         <span class="align-self-center px-1 fw-bold">:</span>'.$field_array[4].'
                        </div>
                    </div>
                </div>';

                //unloading expense
                $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-xl-3 col-form-label text-truncate">'.$lbl_array[1].'</label>
                    <div class="col-8 col-lg-8 col-sm-4">'.$field_array[1].'</div>
                </div>';

                //loading expense
                $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-xl-3 col-form-label text-truncate">'.$lbl_array[2].'</label>
                    <div class="col-8 col-lg-8 col-sm-4">'.$field_array[2].'</div>
                </div>';

                //Special Note
                $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-xl-3 col-form-label text-truncate">'.$lbl_array[14].'</label>
                    <div class="col-8 col-lg-8 col-sm-4">'.$field_array[14].'</div>
                </div>';
                 $output.='</div> <!-- /.col-12 -->
                 ';

                 $output.='
                 <div class="col-12 col-lg-4 col-md-8 col-sm-12">';
                // Net amount
                $output.='
                 <div class="mb-3 d-flex flex-sm-row align-items-sm-center">
                    <label class="flex-shrink-0 col-4 col-lg-4 col-sm-3 col-form-label text-truncate">'.$lbl_array[5].'</label>
                    <div class="col-8 col-lg-8 col-sm-4" style=" max-width:265px;">'.$field_array[5].'</div>
                </div>';
                 // Tax Type
                $output.='
                 <div class="mb-3 row g-2 align-items-center">
                    <label class="col-form-label flex-shrink-0 col-sm-3 col-lg-4 col-4" style="white-space: nowrap;">'.$lbl_array[6].'</label>';
                 $output.='
                    <div class="col-8 col-lg-8 col-sm-4">';
                $tax_type_val = $this->_mdl->_tax_amount ?? '';
                $output.='
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 gap-sm-3">';
                $output.=getChecboxRadios('view_tax_amount','id','value','','tax_amount',$tax_type_val, '', '', 'radio','');
                $output.='
                    </div> <!-- /.d-flex -->
                </div> <!-- /.col-8 -->'; 
                $output.='</div> <!-- /.mb-3 -->
                 ';
                
                // SGST
                $output.='
                <div class="mb-3 d-flex flex-sm-row align-items-sm-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-form-label text-truncate" style="white-space: nowrap;">'.$lbl_array[7].'</label>
                    <div class="d-flex flex-wrap align-items-center flex-grow-1 gap-1 invoice_tax_fields">
                        '.$field_array[7].'
                        <span style="font-size: 16px; line-height: 1;">%</span>
                        '.$field_array[8].'
                    </div>
                </div>';

                // CGST
                $output.='
                <div class="mb-3 d-flex flex-sm-row align-items-sm-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-form-label text-truncate" style="white-space: nowrap;">'.$lbl_array[9].'</label>
                    <div class="d-flex flex-wrap align-items-center flex-grow-1 gap-1 invoice_tax_fields">
                        '.$field_array[9].'
                        <span  style="font-size: 16px; line-height: 1;">%</span>
                        '.$field_array[10].'
                    </div>
                </div>';

                // IGST
                $output.='
                <div class="mb-3 d-flex flex-sm-row align-items-sm-center">
                    <label class="col-4 col-lg-4 col-sm-3 col-form-label text-truncate" style="white-space: nowrap;">'.$lbl_array[11].'</label>
                    <div class="d-flex flex-wrap align-items-center flex-grow-1 gap-1 invoice_tax_fields">
                        '.$field_array[11].'
                        <span style="font-size: 16px; line-height: 1;">%</span>
                        '.$field_array[12].'
                    </div>
                </div>';
                
                // Net amount
                $output.='
                 <div class="mb-3 d-flex flex-sm-row align-items-sm-center">
                    <label class="flex-shrink-0 col-4 col-lg-4 col-sm-3 col-form-label text-truncate">'.$lbl_array[13].'</label>
                    <div class="col-8 col-lg-8 col-sm-4" style=" max-width:265px;">'.$field_array[13].'</div>
                </div>';
                 $output.='</div> <!-- /.col-12 -->
                 ';
                 $output.="</div><!-- /.row -->
              </div> <!-- /.box-body -->";
            }
             /* \MODIFIED BY BHUMITA ON 22/08/2025 */
        } // if ends
        return $output;
    } // function getForm ends
}
 class dal_rentinvoicemaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        try {
            if($_mdl->_rent_invoice_id=="") {
                $_mdl->_rent_invoice_id=0;
            }
            $_dbh->exec("set @p0 = ".$_mdl->_rent_invoice_id);
            $_pre=$_dbh->prepare("CALL rent_invoice_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            
                if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                    foreach($_mdl->generator_fields_names as $i=>$fieldname)
                    {
                        if($i==0)
                            continue;
                        if($fieldname=="company_id") {
                            if(COMPANY_ID==ADMIN_COMPANY_ID) {
                                $field=$_mdl->{"_".$fieldname};
                            } else {
                                $field=COMPANY_ID;
                            }  
                        } else {
                            $field=$_mdl->{"_".$fieldname};
                        }
                        $_pre->bindValue($i,$field);
                    }
                }
                $_pre->bindValue($i+1,$_mdl->_transactionmode);
                $_pre->execute();
                if($_mdl->_transactionmode=="I") {
                    $result = $_dbh->query("SELECT @p0 AS inserted_id");
                    $insertedId = $result->fetchColumn();
                    $_mdl->_rent_invoice_id=$insertedId;
                }
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    if($_mdl->_rent_invoice_id)
                        $return_id=$_mdl->_rent_invoice_id;
                    else 
                        $return_id=0;
                    echo $return_id;
                    exit;
                }
                if($_mdl->_transactionmode=="D") {
                    $_SESSION["sess_message"]="Record Deleted Successfully.";
                }
                else if($_mdl->_transactionmode=="U") {
                    $_SESSION["sess_message"]="Record Updated Successfully.";
                }
                else {
                    $_SESSION["sess_message"]="Record Saved Successfully.";  
                }
                $_SESSION["sess_message_cls"]="success";
                $_SESSION["sess_message_title"]="Success!";
                $_SESSION["sess_message_icon"]="success";
            } catch (PDOException $e) {
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    echo $e->getMessage();
                    exit;
                }
                $_SESSION["sess_message"]=addslashes($e->getMessage());
                $_SESSION["sess_message_cls"]="danger";
                $_SESSION["sess_message_title"]="Error!";
                $_SESSION["sess_message_icon"]="error";
            }
        
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_rent_invoice_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
        public function fillModel($_mdl)
        {
            global $_dbh;
            $_pre = $_dbh->prepare("CALL rent_invoice_master_fillmodel (?) ");
            $_pre->bindParam(1, $_REQUEST["rent_invoice_id"]);
            $_pre->execute();
            $_rs = $_pre->fetchAll(); 
            if(!empty($_rs)) {
                if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                    foreach($_mdl->generator_fields_names as $i=>$fieldname)
                    {
                        $value = $_rs[0][$fieldname];
                        $_mdl->{"_".$fieldname} = $value;
                    }
                }
                $_mdl->_transactionmode = $_REQUEST["transactionmode"];
            }
        }
}
$_bll=new bll_rentinvoicemaster();

/*** FOR DETAIL ***/
$_blldetail=new bll_rentinvoicedetail();
/*** /FOR DETAIL ***/
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
   
    if(isset($_REQUEST["transactionmode"]))
    $tmode=$_REQUEST["transactionmode"];
    else
        $tmode="I";

    /* ADDED BY BHUMITA ON 25/08/2025 */
    $disabled_fields=$_bll->getDisabledOnEditFields();
    if($tmode=="U") {
        $_bll->fillModel();
    }
    /* \ADDED BY BHUMTIA ON 25/08/2025 */
    
    if(isset($_POST["ajaxAdd"]) && $_POST["ajaxAdd"]==1) {
        $_bll->_mdl->_ajaxAdd=1;
    }
    if(is_array($_bll->_mdl->generator_fields_names) && !empty($_bll->_mdl->generator_fields_names)){
        $url_fieldname="";
        foreach($_bll->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if($fieldname==$url_fieldname) {
                continue;
            }
            if($_bll->_mdl->generator_fields_types[$i]=="file") {
                $upload_dir = UPLOAD_DIR ."rent_invoice_master/";
                $upload_path = UPLOAD_PATH ."rent_invoice_master/";
                $url_fieldname = $fieldname . "_url";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $new_logo_uploaded = false;
                if ($_FILES[$fieldname]["name"]!="" && $_FILES[$fieldname]["error"] == UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES[$fieldname]["name"]);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_ext = ["jpg", "jpeg", "png", "gif"];

                    if (in_array($file_ext, $allowed_ext)) {
                        $file_name = preg_replace("/[^A-Za-z0-9._-]/", "", $file_name);
                        $base_name = pathinfo($file_name, PATHINFO_FILENAME);
                        $counter = 0;
                        $new_file_name = $file_name;
                        $abs_path = $upload_path . $new_file_name;
                        $rel_path = $upload_dir . $new_file_name;

                        while (file_exists($relative_path)) {
                            $counter++;
                            $new_file_name = $base_name . "_" . $counter . "." . $file_ext;
                            $abs_path = $upload_path . $new_file_name;
                            $rel_path = $upload_dir . $new_file_name;
                        }

                        if ($tmode == "U" && $_REQUEST[$url_fieldname]!="" && file_exists(BASE_PATH.$_REQUEST[$url_fieldname])) {
                            unlink(BASE_PATH.$_REQUEST[$url_fieldname]);
                        }
                        $tmpName = $_FILES[$fieldname]["tmp_name"];
                        if (move_uploaded_file($tmpName, $abs_path)) {
                            $imageData = file_get_contents($abs_path);
                            $_bll->_mdl->{"_".$fieldname} = $imageData;
                            $_bll->_mdl->{"_".$url_fieldname}= $rel_path;
                            continue;
                        } else {
                            $_SESSION["sess_message"]="Failed to upload file.";
                            $_SESSION["sess_message_cls"]="danger";
                        }
                    } else {
                        $_SESSION["sess_message"]="Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                        $_SESSION["sess_message_cls"]="danger";
                    }
                }
            } 
            if(isset($_REQUEST[$fieldname]) && !empty($_REQUEST[$fieldname])) {
                $field=$_REQUEST[$fieldname];
                if($_bll->_mdl->generator_fields_types[$i]=="datetime-local" || $_bll->_mdl->generator_fields_types[$i]=="datetime" || $_bll->_mdl->generator_fields_types[$i]=="timestamp") {
                    $field=date("Y-m-d H:i:s",strtotime($field));
                } else if($_bll->_mdl->generator_fields_types[$i]=="date") {
                    $field=date("Y-m-d",strtotime($field));
                }else if(is_array($field) && ($_bll->_mdl->generator_fields_types[$i]=="checkbox" || $_bll->_mdl->generator_fields_types[$i]=="radio") && $_bll->_mdl->generator_dropdown_table!="" && $_bll->_mdl->generator_label_column!="" && $_bll->_mdl->generator_value_column!="") {
                    $field = implode(",", $field);
                }
                else if(is_array($field) && $_bll->_mdl->generator_fields_types[$i]=="select") {
                    $field = implode(",", $field);
                }
                if(!is_array($field))
                    $field=trim($field);
            }    
            else {
                $field=null;
                if($tmode=="U") {
                    if(in_array($fieldname,$disabled_fields)) {
                        $field=$_bll->_mdl->{"_".$fieldname};
                    }
                }
            } 

            $_bll->_mdl->{"_".$fieldname}=$field;
        }
    }
    $_bll->_mdl->_transactionmode =$tmode;
  
            /*** FOR DETAIL ***/
            $_bll->_mdl->_array_itemdetail=array();
            $_bll->_mdl->_array_itemdelete=array();
            if(isset($_REQUEST["detail_records"])) {
                $detail_records=json_decode($_REQUEST["detail_records"],true);
                if(!empty($detail_records)) {
                    $arrayobject = new ArrayObject($detail_records);
                        $_bll->_mdl->_array_itemdetail=$arrayobject;
                }
            }
            if(isset($_REQUEST["deleted_records"])) {
                $deleted_records=json_decode($_REQUEST["deleted_records"],true);
                if(!empty($deleted_records)) {
                    $deleteobject = new ArrayObject($deleted_records);
                        $_bll->_mdl->_array_itemdelete=$deleteobject;
                }
            }
            /*** \FOR DETAIL ***/
            /* ADDED BY BHUMTIA ON 25/08/2025 */
            
            /* \ADDED BY BHUMTIA ON 25/08/2025 */
        $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}