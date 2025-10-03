<?php
$menuData = getDynamicMenu($_dbh);
$currentMonth = date("n"); 
$currentYear = date("Y");
$accessibleMenus= [];
?>

<header class="main-header">
    <nav class="navbar navbar-expand-lg navbar-static-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Done By Hetasvi-->
            <div class="collapse navbar-collapse pull-left" id="navbarSupportedContent">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link"><i class="fa fa-home"></i></a>
                    </li>
                    <?php
                    $currentGroup = null;

                    if (!empty($menuData) && is_array($menuData)):
                        foreach ($menuData as $module => $menuGroups): 
                    ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo htmlspecialchars($module); ?>"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($module); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo htmlspecialchars($module); ?>">

                            <?php
                            $totalGroups = count($menuGroups);
                            $groupIndex = 0;

                            foreach ($menuGroups as $menuGroup => $menuItems): 
                                $groupIndex++;
                                ?>
                                <?php 
                                foreach ($menuItems as $menu):
                                    $frm_menu_link=$menu['link'];
                                    $menu_link = str_replace("frm_", "", $frm_menu_link);
                                    $srh_menu_link = str_replace("frm_", "srh_", $frm_menu_link);
                                    array_push($accessibleMenus, $frm_menu_link);
                                    array_push($accessibleMenus, $srh_menu_link);
                                    if(!$canAdd && COMPANY_ID!=ADMIN_COMPANY_ID) {
                                        $meunulink=$srh_menu_link;
                                    } else {
                                        $meunulink=$frm_menu_link;
                                    }
                                    ?>
                                    <li class="dropdown-item">
                                        <!-- Make the entire row clickable -->
                                        <a href="<?php echo $meunulink; ?>" class="text-dark d-block">
                                            <?php echo htmlspecialchars($menu['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>

                                <?php if ($groupIndex < $totalGroups): ?>
                                    <hr>
                                <?php endif; ?>

                            <?php
                            endforeach;
                            ?>
                            </ul>
                        </li>
                    <?php endforeach;
                    else: ?>
                        <li class="nav-item"><a class="nav-link">No modules found</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Done By Mansi-->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="user user-menu d-flex align-items-center">
                        <a href="frm_switch_year_master.php" class="year-range-link">
                    <span class="year-range">
                        <?php
                        echo !empty($_SESSION['sess_selected_year']) ? $_SESSION['sess_selected_year'] : 'Year Not Set';
                        ?>
                    </span>
                </a>
                        <span class="hidden-xs">
                            <?php echo PERSON_NAME; ?>
                        </span>
                        <div class="user-actions ms-3">
                            <?php if($_SESSION["sess_user_id"]> 0) : ?>
                                <a href="logout.php" class="btn btn-logout"><i class="fa fa-sign-out"></i></a>
                            <?php else: ?>
                                <a href="index.php" class="btn btn-logout"><i class="fa fa-sign-in"></i></a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Done By Mansi-->
        </div>
    </nav>
</header>
<?php
/* ADDED BY BHUMITA */
// Check if the current page is a form or search page
if(USER_ID!=ADMIN_USER_ID) {
    $current_page=strtolower(basename($_SERVER['PHP_SELF']));
    if(str_contains($current_page, "frm_") || str_contains($current_page, "srh_")) {
        if(!in_array($current_page, $accessibleMenus)) {
            echo "<script>location.href='dashboard.php'</script>";
            exit();
        }
    } else if(str_contains($current_page, "cls_")) {
        echo "<script>location.href='dashboard.php'</script>";
        exit();
    }
}
/* \ADDED BY BHUMITA */
?>