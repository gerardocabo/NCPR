<?php
require 'conn.php'; // Ensure DB connection

// Retrieve ncpr_num and validate it
$ncpr_num = $_POST['ncpr_num'] ?? null;
$created_at = date('Y-m-d H:i:s');

// Ensure ncpr_num is provided
if (!$ncpr_num) {
    echo json_encode(['status' => 'error', 'message' => 'NCPR number is required.']);
    exit; // Stop further execution if no NCPR number is provided
}

// Allowed file MIME types (JPEG, Office files, and PDF)
$allowedTypes = [
    'application/pdf', // PDF
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (xlsx)
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (docx)
    'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PowerPoint (pptx)
    'image/jpeg', // JPEG images (jpg, jpeg)
    'image/jpg' // JPG images
];

// Function to handle file uploads
function uploadFile($file, $uploadDir, $fileType, $ncpr_num, $conn, $allowedTypes)
{
    // Ensure the file is valid and is one of the allowed types
    if (!isset($file['name']) || !isset($file['tmp_name']) || $file['error'] != 0) {
        return false;  // If there's an issue with the file, return false
    }

    $fileName = basename($file['name']);
    $fileTmp = $file['tmp_name'];
    $uniqueFileName = time() . '_' . $fileName;  // Ensure uniqueness by prepending timestamp
    $filePath = $uploadDir . $uniqueFileName;

    // Fetch the file type (MIME type)
    $fileType = mime_content_type($fileTmp);

    // Check if the file type is allowed
    if (!in_array($fileType, $allowedTypes)) {
        return false;  // If not allowed, return false
    }

    // Attempt to move the file to the server directory
    if (move_uploaded_file($fileTmp, $filePath)) {
        // Insert file information into the database
        $sql = "INSERT INTO uploaded_filedispo (ncpr_num, file_name, file_path, file_type, uploaded_at) 
                VALUES (?, ?, ?, ?, NOW())";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $ncpr_num, $fileName, $filePath, $fileType);
            $stmt->execute();
            $stmt->close();
            return true;  // Return true if file was uploaded and database was updated
        } else {
            error_log("Database insert error: " . $conn->error);
            return false;  // Return false if database insert fails
        }
    } else {
        error_log("File upload failed: " . $fileName);
        return false;  // Return false if file move operation fails
    }
}

// Check if files were uploaded
if (!empty($_FILES['attachments']['name'][0])) {
    $uploadSuccess = true;

    // Loop through each uploaded file
    foreach ($_FILES['attachments']['name'] as $key => $attachment) {
        // If no error, attempt to upload
        if ($_FILES['attachments']['error'][$key] == 0) {
            $file = [
                'name' => $attachment,
                'tmp_name' => $_FILES['attachments']['tmp_name'][$key],
                'error' => $_FILES['attachments']['error'][$key]
            ];
            // If any file upload fails, stop and return failure
            if (!uploadFile($file, 'assets/attachments/', 'attachment', $ncpr_num, $conn, $allowedTypes)) {
                $uploadSuccess = false;
                break;
            }
        } else {
            $uploadSuccess = false;  // If there was an error with the file, stop processing
            break;
        }
    }

    // Provide feedback based on upload success
    if ($uploadSuccess) {
        echo json_encode(['status' => 'success', 'message' => 'Files uploaded successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Some files could not be uploaded.']);
    }
} else {
    // If no files are uploaded, return an appropriate response
    echo json_encode(['status' => 'error', 'message' => 'No files selected for upload.']);
}
 // Ensure no further code is executed
?>
