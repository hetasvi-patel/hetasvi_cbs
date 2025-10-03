<?php
function valueExists($db, $tbl, $key, $val)
{
    $query = "SELECT * FROM ".$tbl." WHERE ".$key."=:val;";
    $stmt = $db->prepare($query);
    $stmt->execute(array(':val' => $val));
    return !!$stmt->fetch(PDO::FETCH_ASSOC);
}
class TableRows extends RecursiveIteratorIterator {
  function __construct($it) {
    parent::__construct($it, self::LEAVES_ONLY);
  }
  function current(): mixed {
      $val=parent::current();
      return $val;
  }
  function beginChildren(): void {
    echo "<tr>";
  }
  function endChildren(): void {
    echo "</tr>" . "\n";
  }
}
function isDetailTable(string $tableName): bool {
    $tableName = strtolower($tableName);
    return str_ends_with($tableName, '_detail');
}
function getDropdown($table_name,$id,$name,$wherestr,$dropdown_name,$selected_value,$classes="",$required="",$disabled="",$data1="",$datavalue1="") {
        global $_dbh,$tbl_company_master;
        $datastr1="";
        $columns=$id.",".$name;
        if($datavalue1!="") {
            $columns.=",".$datavalue1;
        }
        if($table_name=="view_storage_duration") {
            $columns.=",label";
        }
        if($wherestr!="") {
             if(!isDetailTable($table_name) && COMPANY_ID==ADMIN_COMPANY_ID && strpos($wherestr, "COMPANY_QUERY") !== false) {
                $columns.=",company_name";
                if(strpos($table_name, "JOIN") !== false)
                    $table_name=$table_name." LEFT JOIN ".$tbl_company_master." ON ".$tbl_company_master.".company_id = cm.company_id";
                else
                    $table_name=$table_name." LEFT JOIN ".$tbl_company_master." ON ".$tbl_company_master.".company_id = ".$table_name.".company_id";
            }
            $wherestr=str_replace("USER_ID", USER_ID, $wherestr);
            $wherestr=str_replace("PERSON_NAME", PERSON_NAME, $wherestr);
            $wherestr=str_replace("COMPANY_ID", COMPANY_ID, $wherestr);
            $wherestr=str_replace("COMPANY_YEAR_ID", COMPANY_YEAR_ID, $wherestr);
            $wherestr=str_replace("cm.COMPANY_QUERY", COMPANY_QUERY_ALIAS, $wherestr);
            $wherestr=str_replace("COMPANY_QUERY", COMPANY_QUERY, $wherestr);
            $wherestr=str_replace("STATUS_QUERY", STATUS_QUERY, $wherestr);
           
        }
        $wherestr=" ".$wherestr;
        
        /*echo $table_name;
        echo "<br><br>";
        echo $wherestr; 
        echo "<br><br>";
        echo $columns;  
        echo "<br><br>";*/

        $sql="CALL csms_search_detail(?,?,?)";
        $stmt = $_dbh->prepare($sql);
        $stmt->bindParam(1, $columns);
        $stmt->bindParam(2, $table_name);
        $stmt->bindParam(3, $wherestr);

        $name=str_replace("cm.","",$name);

        $field_str="<select id=\"".$dropdown_name."\" name=\"".$dropdown_name."\" class=\"".$classes."\" style=\"width: 100%;\" ".$required." ".$disabled." >";
        $field_str.="<option value=''>Select</option>";
        if(!empty($columns) && !empty($table_name) && $stmt->execute()) {
            while( $_rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $value=$_rs[$id];
                if(strpos($name, ",") !== false) {
                    $name_array=explode(",",$name);
                    $name1=$_rs[$name_array[0]];
                    $name2=$_rs[$name_array[1]];
                    if($name1=="" && $name2=="") {
                        $label="";
                    } elseif($name1=="" && $name2!="") {
                        $label=$name2;
                    } elseif($name1!="" && $name2=="") {
                        $label=$name1;
                    } else {
                        $label=$name1."-".$name2;
                    }
                } else {
                    $label=$_rs[$name];
                }
               
                $selected="";
                if($selected_value!="" && $value==$selected_value) {
                    $selected='selected="selected"';
                }
                if($data1!="" && $datavalue1!="") {
                    $datastr1=" data-".$data1."=\"".$_rs[$datavalue1]."\"";
                }
                if($label)
                    $label=ucfirst($label);
                $label_company=$label;
                if(COMPANY_ID==ADMIN_COMPANY_ID && isset($_rs['company_name']) && $_rs['company_name']!="") {
                    $label_company=$label." (".ucfirst($_rs['company_name']).")";
                }
                $data_label=$label;
                if($table_name=="view_storage_duration") {
                    $data_label = $_rs['label'] ?? $label;
                }
                $field_str.='<option data-label="'.$data_label.'" '.$datastr1.' value="'.$value.'" '.$selected.'  >'.$label_company.'</option>';
            }
        }
        $field_str.="</select>";
        if($disabled!="") {
            $field_str.='<input type="hidden" name="'.$dropdown_name.'" value="'.$selected_value.'" />';
        }
        return $field_str;
}
/* ADDED BY BHUMITA */
function getChecboxRadios($table_name,$id,$name,$wherestr,$field_name,$selected_value,$classes="",$required="",$field_type="checkbox",$disabled="") {
  global $_dbh;
  $columns=$id.",".$name;

  if($wherestr!="") {
        $wherestr=str_replace("USER_ID", USER_ID, $wherestr);
        $wherestr=str_replace("PERSON_NAME", PERSON_NAME, $wherestr);
        $wherestr=str_replace("COMPANY_ID", COMPANY_ID, $wherestr);
        $wherestr=str_replace("COMPANY_YEAR_ID", COMPANY_YEAR_ID, $wherestr);
        $wherestr=str_replace("cm.COMPANY_QUERY", COMPANY_QUERY_ALIAS, $wherestr);
        $wherestr=str_replace("COMPANY_QUERY", COMPANY_QUERY, $wherestr);
        $wherestr=str_replace("STATUS_QUERY", STATUS_QUERY, $wherestr);
    }

  $sql="CALL csms_search_detail(?,?,?)";
  $stmt = $_dbh->prepare($sql);
  $stmt->bindParam(1, $columns);
  $stmt->bindParam(2, $table_name);
  $stmt->bindParam(3, $wherestr);

  $field_str="";
  if($field_type=="checkbox") {
    $field_name=$field_name."[]";
  }
  if($stmt->execute()) {
    $field_str.='<div class="d-flex align-items-center gap-3 flex-wrap">';
      while($_rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $value=$_rs[$id];
          $label=$_rs[$name];
          $selected="";
          if($selected_value!="") {
            $selected_array=explode(",",$selected_value);
            if(in_array($value,$selected_array)) {
              $selected='checked="checked"';
            } 
          }
          
          $field_str.='<div class="form-check form-check-inline">
          ';       
          
          $field_str.='<input class="form-check-input '.$classes.'" id="'.$field_name.'" type="'.$field_type.'" name="'.$field_name.'" value="'.$value.'" '.$selected.' />
          <label class="form-check-label" for="'.$field_name.'">'.$label.'</label>
          ';
          
          $field_str.="</div>
          ";
      }
      $field_str.="</div>";
      if($disabled!="") {
          $field_str.='<input type="hidden" name="'.$field_name.'" value="'.$selected_value.'" />';
      }
  }
  return $field_str;
}   
/* \ADDED BY BHUMITA */
/* \ADDED BY HETASVI */
function render_date_filter($fromDate, $toDate, $fromId = 'from-date', $toId = 'to-date', $btnId = 'btn-date-search', $containerId = 'date-filters') {
    echo '<div class="row gx-2 gy-1 align-items-center mb-2" id="'.htmlspecialchars($containerId ?? '').'">';
    echo '<div class="col-auto d-flex align-items-center">
            <label for="'.htmlspecialchars($fromId ?? '').'" class="form-label mb-0 me-2">From</label>
            <input type="text" class="form-control date-filter" id="'.htmlspecialchars($fromId ?? '').'" name="'.htmlspecialchars($fromId ?? '').'" placeholder="From Date" value="'.htmlspecialchars($fromDate ?? '').'" autocomplete="off" style="width:120px;" />
          </div>';
    echo '<div class="col-auto d-flex align-items-center">
            <label for="'.htmlspecialchars($toId ?? '').'" class="form-label mb-0 me-2">To</label>
            <input type="text" class="form-control date-filter" id="'.htmlspecialchars($toId ?? '').'" name="'.htmlspecialchars($toId ?? '').'" placeholder="To Date" value="'.htmlspecialchars($toDate ?? '').'" autocomplete="off" style="width:120px;" />
          </div>';
    echo '<div class="col-auto">
            <button type="button" class="btn btn-primary" id="'.htmlspecialchars($btnId ?? '').'">Search</button>
          </div>';
    echo '</div>';
}
/* \ADDED BY HETASVI */
//Done By Hetasvi getDynamicMenu()
function getDynamicMenu() {
    global $_dbh;
    $menuData = [];
    try {
        if(USER_ID==ADMIN_USER_ID) {
            $userid=0;
        } else {
            $userid=USER_ID;
        }
        $stmt = $_dbh->prepare("CALL csms_menu_search(".$userid.")");
        $stmt->execute();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $module = $row['module_text'] ?? 'Others'; 
            $menuGroup = $row['menu_group'] ?? 0;
            $menuItem = [
                'menu_id'   => $row['menu_id'] ?? 0,
                'name' => $row['menu_text'] ?? 'No Name', 
                'link' => $row['menu_link'] ?? '#',
                'menu_group' => $menuGroup
            ];
            $menuData[$module][$menuGroup][] = $menuItem;
        }
        $stmt->closeCursor();
    } catch (PDOException $e) {
        error_log("Menu Fetch Error: " . $e->getMessage());
    }
    return $menuData;
}

/* ADDED BY BHUMITA */
function checkPermissionVersion() {
  global $tbl_user_master;
  global $_dbh;
    if (empty($_SESSION['user_id']) || empty($_SESSION['permission_version'])) {
        return; // User not logged in or session not fully initialized yet
    }

    $user_id = $_SESSION['sess_user_id'] ?? null;
    if (!$user_id) return; // Not logged in

    if($user_id==ADMIN_USER_ID) return; // Admin user, no need to check
    
    $stmt = $_dbh->prepare("CALL csms_search_detail('permission_version', '".$tbl_user_master."','user_id=".$user_id."')");
    $stmt->execute();
    $currentVersion = $stmt->fetchColumn();
    $stmt->closeCursor();
    if ($currentVersion !== $_SESSION['sess_permission_version']) {
        // Permissions changed! Destroy session to force logout
        session_unset();
        session_destroy();

        // Optionally redirect
       echo "<script>location.href='".ENCODED_BASE_URL."?err=permission_changed'</script>";
      exit();
    }
}
function userHasRight($user_id, $menu_id, $right_name) {
    global $_dbh;
    global $tbl_user_right_master;
    global $tbl_menu_right_master;

    /* echo $user_id;
    echo "<br>";
    echo $menu_id;
    echo "<br>";
    echo $right_name;
    echo "<br>"; */

    if (empty($user_id) || empty($menu_id) || empty($right_name)) {
        return false; // Invalid parameters
    }
    if($user_id==ADMIN_USER_ID) return true; // Admin user, has all rights
    
    
    $sql = "SELECT COUNT(*) 
            FROM ".$tbl_user_right_master." urm
            JOIN ".$tbl_menu_right_master." mrm ON urm.menu_right_id = mrm.menu_right_id
            WHERE urm.user_id = :user_id 
              AND mrm.menu_id = :menu_id
              AND mrm.right_name = :right_name
              AND urm.has_right = 1";

    $stmt = $_dbh->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':menu_id' => $menu_id,
        ':right_name' => $right_name
    ]);

    return $stmt->fetchColumn() > 0;
}
function getMenuId($frm_page_name="") {
    global $_dbh;
    global $tbl_menu_master;
    if($frm_page_name!="")
    {
        $url = strtolower($frm_page_name);
    } else {
        $url = strtolower(basename($_SERVER['PHP_SELF']));
    }
    $url_array=explode("frm_",$url); // current file name
    $pagename=$url_array[1] ?? '';
    if(empty($pagename)) {
        $url_array=explode("srh_",$url); // current file name
        $pagename=$url_array[1] ?? '';
    }
    $stmt = $_dbh->prepare("SELECT menu_id FROM ".$tbl_menu_master." WHERE menu_url LIKE :url LIMIT 1");
    $stmt->execute([':url' => '%' . $pagename]);
    return $stmt->fetchColumn();
}
function getCurrentMenuLabel() {
    global $_dbh;
    global $tbl_menu_master;
    $url = strtolower(basename($_SERVER['PHP_SELF']));
    $url_array=explode("frm_",$url); // current file name
    $pagename=$url_array[1] ?? '';
    if(empty($pagename)) {
        $url_array=explode("srh_",$url); // current file name
        $pagename=$url_array[1] ?? '';
    }
    $stmt = $_dbh->prepare("SELECT menu_text FROM ".$tbl_menu_master." WHERE menu_url LIKE :url LIMIT 1");
    $stmt->execute([':url' => '%' . $pagename]);
    return $stmt->fetchColumn();
}
function addInput($type, $name, $value = '', $required = "", $disabled = "", $cls="", $duplicate="", $chk="",$label = '', $disabled_value = "") {
    if($label==""){
        $label=ucwords(str_replace("_"," ",$name));
    }
    $disabled_value = $disabled_value ?: $value; // Use provided disabled value or fallback to value
    $html= '<input type="'.$type.'" class="'.$cls.'" id="'.$name.'" name="'.$name.'" placeholder="'.$label.'" value= "'.$disabled_value.'" '.$disabled.' '.$required.' '.$chk.' '.$duplicate.' />';
    if($disabled!="") {
        $html.= '<input type="hidden" id="hid_'.$name.'" name="'.$name.'" value="'.$value.'" />';
    }
    return $html;
}
function addNumber($name, $value = "", $required = "", $disabled = "", $cls="", $duplicate="", $min = "", $step = "",$label = '',$disabled_value = "",$max_str="") {
    if($label==""){
        $label=ucwords(str_replace("_"," ",$name));
    }
    if($disabled_value=="") {
        $disabled_value=$value;
    }
    $html= '<input type="number" class="'.$cls.'" id="'.$name.'" name="'.$name.'" placeholder="'.$label.'" '.$max_str.'  '.$min.' '.$step.' '.$disabled.' '.$required.'  '.$duplicate.'  value="'.$disabled_value.'" />';
    if($disabled!="") {
        $html.= '<input type="hidden" id="hid_'.$name.'" name="'.$name.'" value="'.$value.'" />';
    }
    return $html;
}
function addHidden($name, $value = '', $cls="") {
    $html= '<input type="hidden" class="'.$cls.'" id="'.$name.'" name="'.$name.'"  value= "'.$value.'" />';
    return $html;
}
function addCheckboxRadio($type, $name, $label = '', $value = '', $required = "", $disabled = "", $cls="" ,$duplicate="", $chk="") {
    $html= '<input type="'.$type.'" class="'.$cls.'" id="'.$name.'" name="'.$name.'" placeholder="Enter '.ucwords(str_replace("_"," ",$name)).'" value= "'.$value.'"  '.$chk.' '.$disabled.' '.$required.' '.$duplicate.' /> '.$label;
    if($disabled!="") {
        $html.= '<input type="hidden" id="hid_'.$name.'" name="'.$name.'" value="'.$value.'" />';
    }
    return $html;
}
function addDropDown($name, $options = [], $value = '', $required = "", $disabled = "", $cls="",$duplicate="", $selectLabel="Select") {
    $html= '<select id="'.$name.'" name="'.$name.'" class="'.$cls.'" '.$disabled.' '.$required.' '.$duplicate.'>';
    $html.= '<option value="">'.$selectLabel.'</option>';
    if(is_array($options) && !empty($options)) {
        foreach ($options as $option) {
            $optionValue = $option['value'] ?? '';
            $optionText =$option['label'] ?? '';
            $selected = ($optionValue == $value) ? 'selected' : '';
            $html.= '<option value="'.$optionValue.'" '.$selected.'>'.$optionText.'</option>';
        }
    }
    $html.= '</select>';
    if($disabled!="") {
        $html.= '<input type="hidden" id="hid_'.$name.'" name="'.$name.'" value="'.$value.'" />';
    }
    return $html;
}
function addTextArea($name, $value = '', $required = "", $disabled = "", $cls="", $duplicate="", $rows=3,$label = '') {
    if($label=="")
    {
        $label=ucwords(str_replace("_"," ",$name));
    }
    $html= '<textarea class="'.$cls.'" id="'.$name.'" name="'.$name.'" rows="'.$rows.'" placeholder="'. $label.'" '.$disabled.' '.$required.' '.$duplicate.'>'.$value.'</textarea>';
    if($disabled!="") {
        $html.= '<input type="hidden" id="hid_'.$name.'" name="'.$name.'" value="'.$value.'" />';
    }
    return $html;
}
function getMessageHTML() {
    
    $message = '';

    /*  
        ICON CLASSES
    -------------------------
        info-fill
        exclamation-triangle-fill
        check-circle-fill
        x-circle-fill
        question-circle-fill
        exclamation-octagon-fill
        x-octagon-fill
        check-square-fill
        x-square-fill
        info-square-fill
        question-square-fill

        You can pass these values to icon in Swal icon "success", "error", "warning", "info" or "question",

        MESSAGE CLASSES
    -------------------------
        primary
        secondary
        success
        danger
        warning
        info
        light
        dark
    */
     if(isset($_SESSION['sess_message_cls']) && !empty($_SESSION['sess_message_cls'])) {
        $cls= $_SESSION['sess_message_cls'];
    } else {
        $cls = 'primary'; // Default class if not set
    }
    if(isset($_SESSION['sess_message_title']) && !empty($_SESSION['sess_message_title'])) {
        $title= $_SESSION['sess_message_title'];
    } else {
        $title = ''; // Default class if not set
    }
    if(isset($_SESSION['sess_message_icon']) && !empty($_SESSION['sess_message_icon'])) {
        $icon= $_SESSION['sess_message_icon'];
    } else {
        $icon = 'info-fill'; // Default class if not set
    }
    if (isset($_SESSION['sess_message']) && !empty($_SESSION['sess_message'])) {
        $message = '<div class="alert alert-'.$cls.' alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"> <i class="bi bi-'.$icon.' me-3 fs-4"></i>'.$title.'</h4>
            <p>'.$_SESSION['sess_message'].'</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        unset($_SESSION['sess_message']);
        unset($_SESSION['sess_message_cls']);
        unset($_SESSION['sess_message_title']);
        unset($_SESSION['sess_message_icon']);
    }
    return $message;
}
function frmAlert($redirect_url="")
{
    if(isset($_SESSION["sess_message_cls"]) && $_SESSION["sess_message_cls"]!="") {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            const result=Swal.fire({
                title: '".$_SESSION["sess_message_title"]."',
                text: '".addslashes($_SESSION["sess_message"])."',
                icon: '".$_SESSION["sess_message_icon"]."',
                didOpen: () => {
                    const okButton = Swal.getConfirmButton();

                    // Fallback 1: Set tabindex and autofocus
                    okButton.setAttribute('tabindex', '0');
                    okButton.setAttribute('autofocus', 'true');
                    setTimeout(() => {
                        okButton.focus();
                    }, 50);
                }
            }).then(() => {
                const masterForm = document.getElementById('masterForm');
                if (masterForm) {
                    const firstInput = masterForm.querySelector('input:not([type=hidden]), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }
            });";
            if($redirect_url!="") {
                echo "if (result.isConfirmed) {location.href='".$redirect_url."';}";
            }
        echo "});
       </script>";
        unset($_SESSION["sess_message"]);
        unset($_SESSION["sess_message_cls"]);
        unset($_SESSION["sess_message_title"]);
        unset($_SESSION["sess_message_icon"]);
    }
}
$menu_id = getMenuId();
$canAdd = userHasRight(USER_ID, $menu_id, 'add');
$canUpdate = userHasRight(USER_ID, $menu_id, 'edit');
$canDelete = userHasRight(USER_ID, $menu_id, 'delete');
$canView = userHasRight(USER_ID, $menu_id, 'view');
$canExcel = userHasRight(USER_ID, $menu_id, 'excel');

if(USER_ID==ADMIN_USER_ID) {
    $canAdd = false;
    $canUpdate = false;
    $canDelete = false;
    $canView = true;
    $canExcel = false;
}

function checkFrmPermission($transactionmode,$currentmenu_label,$page) {
    global $canAdd,$canUpdate;
   
    if( $transactionmode=="U")       
    {
        if (!$canUpdate) {
            $_SESSION["sess_message"]="You don't have permission to update ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            echo "<script>location.href='".BASE_URL.$page."'</script>";
            exit();
        }
    } else {
        if (!$canAdd) {
            $_SESSION["sess_message"]="You don't have permission to add ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            echo "<script>location.href='".BASE_URL.$page."'</script>";
            exit();
        }
        $label="Add";
    }
}
function checkSrhPermission() {
    global $canView;
    if(!$canView) {
        if(!isset($_SESSION["sess_message"]) || $_SESSION["sess_message"]=="") {
            $_SESSION["sess_message"]="You don't have permission to view countries.";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
        }
        echo "<script>location.href='".BASE_URL."dashboard.php'</script>";
        exit;
    }
}
/* \ADDED BY BHUMITA */

/* CREATED BY MANSI. MODIFIED BY BHUMITA ON 04/08/2025 */
function getNextSequenceAndNo($table_name, $sequence_column, $no_column, $date_column, $company_year_id, $id = null, $id_column = 'id', $invoice_type = null, $invoice_type_column = '') {
    global $_dbh;
    $next_sequence = 1;
    $formatted_no = '';
    $fin_year = '';
    $start_date=date('Y-m-d');
    $end_date=date('Y-m-d');
 
    try {
        if ($company_year_id) {
            $stmt = $_dbh->prepare("
                SELECT
                    CONCAT(LPAD(YEAR(start_date) % 100, 2, '0'), '-', LPAD(YEAR(end_date) % 100, 2, '0')) AS short_range,
                    start_date, end_date
                FROM tbl_company_year_master
                WHERE company_year_id = ? ".COMPANY_QUERY."
            ");
            $stmt->execute([$company_year_id]);
            $year_row = $stmt->fetch(PDO::FETCH_ASSOC);
 
            if ($year_row) {
                $fin_year = $year_row['short_range'];
                $start_date = $year_row['start_date'];
                $end_date = $year_row['end_date'];
                // If editing existing record, get its sequence and number
                if ($id) {
                    if($invoice_type_column!="" && $invoice_type !== null) {
                        $select_columns=$sequence_column.", ".$no_column.", ".$invoice_type_column;
                    } else {
                        $select_columns=$sequence_column.", ".$no_column;
                    }
                    $stmt3 = $_dbh->prepare("SELECT $select_columns FROM $table_name WHERE $id_column = ? ".COMPANY_QUERY);
                    $stmt3->execute([$id]);
                    $row = $stmt3->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $next_sequence = $row[$sequence_column];
                        $formatted_no = $row[$no_column];
                        return [
                            'next_sequence' => $next_sequence,
                            'formatted_no' => $formatted_no,
                            'fin_year' => $fin_year,
                            'start_date' =>  $start_date,
                            'end_date' =>  $end_date,
                        ];
                    }
                }
 
                // Build condition for invoice type
                $extra_condition = '';
                $params = [$start_date, $end_date];
                if ($invoice_type !== null && $invoice_type_column!="") {
                    $extra_condition = " AND $invoice_type_column = ? ";
                    $params[] = $invoice_type;
                }
 
                // Get max sequence for company and year
                $stmt2 = $_dbh->prepare("
                    SELECT MAX($sequence_column) AS max_seq
                    FROM $table_name
                    WHERE $date_column BETWEEN ? AND ? ".$extra_condition." ".COMPANY_QUERY."
                ");
                $stmt2->execute($params);
                $seq_row = $stmt2->fetch(PDO::FETCH_ASSOC);
 
                if ($seq_row && is_numeric($seq_row['max_seq'])) {
                    $next_sequence = $seq_row['max_seq'] + 1;
                }
 
                $sequence_padded = str_pad($next_sequence, 4, '0', STR_PAD_LEFT);
                if($invoice_type !== null && $invoice_type_column!="") {
                    switch ($invoice_type) {
                        case 1: // Regular
                            $sequence_padded = $sequence_padded;
                            break;
                        case 2: // Tax Invoice
                            $sequence_padded = 'TS' . $sequence_padded;
                            break;
                        case 3: // Bill of Supply
                            $sequence_padded = 'ES' . $sequence_padded;
                            break;
                        default:
                            $sequence_padded = $sequence_padded; // fallback
                    }
                }
                $formatted_no = $sequence_padded . '/' . $fin_year;
            } else {
                throw new Exception("Financial year not found for company_year_id: $company_year_id");
            }
        } else {
            throw new Exception("Company year ID or company ID not provided");
        }
    } catch (PDOException $e) {
        $_SESSION["sess_message"] = "Database error: " . $e->getMessage();
        $_SESSION["sess_message_cls"] = "danger";
        $_SESSION["sess_message_title"] = "Error!";
        $_SESSION["sess_message_icon"] = "error";
    } catch (Exception $e) {
        $_SESSION["sess_message"] = $e->getMessage();
        $_SESSION["sess_message_cls"] = "danger";
        $_SESSION["sess_message_title"] = "Error!";
        $_SESSION["sess_message_icon"] = "error";
    }
 
    return [
        'next_sequence' => $next_sequence,
        'formatted_no' => $formatted_no,
        'fin_year' => $fin_year,
        'start_date' =>  $start_date,
        'end_date' =>  $end_date,
    ];
}
/* \CREATED BY MANSI. MODIFIED BY BHUMITA ON 04/08/2025 */

/* ADDED BY BHUMITA ON 20/08/2025 */
 function getCompanyField($field_name){
    global $_dbh,$tbl_state_master, $tbl_company_master;
    $state="";
    try {
        $stmt = $_dbh->prepare("CALL csms_getval('company_id', ".COMPANY_ID.",'".$field_name."','".$tbl_company_master."')");
        $stmt->execute();
        $state = $stmt->fetchColumn();
        $stmt->closeCursor();
    } catch (PDOException $e) {
         $_SESSION["sess_message"]=addslashes($e->getMessage());
        $_SESSION["sess_message_cls"]="danger";
        $_SESSION["sess_message_title"]="Error!";
        $_SESSION["sess_message_icon"]="error";
    }
    return $state;
}
/* \ADDED BY BHUMTIA ON 20/08/2025 */

/* ADDED BY hetanshree ON 01/09/2025 */
function getForeignKeyChildTables($parentTable, $parentColumn) {
    global $_dbh;
    $sql = "SELECT TABLE_NAME, COLUMN_NAME
              FROM information_schema.KEY_COLUMN_USAGE
             WHERE REFERENCED_TABLE_NAME = :parentTable
               AND REFERENCED_COLUMN_NAME = :parentColumn
               AND TABLE_SCHEMA = DATABASE()";
    $stmt = $_dbh->prepare($sql);
    $stmt->execute([':parentTable' => $parentTable, ':parentColumn' => $parentColumn]);
    $tables = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tables[] = ['table' => $row['TABLE_NAME'], 'column' => $row['COLUMN_NAME']];
    }
    return $tables;
}

function getForeignKeyChildTablesInUse($parentTable, $parentColumn, $parentId) {
    global $_dbh;
    $refs = getForeignKeyChildTables($parentTable, $parentColumn);
    $usedTables = [];
    foreach ($refs as $ref) {
        $table = $ref['table'];
        $column = $ref['column'];
        $stmt = $_dbh->prepare("SELECT COUNT(*) FROM `$table` WHERE `$column` = ?");
        $stmt->execute([$parentId]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $usedTables[] = $table;
        }
    }
    return $usedTables;
}

function getForeignKeyErrorMessageDynamic($parentTable, $parentColumn, $usedInTables = null) {
    $tableToModule = function($table) {
        $table = preg_replace('/^tbl_/', '', $table);           
        $table = preg_replace('/_id$/', '', $table);            
        return ucwords(str_replace('_', ' ', $table));          
    };
    $columnToName = function($column) {
        $column = preg_replace('/_id$/', '', $column); 
        return ucwords(str_replace('_', ' ', $column)); 
    };
    $parentName = $columnToName($parentColumn);
    if (empty($usedInTables)) {
        $usedStr = 'other tables';
    } else {
        $usedNames = array_map($tableToModule, $usedInTables);
        if (count($usedNames) === 1) {
            $usedStr = $usedNames[0];
        } else {
            $last = array_pop($usedNames);
            $usedStr = implode(', ', $usedNames) . ' and ' . $last;
        }
    }
    return "$parentName is used in $usedStr. Can't delete $parentName.";
}

function errorHandling($erroObj, $field_name, $table_name, $ajax=0, $parentId = null) {
    $err_info = $erroObj->errorInfo;
    $message="";
    if (is_array($err_info) && $err_info[1] == 1451) {
        $usedTables = [];
        if ($parentId !== null) {
            $usedTables = getForeignKeyChildTablesInUse($table_name, $field_name, $parentId);
        } else {
            $usedTables = getForeignKeyChildTables($table_name, $field_name);
        }
        $message = getForeignKeyErrorMessageDynamic($table_name, $field_name, $usedTables);

    } else if (is_array($err_info) && $err_info[1] == 1062) {
        $msg=$err_info[2];
        $msg=explode("for key ",$msg);
        $msg=$msg[0];
        $msg=explode("-",$msg);
        $msg=$msg[0];
        if($msg!="")
           // $msg.="'";  // COMMENTED BY BHUMITA ON 25/09/2025
        $message = $msg;

    } else {
        $message = $erroObj->getMessage();
    }
    if($ajax==1) {
        echo $message;
        exit;
    }
    $_SESSION["sess_message"]=$message;
    $_SESSION["sess_message_cls"] = "danger";
    $_SESSION["sess_message_title"] = "Error!";
    $_SESSION["sess_message_icon"] = "error";
}
/* \ADDED BY hetanshree ON 01/09/2025 */