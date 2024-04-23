<?php 
ini_set('memory_limit', '8192M');

// Connect to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cad2023";

$table = "wise";
$startid = 28409362;

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Run SQL query to retrieve data

$sql = "SELECT id, batch_id, state, county, is_proposed, run_date, cad_property_id, cad_geoid, tax_year, situs_address, situs_number, situs_predir, situs_name, situs_suffix, situs_postdir, situs_unit, situs_city, situs_state, situs_zip, legal_description, taxing_unit, appraised, assessed, land, improvement, agricultural, last_ag_value, subdivision, acreage, deed_reference, cap_loss, ownership, exemptions, parcel_type, tax_assessor_id, cross_tax_id, owner_name, owner_dba_name, mail_address1, mail_address2, mail_city, mail_state, mail_zip, ag_cleared, parcel_comments, tax_suit_indicator, tax_suit, future_review, linked_parcels, parcel_record_history, partial_owner, created_at, updated_at FROM $table";
// $sql = "SELECT id, cad_property_id, ownership FROM $table";
$result = $conn->query($sql);

// Open CSV file for writing
$fp = fopen("$table Results 2023 Certified.csv", 'w');

// Write header row to CSV file
$header_row = array('id', 'batch_id', 'state', 'county','is_proposed', 'run_date', 'cad_property_id', 'cad_geoid', 'tax_year', 'situs_address', 'situs_number', 'situs_predir', 'situs_name', 'situs_suffix', 'situs_postdir', 'situs_unit', 'situs_city', 'situs_state', 'situs_zip', 'legal_description', 'taxing_unit', 'appraised', 'assessed', 'land', 'improvement', 'agricultural', 'last_ag_value', 'subdivision', 'acreage', 'deed_reference', 'cap_loss', 'ownership', 'exemptions', 'parcel_type', 'tax_assessor_id', 'cross_tax_id', 'owner_name', 'owner_dba_name', 'mail_address1', 'mail_address2', 'mail_city', 'mail_state', 'mail_zip', 'ag_cleared', 'parcel_comments', 'tax_suit_indicator', 'tax_suit', 'future_review', 'linked_parcels', 'parcel_record_history', 'ownership_code', 'created_at', 'updated_at');
// $header_row = array('id', 'cad_property_id', 'ownership');
fputcsv($fp, $header_row);

// Write data rows to CSV file
if ($result->num_rows > 0) {
	
    while ($row = $result->fetch_assoc()) {
		$modified_value = $startid; // modify the value as needed
		$modified_value = "";
		$row['id'] = $modified_value;
		
		// Write the modified row to the output CSV file
		fputcsv($fp, $row);
		$startid++;
    }
}

// Close CSV file and database connection
fclose($fp);
$conn->close();


?>