<?php
include('config/connection.php');
$customer = $_REQUEST['customer'] ?? '';
$outward_id = $_REQUEST['outward_id'] ?? '';

$used_detail_ids = [];
if ($outward_id) {
    $q = $_dbh->prepare("SELECT inward_detail_id FROM tbl_outward_detail WHERE outward_id = ?");
    $q->execute([$outward_id]);
    $used_detail_ids = $q->fetchAll(PDO::FETCH_COLUMN, 0);
}
$used_detail_ids_str = implode(',', array_map('intval', $used_detail_ids));

// --- This is the ONLY change:
$sql = "SELECT 
    m.inward_id,
    d.inward_detail_id,
    m.inward_no,
    m.inward_date,
    c.customer_name AS broker,
    m.customer,
    d.lot_no,
    im.item_name AS item,
    d.marko,
    d.inward_qty,
    um.packing_unit_name AS packing_unit,
    d.inward_wt,
    (d.inward_qty - COALESCE((
        SELECT SUM(od.out_qty) FROM tbl_outward_detail od
        WHERE od.inward_detail_id = d.inward_detail_id
        AND (od.outward_id <> :outward_id OR :outward_id IS NULL OR :outward_id = '')
    ), 0)) AS stock_qty,
    d.inward_wt AS stock_wt,
    0 AS out_qty,
    0 AS out_wt,
    um.loading_charge,
    d.location
FROM tbl_inward_master m
INNER JOIN tbl_inward_detail d ON m.inward_id = d.inward_id
LEFT JOIN tbl_packing_unit_master um ON d.packing_unit = um.packing_unit_id
LEFT JOIN tbl_customer_master c ON m.customer = c.customer_id
LEFT JOIN tbl_item_master im ON d.item = im.item_id
WHERE ";

$filters = [];

if ($customer) {
    $filters[] = "m.customer = :customer";
}

$main_filter = "(d.inward_qty - COALESCE((
        SELECT SUM(od.out_qty) FROM tbl_outward_detail od
        WHERE od.inward_detail_id = d.inward_detail_id
        AND (od.outward_id <> :outward_id OR :outward_id IS NULL OR :outward_id = '')
    ), 0)) > 0";

if ($outward_id && !empty($used_detail_ids)) {
    $main_filter = "($main_filter OR d.inward_detail_id IN ($used_detail_ids_str))";
}

$filters[] = $main_filter;
$sql .= implode(' AND ', $filters);
$sql .= " ORDER BY m.inward_date DESC, d.lot_no ASC";

$stmt = $_dbh->prepare($sql);
if ($customer) $stmt->bindParam(':customer', $customer, PDO::PARAM_STR);
$stmt->bindParam(':outward_id', $outward_id, PDO::PARAM_STR);
$stmt->execute();
$inwards = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($inwards);
?>