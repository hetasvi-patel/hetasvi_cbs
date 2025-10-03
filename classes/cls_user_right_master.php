<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_userrightmaster 
{                        
    public $_user_right_master_id;          
    public $_user_id;          
    public $_permissions;                
    public $_transactionmode;
}

class bll_userrightmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_userrightmaster(); 
        $this->_dal =new dal_userrightmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_user_right_master.php");
       }
    }
    public function fillModel()
    {
        global $_dbh;
        $this->_dal->fillModel($this->_mdl);
    }
    public function fetchPermissions()
    {
        global $_dbh;
        global $tbl_user_right_master;
        global $tbl_menu_right_master;
        global $tbl_menu_master;


        $user_id = intval($_GET['user_id']);

        // Fetch permissions
        $stmt = $_dbh->prepare("SELECT mm.menu_id, mrm.right_name, ur.has_right FROM ".$tbl_user_right_master." ur JOIN ".$tbl_menu_right_master." mrm ON ur.menu_right_id = mrm.menu_right_id JOIN ". $tbl_menu_master." mm ON mrm.menu_id = mm.menu_id WHERE ur.user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        $permissions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $menu_id = $row['menu_id'];
            $right = strtolower($row['right_name']);
            $permissions[$menu_id][$right] = (bool)$row['has_right'];
        }
        //$permissions['user_id'] = $user_id;
        //header('Content-Type: application/json');
        echo json_encode($permissions);
    }
}
 class dal_userrightmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        global $tbl_user_right_master;
        global $tbl_menu_right_master;
        global $tbl_user_master;
        if(!isset($_mdl->_user_id) || empty($_mdl->_user_id))
            return; 
         // Step 1: Clear existing rights
        $_dbh->prepare("DELETE FROM ".$tbl_user_right_master." WHERE user_id = ?")->execute([$_mdl->_user_id]);
        
        // Step 2: Get all rights mappings
        $columns = "menu_right_id, menu_id, right_name";
        $rightMap = [];
        $rightMapStmt = $_dbh->prepare("CALL csms_search(:columns, :tableName)");
        $rightMapStmt->bindParam(':columns', $columns, PDO::PARAM_STR);
        $rightMapStmt->bindParam(':tableName', $tbl_menu_right_master, PDO::PARAM_STR);
        $rightMapStmt->execute();
        while ($row = $rightMapStmt->fetch(PDO::FETCH_ASSOC)) {
            $rightMap[$row['menu_id']][$row['right_name']] = $row['menu_right_id'];
        }
        $rightMapStmt->closeCursor();
        // Step 3: Insert selected rights
        $insertStmt = $_dbh->prepare("
            INSERT INTO ".$tbl_user_right_master." (user_id, menu_right_id, has_right)
            VALUES (?, ?, 1)
        ");
        foreach ($_mdl->_permissions as $menu_id => $rights) {
            foreach ($rights as $right_name => $has_right) {
                if (!empty($rightMap[$menu_id][$right_name])) {
                    $menu_right_id = $rightMap[$menu_id][$right_name];
                    $insertStmt->execute([$_mdl->_user_id, $menu_right_id]);
                }
            }
        }
         $_dbh->prepare("UPDATE ".$tbl_user_master." SET permission_version = NOW() WHERE user_id = ?")->execute([$_mdl->_user_id]);

    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        global $tbl_user_right_master;
        global $tbl_menu_right_master;
        // Step 1: Get existing user rights
        $query = $_dbh->prepare("
            SELECT mr.menu_id, mr.right_name
            FROM ".$tbl_user_right_master." urm
            JOIN ".$tbl_menu_right_master." mr ON urm.menu_right_id = mr.menu_right_id
            WHERE urm.user_id = :user_id AND urm.has_right = '1'
        ");
        $query->execute([':user_id' => $_mdl->_user_id]);

        $savedPermissions = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $_mdl->_permissions[$row['menu_id']][$row['right_name']] = 1;
        }
    }
}
$_bll=new bll_userrightmaster();

if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}

 if (isset($_POST["masterHidden"]) && $_POST["masterHidden"]=="save" && isset($_POST["user_id"]) && ($_POST["user_id"]>0)) {
    $permissions = $_POST['permissions'] ?? [];
    $user_id = $_POST['user_id']; // make sure it's validated

    $_bll->_mdl->_user_id=$user_id;
    $_bll->_mdl->_permissions=$permissions;
    $_bll->_mdl->_transactionmode="I";
    $_bll->dbTransaction();
}
if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
