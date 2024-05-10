<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$parcel = $batchDetails->parcel;
$address = strtoupper($batchDetails->address);

$file = fopen('input.csv', 'r');

    // Initialize an empty array to store data
    $data = [];

    // Read each row of the CSV file
    while (($row = fgetcsv($file)) !== false) {
        // Store each row in the data array
        $data[] = $row;
    }

    // Close the file
    fclose($file);

 


function remove($amt) {
	return str_replace(",", "", str_replace("$", "",  $amt));
}
function space($space) {
	return preg_replace('!\s+!', ' ', str_replace("<br>", "", str_replace("&nbsp;", "", $space)));
}
$prints = ""; $year = date("Y"); $year1 = $year - 1; $year2 = $year - 2; $k = 0;


try 
{	

	echo $parcel." : ".$batchDetails->county."\n";
	
	// Navigate to URL
	$url = "https://taxline.zenithtaxes.com/login";
	$driver->get($url);
		
	$driver->wait()->until( 
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('email')));

	$driver->findElement(WebDriverBy::id('email'))->sendKeys("bobish@stellaripl.com");
	$driver->findElement(WebDriverBy::id('password'))->sendKeys("Welcome@123*");	
	$driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

	$driver->wait()->until( 
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('clientsdrop')));
    
    echo "reached\n";
    $taxlineurl = "https://taxline.zenithtaxes.com/taxlinetracking/all";
	$driver->get($taxlineurl);

	$driver->wait()->until( 
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('portfolio')));
        sleep(5); 
    $driver->findElement(WebDriverBy::id('portfolio'))->sendKeys("ZTP-000004");

    $driver->wait()->until( 
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('datatable1_filter')));
        sleep(5); 
        // echo $driver->getPageSource(); 
        foreach ($data as $row) {
            $order_num = $row[0];
            $dbid = $row[1];
            $link = "";
            $json_data = file_get_contents($rootPath."/scripts/Zenith/".$dbid."/result.json");
            $jsonData = json_decode($json_data, true);
            
            if(count($driver->findElements(WebDriverBy::id('datatable1_filter'))) > 0)
            {
                $driver->findElement(WebDriverBy::id('datatable1_filter'))->findElement(WebDriverBy::tagName('input'))->sendKeys($order_num);
                $link = $driver->findElement(WebDriverBy::id('datatable1'))->findElement(WebDriverBy::tagName('a'))->getAttribute('href');
            } elseif(count($driver->findElements(WebDriverBy::id('datatable_filter'))) > 0){
                $driver->findElement(WebDriverBy::id('datatable_filter'))->findElement(WebDriverBy::tagName('input'))->sendKeys($order_num);
                $link = $driver->findElement(WebDriverBy::id('datatable'))->findElement(WebDriverBy::tagName('a'))->getAttribute('href');
            }
            
            // "firstInstDueAmt": "0.00",
            // "firstInstStatus": "Paid", 
            // "firstInstDueDate": "",
            // "firstInstPaidAmt": "18387.94",
            // "firstInstPaidDate": "1/24/2024",
            // "firstInstBilledAmt": "18387.94",
            // "penaltyAndInt": 0,
            $bill = $jsonData['firstInstBilledAmt'];
            $paid = $jsonData['firstInstPaidAmt'];
            $pi = $jsonData['penaltyAndInt'];
            $balance = $jsonData['firstInstDueAmt'];

            $dateString = $jsonData['firstInstPaidDate'];
            $date = DateTime::createFromFormat('m/d/Y', $dateString);
            $formattedDate = $date->format('m/d/Y');
            

            $driver->get($link);
            sleep(3);
            // $driver->get($taxlineurl);
            $driver->findElement(WebDriverBy::cssSelector('.btn.btn-success[data-target="#yearmodal"]'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::id('tax_year'))->sendKeys("2023");
            sleep(1);
            $driver->findElement(WebDriverBy::xpath("//button[text()='Add']"))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::id('year_2023'))->click();
            sleep(2);
            $dataTable = $driver->findElement(WebDriverBy::cssSelector('.table.table-bordered'));
            $tabBody =  $dataTable->findElement(WebDriverBy::tagName("tbody"));            
            
            echo "Before\n";
            $driver->findElement(WebDriverBy::id('statusall'))->findElement(WebDriverBy::tagName('a'))->click();
            echo "After\n";
            sleep(3);
            // echo $driver->getPageSource(); 
        }        


    // sleep(22);
    
	
					
	$driver->quit();
}
catch (Exception $e)
{
	// Log error
	error_log($parcel.":".$e->getMessage()." ".$e->getTraceAsString());
	
	$result->otherError = $e->getMessage();
	
	if ($driver != null)
		$driver->quit();
}
?>