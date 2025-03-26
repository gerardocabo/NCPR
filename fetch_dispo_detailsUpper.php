<?php
include 'connection.php'; // Ensure database connection is established

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ncpr_num'])) {
    $ncpr_num = $_POST['ncpr_num'];
    $viewOnly = isset($_POST['viewOnly']) ? $_POST['viewOnly'] : false;

    // Debugging: Log received NCPR number
    error_log("Received NCPR number: " . $ncpr_num);

    if ($conn) {
        // Prepare the query to fetch data from multiple related tables
        $stmt = $conn->prepare("
            SELECT 
                ncpr.dispo_id, ncpr.ncpr_num,

                -- Data from dispo_table
                dispo.id AS dispo_table_id, dispo.QAVCIA, dispo.CNC, dispo.man, dispo.man_id_num, dispo.name AS dispo_name, 
                dispo.method, dispo.machine, dispo.CNC_mat_id, dispo.created_at AS dispo_created_at, dispo.updated_at AS dispo_updated_at,

                -- Data from cnc_mat_tbl
                cnc.id AS cnc_id, cnc.nfld_item, cnc.nfld_item_pur_item, cnc.FE_expired, cnc.local_supp, 
                cnc.imi, cnc.pff, cnc.CAR_id, cnc.created_at AS cnc_created_at, cnc.updated_at AS cnc_updated_at,

                -- Data from car_tbl
                car.id AS car_id, car.car_is_approved, car.car_num_active, car.car_num, car.8d_report_active, 
                car.scar_active, car.scar_num, car.DRF_id, car.created_at AS car_created_at, car.updated_at AS car_updated_at,

                -- Data from drf_tbl
                drf.id AS drf_id, drf.NTPI_active, drf.MRB_active, drf.NFLD_active, drf.cust_is_approve, 
                drf.doc_alert_num, drf.prod_dispo_id, drf.created_at AS drf_created_at, drf.updated_at AS drf_updated_at,

                -- Data from prod_dispo_tbl
                prod.id AS prod_dispo_id, prod.use_as_isActive, prod.re_inspectionActive, prod.run_normalActive, 
                prod.regrade_Active, prod.rework_Active, prod.repair_Active, prod.rework_traveler_Active, 
                prod.scrap_Active, prod.RTV_Active, prod.yield_off, prod.da_no, prod.rework_da_no, prod.wis_no, 
                prod.scrap_amount, prod.shipment_date, prod.intervention_id, prod.rework_type_id, 
                prod.created_at AS prod_created_at, prod.updated_at AS prod_updated_at

            FROM ncpr_table ncpr
            LEFT JOIN dispo_table dispo ON ncpr.dispo_id = dispo.id
            LEFT JOIN cnc_mat_tbl cnc ON dispo.CNC_mat_id = cnc.id  -- Join cnc_mat_tbl
            LEFT JOIN car_tbl car ON cnc.CAR_id = car.id  -- Join car_tbl
            LEFT JOIN drf_tbl drf ON car.DRF_id = drf.id  -- Join drf_tbl
            LEFT JOIN prod_dispo_tbl prod ON drf.prod_dispo_id = prod.id  -- Join prod_dispo_tbl

            WHERE ncpr.ncpr_num = ?
        ");

        if ($stmt) {
            $stmt->bind_param("s", $ncpr_num);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Store fetched data in variables for use in the included file
                $dipspo_num = $row;
                include "viewdisposition.php"; 
            } else {
                echo "<p>No data found for the given NCPR number.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Database query failed.</p>";
        }
    } else {
        echo "<p>Database connection error.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>
