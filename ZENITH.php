<?php
//ini_set("memory_limit", "1024M");

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;


//***********TAKING INPUTS********* */
$parcel = $batchDetails->parcel;

//************VARIABLES USED********* */
$l = 0;$prior=0;$parcel_found=false;
$juri_Extract_Count=0;
$del_tax_due = 0;$t=$k=0;
$total_year_due = 0;
$juri_Fee=	$juri_Pen_int=$juri_TOTAL_TAXES=0;
$f  =0;$d = 0;
$print = "";  $tax_details = array();

//****************FUNCTIONS USED********* */
function remove($amt)
{
	return str_replace(",", "", str_replace("$", "",  $amt));
}
function space($space) 
{
	return preg_replace('!\s+!', ' ', str_replace("<br>", "", str_replace("&nbsp;", "", $space)));
}

function landScapePrint($driver)
{
	$driver->executeScript("var css = '@page { size: a3 landscape;transform: rotate(-90deg);transform-origin: left top; }',
			head = document.head || document.getElementsByTagName('head')[0],
			style = document.createElement('style');

		style.type = 'text/css';
		style.media = 'print';

		if (style.styleSheet){
		  style.styleSheet.cssText = css;
		} else {
		  style.appendChild(document.createTextNode(css));
		}

		head.appendChild(style);window.print();");
}

try
{
	echo $parcel." : ".$batchDetails->county."\n";	
	
	$url1 = "https://taxline.zenithtaxes.com/login";
	$driver->get($url1);
	
	$driver->wait()->until( 
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('email'))
	);	
	$driver->findElement(WebDriverBy::id('email'))->sendKeys("bobish@stellaripl.com");	
	$driver->findElement(WebDriverBy::id('password'))->sendKeys("Welcome@123*");	
	sleep(1);
    $driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
	
	
	$driver->wait()->until( function($driver)
	{
		// first scenario Wrong parcel 
		$res1 = strpos($driver->getPageSource(),"Taxline Tracking" ) !== false;
		//success scenario
		$res2 = WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('navigation'));
		return $res1 || $res2;
	});

	if (strpos($driver->getPageSource(), "No items to display") !== false)
	{   
		$apperror = "No results match your search criteria ";
		$result->taxError = $apperror;

	}
    else
    {
        $parcel_found = true;
    }
	echo "Reached Here";
	sleep(20);
	
    if($parcel_found == true)
    {
        $row_count = count($driver->findElements(WebDriverBy::xpath('//*[@id="grid"]/div[3]/table/tbody/tr')));

		if($row_count > 2 )
		{	
			for($i = 1; $i <= $row_count; $i++)
			{	
				$get_parcel = $driver->findElement(WebDriverBy::xpath('//*[@id="grid"]/div[3]/table/tbody/tr['.$i.']/td[1]'))->getText();
			
                if($get_parcel == $parcel )
				{					
					$t += 1;
					$k=$i;					
				}				
			} 

			if($t == 1)
			{
				$driver->findElement(WebDriverBy::xpath('//*[@id="grid"]/div[3]/table/tbody/tr['.$k.']/td[1]'))->click();
				$print = true;
			}
			elseif($t > 1)
			{
				$tax = "Multiple properties found";
				$result->taxError = $tax;
			}
			else
			{
				$tax = "No properties found";
				$result->taxError = $tax;
			}
		}

		else
		{
			$driver->findElement(WebDriverBy::xpath('//*[@id="grid"]/div[3]/table/tbody/tr/td[4]'))->click();
            $print = true;
		}

    }

	if($print == true)
	{
		$tax_stat = "Tax details";
        $tax_stat1= "Payment history";
        $tax_stat2 = "Tax Statement";
    
        $driver->wait()->until( 
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('tabBills')));
        $driver->findElement(WebDriverBy::id('tabBills'))->click();
        $driver->wait()->until( 
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('tblBills')));
			
		try
		{
            $page = $driver->getPageSource();    
            $taxYear = trim(explode("</td>", explode('">', explode('dnn_ctr364_View_divBillDetails">', $page)[1])[3])[0]);
            $result->taxYear = $taxYear;   
            $extracted_parcel = trim(explode("</td>", explode('Property:</span>', explode('dnn_ctr364_View_tdPropertyID', $page)[1])[1])[0]);
            $result->extracted_parcel = $extracted_parcel;    

            //************Delinquent starts            
            $del_tax_due = str_replace("$","",str_replace(",","",explode("</",explode("\">",explode("dnn_ctr364_View_tdPMPastYearsDue", $page)[1])[1])[0]));
                   
            if($del_tax_due > 0)
            {	
                $result->delinquentStatus = true;
                $result->delinquentTotalTax = $del_tax_due;
                $result->delinquentStatus_Eflag = 1;
                $result->delinquentTotalTax_Eflag = 1;
            }
            else
            {
                $result->delinquentTotalTax = 0;
                $result->delinquentStatus_Eflag = 1;
                $result->delinquentTotalTax_Eflag = 1;
            }
                    
            	
				$del_tax_due=0;
				
				$prior_count=count($driver->findElements(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div')));
				if($prior_count > 0)
				{
					$maxYear = 0; $minYear = $taxYear; 
					
					for($k = 0; $k < $prior_count; $k++)
					{					
						$tr1=count($driver->findElements(WebDriverBy::xpath('//*[@id="tblBills'.$k.'"]/tbody/tr')));
							
						$className= $driver->findElement(WebDriverBy::xpath('//*[@id="tblBills'.$k.'"]/tbody/tr['.$tr1.']'))->getAttribute('class');
					
						if($className =="totals")
						{				
							$m=$k+1;		
							$pyear= remove(trim($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[' . $m . ']/table[1]/tbody/tr/td[1]'))->getText()));
							
							$get_total_amt=remove(trim($driver->findElement(WebDriverBy::xpath('//*[@id="tblBills'.$k.'"]/tbody/tr[' . $tr1 . ']/td[5]'))->getText()));
							if($get_total_amt > 0  && $pyear != $taxYear )
							{	
								$del_tax_due += $get_total_amt;
								$result->delinquentStatus = true;
								$result->delinquentTotalTax = round($del_tax_due, 2);
								
							}
							
							if($get_total_amt > 0  && $pyear != $taxYear)
							{
								if ($pyear > $maxYear) {
									$maxYear = $pyear; // Update the latest year if a newer one is found
								}
								if ($pyear < $minYear) {
									$minYear = $pyear; // Update the latest year if a newer one is found
								}
								
							}						
							
						}	
							
					}
					if( $del_tax_due > 0)
					{
						$result->priorYears[$prior] = new priorYear();
						$result->priorYears[$prior]->year = $minYear.'-'.$maxYear;
						$result->priorYears[$prior]->dueAmount = round($del_tax_due,2);
						$result->priorYears[$prior]->taxCollectorName = 'Bastrop County Tax Assessor';
						$result->priorYears[$prior]->extractorName = 'TX0110000';
						$prior++;	
					}
					
				}
           
           
            //**************************************Delinquent Ends              
			$driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[1]/tbody/tr/td[3]'))->click();
			sleep(2);
            $rep_count = count($driver->findElements(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr')));          
            
            for($i = 2; $i <= $rep_count-1; $i++)
            {
                if($i % 2 == 0)
                {
                    $jurisdiction = trim($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[1]'))->getText());                   
                    $paid_jurisdiction = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[4]'))->getText()));
                    $due_jurisdiction= trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[5]'))->getText()));                    
                                     
                }
				else
				{
					$juri_TOTAL_TAXES	= trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[2]/table/tbody/tr[1]/td[2]'))->getText()));
					$juri_Pen_int	= trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[2]/table/tbody/tr[2]/td[2]'))->getText()));
					$juri_Fee	= trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divBillDetails"]/div[1]/table[2]/tbody/tr['.$i.']/td[2]/table/tbody/tr[3]/td[2]'))->getText()));
				
				}
				
				array_push($tax_details, $jurisdiction."@@".$juri_TOTAL_TAXES."@@".$juri_Pen_int."@@".$juri_Fee."@@".$due_jurisdiction);  
                            
            }            
           
            $firstInstBilledAmt = trim(remove(explode("</td>", explode('TOTAL TAXES DUE:</span>', explode("TOTALS</td>", explode('dnn_ctr364_View_divBillDetails">', $page)[1])[1])[1])[0]));
            $result->firstInstBilledAmt = $firstInstBilledAmt;
            $firstInstPaidAmt = trim(remove(explode("</td>", explode('AMOUNT PAID:</span>', explode("TOTALS</td>", explode('dnn_ctr364_View_divBillDetails">', $page)[1])[1])[1])[0]));
            $result->firstInstPaidAmt = $firstInstPaidAmt;
            $firstInstDueAmt = trim(remove(explode("</td>", explode('BALANCE:</span>', explode("TOTALS</td>", explode('dnn_ctr364_View_divBillDetails">', $page)[1])[1])[1])[0]));
            $totalTaxDue = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_tdPMTotalDue"]'))->getText()));
            
            $result->totalTaxDue = $totalTaxDue;
            $result->firstInstDueAmt = $firstInstDueAmt;
            
            if($firstInstBilledAmt != 0)
            {
                if($result->totalTaxDue == 0)
                {
                    $firstInstStatus = 'Paid'; 
                    $result->firstInstStatus = $firstInstStatus;
                    
                    $firstInstPaidDate = trim($driver->findElement(WebDriverBy::xpath('/html/body/form/div[5]/div/table/tbody/tr[1]/td/div/div/div[1]/div/div/div/div[3]/table[3]/tbody/tr[2]/td[1]/table/tbody/tr/td/div/div[1]/table[2]/tbody/tr[2]/td[3]'))->getText());
                    $result->firstInstPaidDate = $firstInstPaidDate;
                    
                }
                else
                {
                    $firstInstStatus = 'Due';
                    $result->firstInstStatus = $firstInstStatus;
                    $firstInstPaidDate = "";
                }
            }
            else
            {
                $firstInstStatus = '';
                $firstInstPaidDate = "";	
            }			
		}
		catch(Exception $ex)
		{
			$result->extractionError = "An error has occurred while extracting data from the county website, Please try again later1 ";
		}
		
		$driver->wait()->until( 
		WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('tdDetailsTab')));
		$driver->findElement(WebDriverBy::id('tdDetailsTab'))->click();

		$driver->wait()->until( 
		WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('tdGeneralInformation')));
		
		
		try
		{
            $page = $driver->getPageSource();            
            $t_year = trim(explode("\">",explode("dnn_ctr364_View_tdEETitle",explode("ENTITIES &amp; EXEMPTIONS",$page)[0])[1])[1]);
           			
			$rep_count = count($driver->findElements(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divEntitiesAndExemptionsData"]/table/tbody/tr')));

			if(strpos($page , "dnn_ctr364_View_tdVITotalAssessedValueOthers") !== false)
			{
				$assessed = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_tdVITotalAssessedValueOthers"]'))->getText()));
			}
			elseif(strpos($page , "dnn_ctr364_View_tdVITotalAssessedValueRP") !== false)
			{
				$assessed = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_tdVITotalAssessedValueRP"]'))->getText()));
			}

           
            $im=0;
			                                                                     
			for($i = 2; $i <= $rep_count-1; $i++)
			{
				$jurisdiction = trim($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divEntitiesAndExemptionsData"]/table/tbody/tr['.$i.']/td[1]'))->getText());
				$exmp_amt = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divEntitiesAndExemptionsData"]/table/tbody/tr['.$i.']/td[3]'))->getText()));
				$taxable_value = trim(remove($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divEntitiesAndExemptionsData"]/table/tbody/tr['.$i.']/td[4]'))->getText()));
				$tax_rate= trim($driver->findElement(WebDriverBy::xpath('//*[@id="dnn_ctr364_View_divEntitiesAndExemptionsData"]/table/tbody/tr['.$i.']/td[5]'))->getText());
				
				$juri_code = trim(explode("-",$jurisdiction)[0]);

				$juri_name =trim(explode("-",$jurisdiction)[1]);
				if($juri_code == "TCESD")
				{
					$juri_name="Bastrop-Travis ESD1";
				}    
				if($juri_code == "PHCSF")
				{
					$juri_name="Pid - Hunter's Crossing Single Family";
				}                    
					foreach($tax_details as $item)
					{
						$juriName = trim(explode("@@",$item)[0]);
						$juriTax = trim(remove(explode("@@",$item)[1]));
						$juripen_int = trim(remove(explode("@@",$item)[2]));
						$juriFee =trim( remove(explode("@@",$item)[3]));
						$juriDue = trim(remove(explode("@@",$item)[4]));
					
						if($juri_name == $juriName)
						{
							$result->taxes[$im] = new tax();
							$result->taxes[$im]->jurisdiction = $juriName;
							$result->taxes[$im]->jurisdictionCode = $juri_code;
							$result->taxes[$im]->estimatedTax =$result->taxes[$im]->totalTax = $juriTax;
							if($juriDue == 0)
							{
								$result->taxes[$im]->baseTaxDue =$result->taxes[$im]->taxDue = $juriDue;
							}
							else{
								$result->taxes[$im]->taxDue = $juriDue;
								$result->taxes[$im]->baseTaxDue =round($juriDue - ($juripen_int+$juriFee),2);
							}
							
							$result->taxes[$im]->taxYear = $taxYear;
							$result->taxes[$im]->taxRate = $tax_rate;
							$result->taxes[$im]->assessed = $assessed;
							$result->taxes[$im]->taxable = $taxable_value;
							$result->taxes[$im]->exemption = $exmp_amt;	
							$result->taxes[$im]->penaltyAndInterest =$juripen_int;
							$result->taxes[$im]->fee =$juriFee ;
								
						}                               
					}
					$im++;
				
			}            
                
                $exemptionDescription = trim($driver->findElement(WebDriverBy::id('dnn_ctr364_View_tdOIExemptions'))->getText());
                $result->exemptionDescription = $exemptionDescription;
						 
		}
		catch(Exception $ex)
		{
			$result->extractionError = "An error has occurred while extracting data from the county website, Please try again later2 ". $parcel;
		}
		
		landScapePrint($driver);
		sleep(2);	
		$result->rename($tax_stat);

		$driver->findElement(WebDriverBy::id('tdPaymentHistoryTab'))->click();
		
		landScapePrint($driver);
		sleep(2);	
		$result->rename($tax_stat1);

		
		$driver->findElement(WebDriverBy::id('tabBills'))->click();
		$driver->wait()->until( 
			WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('tblBills')));
		
		if (count($driver->findElements(WebDriverBy::id('btnPrint')))!=0)
		{
			$driver->findElement(WebDriverBy::id('btnPrint'))->click();
			sleep(1);
			$result->rename($tax_stat2); 
		}

		$year = date("Y"); $year1 = $year - 1; $year2 = $year - 2;

		if(count($driver->findElements(WebDriverBy::id("btnPrintTaxStatement$year"))) > 0){
			$driver->findElement(WebDriverBy::id("btnPrintTaxStatement$year"))->click();
		}else if(count($driver->findElements(WebDriverBy::id("btnPrintTaxStatement$year1"))) > 0){
			$driver->findElement(WebDriverBy::id("btnPrintTaxStatement$year1"))->click();
		}else{
			$driver->findElement(WebDriverBy::id("btnPrintTaxStatement$year2"))->click();
		}
		for($pt=1; $pt<=40; $pt++){
			sleep(1);
			if(count(glob($path . "/download*.pdf"))  == 1)
			{
				break;
			}
		}
		
		sleep(1);
		$tax_stat2 = "Tax statement detail";
		$result->rename($tax_stat2);
		sleep(1);

		try
		{
			$source_pdf = $path."\\$tax_stat2.pdf";
			$st = ConvertPDFToHTML($rootPath, $path, $source_pdf);

			$firstInstDueDate = trim(explode("<b>",explode("</b",explode('TOTAL DUE IF PAID BY</b><br>', $st)[1])[0])[1]);
			$result->firstInstDueDate = $firstInstDueDate;			

			unlink("$path\\Results.html");
			unlink("$path\\Result.html");
			unlink("$path\\Result_ind.html");

			for($i=1; $i<=10;$i++)
				{
					for($j=1; $j<=10;$j++)
				{
					if(file_exists("$path\\Result-".$j."_".$i.".jpg"))
						unlink("$path\\Result-".$j."_".$i.".jpg");
				}
				}
		}
		catch(Exception $ex)
		{
			$result->extractionError = "An error has occurred while extracting data from the county website, Please try again later4 ";
		}

	}	

	$driver->quit();
}
catch (Exception $e)
{
	// Log error
	error_log($parcel.":".$e->getMessage()." ".$e->getTraceAsString());
	//WriteResult($parcelData,"","","","",$e->getMessage());
	$result->otherError = $e->getMessage();
	if ($driver != null)
		$driver->quit();
}
?>