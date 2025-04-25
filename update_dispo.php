<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

function getColumnNames(PDO $pdo, $tableName)
{
    $query = "DESCRIBE $tableName";

    try {
        $stmt = $pdo->query($query);
        $columns = [];

        // Loop through the result set and store column names
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field']; // 'Field' is the column name in the 'DESCRIBE' query
        }
        return $columns;
    } catch (PDOException $e) {
        // In case of error, return an empty array
        return [];
    }
}

function getDispoRadio($ncprnum, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT field_name, field_value FROM dispo_radio_values WHERE ncpr_num = :ncprnum");
        $stmt->bindParam(':ncprnum', $ncprnum, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            // Return the data as an object if you prefer (or leave it as an associative array)
            return $data; // Converts array to object
        } else {
            return null; // No data found
        }
    } catch (PDOException $e) {
        // Handle error if query fails
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function getCheckboxes($ncprnum, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT b.checkbox_name 
                                FROM dispo_table a 
                                LEFT JOIN predefined_checkboxes b ON a.checkbox_id = b.id 
                                WHERE a.ncpr_num = :ncprnum");
        $stmt->bindParam(':ncprnum', $ncprnum, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            // Return the data as an object if you prefer (or leave it as an associative array)
            return $data; // Converts array to object
        } else {
            return null; // No data found
        }
    } catch (PDOException $e) {
        // Handle error if query fails
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function getCheckboxesInter($ncprnum, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT b.key 
                                FROM dispo_table_intervention a 
                                LEFT JOIN predefined_checkboxes_2 b ON a.checkbox_id = b.id 
                                WHERE a.ncpr_num = :ncprnum");
        $stmt->bindParam(':ncprnum', $ncprnum, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            // Return the data as an object if you prefer (or leave it as an associative array)
            return $data; // Converts array to object
        } else {
            return null; // No data found
        }
    } catch (PDOException $e) {
        // Handle error if query fails
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function getInterInputs($ncprnum, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT input_name, inputted_data FROM disposition_tbl_intervention WHERE ncpr_num = :ncprnum");
        $stmt->bindParam(':ncprnum', $ncprnum, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            // Return the data as an object if you prefer (or leave it as an associative array)
            return $data; // Converts array to object
        } else {
            return null; // No data found
        }
    } catch (PDOException $e) {
        // Handle error if query fails
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function extractPostData(array $expectedKeys): array
{
    $result = [];

    foreach ($expectedKeys as $key) {
        if (isset($_POST[$key])) {
            $result[$key] = $_POST[$key];
            unset($_POST[$key]); // Clean up $_POST
        }
    }

    return $result;
}

function mergePostData(array $data): array
{
    $merged = [];

    foreach ($data as $value) {
        if (is_array($value)) {
            $merged = array_merge($merged, $value);
        } else {
            $merged[] = $value;
        }
    }

    return $merged;
}

function updateInputs($pdo, $ncprnum, $data)
{
    $table = 'disposition_tbl';

    if (empty($data)) {
        return "No data provided for update.";
    }

    // Build SET part
    $setParts = [];
    $params = [':id' => $ncprnum];

    foreach ($data as $col => $val) {
        $placeholder = ":$col";
        $setParts[] = "`$col` = $placeholder";
        $params[$placeholder] = $val;
    }

    $sql = "UPDATE `$table` SET " . implode(', ', $setParts) . " WHERE ncpr_num = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            return "Update successful for NCPR #$ncprnum.";
        } else {
            return "No rows were updated (possibly no change or invalid ID).";
        }
    } catch (PDOException $e) {
        return "Update failed: " . $e->getMessage();
    }
}

/**
 * Compares two associative arrays for added, updated, and deleted entries.
 */
function compareAssociativeChanges(array $oldData, array $newData): array
{
    $added = [];
    $updated = [];
    $deleted = [];

    foreach ($newData as $key => $newValue) {
        if (!array_key_exists($key, $oldData)) {
            $added[$key] = $newValue;
        } elseif ($oldData[$key] !== $newValue) {
            $updated[$key] = ['old' => $oldData[$key], 'new' => $newValue];
        }
    }

    foreach ($oldData as $key => $oldValue) {
        if (!array_key_exists($key, $newData)) {
            $deleted[$key] = $oldValue;
        }
    }

    return compact('added', 'updated', 'deleted');
}

/**
 * Converts the structured database array into a flat associative array for comparison.
 */
function normalizeAndCompare(array $dbArray, array $formArray, array $fieldMap): array
{
    $formattedOld = [];
    foreach ($dbArray as $item) {
        $formattedOld[$item[$fieldMap[0]]] = $item[$fieldMap[1]];
    }

    return compareAssociativeChanges($formattedOld, $formArray);
}

/**
 * Applies the changes to the database using PDO.
 */
function applyDbChangesPDO(array $diff, string $table, string $keyCol, string $valCol, string $ncprnum, PDO $pdo): void
{
    // Insert
    foreach ($diff['added'] as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO `$table` (ncpr_num, `$keyCol`, `$valCol`) VALUES (:ncprnum, :key, :val)");
        $stmt->execute(['ncprnum' => $ncprnum, 'key' => $key, 'val' => $val]);
    }

    // Update
    foreach ($diff['updated'] as $key => $values) {
        $stmt = $pdo->prepare("UPDATE `$table` SET `$valCol` = :val WHERE `ncpr_num` = :ncprnum AND `$keyCol` = :key");
        $stmt->execute(['ncprnum' => $ncprnum, 'key' => $key, 'val' => $values['new']]);
    }

    // Delete
    foreach ($diff['deleted'] as $key => $val) {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `ncpr_num` = :ncprnum  AND `$keyCol` = :key");
        $stmt->execute(['ncprnum' => $ncprnum, 'key' => $key]);
    }
}

/**
 * Wrapper to sync form data with DB using PDO.
 *
 * @param array $existingData Existing rows from DB in [ [keyCol => x, valCol => y], ... ]
 * @param array $formData New submitted data in [key => value] format
 * @param array $fieldMap Column names in DB: [keyCol, valCol]
 * @param string $table Table name
 * @param PDO $pdo PDO connection
 * @return array Difference data: ['added' => [], 'updated' => [], 'deleted' => []]
 */
function syncFormDataPDO(array $existingData, array $formData, array $fieldMap, string $table, $ncprnum, PDO $pdo): array
{
    $formData = array_filter($formData, function ($val) {
        return !is_null($val) && $val !== '';
    });
    $diff = normalizeAndCompare($existingData, $formData, $fieldMap);
    applyDbChangesPDO($diff, $table, $fieldMap[0], $fieldMap[1], $ncprnum, $pdo);
    return $diff;
}

function compareArrays($assocArray_old, $simpleArray_new)
{
    // Compare the two arrays
    $update_Cb = array_diff($simpleArray_new, $assocArray_old);
    $delete_Cb = array_diff($assocArray_old, $simpleArray_new);

    // Return the differences
    return [
        'update' => $update_Cb,
        'delete' => $delete_Cb,
    ];
}

function insertCheckboxes($pdo, $ncprnum, $arrayToInsert, $tableName, $keyCol)
{
    $table_first = $tableName[0];
    $table_second = $tableName[1];

    try {
        // Prepare the SELECT statement to get checkbox IDs from names
        $selectStmt = $pdo->prepare("SELECT id FROM `$table_second` WHERE `$keyCol` = :checkbox_name");

        // Prepare the INSERT statement using checkbox_id
        $insertStmt = $pdo->prepare("INSERT INTO `$table_first` (ncpr_num, checkbox_id) VALUES (:ncpr_num, :checkbox_id)");

        foreach ($arrayToInsert as $checkboxName) {
            // Get the checkbox ID from its name
            $selectStmt->bindParam(':checkbox_name', $checkboxName, PDO::PARAM_STR);
            $selectStmt->execute();
            $result = $selectStmt->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result['id'])) {
                $checkboxId = $result['id'];

                // Insert into assigned_checkboxes using checkbox_id
                $insertStmt->bindParam(':ncpr_num', $ncprnum, PDO::PARAM_STR);
                $insertStmt->bindParam(':checkbox_id', $checkboxId, PDO::PARAM_INT);
                $insertStmt->execute();
            }
        }

        return "Inserted successfully into $table_first using → $ncprnum.";
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function deleteCheckboxes($pdo, $ncprnum, $arrayToDelete, $tableName, $keyCol)
{
    $table_first = $tableName[0];
    $table_second = $tableName[1];

    try {
        // Prepare the SELECT statement to get checkbox IDs from names
        $selectStmt = $pdo->prepare("SELECT id FROM `$table_second` WHERE `$keyCol` = :checkbox_name");

        // Prepare the DELETE statement using checkbox_id
        $deleteStmt = $pdo->prepare("DELETE FROM `$table_first` WHERE ncpr_num = :ncpr_num AND checkbox_id = :checkbox_id");

        foreach ($arrayToDelete as $checkboxName) {
            // Get the checkbox ID from its name
            $selectStmt->bindParam(':checkbox_name', $checkboxName, PDO::PARAM_STR);
            $selectStmt->execute();
            $result = $selectStmt->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result['id'])) {
                $checkboxId = $result['id'];

                // Now delete from assigned_checkboxes using the ID
                $deleteStmt->bindParam(':ncpr_num', $ncprnum, PDO::PARAM_STR);
                $deleteStmt->bindParam(':checkbox_id', $checkboxId, PDO::PARAM_INT);
                $deleteStmt->execute();
            }
        }

        return "Deleted successfully from $table_first based on → $ncprnum.";
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function handleInsertDelete($pdo, $assocArray_old, $simpleArray, $ncpr_num, $tableNames, $keyCol)
{

    // Extract 'checkbox_name' values from the associative array
    $assocArray = array_column($assocArray_old, $keyCol);
    // Compare the arrays
    $result = compareArrays($assocArray, $simpleArray);

    $response = [];
    // Insert new checkboxes (found in simple array but not in assoc array)
    if (!empty($result['update'])) {
        $response[] = insertCheckboxes($pdo, $ncpr_num, $result['update'], $tableNames, $keyCol);
    }

    // Delete checkboxes (found in assoc array but not in simple array)
    if (!empty($result['delete'])) {
        $response[] = deleteCheckboxes($pdo, $ncpr_num, $result['delete'], $tableNames, $keyCol);
    }

    return implode(" | ", $response);
}

function rmdlFiles(PDO $pdo, $ncprnum, array $fileNames, $table = 'uploaded_filedispo')
{
    if (empty($ncprnum) || empty($fileNames)) {
        return;
    }

    try {
        $sql = "DELETE FROM `$table` WHERE `ncpr_num` = :num AND `file_name` = :file_name";
        $stmt = $pdo->prepare($sql);

        foreach ($fileNames as $fileName) {
            $stmt->bindParam(':num', $ncprnum, PDO::PARAM_INT);
            $stmt->bindParam(':file_name', $fileName, PDO::PARAM_STR);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        return "Delete failed: " . $e->getMessage();
    }
}

// Default response and main program of update
$response = ['success' => false, 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ncpr_num = $_POST['ncpr_num'] ?? null;
    // 🔹 Then remove it from the rest if needed
    unset($_POST['ncpr_num']);
    // unset the delete_file array
    $deleteFiles = $_POST['delete_file'] ?? [];
    unset($_POST['delete_file']);

    //list of name from form inputs.
    $tableName = 'disposition_tbl';
    $keyfieldName = ['checkbox_name', 'key'];
    $dispo_Table = ['dispo_table', 'predefined_checkboxes'];
    $dispo_Inter = ['dispo_table_intervention', 'predefined_checkboxes_2'];
    $otherTable = ['dispo_radio_values', 'disposition_tbl_intervention'];
    $columnRadio = ['field_name', 'field_value'];
    $columnInterInputs = ['input_name', 'inputted_data'];
    $cbArrayList = ['cause', 'car', 'scar', 'dispo_from', 'impact_analysis', 'affected_business', 'other_instructions', 'product_dispo'];
    $cbIntervention = ['actions_taken', 'process_dispo', 'resumption_reason', 'instructions_detail', 'documents_revision'];
    $radioList = ['corrective_action', 'potential_failure', 'bd_report', 'mrb', 'customer_approval'];


    $listInputs = getColumnNames($pdo, $tableName);
    $getDispoRadio = getDispoRadio($ncpr_num, $pdo) ?? [];
    $getCheckboxes = getCheckboxes($ncpr_num, $pdo) ?? [];
    $getCheckboxesInter = getCheckboxesInter($ncpr_num, $pdo) ?? [];
    $getInter = getInterInputs($ncpr_num, $pdo) ?? [];

    $inp_data = extractPostData($listInputs);
    $radio = extractPostData($radioList);
    $checkBoxes = mergePostData(extractPostData($cbArrayList));
    $cbsIntervention = mergePostData(extractPostData($cbIntervention));
    $InterInputs = $_POST; //here the ungrouped form data.

    // ---- action for updates ---- 
    $inp_dataResult = updateInputs($pdo, $ncpr_num, $inp_data);
    $radioUpdate = syncFormDataPDO($getDispoRadio, $radio, $columnRadio, $otherTable[0], $ncpr_num, $pdo);
    $checkBoxes = handleInsertDelete($pdo, $getCheckboxes, $checkBoxes, $ncpr_num, $dispo_Table, $keyfieldName[0]);
    $cbsInterventions = handleInsertDelete($pdo, $getCheckboxesInter, $cbsIntervention, $ncpr_num, $dispo_Inter, $keyfieldName[1]);
    $Inter_Inputs = syncFormDataPDO($getInter, $InterInputs, $columnInterInputs, $otherTable[1], $ncpr_num, $pdo);
    $delFiles = rmdlFiles($pdo, $ncpr_num, $deleteFiles);

    $response = [
        'success' => true,
        'message' => ' ,Successfully updated.'
    ];
}

echo json_encode($response);
