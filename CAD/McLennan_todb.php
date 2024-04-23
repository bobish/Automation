<?php
	
	include_once("config.php");
	ini_set('memory_limit', '8192M');

	date_default_timezone_set("Asia/Kolkata");
	// require 'vendor/autoload.php';
	function numericCheck($stringVal)
	{
		if (is_numeric($stringVal)) 
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}	
	
	$state_a = "TX";
	$county_a = "McLennan";
	$table = "mclennan";

	$path = dirname(__FILE__)."\\Data\\";
	$common = "2022-07-25_003789_";

	$headerText = file_get_contents($path."\\".$common."APPRAISAL_HEADER.txt");


	$appInfoText = file_get_contents($path."\\".$common."APPRAISAL_INFO.txt");
	$appInfoHandle = fopen($path."\\".$common."APPRAISAL_INFO.txt", "r");

	echo "Time_Started ".date("d")."_".date("m")."_".date("Y")."_".date("His")."\n";
	$runDateTime = trim(substr($headerText, 0,16));
	
	$fileDesc = trim(substr($headerText, 16,40));
	$app_tax_yr = trim(substr($headerText, 56,4));
	
	$suppNum = trim(substr($headerText, 60,4));
	$entCd = trim(substr($headerText, 64,10));
	$entDesc = trim(substr($headerText, 74,40));
	$offName = trim(substr($headerText, 114,30));
	$oper = trim(substr($headerText,144,20));
	$pacsVer = trim(substr($headerText,164,10));
	$expVer = trim(substr($headerText,174,10));
	$valOpt = trim(substr($headerText,184,10));
	$offUseOnly = trim(substr($headerText,194,50));
	
	$ind = 0;
	// $handle = fopen($path."\\2022-08-01_001820_APPRAISAL_INFO_MOD.TXT", "r");
	// $handle = fopen($path."\\2022-08-25_000215_MOD.TXT", "r");
	// $subdivisionCd = trim(substr($subdivText,0,10);	
	
	if ($appInfoHandle) {
		while (($line = fgets($appInfoHandle)) !== false) {
			$prop_type_cd = trim(substr($line,12,5));
			if($prop_type_cd != "P")
			{
							
				$cad_prop_id1 = trim(substr($line,0,12));
				$cad_prop_id = ltrim($cad_prop_id1, "0"); 

				
				$cad_geo_id = trim(substr($line,546, 25));

				$acre1 = trim(substr($line,2771, 20));
				$acre2 = ltrim($acre1, "0"); 
				
				if($acre2 != "")
					$acre = (string)($acre2/10000);
				else
					$acre = "";

				//Ownername
				$py_owner_name = trim(substr($line,608,70));
				$py_addr_line1 = trim(substr($line,693,60));
				$owner_name = trim(preg_replace('!\s+!', ' ', $py_owner_name.' '.$py_addr_line1));
				$owner_name = mysqli_real_escape_string($dbcon, $owner_name);
				
				$mailing_add_line1 = trim(substr($line,753,60)); // py_addr_line1
				$mailing_add_line1 = mysqli_real_escape_string($dbcon, $mailing_add_line1);
				$siteNum = "";
				// $siteNum = trim(substr($line,753,60)); // py_addr_line2
				$mailing_add_line3 = trim(substr($line,813,60)); // py_addr_line2
				// $mail_add3 = trim(substr($line,813,60)); // py_addr_line3
				// $mailing_add_line3 = "";
				// if($mail_add2 != "" && $mail_add3 != "")
				// {
				// 	$mailing_add_line3 = trim(preg_replace('!\s+!', ' ',$mail_add2.', '.$mail_add3));
				// }else if($mail_add2 != "" && $mail_add3 == "")
				// {
				// 	$mailing_add_line3 = trim(preg_replace('!\s+!', ' ',$mail_add2));
				// }else if($mail_add2 == "" && $mail_add3 != "")
				// {
				// 	$mailing_add_line3 = trim(preg_replace('!\s+!', ' ',$mail_add3));
				// }
				$mailing_add_line3 = mysqli_real_escape_string($dbcon, $mailing_add_line3);
				$mailing_city = trim(substr($line,873,50)); // py_addr_city
				$mailing_city = mysqli_real_escape_string($dbcon, $mailing_city);
				$mailing_state = trim(substr($line,923,50)); //py_addr_state
				$mailing_zip4 = "";
				$mailing_zip = trim(substr($line,978,5)); // py_addr_zip
				$mailing_zip4 = trim(substr($line,983,4)); 
				
				if($mailing_zip4 != "")
				{
					$mailing_zip = $mailing_zip."-".$mailing_zip4;
				}	
				
				$situsNum = trim(substr($line,4459,10));
				$situs_street_prefx = trim(substr($line,1039,10));
				$situs_street = trim(substr($line,1049,50));
				$situs_street_suffix = trim(substr($line,1099,10));
				$situs_unit = trim(substr($line,4474,5));
				$situs_add1 = trim(preg_replace('!\s+!', ' ', $situsNum." ".$situs_street_prefx." ".$situs_street." ".$situs_street_suffix." ".$situs_unit));
								
				$situs_add1 = mysqli_real_escape_string($dbcon, $situs_add1);
				$situs_city = mysqli_real_escape_string($dbcon,trim(substr($line,1109,30)));
				$situs_zip = trim(substr($line,1139,10));
				$legal_desc = trim(substr($line,1149,255));
				$legal_desc2 = trim(substr($line,1404,255));
				if(trim($legal_desc2) != "")
				{
					$legal_desc = $legal_desc."\n".$legal_desc2;
				}
				
				$legal_desc = mysqli_real_escape_string($dbcon, $legal_desc);
				
				$legal_acreage = trim(substr($line,1659,16));
				$abs_subdv_cd = trim(substr($line,1675,10));
				
/* 				// $string = "A12345"; // Example string

				if (preg_match('/^[AS]\d{5}$/', $abs_subdv_cd))
				{
					$abs_subdv_cd = substr($cad_geo_id, 0, 9);
				} */

				$caploss = 0;
				$landh = trim(substr($line,1795,15));
				$landh = numericCheck($landh)? $landh : 0;
				$landnon = trim(substr($line,1810,15));
				$landnon = numericCheck($landnon)? $landnon : 0;
				// echo $cad_prop_id."\n";
				// echo $landh."\n";
				// echo $landnon."\n";
				$land = $landh+$landnon;
				$improvh = trim(substr($line,1825,15));
				$improvh = numericCheck($improvh)? $improvh : 0;
				$improvnon = trim(substr($line,1840,15));
				$improvnon = numericCheck($improvnon)? $improvnon : 0;
				$improv = $improvh + $improvnon;

				// $appr = trim(substr($line,1915,15)); 
				$caploss = trim(substr($line,1930,15));  
				// $assessed = $appr - $caploss; 
				$caploss = numericCheck($caploss)? $caploss : 0;
				$agri_use = trim(substr($line,1855,15));
				$agri_use = numericCheck($agri_use)? $agri_use : 0;
				$agri_market = trim(substr($line,1870,15));
				$agri_market = numericCheck($agri_market)? $agri_market : 0;
				$agri_use_red = $agri_market - $agri_use;
				 
				// echo "AGRI MARKET ".$agri_market."\n";
				$timber_market = trim(substr($line,1900,15));
				$timber_use = trim(substr($line,1885,15));
				$timber_market = numericCheck($timber_market)? $timber_market : 0;
				$timber_use = numericCheck($timber_use)? $timber_use : 0;
				$timber_use_red = $timber_market - $timber_use;
				
				$market_value = $land+$improv+$agri_market+$timber_market;
				// echo "MARKET ".$market_value."\n";
				$appr = $market_value - ($agri_use_red + $timber_use_red);
				// echo "Appraised ".$appr."\n";
				$assessed = $appr - $caploss;
				$land_market = $land+$agri_market;
				
				$deed_num = trim(substr($line,5357,50));
				$deed_book_id = trim(substr($line,1993,20));
				$deed_book_page = trim(substr($line,2013,20));
				$deed_dt = trim(substr($line,2033,25));
				$deed_ref = trim($deed_num.'/'.$deed_book_id.'/'.$deed_book_page.'/'.$deed_dt);
				if($deed_ref == "///")
				{
					$deed_ref = "";
				}
				$deed_ref = mysqli_real_escape_string($dbcon, $deed_ref);
				

				$entities = trim(substr($line,5201,140));
				
				// Exemptions
				
				$exe = "";
				$hs_exe = trim(substr($line,2608,1));
				if($hs_exe == "T")
				{
					$exe = "Hms";
				}
				$over65_exe = trim(substr($line,2609,1));
				
				if($over65_exe == "T")
				{
					if ($exe == "")
						$exe = "Over65";
					else
						$exe = $exe.", Over65";
				}
				$over65s_exe = trim(substr($line,2660,1));
				if($over65s_exe == "T")
				{
					if ($exe == "")
						$exe = "Over65";
					else
						$exe = $exe.", Over65";
				}			
				$dis_per_exe = trim(substr($line,2661,1));
				if($dis_per_exe == "T")
				{
					if ($exe == "")
						$exe = "Disabled Person";
					else
						$exe = $exe.", Disabled Person";
				}
				
				$dis_vet30 = trim(substr($line,2662,1));
				if($dis_vet30 == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran1";
					else
						$exe = $exe.", Disabled Veteran1";
				}			
				$dis_vet30s = trim(substr($line,2663,1));
				if($dis_vet30s == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran1";
					else
						$exe = $exe.", Disabled Veteran1";
				}

				$dis_vet50 = trim(substr($line,2664,1));
				if($dis_vet50 == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran2";
					else
						$exe = $exe.", Disabled Veteran2";
				}			
				$dis_vet50s = trim(substr($line,2665,1));
				if($dis_vet50s == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran2";
					else
						$exe = $exe.", Disabled Veteran2";
				}
				$dis_vet70 = trim(substr($line,2666,1));
				if($dis_vet70 == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran3";
					else
						$exe = $exe.", Disabled Veteran3";
				}			
				$dis_vet70s = trim(substr($line,2667,1));
				if($dis_vet70s == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran3";
					else
						$exe = $exe.", Disabled Veteran3";
				}
				$dis_vet100 = trim(substr($line,2668,1));
				if($dis_vet100 == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran4";
					else
						$exe = $exe.", Disabled Veteran4";
				}			
				$dis_vet100s = trim(substr($line,2669,1));
				if($dis_vet100s == "T")
				{
					if ($exe == "")
						$exe = "Disabled Veteran4";
					else
						$exe = $exe.", Disabled Veteran4";
				}
				
				$ex_exempt = trim(substr($line,2670,1));
				$ex_xv_exempt = trim(substr($line,8253,1));
				
				if($ex_exempt == "T" or $ex_xv_exempt == "T")
				{
					if ($exe == "")
						$exe = "Total Exempt";
					else
						$exe = $exe.", Total Exempt";
				}
				$ab_exempt = trim(substr($line,2722,1));
				if($ab_exempt == "T")
				{
					if ($exe == "")
						$exe = "Abatement";
					else
						$exe = $exe.", Abatement";
				}
				$en_exempt = trim(substr($line,2723,1));
				if($en_exempt == "T")
				{
					if ($exe == "")
						$exe = "Energy";
					else
						$exe = $exe.", Energy";
				}
				$fr_exempt = trim(substr($line,2724,1));
				if($fr_exempt == "T")
				{
					if ($exe == "")
						$exe = "Freeport";
					else
						$exe = $exe.", Freeport";
				}
				$ht_exempt = trim(substr($line,2725,1));
				if($ht_exempt == "T")
				{
					if ($exe == "")
						$exe = "Historical";
					else
						$exe = $exe.", Historical";
				}
				$pc_exempt = trim(substr($line,2727,1));
				if($pc_exempt == "T")
				{
					if ($exe == "")
						$exe = "Pollution";
					else
						$exe = $exe.", Pollution";
				}
				$so_exempt = trim(substr($line,2728,1));
				if($so_exempt == "T")
				{
					if ($exe == "")
						$exe = "Solar";
					else
						$exe = $exe.", Solar";
				}
				$ex366_exempt = trim(substr($line,2729,1));
				if($ex366_exempt == "T")
				{
					if ($exe == "")
						$exe = "Ex 366";
					else
						$exe = $exe.", Ex 366";
				}
				$ch_exempt = trim(substr($line,2730,1));
				if($ch_exempt == "T")
				{
					if ($exe == "")
						$exe = "Charitable";
					else
						$exe = $exe.", Charitable";
				}
				$eco_exempt = trim(substr($line,5341,1));
				if($eco_exempt == "T")
				{
					if ($exe == "")
						$exe = "Eco";
					else
						$exe = $exe.", Eco";
				}
				$chodo_exempt = trim(substr($line,5407,1));
				if($chodo_exempt == "T")
				{
					if ($exe == "")
						$exe = "Charitable";
					else
						$exe = $exe.", Charitable";
				}
				$local_option_pct_only_flag_hs = trim(substr($line,5408,1));
				if($local_option_pct_only_flag_hs == "T")
				{
					if ($exe == "")
						$exe = "Local Hms";
					else
						$exe = $exe.", Local Hms";
				}
				$local_option_pct_only_flag_ov65 = trim(substr($line,5409,1));
				if($local_option_pct_only_flag_ov65 == "T")
				{
					if ($exe == "")
						$exe = "Local Over65";
					else
						$exe = $exe.", Local Over65";
				}
				$local_option_pct_only_flag_ov65s = trim(substr($line,5410,1));
				if($local_option_pct_only_flag_ov65s == "T")
				{
					if ($exe == "")
						$exe = "Local Over65";
					else
						$exe = $exe.", Local Over65";
				}
				$local_option_pct_only_flag_dp = trim(substr($line,5411,1));
				if($local_option_pct_only_flag_dp == "T")
				{
					if ($exe == "")
						$exe = "Local Disabled Person";
					else
						$exe = $exe.", Local Disabled Person";
				}
				$freeze_only_flag_ov65 = trim(substr($line,5412,1));
				if($freeze_only_flag_ov65 == "T")
				{
					if ($exe == "")
						$exe = "Over65 Freeze";
					else
						$exe = $exe.", Over65 Freeze";
				}
				$freeze_only_flag_ov65s = trim(substr($line,5413,1));
				if($freeze_only_flag_ov65s == "T")
				{
					if ($exe == "")
						$exe = "Over65 Freeze";
					else
						$exe = $exe.", Over65 Freeze";
				}
				$freeze_only_flag_dp = trim(substr($line,5414,1));
				if($freeze_only_flag_dp == "T")
				{
					if ($exe == "")
						$exe = "Disabled Person Freeze";
					else
						$exe = $exe.", Disabled Person Freeze";
				}
				$lih_exempt = trim(substr($line,5432,1));
				if($lih_exempt == "T")
				{
					if ($exe == "")
						$exe = "Low Income Housing";
					else
						$exe = $exe.", Low Income Housing";
				}
				$git_exempt = trim(substr($line,5433,1));
				if($git_exempt == "T")
				{
					if ($exe == "")
						$exe = "Transit";
					else
						$exe = $exe.", Transit";
				}
				$dps_exempt = trim(substr($line,5434,1));
				if($dps_exempt == "T")
				{
					if ($exe == "")
						$exe = "Disabled Person";
					else
						$exe = $exe.", Disabled Person";
				}
				$local_option_pct_only_flag_dps = trim(substr($line,5460,1));
				if($local_option_pct_only_flag_dps == "T")
				{
					if ($exe == "")
						$exe = "Local Disabled Person";
					else
						$exe = $exe.", Local Disabled Person";
				}
				$freeze_only_flag_dps = trim(substr($line,5461,1));
				if($freeze_only_flag_dps == "T")
				{
					if ($exe == "")
						$exe = "Disabled Person Freeze";
					else
						$exe = $exe.", Disabled Person Freeze";
				}
				$dvhs_exempt = trim(substr($line,5462,1));
				if($dvhs_exempt == "T")
				{
					if ($exe == "")
						$exe = "100% Disabled Veteran";
					else
						$exe = $exe.", 100% Disabled Veteran";
				}
				$clt_exempt = trim(substr($line,7183,1));
				if($clt_exempt == "T")
				{
					if ($exe == "")
						$exe = "Community Land Trust";
					else
						$exe = $exe.", Community Land Trust";
				}
				$dvhss_exempt = trim(substr($line,7238,1));
				if($dvhss_exempt == "T")
				{
					if ($exe == "")
						$exe = "100% Disabled Veteran";
					else
						$exe = $exe.", 100% Disabled Veteran";
				}
				$dvch_exempt = trim(substr($line,8422,1));
				if($dvch_exempt == "T")
				{
					if ($exe == "")
						$exe = "Charitable";
					else
						$exe = $exe.", Charitable";
				}
				$masss_exempt = trim(substr($line,8532,1));
				if($masss_exempt == "T")
				{
					if ($exe == "")
						$exe = "Armed Service Spouse";
					else
						$exe = $exe.", Armed Service Spouse";
				}
				$frss_exempt = trim(substr($line,8632,1));
				if($frss_exempt == "T")
				{
					if ($exe == "")
						$exe = "First Responder Spouse";
					else
						$exe = $exe.", First Responder Spouse";
				}
				$dstr_exempt = trim(substr($line,8942,1));
				if($dstr_exempt == "T")
				{
					if ($exe == "")
						$exe = "Disaster Damage";
					else
						$exe = $exe.", Disaster Damage";
				}
				$dstrs_exempt = trim(substr($line,9012,1));
				if($dstrs_exempt == "T")
				{
					if ($exe == "")
						$exe = "Disaster Damage";
					else
						$exe = $exe.", Disaster Damage";
				}
				$abmno_exempt = trim(substr($line,8687,1));
				if($abmno_exempt == "T")
				{
					if ($exe == "")
						$exe = "Abatement";
					else
						$exe = $exe.", Abatement";
				}
				if($caploss > 0)
				{
					if ($exe == "")
						$exe = "Cap";
					else
						$exe = $exe.", Cap";
				}			
				if($agri_use > 0)
				{
					if ($exe == "")
						$exe = "Ag Use";
					else
						$exe = $exe.", Ag Use";
				}	

				// partial Owner
				
				$partial_owner = trim(substr($line,678,1));
				$ownercode = trim(substr($line,679,12));
				
				if($partial_owner == "F")
				{
					$ownership_per = "100";
				}
				else
				{
					$ownership_per = "";
				}
				
				$insquery = "INSERT INTO $table(batch_id,state,county,run_date,cad_property_id,cad_geoid,tax_year,situs_address,situs_number,situs_predir,situs_name,situs_suffix,situs_postdir,situs_unit,situs_city,situs_state,situs_zip,legal_description,taxing_unit,appraised,assessed,land,improvement,agricultural,last_ag_value,subdivision,acreage,deed_reference,cap_loss,ownership,exemptions,parcel_type,tax_assessor_id,cross_tax_id,owner_name,owner_dba_name,mail_address1,mail_address2,mail_city,mail_state,mail_zip,ag_cleared,parcel_comments,tax_suit_indicator,tax_suit,future_review,linked_parcels,parcel_record_history,partial_owner,created_at,updated_at,ownershipCd) VALUES ('2','$state_a','$county_a','$runDateTime','$cad_prop_id','$cad_geo_id','$app_tax_yr','$situs_add1','$siteNum','','','','','','$situs_city','$state_a','$situs_zip','$legal_desc','$entities','$appr','$assessed','$land_market','$improv','$agri_use',0,'$abs_subdv_cd','$acre','$deed_ref','$caploss','$ownership_per','$exe','$prop_type_cd','','','$owner_name','','$mailing_add_line1','$mailing_add_line3','$mailing_city','$mailing_state','$mailing_zip','','','','','','','','$partial_owner','','','$ownercode')";
				
				$results = mysqli_query($dbcon, $insquery);
				
				if ($results === false || $dbcon->affected_rows == 0) {
					echo "Insert failed: " . $dbcon->error."\n".$insquery."\n";
					sleep(1);
				} else {
					// echo "Insert successful!\n";
				}
				
				// sleep(2);
					// echo "INSERT INTO `$table`(id,batch_id,state,county,run_date,cad_property_id,cad_geoid,tax_year,situs_address,situs_number,situs_predir,situs_name,situs_suffix,situs_postdir,situs_unit,situs_city,situs_state,situs_zip,legal_description,taxing_unit,appraised,assessed,land,improvement,agricultural,last_ag_value,subdivision,acreage,deed_reference,cap_loss,ownership,exemptions,parcel_type,tax_assessor_id,cross_tax_id,owner_name,owner_dba_name,mail_address1,mail_address2,mail_city,mail_state,mail_zip,ag_cleared,parcel_comments,tax_suit_indicator,tax_suit,future_review,linked_parcels,parcel_record_history,ownership_code,created_at,updated_at) VALUES (0,'2','$state_a','$county_a','$runDateTime','$cad_prop_id','$cad_geo_id','$app_tax_yr','$situs_add1','$siteNum','','','','','','$situs_city','$state_a','$situs_zip','$legal_desc','$entities','$appr','$assessed','$land_market','$improv','$agri_use',0,'$abs_subdv_cd','$acre','$deed_ref','$caploss','$ownership_per','$exe','$prop_type_cd','','','$owner_name','','$mailing_add_line1','$mailing_add_line3','$mailing_city','$mailing_state','$mailing_zip','','','','','','','','$partial_owner','','')\n\n";
				// sleep (1);
				$ind++;

				// if($ind > 25000)
				// {
					// break;
				// }
		

			}
		}

		fclose($appInfoHandle);
	}
	
	
echo "Time_Ended ".date("d")."_".date("m")."_".date("Y")."_".date("His")."\n";

	$che_own = "select id, partial_owner, ownershipCd from $table where partial_owner != 'F' ";
	echo $che_own."\n";
	// $che_own = "select id, partial_owner, ownershipCd from $table";

	$part_owners = mysqli_query($dbcon, $che_own);

	while($part = mysqli_fetch_assoc($part_owners))
	{
		// echo $part['ownershipCd'];
		$own = $part['ownershipCd'];
		$ch = "select count(ownershipCd) as count from $table where ownershipCd = '$own' ";
		$counter = mysqli_query($dbcon, $ch);
		$a_data = mysqli_fetch_assoc($counter);
		$numTime = $a_data['count'];
		// if($numTime == 0)
			// $numTime = 1;
			
		$owner_per = 100/$numTime;
		$up_per = round($owner_per,2);
		$up = "UPDATE $table set ownership = '$up_per' where ownershipCd = '$own' and partial_owner != 'F' ";
		// $up = "UPDATE $table set ownership = '$up_per' where ownershipCd = '$own'";
		$res = mysqli_query($dbcon, $up);
		
	}
	
	

?>