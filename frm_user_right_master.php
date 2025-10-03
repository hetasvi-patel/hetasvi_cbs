<?php
    include("classes/cls_user_right_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");

    $savedPermissions=[];
    if(isset($_POST["user_id"])) {
      $user_id=$_POST["user_id"];
      $_bll->_mdl->_user_id=$user_id;
       $_bll->fillModel();
       $savedPermissions = $_bll->_mdl->_permissions;
    } else {
        $user_id=0;
    }
    
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Update User Permissions
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="userForm"  method="post" class="form-horizontal needs-validation" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
                    <label for="user_id" class="col-4 col-sm-3 col-md-2 col-lg-1 control-label">User Id*</label>                                    
                    <div class="col-8 col-sm-4 col-md-3 col-lg-2 ">
                        <?php
                            if(isset($_POST["user_id"])) {
                                $user_id=$_POST["user_id"];
                            } else {
                                $user_id=0;
                            }
                            echo getDropdown("tbl_user_master","user_id","person_name"," and user_id NOT IN (".ADMIN_USER_ID.",".USER_ID.") ".STATUS_QUERY." ".COMPANY_QUERY,"user_id",$user_id,"required form-select","required");
                        ?>
                        <div class="invalid-feedback"></div>                                    
                    </div>
                    <?php
                             if($user_id>0) {
                    ?>
                    <label for="user_id" class="col-4 col-sm-3 col-md-2 col-lg-1 control-label">Same As</label>                                    
                    <div class="col-8 col-sm-4 col-md-3 col-lg-2 ">
                        <?php
                           
                               echo getDropdown("tbl_user_master","user_id","person_name"," and user_id NOT IN (".ADMIN_USER_ID.",".USER_ID.",".$user_id.") ".STATUS_QUERY." ".COMPANY_QUERY,"copyFromUser",$user_id,"form-select","");
                            
                        ?>                                    
                    </div>
                    <?php
                                }
                    ?>
                </div>
                
              </div>
              </form>
              <!-- form end -->
            <!-- form start -->
            <form id="masterForm" action="classes/cls_user_right_master.php"   method="post" class="form-horizontal needs-validation" novalidate>
                <div class="box-body">
                    <?php
                    $columns="*";
                    $stmt = $_dbh->prepare("CALL csms_search(:columns, :tableName)");
                    $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
                    $stmt->bindParam(':tableName', $tbl_module_master, PDO::PARAM_STR);
                    $stmt->execute();               
                    $modules_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    $modules = [];
                    $menu_rights = [];
                    foreach($modules_array as $module) {
                        $module_id=$module["module_id"];
                        $columns="*";
                        $whereClause=" and module_id=$module_id";
                        $stmt1 = $_dbh->prepare("CALL csms_search_detail(:columns, :tableName, :whereClause)");
                        $stmt1->bindParam(':columns', $columns, PDO::PARAM_STR);
                        $stmt1->bindParam(':tableName', $tbl_menu_master, PDO::PARAM_STR);
                        $stmt1->bindParam(':whereClause', $whereClause, PDO::PARAM_STR);
                        $stmt1->execute();
                        $menus_array = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                        $stmt1->closeCursor();
                        $menus=[];
                        foreach($menus_array as $menu) {
                            $menu_id = $menu['menu_id'];
                            $menu_text = $menu['menu_text'];
                            $menus[] = [
                                'menu_id' => $menu_id,
                                'menu_text' => $menu_text
                            ];
                            $columns="*";
                            $whereClause=" and menu_id=$menu_id";
                            $stmt2 = $_dbh->prepare("CALL csms_search_detail(:columns, :tableName, :whereClause)");
                            $stmt2->bindParam(':columns', $columns, PDO::PARAM_STR);
                            $stmt2->bindParam(':tableName', $tbl_menu_right_master, PDO::PARAM_STR);
                            $stmt2->bindParam(':whereClause', $whereClause, PDO::PARAM_STR);
                            $stmt2->execute();
                            $menu_rights[$menu_id] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                            $stmt2->closeCursor(); 
                        }
                        $modules[] = [
                            'module_text' => $module['module_text'],
                            'menus' => $menus
                        ];
                        
                    }
                 
                  ?>
                  <div class="accordion" id="moduleAccordion">
                    <?php foreach ($modules as $moduleIndex => $module): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="module-heading-<?php echo $moduleIndex; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#module-collapse-<?php echo $moduleIndex; ?>" aria-expanded="false"
                                        aria-controls="module-collapse-<?php echo $moduleIndex; ?>">
                                    <?php echo htmlspecialchars($module['module_text']); ?>
                                </button>
                            </h2>
                            <div id="module-collapse-<?php echo $moduleIndex; ?>" class="accordion-collapse collapse"
                                aria-labelledby="module-heading-<?php echo $moduleIndex; ?>" data-bs-parent="#moduleAccordion">
                                <div class="accordion-body">

                                    <!-- Select All Row -->
                                    <div class="row mb-2">
                                      <div class="col-6 col-md-2"><strong>Select All:</strong></div>
                                       <div class="col-6 col-md-1">
                                                <input class="form-check-input select-all-module"
                                                          type="checkbox"
                                                          data-module="<?php echo  $moduleIndex; ?>"
                                                          id="selectAllModule<?php echo $moduleIndex; ?>">
                                                    <label class="form-check-label fw-bold"
                                                          for="selectAllModule<?php echo $moduleIndex; ?>">
                                                    </label>
                                                </div> 
                                        <?php 
                                           
                                              foreach ($menu_permissions as $action=> $action_text): 
                                          ?>
                                            <div class="col-6 col-md-1">
                                                <div class="form-check">
                                                    <input class="form-check-input select-all-action"
                                                          type="checkbox"
                                                          data-action="<?php echo $action; ?>"
                                                          data-module="<?php echo $moduleIndex; ?>"
                                                          id="select-all-action-<?php echo $moduleIndex . '-' . $action; ?>">
                                                    <label class="form-check-label fw-normal"
                                                          for="select-all-action-<?php echo $moduleIndex . '-' . $action; ?>">
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <hr>

                                    <!-- Menu Permission Rows -->
                                    <?php 
                                        foreach ($module['menus'] as $menu): 
                                            $menu_id = $menu['menu_id'];
                                            $menu_text = $menu['menu_text'];
                                    ?>
                                        <div class="mb-3 border-bottom pb-2">
                                            
                                            <div class="row mt-2">
                                              <div class="col-6 col-md-2">
                                                  <strong><?php echo htmlspecialchars($menu_text); ?></strong>
                                                </div>
                                              <div class="col-6 col-md-1">
                                                  <input type="checkbox"
                                                          class="form-check-input select-all-menu"
                                                          data-module="<?php echo $moduleIndex; ?>"
                                                          data-menu="<?php echo $menu_id; ?>"
                                                          id="select-all-menu-<?php echo $menu_id; ?>">
                                                    <label for="select-all-menu-<?php echo $menu_id; ?>">All</label>
                                                </div>
                                                <?php
                                                    foreach ($menu_permissions as $action=> $action_text):
                                                        // Check if the action is available for this menu
                                                        if (!isset($menu_rights[$menu_id])) continue;
                                                        // Filter the menu rights to find the specific action
                                                        $menu_right = [];
                                                        $menu_right = array_filter($menu_rights[$menu_id], function($item) use ($action) {
                                                            return $item['right_name'] === $action;
                                                        });
                                                        if (empty($menu_right)) {
                                                            $disabled="disabled";
                                                        } else {   
                                                            $disabled="";
                                                        }
                                                                                                        
                                                      
                                                ?>
                                                    <div class="col-6 col-md-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input action-checkbox"
                                                              type="checkbox"
                                                              name="permissions[<?php echo $menu_id; ?>][<?php echo $action; ?>]"
                                                              id="<?php echo $action . '-' . $menu_id; ?>"
                                                              value="1"
                                                              data-action="<?php echo $action; ?>"
                                                              data-module="<?php echo $moduleIndex; ?>"
                                                              data-menu="<?php echo $menu_id; ?>"
                                                              <?php if (!empty($savedPermissions[$menu_id][$action])) echo 'checked'; ?> <?php echo $disabled;?>>

                                                            <label class="form-check-label"
                                                                  for="<?php echo $action . '-' . $menu_id; ?>">
                                                                <?php echo ucfirst($action); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>


                </div>
              <!-- /.box-body -->
              <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="user_id" name="user_id" value= "<?php if(isset($user_id))  echo $user_id; else echo 0; ?>">
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- ./wrapper -->

<?php
    include("include/footer_includes.php");
?>
<script>
document.addEventListener("DOMContentLoaded", function () {    
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    document.getElementById("user_id").addEventListener("change", function (event) {
        document.getElementById("userForm").submit();
    });
    if(document.querySelector('#copyFromUser')) {
        document.getElementById('copyFromUser').addEventListener('change', function () {
        const userId = this.value;
        if (!userId) return;

        fetch('classes/cls_user_right_master.php?action=fetchPermissions&user_id=' + userId)
            .then(response => response.json())
            .then(data => {
                // Clear all checkboxes first
                document.querySelectorAll('.action-checkbox, .select-all-menu, .select-all-action, .select-all-module').forEach(cb => cb.checked = false);

                // Loop through permissions and check relevant boxes
                for (const [menuId, rights] of Object.entries(data)) {
                    for (const [action, value] of Object.entries(rights)) {
                        const selector = `.action-checkbox[data-menu="${menuId}"][data-action="${action}"]`;
                        const checkbox = document.querySelector(selector);
                        if (checkbox) checkbox.checked = value;
                    }
                }

                // Trigger change to update row/module/action-level checkboxes
                document.querySelectorAll('.action-checkbox').forEach(cb => cb.dispatchEvent(new Event('change')));
            });
        });
    }
    // Select All by Action (column) in a module
    document.querySelectorAll(".select-all-action").forEach(actionCb => {
        actionCb.addEventListener("change", function () {
            const { action, module } = this.dataset;
            document.querySelectorAll(`.action-checkbox[data-action="${action}"][data-module="${module}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Select All by Menu (row)
    document.querySelectorAll(".select-all-menu").forEach(rowCb => {
        rowCb.addEventListener("change", function () {
            const menuId = this.dataset.menu;
            document.querySelectorAll(`.action-checkbox[data-menu="${menuId}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Select All by Module (everything in one module)
    document.querySelectorAll(".select-all-module").forEach(moduleCb => {
        moduleCb.addEventListener("change", function () {
            const moduleId = this.dataset.module;
            document.querySelectorAll(`.action-checkbox[data-module="${moduleId}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
            document.querySelectorAll(`.select-all-menu[data-module="${moduleId}"], .select-all-action[data-module="${moduleId}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Sync individual menu-row checkboxes with menu-level select-all
    document.querySelectorAll(".action-checkbox").forEach(cb => {
        cb.addEventListener("change", function () {
            const menuId = this.dataset.menu;
            const moduleId = this.dataset.module;
            const allChecked = Array.from(document.querySelectorAll(`.action-checkbox[data-menu="${menuId}"]`)).every(x => x.checked);
            document.querySelector(`.select-all-menu[data-menu="${menuId}"]`).checked = allChecked;

            // Sync action-level checkbox
            const action = this.dataset.action;
            const allActionChecked = Array.from(document.querySelectorAll(`.action-checkbox[data-action="${action}"][data-module="${moduleId}"]`)).every(x => x.checked);
            document.querySelector(`.select-all-action[data-action="${action}"][data-module="${moduleId}"]`).checked = allActionChecked;

            // Sync module-level checkbox
            const allModuleChecked = Array.from(document.querySelectorAll(`.action-checkbox[data-module="${moduleId}"]`)).every(x => x.checked);
            document.querySelector(`.select-all-module[data-module="${moduleId}"]`).checked = allModuleChecked;
        });
    });
    document.getElementById("btn_add").addEventListener("click", function (event) {
        //event.preventDefault();
        const form = document.getElementById("userForm"); // Store form reference
        let i=0;
        let firstelement;
        checkFormValidation(form);
        setTimeout(function(){
            const invalidInputs = document.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
            
                let message = "Permissions updated successfully!";
                let title = "Update Successful!";
                let icon = "success";
                
                (async function() {
                //await customAlert(message);
                result=await Swal.fire(title, message, icon);
                    if (result.isConfirmed) {
                    $("#masterForm").submit();
                    }
                    
                })();
            }
        },200);
    });
} );
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    
});
</script>

<?php
    include("include/footer_close.php");
?>