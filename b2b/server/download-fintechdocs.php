<?php
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $fileUrl = $_GET['file'];

    // Get the file name from the URL
    $fileName = basename($fileUrl);

    // Initialize cURL session
    $ch = curl_init($fileUrl);

    // Set options for the cURL request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    // Execute the cURL request
    $fileContent = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    if ($fileContent !== false) {
        // Set the appropriate headers for the download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($fileContent));

        // Output the file content
        echo $fileContent;

        exit;
    } else {
        echo "Error fetching the file content.";
    }
} else {
    echo "Invalid file URL.";
}
?>
