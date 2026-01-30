<?
$csvFile = 'selectel.csv'; // Replace 'example.csv' with the path to your CSV file

if (($handle = fopen($csvFile, 'r')) !== false) {
    $header = fgetcsv($handle); // Get the header row
    $asanas = [];

    while (($row = fgetcsv($handle)) !== false) {
        $asanas[] = array_combine($header, $row); // Combine header with each row
    }

    fclose($handle); // Close the file handle

    print nl2br(print_r($asanas,true)); // Output or process the associative array as needed
} else {
    echo "Failed to open file for reading.";
}
?>
