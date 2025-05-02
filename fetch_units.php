<?php
// Include database connection
include("config/connection.php");

// Always set response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['item_id'])) {
    $item_id = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);

    if ($item_id) {
        try {
            $query = "
                SELECT 
                    pum.packing_unit_id,
                    pum.packing_unit_name,
                    COALESCE(ippl.rent_kg_per_month, '0.00') AS rent_kg_per_month,
                    COALESCE(ippl.season_rent_per_kg, '0.00') AS season_rent_per_kg
                FROM tbl_packing_unit_master pum
                LEFT JOIN tbl_item_preservation_price_list_master ippl 
                    ON pum.packing_unit_id = ippl.packing_unit_id 
                    AND ippl.item_id = :item_id
                WHERE pum.packing_unit_id IS NOT NULL
            ";

            $stmt = $_dbh->prepare($query);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $packingUnits = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($packingUnits);
        } catch (PDOException $e) {
            error_log("Database Error (GET): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error fetching data. Please try again later.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid item ID provided.']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['packing_unit_id'])) {
    $packing_unit_id = filter_input(INPUT_POST, 'packing_unit_id', FILTER_VALIDATE_INT);
    $rent_kg_per_month = filter_input(INPUT_POST, 'rent_kg_per_month', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $season_rent_per_kg = filter_input(INPUT_POST, 'season_rent_per_kg', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);

    if ($packing_unit_id && $item_id) {
        try {
            $checkQuery = "
                SELECT COUNT(*) 
                FROM tbl_item_preservation_price_list_master 
                WHERE packing_unit_id = :packing_unit_id AND item_id = :item_id
            ";
            $checkStmt = $_dbh->prepare($checkQuery);
            $checkStmt->bindParam(':packing_unit_id', $packing_unit_id, PDO::PARAM_INT);
            $checkStmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $updateQuery = "
                    UPDATE tbl_item_preservation_price_list_master
                    SET rent_kg_per_month = :rent_kg_per_month,
                        season_rent_per_kg = :season_rent_per_kg
                    WHERE packing_unit_id = :packing_unit_id AND item_id = :item_id
                ";
                $stmt = $_dbh->prepare($updateQuery);
            } else {
                $insertQuery = "
                    INSERT INTO tbl_item_preservation_price_list_master 
                    (packing_unit_id, item_id, rent_kg_per_month, season_rent_per_kg)
                    VALUES (:packing_unit_id, :item_id, :rent_kg_per_month, :season_rent_per_kg)
                ";
                $stmt = $_dbh->prepare($insertQuery);
            }
            $stmt->bindParam(':packing_unit_id', $packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindParam(':rent_kg_per_month', $rent_kg_per_month, PDO::PARAM_STR);
            $stmt->bindParam(':season_rent_per_kg', $season_rent_per_kg, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            error_log("Database Error (POST): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error updating data. Please try again later.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid packing unit ID or item ID provided.']);
    }

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>