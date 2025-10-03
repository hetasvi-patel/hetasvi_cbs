<?php  
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");
include("cls_inward_detail.php"); 
class mdl_inwardmaster 
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
        global $tbl_inward_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_inward_master);
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

class bll_inwardmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_inwardmaster(); 
        $this->_dal =new dal_inwardmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
        $_bllitem= new bll_inwarddetail();
        if($this->_mdl->_transactionmode!="D")
        {
            if(!empty($this->_mdl->_array_itemdetail)) {
                    for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            foreach($detailrow as $name=>$value) {
                                if($value==="")
                                        $value=null;
                                $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->inward_id = $this->_mdl->_inward_id;
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
                        $_bllitem->_mdl->inward_id = $this->_mdl->_inward_id;
                        $_bllitem->dbTransaction();
                    }
                }
        }
    /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_inward_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_inward_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_inward_master.php");
       }

    }
 
    public function fillModel()
    {
        $this->_dal->fillModel($this->_mdl);
    
    }
    /* FUNCTION MODIFIED BY BHUMITA ON 15/08/2025 */
    public function pageSearch()
    {
        global $_dbh;
        global $canUpdate;
        global $canDelete;
        $_grid="";
        $j=0;
        $company_query=str_replace("company_id","t.company_id",COMPANY_QUERY);
        $sql="CAll csms_search_detail(
        't.*, t4.customer as customer_name, t5.customer as broker_name, c1.company_name as company_id, t6.*, c.city_name,t7.item_name,t8.packing_unit_name, t9.`value` as storage_duration_value, t10.`value` as rent_per_value, t11.`value` as gst_type',
        'tbl_inward_master t INNER JOIN tbl_inward_detail t6 ON t.inward_id=t6.inward_id LEFT JOIN tbl_item_master t7 ON t6.item=t7.item_id LEFT JOIN tbl_packing_unit_master t8 ON t6.packing_unit=t8.packing_unit_id LEFT JOIN tbl_customer_master t4 ON t.customer=t4.customer_id LEFT JOIN tbl_city_master c ON c.city_id=t4.city_id LEFT JOIN tbl_customer_master t5 ON t.broker=t5.customer_id LEFT JOIN tbl_company_master c1  ON t.company_id=c1.company_id LEFT JOIN view_storage_duration t9 ON t6.storage_duration=t9.id LEFT JOIN view_rent_type t10 ON t6.rent_per=t10.id LEFT JOIN view_item_gst_type t11 ON t6.gst_type=t11.id','
        ".$company_query."')";
        $result=$_dbh->query($sql, PDO::FETCH_ASSOC);

        $_grid.='<div class="row gx-2 gy-1 align-items-center" id="search-filters">';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Customer" data-index="1" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Broker" data-index="2" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="City" data-index="3" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Inward No." data-index="4" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control date-filter" placeholder="Inward Date" data-index="5" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Lot No." data-index="6" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Item" data-index="7" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="GST Status" data-index="8" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Storage Duration" data-index="14" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Location" data-index="17" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Vehicle No." data-index="20" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Driver Name" data-index="21" /></div>';
        $_grid.='<div class="col-auto"><input type="text" class="form-control" placeholder="Transporter" data-index="22" /></div>';
        if(COMPANY_ID==ADMIN_COMPANY_ID) {
             $_grid.='<div class="col-auto"><input type="text" class="form-control date-filter" placeholder="Search Company" data-index="23" /></div>';
         }
         $_grid.='</div>';
         $_grid.='
         <table  id="searchMaster" class="ui celled table display">
         <thead>
            <tr>
                <th>Action</th>
                <th>Customer</th>
                <th>Broker</th>
                <th>City</th>
                <th>Inward No.</th>
                <th>Inward Date</th>
                <th>Lot No.</th>
                <th>Item</th>
                <th>GST Status</th>
                <th>Marko</th>
                <th>In. Qty.</th>
                <th>Unit</th>
                <th>In. Wt.<br>(kg)</th>
                <th>Avg. Wt./<br>Unit</th>
                <th>Storage Duration</th>
                <th>Rent</th>
                <th>Rent Per</th>
                <th>Location</th>
                <th>Remark</th>
                <th>Unloading Charges</th>
                <th>Vehicle No.</th>
                <th>Driver Name</th>
                <th>Transporter</th>';
         if(COMPANY_ID==ADMIN_COMPANY_ID) {
            $_grid.='<th>Company</th>';
         }
         $_grid.="</tr>
            <tbody>
        ";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {   
                $j++;
                $_grid.="<tr>";
                if($canUpdate || $canDelete) {
                    $_grid.="<td data-label=\"Action\">";
                }
                if($canUpdate) {
                    $_grid.="<form  method=\"post\" action=\"frm_inward_master.php\" style=\"display:inline; margin-rigth:5px;\">
                        <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
                        <input type=\"hidden\" name=\"inward_id\" value=\"".$_rs["inward_id"]."\" />
                        <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
                        </form>";
                }
                if($canDelete) { 
                    $_grid.="<form  method=\"post\" action=\"classes/cls_inward_master.php\" style=\"display:inline;\">
                        <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
                        <input type=\"hidden\" name=\"inward_id\" value=\"".$_rs["inward_id"]."\" />
                        <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
                        </form>";
                }
                if($canUpdate || $canDelete) {
                    $_grid.="</td>";
                }
                $_grid.= "<td data-label=\"HSN Code\"> ".$_rs['customer_name']." </td>";
                $_grid.= "<td data-label=\"Description\"> ".$_rs['broker_name']." </td>";
                $_grid.= "<td data-label=\"Tax Type\"> ".$_rs['city_name']." </td>";
                $_grid.= "<td data-label=\"Tax(%)\"> ".$_rs['inward_no']." </td>";
                $_grid.= "<td data-label=\"Effective Date\"> ".date("d/m/Y",strtotime($_rs['inward_date']))." </td>";
                $_grid.= "<td data-label=\"Remark\"> ".$_rs['lot_no']." </td>";
                $_grid.= "<td data-label=\"Item\"> ".$_rs['item_name']." </td>";
                $_grid.= "<td data-label=\"Tax Type\"> ".$_rs['gst_type']."</small></td>";
                $_grid.= "<td data-label=\"Marko\"> ".$_rs['marko']."</small></td>";
                $_grid.= "<td data-label=\"In. Qty.\"> ".$_rs['inward_qty']."</small></td>";
                $_grid.= "<td data-label=\"Unit\"> ".$_rs['packing_unit_name']."</small></td>";
                $_grid.= "<td data-label=\"In. Wt.<br>(kg)\"> ".$_rs['inward_wt']."</small></td>";
                $_grid.= "<td data-label=\"Avg. Wt./<br>Unit\"> ".$_rs['avg_wt_per_bag']."</small></td>";
                $_grid.= "<td data-label=\"Storage Duration\"> ".$_rs['storage_duration_value']."</small></td>";
                $_grid.= "<td data-label=\"Rent\"> ".$_rs['rent_per_storage_duration']."</small></td>";
                $_grid.= "<td data-label=\"Rent Per\"> ".$_rs['rent_per_value']."</small></td>";
                $_grid.= "<td data-label=\"Location\"> ".$_rs['location']."</small></td>";
                $_grid.= "<td data-label=\"Remark\"> ".$_rs['remark']."</small></td>";
                $_grid.= "<td data-label=\"Unloading Charges\"> ".$_rs['total_unloading_charge']."</small></td>";
                $_grid.= "<td data-label=\"Vehicle No.\"> ".$_rs['vehicle_no']."</small></td>";
                $_grid.= "<td data-label=\"Driver Name\"> ".$_rs['driver_name']."</small></td>";
                $_grid.= "<td data-label=\"Transporter\"> ".$_rs['transporter']."</small></td>";
                if(COMPANY_ID==ADMIN_COMPANY_ID) {
                    $_grid.= "<td data-label=\"Company\"> ".$_rs["company_id"]." </td>"; 
                }
                $_grid.= "</tr>\n";
            }
        }
        if($j==0) {
            $_grid.= "<tr>";
            $_grid.="<td>No records available.</td>";
            for($k=1;$k<23;$k++)
            {
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
            }
            if(COMPANY_ID==ADMIN_COMPANY_ID) {
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
            }
            $_grid.="</tr>";
        }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    /* \FUNCTION MODIFIED BY BHUMITA ON 15/08/2025 */
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
    /* FUNCTIONS ADDED BY BHUMITA ON 12/08/2025 */
    function fetchPackingUnitData() {
        global $_dbh,$tbl_packing_unit_master;
        if (isset($_REQUEST['packing_unit'])) {
            try {
                $packing_unit_id = intval($_REQUEST['packing_unit']);
                $stmt = $_dbh->prepare("SELECT conversion_factor, unloading_charge FROM ".$tbl_packing_unit_master." WHERE packing_unit_id = ?");
                $stmt->execute([$packing_unit_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => (bool)$row,
                    "conversion_factor" => $row ? (float)$row['conversion_factor'] : "",
                    "unloading_charge" => $row ? (float)$row['unloading_charge'] : ""
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    "success" => false,
                    "error" => "Database error: " . $e->getMessage()
                ]);
            }
            exit;
        }
        exit;
    }
    function fetchRentPerMonth() {
        global $_dbh, $tbl_customer_wise_item_preservation_price_list_detail, $tbl_customer_wise_item_preservation_price_list_master, $tbl_item_preservation_price_list_detail, $tbl_item_preservation_price_list_master;
        $customerId = $_REQUEST['customer_id'] ?? 0;
        $itemId = $_REQUEST['item_id'] ?? 0;
        $unitId = $_REQUEST['unit_id'] ?? 0;
        $rentPer = $_REQUEST['rent_per'] ?? '';
        $companyYearId = $_REQUEST['company_year_id'] ?? COMPANY_YEAR_ID;
        $isSeasonal = isset($_REQUEST['seasonal']) && $_REQUEST['seasonal'] == 1;

        if (!is_numeric($customerId) || !is_numeric($itemId) || !in_array($rentPer, ['Quantity', 'Kg']) || !is_numeric($companyYearId) || $companyYearId == 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
            exit;
        }
        try {
            $rentPerMonth = null;
            $rentPerSeason= null;
            if ($rentPer === 'Quantity') {
                $stmt = $_dbh->prepare("
                    SELECT d.rent_per_qty_month, d.rent_per_qty_season 
                    FROM ".$tbl_customer_wise_item_preservation_price_list_detail." d
                    JOIN ".$tbl_customer_wise_item_preservation_price_list_master." m 
                        ON d.customer_wise_item_preservation_price_list_id = m.customer_wise_item_preservation_price_list_id
                    WHERE m.customer_id = ? AND m.item_id = ? AND d.packing_unit_id = ? AND m.company_year_id = ? ".COMPANY_QUERY."
                    LIMIT 1
                ");
                $stmt->execute([$customerId, $itemId, $unitId, $companyYearId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                if (!$result) {
                    $stmt = $_dbh->prepare("
                        SELECT d.rent_per_qty_month, d.rent_per_qty_season 
                        FROM ".$tbl_item_preservation_price_list_detail." d
                        JOIN ".$tbl_item_preservation_price_list_master." m 
                            ON d.item_preservation_price_list_id = m.item_preservation_price_list_id
                        WHERE m.item_id = ? AND d.packing_unit_id = ? AND m.company_year_id = ? ".COMPANY_QUERY."
                        LIMIT 1
                    ");
                    $stmt->execute([$itemId, $unitId, $companyYearId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($isSeasonal) {
                    $rentPerSeason = $result['rent_per_qty_season'] ?? null;
                } 
                $rentPerMonth = $result['rent_per_qty_month'] ?? null;
                
                $stmt->closeCursor();
            } elseif ($rentPer === 'Kg') {
                $stmt = $_dbh->prepare("
                    SELECT rent_per_kg_month, rent_per_kg_season 
                    FROM ".$tbl_customer_wise_item_preservation_price_list_master." 
                    WHERE customer_id = ? AND item_id = ? AND company_year_id = ? ".COMPANY_QUERY."
                    LIMIT 1
                ");
                $stmt->execute([$customerId, $itemId, $companyYearId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                if (!$result) {
                    $stmt = $_dbh->prepare("
                        SELECT rent_per_kg_month, rent_per_kg_season 
                        FROM ".$tbl_item_preservation_price_list_master." 
                        WHERE item_id = ? AND company_year_id = ? ".COMPANY_QUERY."
                        LIMIT 1
                    ");
                    $stmt->execute([$itemId, $companyYearId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($isSeasonal) {
                    $rentPerSeason = $result['rent_per_kg_season'] ?? null;
                }
                $rentPerMonth = $result['rent_per_kg_month'] ?? null;
                
                $stmt->closeCursor();
            }
            if ($rentPerMonth !== null) {
                echo json_encode(['success' => true, 'rent_per_month' => $rentPerMonth, 'rent_per_season' => $rentPerSeason]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No rate found for the selected parameters']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;

    }
    function fetchFloors() {
        global $_dbh, $tbl_floor_master, $tbl_company_master;
        if (!isset($_REQUEST['chamber_id']) || !is_numeric($_REQUEST['chamber_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid chamber ID']);
            exit;
        }
        $chamberId = intval($_REQUEST['chamber_id']);
        try {
            $columns = "floor_id, floor_name";
            $table_name = $tbl_floor_master;
             if(COMPANY_ID==ADMIN_COMPANY_ID) {
                $columns.=", company_name";
                $table_name=$table_name." LEFT JOIN ".$tbl_company_master." ON ".$tbl_company_master.".company_id = ".$table_name.".company_id";
            }
            $wherestr = COMPANY_QUERY." AND chamber_id=".$chamberId;

            $sql="CALL csms_search_detail(?,?,?)";
            $stmt = $_dbh->prepare($sql);
            $stmt->bindParam(1, $columns);
            $stmt->bindParam(2, $table_name);
            $stmt->bindParam(3, $wherestr);
            $stmt->execute();
            $floors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if ($floors) {
                echo json_encode(['success' => true, 'floors' => $floors]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No floors found for the selected company and year']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
    /* \FUNCTIONS ADDED BY BHUMITA ON 12/08/2025 */
    public function getForm($transactionmode="I",$popup=false,$label_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1", $field_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2") {
        /* ADDED BY BHUMITA ON 30/07/2025 */
        global $next_inward_sequence, $inward_no_formatted;
        $next_inward_sequence = isset($next_inward_sequence) ? $next_inward_sequence : 1;
        $inward_no_formatted = isset($inward_no_formatted) ? $inward_no_formatted : '';
        /* \ADDED BY BHUMITA ON 30/07/2025 */

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
            /* ADDED BY BHUMITA ON 30/07/2025 */
            $output .= '
                <div class="row mb-2 align-items-center">
                    <label for="inward_sequence" class="' . $label_layout_classes . '">Inward No</label>
                    <div class="' . $field_layout_classes . '">
                        <div class="row g-2">
                            <div class="col-6">'.
                            addNumber("inward_sequence", $next_inward_sequence,"required","","form-control duplicate","duplicate","min='1'","","Inward Sequence")
                                .'<div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">'.
                            addInput("text","inward_no",$inward_no_formatted,"required","disabled","form-control duplicate","duplicate","",$this->_mdl->generator_fields_labels[2])
                                .'
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>';
            /* \ADDED BY BHUMITA ON 30/07/2025 */
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                /* ADDED BY BHUMITA ON 30/07/2025 */
                if ($fieldname == "inward_sequence" || $fieldname == "inward_no") {
                    continue;
                }
                /* \ADDED BY BHUMITA ON 30/07/2025 */
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
                                $field_str.=addNumber($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str,$this->_mdl->generator_fields_labels[$i],$disabled_value,$max_str).$error_container;
                            }
                            else if($this->_mdl->generator_fields_types[$i]=="select") {
                                $cls="form-select ".$required_str." ".$duplicate_str;
                                
                                if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                    $field_str.=getDropdown($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $disabled_str).$error_container;
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
                        $output.='<div class="row mb-2 align-items-center">';
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
                    $lbl_array[]=$this->_mdl->generator_fields_labels[$i]; // CHANGED BY BHUMITA ON 30/07/2025
                    $field_array[]=$field_str;
                }
            } // foreach ends
            $output.="</div><!-- /.row -->";
            $output.=$hidden_str;
               $output.="</div> <!-- /.box-body -->";
        
                $output.="<!-- detail table content--> 
                        <div class=\"box-body\">
                            <div class=\"box-detail\">";
                                     $_blldetail = new bll_inwarddetail();
                                     $detailHtml = $_blldetail->pageSearch();
                                    if($detailHtml)
                                        $output.=$detailHtml; 
                                $output.="<button type=\"button\" name=\"detailBtn\" id=\"detailBtn\" class=\"btn btn-primary add\" data-bs-toggle=\"modal\" data-bs-target=\"#modalDialog\"  onclick=\"openModal()\">Add Detail Record</button>
                        </div>
                    </div>
                    <!-- /.box-body detail table content -->";
            
                /* BELOW CODE MODIFIED BY BHUMITA ON 30/07/2025 */
               if (!empty($field_array)) {
                    $output .= '<div class="box-body">';
                    $output .= '<div class="form-group"><div class="row">';
                    for ($j = 0; $j < 6; $j++) {
                        $label = isset($lbl_array[$j]) ? htmlspecialchars($lbl_array[$j], ENT_QUOTES, 'UTF-8') : '';
                        $field = isset($field_array[$j]) ? $field_array[$j] : '';
                        $output .= '
                            <div class="col-12 col-md-6 col-lg-4 mb-2">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label text-truncate" title="' . $label . '">' . $label . '</label>
                                    <div class="col-7">' . $field . '</div>
                                </div>
                            </div>';
                    }
                    $output .= '</div></div>';
                    $output .= '
                        <div class="form-group">
                            <div class="row">
                                <!-- Left column -->
                                <div class="col-12 col-lg-4 mb-2">
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[10], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[10] . '</label>
                                        <div class="col-7">' . $field_array[10] . '</div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[8], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[8] . '</label>
                                        <div class="col-7">' . $field_array[8] . '</div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[9], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[9] . '</label>
                                        <div class="col-7">' . $field_array[9] . '</div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[7], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[7] . '</label>
                                        <div class="col-7">' . $field_array[7] . '</div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate" title="' . htmlspecialchars($lbl_array[6], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[6] . '</label>
                                        <div class="col-7">' . $field_array[6] . '</div>
                                    </div>
                                </div>

                                <!-- Middle column -->
                                <div class="col-12 col-lg-4 mb-2">
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate"
                                               title="' . htmlspecialchars($lbl_array[11], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[11] . '</label>
                                        <div class="col-7">
                                            <div class="row gx-2 align-items-center">
                                                <div class="col">' . $field_array[11] . '</div>
                                                <div class="col-auto text-center px-1">:</div>
                                                <div class="col">' . $field_array[12] . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate"
                                               title="' . htmlspecialchars($lbl_array[13], ENT_QUOTES, 'UTF-8') . '">' . $lbl_array[13] . '</label>
                                        <div class="col-7">
                                            <div class="row gx-2 align-items-center">
                                                <div class="col">' . $field_array[13] . '</div>
                                                <div class="col-auto text-center px-1">:</div>
                                                <div class="col">' . $field_array[14] . '</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column (totals etc.) -->
                                <div class="col-12 col-lg-4 mb-2">
                                    <!-- Example: put Total Wt., Net Wt. etc. here in same label+input pattern -->
                                    <!--
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate">Total Wt. (Kg)</label>
                                        <div class="col-7"><input class="form-control" readonly value="0.00"></div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <label class="col-4 col-form-label text-truncate">Net Wt. (Kg)</label>
                                        <div class="col-7"><input class="form-control" readonly value="0.00"></div>
                                    </div>
                                    -->
                                </div>

                            </div>
                        </div>
                    ';

                    $output .= '</div>'; // /.box-body
            }
              /* \BELOW CODE MODIFIED BY BHUMITA ON 30/07/2025 */
        } // if ends
        return $output;
    } // function getForm ends
}
 class dal_inwardmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        try {
            if($_mdl->_inward_id=="") {
                $_mdl->_inward_id=0;
            }
            $_dbh->exec("set @p0 = ".$_mdl->_inward_id);
            $_pre=$_dbh->prepare("CALL inward_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            
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
                    $_mdl->_inward_id=$insertedId;
                }
                if($_mdl->_ajaxAdd==1) {
                    $_mdl->_ajaxAdd=0;
                    if($_mdl->_inward_id)
                        $return_id=$_mdl->_inward_id;
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
            }    catch (PDOException $e) {
                global $tbl_inward_master;
                $ajax=0;
                if($_mdl->_ajaxAdd==1) {
                    $ajax=1;
                    $_mdl->_ajaxAdd=0;
                }
                errorHandling($e, "inward_id", $tbl_inward_master, $ajax, $_mdl->_inward_id);
                
        }
            /* \ ADDED BY HETANSHREE FOREIGNKEY DELETE ERROR MESSAGE*/
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_inward_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL inward_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["inward_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {
            if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                foreach($_mdl->generator_fields_names as $i=>$fieldname)
                {
                    $_mdl->{"_".$fieldname}=$_rs[0][$fieldname];
                }
            }
            $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_inwardmaster();

/*** FOR DETAIL ***/
$_blldetail=new bll_inwarddetail();
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
    
    /* ADDED BY HETANSHREE*/
    $outwarded_fields = ['inward_sequence','customer','broker','inward_date'];
    $db_field_values = [];
    if ($tmode == "U" && isset($_POST['inward_id'])) {
        $sql = "SELECT COUNT(*) FROM tbl_outward_detail od INNER JOIN tbl_inward_detail id ON od.inward_detail_id = id.inward_detail_id WHERE id.inward_id = ?";
        $stmt = $_dbh->prepare($sql);
        $stmt->execute([$_POST['inward_id']]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $sql2 = "SELECT inward_sequence, customer, broker, inward_date FROM tbl_inward_master WHERE inward_id = ?";
            $stmt2 = $_dbh->prepare($sql2);
            $stmt2->execute([$_POST['inward_id']]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $db_field_values = $row;
            }
        }
    }
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
                $upload_dir = UPLOAD_DIR ."inward_master/";
                $upload_path = UPLOAD_PATH ."inward_master/";
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
            /* MODIFIED BY HETANSHREE */
            if(!empty($db_field_values) && in_array($fieldname, $outwarded_fields)) {
                $field = $db_field_values[$fieldname];
            } else if(isset($_REQUEST[$fieldname]) && $_REQUEST[$fieldname] !== '') {
                $field = $_REQUEST[$fieldname];
                if($_bll->_mdl->generator_fields_types[$i]=="datetime-local" || $_bll->_mdl->generator_fields_types[$i]=="datetime" || $_bll->_mdl->generator_fields_types[$i]=="timestamp") {
                    $field=date("Y-m-d H:i:s",strtotime($field));
                } else if($_bll->_mdl->generator_fields_types[$i]=="date") {
                    $field=date("Y-m-d",strtotime($field));
                }else if(is_array($field) && ($_bll->_mdl->generator_fields_types[$i]=="checkbox" || $_bll->_mdl->generator_fields_types[$i]=="radio") && $_bll->_mdl->generator_dropdown_table!="" && $_bll->_mdl->generator_label_column!="" && $_bll->_mdl->generator_value_column!="") {
                    $field = implode(",", $field);
                }
                $field=trim($field);
            } else {
                $field=null;
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
        $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
