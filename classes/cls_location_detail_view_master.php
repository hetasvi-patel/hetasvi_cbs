<?php
include_once(__DIR__ . "/../config/connection.php");
require_once(__DIR__ . "/../include/functions.php");

class bll_location_detail_view_master {
    public function pageSearch() {
        global $_dbh;

        $unitType = $_POST['unit_type'] ?? 'Quantity';

        // 1. Chambers
        $chambers = [];
        $stmt = $_dbh->query("SELECT chamber_id, chamber_name FROM tbl_chamber_master ORDER BY chamber_name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chambers[$row['chamber_id']] = $row['chamber_name'];
        }

        // 2. Floors
        $floors = [];
        $stmt = $_dbh->query("SELECT floor_id, floor_name, chamber_id FROM tbl_floor_master ORDER BY floor_name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $floors[$row['floor_id']] = [
                'name' => $row['floor_name'],
                'chamber_id' => $row['chamber_id']
            ];
        }

        // 3. Rack names from location
        $rackNames = [];
        $stmt = $_dbh->query("SELECT DISTINCT location FROM tbl_inward_detail WHERE location IS NOT NULL AND location != ''");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parts = explode('-', $row['location']);
            if (count($parts) == 3) {
                $rackNames[$parts[2]] = $parts[2];
            }
        }
        ksort($rackNames);

        // 4. Fetch inward data
        $inwardData = [];
        $stmt = $_dbh->query("SELECT location, SUM(inward_qty) as qty, SUM(inward_wt) as kg FROM tbl_inward_detail GROUP BY location");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parts = explode('-', $row['location']);
            if (count($parts) == 3) {
                $chamber = strtolower(trim($parts[0]));
                $floor   = strtolower(trim($parts[1]));
                $rack    = strtolower(trim($parts[2]));
                $inwardData[$chamber][$floor][$rack] = [
                    'inward_qty' => $row['qty'] ?? 0,
                    'inward_kg' => $row['kg'] ?? 0
                ];
            }
        }

        // 5. Fetch outward data
        $outwardData = [];
        $stmt = $_dbh->query("
            SELECT i.location, SUM(o.out_qty) as qty, SUM(o.out_wt) as kg
            FROM tbl_outward_detail o
            JOIN tbl_inward_detail i ON o.inward_detail_id = i.inward_detail_id
            GROUP BY i.location
        ");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parts = explode('-', $row['location']);
            if (count($parts) == 3) {
                $chamber = strtolower(trim($parts[0]));
                $floor   = strtolower(trim($parts[1]));
                $rack    = strtolower(trim($parts[2]));
                $outwardData[$chamber][$floor][$rack] = [
                    'outward_qty' => $row['qty'] ?? 0,
                    'outward_kg' => $row['kg'] ?? 0
                ];
            }
        }

        // min-width calculation for table (used in CSS)
        $minTableWidth = (count($rackNames) * 130 + 320);
        ?>
        <form method="post" id="unit-type-form" autocomplete="off">
            <div class="location-detail-form-wrapper">
                <div class="location-detail-unit-type">
                    <label>
                        <input type="radio" name="unit_type" value="Quantity" <?php if($unitType=="Quantity") echo "checked";?> onchange="document.getElementById('unit-type-form').submit();">
                        Quantity
                    </label>
                    <label>
                        <input type="radio" name="unit_type" value="Kg" <?php if($unitType=="Kg") echo "checked";?> onchange="document.getElementById('unit-type-form').submit();">
                        Kg.
                    </label>
                </div>
                <div class="location-detail-search-row" style="display:flex;gap:8px;align-items:center;">
                    <div>
                        <input type="text" class="form-control location-detail-chamber-search" placeholder="Chamber" id="chamber-search" autocomplete="off">
                    </div>
                    <div>
                        <input type="text" class="form-control location-detail-floor-search" placeholder="Floor" id="floor-search" autocomplete="off">
                    </div>
                    <div>
                        <button type="button" class="btn btn-default location-detail-btn-excel" id="btn-excel">Excel</button>
                    </div>
                </div>
            </div>
           <div class="location-detail-legend-row">
    <span class="legend-item">
        <span class="legend-color legend-full"></span>
        <span class="legend-label">Space is available for storage.</span>
    </span>
    <span class="legend-item">
        <span class="legend-color legend-partial"></span>
        <span class="legend-label">Space already allocated.</span>
    </span>
    <span class="legend-item">
        <span class="legend-color legend-norack"></span>
        <span class="legend-label">Rack No. is not inserted in Rack Master.</span>
    </span>
</div>
        </form>
        <div class="location-detail-table-scroll-x">
            <div class="location-detail-table-scroll-y">
                <table id="rack-view-table" class="location-detail-table location-detail-custom-grid" style="min-width:<?php echo $minTableWidth; ?>px;">
                    <thead>
                        <tr>
                            <th class="location-detail-grid-header location-detail-grid-header-chamber">Chamber</th>
                            <th class="location-detail-grid-header location-detail-grid-header-floor">Floor</th>
                            <?php foreach($rackNames as $rk){ echo '<th class="location-detail-grid-header location-detail-grid-header-rack">'.htmlspecialchars($rk).'</th>'; } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($chambers as $chamber_id => $chamber_name){
                            $chamberCode = $chamber_name;
                            foreach($floors as $floor_id => $floor){
                                echo "<tr>";
                                echo "<td class='chamber-cell location-detail-grid-chamber'>" . htmlspecialchars($chamber_name) . "</td>";
                                echo "<td class='floor-cell location-detail-grid-floor'>" . htmlspecialchars($floor['name']) . "</td>";
                                foreach($rackNames as $rk){
                                    $chKey = strtolower(trim($chamberCode));
                                    $flKey = strtolower(trim($floor['name']));
                                    $rkKey = strtolower(trim($rk));
                                    // Fetch inward & outward qty/kg
                                    if ($unitType == "Kg") {
                                        $inward = $inwardData[$chKey][$flKey][$rkKey]['inward_kg'] ?? 0;
                                        $outward = $outwardData[$chKey][$flKey][$rkKey]['outward_kg'] ?? 0;
                                    } else {
                                        $inward = $inwardData[$chKey][$flKey][$rkKey]['inward_qty'] ?? 0;
                                        $outward = $outwardData[$chKey][$flKey][$rkKey]['outward_qty'] ?? 0;
                                    }
                                    $net = $inward - $outward;
                                    // Custom cell design based on image1
                                    if ($inward > 0) {
                                        if ($net <= 0) {
                                            echo '<td class="location-detail-grid-cell grid-cell-outwarded"><span class="grid-cell-value">0</span></td>';
                                        } else {
                                            echo '<td class="location-detail-grid-cell grid-cell-partial"><span class="grid-cell-value">'.(float)$net.'</span></td>';
                                        }
                                    } else {
                                        echo '<td class="location-detail-grid-cell grid-cell-empty"></td>';
                                    }
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
        // Chamber/Floor search filter
        document.addEventListener("DOMContentLoaded", function() {
            function filterRows() {
                let chamberVal = document.getElementById("chamber-search").value.trim().toLowerCase();
                let floorVal = document.getElementById("floor-search").value.trim().toLowerCase();
                let table = document.getElementById("rack-view-table");
                let trs = table.querySelectorAll("tbody tr");
                trs.forEach(row => {
                    let chamberCell = row.querySelector(".chamber-cell");
                    let floorCell = row.querySelector(".floor-cell");
                    let chamberText = chamberCell ? chamberCell.textContent.trim().toLowerCase() : "";
                    let floorText = floorCell ? floorCell.textContent.trim().toLowerCase() : "";
                    let show = true;
                    if (chamberVal && !chamberText.includes(chamberVal)) show = false;
                    if (floorVal && !floorText.includes(floorVal)) show = false;
                    row.style.display = show ? "" : "none";
                });
            }
            document.getElementById("chamber-search").addEventListener("input", filterRows);
            document.getElementById("floor-search").addEventListener("input", filterRows);
        });
        </script>
        <?php
    }
}
$_bll = new bll_location_detail_view_master();

?>