require 'csv'
require 'mysql2'


state = "TX"

county = "Montgomery"

taxyear = "2022"
con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "montgomery")

ar = []
cn = 1
 # `Owner`(`RecordType`, `PropertyID`, `QuickRefID`, `PropertyNumber`, `OwnerID`, `OwnerQuickRefID`, `OwnerPropertyNumber`, `OwnerName`, `Address1`, `Address2`, `Address3`, `City`, `State`, `Zip`, `OwnershipPercent`, `ConfidentialOwner`, `ExemptionList`, `HSCapAdj`, `CurrHSCapAdj`


code_meaning = {
	"HS" => "Hms",
	"DTD" => "Disabled Person Deferral",
	"DV" => "Disabled Veteran",
	"HB366" => "HB366",
	"DP" => "Disabled Person",
	"AG" => "Agricultural Use",
	"EX" => "Total Exempt",
	"EXC" => "Total Exempt",
	"EXCM" => "Total Exempt",
	"AUTO" => "Auto",
	"EXCHA" => "Total Exempt",
	"EXS" => "Total Exempt",
	"AGW" => "Agricultural Use",
	"EXPPI" => "Total Exempt",
	"FR" => "First Responder",
	"FP" => "Freeport",
	"SOL" => "Solar",
	"HT" => "Historical",
	"PC" => "Pollution Control",
	"AB" => "Abatement",
	"EXPS" => "Total Exempt",
	"CDV" => "Charity Donated DV Homestead",
	"DVTD" => "Disabled Veteran Deferral",
	"OA" => "Over 65",
	"WS1" => "Rainwater",
	"COLOHO" => "Low Income Housing 50%",
	"PRO" => "Prorated Exempt",
	"WSA" => "Waiver of Special Appraisal",
	"HSTD" => "Homestead Deferral",
	"DPTD" => "Disabled Person Deferral",
	"O65TD" => "Over65 Deferral",
	"OAS" => "Over 65",
	"TIM" => "Ag Use",
	"ENG" => "Energy",
	"CHAR" => "Charitable"
}

# Access the meaning of a code
# puts code_meaning["HS"]  # Output: Hms

# Loop through all codes and meanings
# code_meaning.each do |code, meaning|
  # puts "#{code}: #{meaning}"
# end

# define the headers as an array
headers = ["Id","Batch Id","State","County","Run Date","Cad Property Id","Cad Geoid","Tax Year","Situs Address","Situs Number","Situs Predir","Situs Name","Situs Suffix","Situs Postdir","Situs Unit","Situs City","Situs State","Situs Zip","Legal Description","Taxing Unit List","Appraised","Assessed","Land","Improvement","Agricultural","Last Ag Value","Subdivision","Acreage","Deed Reference","Cap Loss","Ownership","Exemptions","Parcel Type","Tax Assessor Id","Cross Tax Id","Owner Name","Owner Dba Name","Mail Address1","Mail Address2","Mail City","Mail State","Mail Zip","Ag Cleared","Parcel Comments","Tax Suit Indicator","Tax Suit Fees/Comments","Future Review","Linked Parcels","Parcel Id - Same Record - Build History","Ownership Code","Created At","Updated At"]

id = owner_dba_name = lastagvalue = ag_cleared = parcel_comments = tax_suit_indicator = tax_suit_fees_comments = future_review = linked_parcels = parcel_id_build_history = ownership_code = created_at = updated_at = tax_assessor_id = cross_tax_id = ""
batch_id = 2
rundate = "05/26/2023"


# open a new CSV file in write mode
CSV.open("#{county}_1.csv", 'w') do |csv|
  # write the headers as the first row
  csv << headers
end

CSV.open("#{county}_1.csv","a+") do |csv|


	con.query("SELECT * FROM `property`").each do |row|
		cad_propertyid = row['QuickRefID']
		parcel_type = cad_propertyid[0,1]
		# ar << ini if not ar.include?(ini)
		# consider only R or M

		
				
		if(parcel_type == "R" or parcel_type == "M")
		
		deedreference = ""
		qu2 = "Select * from property_sales where `property_sales`.`QuickRefID` = '#{cad_propertyid}' "
		
		# puts qu2
			salesres = con.query(qu2)		
			salesres.each do |sa|
				# puts "here"
				da = sa['DeedDate']
				instNm = sa['InstrumentNumber'] 
				book = sa['Book'] 
				pg = sa['Page'] 
				deedreference = da+"/"+instNm+"/"+book+"/"+pg
				deedreference = deedreference.gsub("///","").gsub("//","")
				break
			end	
			
			cad_geoid = row['PropertyNumber']
			subdivision = row['LegalLocationCode']
			# combine SitusStreetNumber + SitusPreDirectional + SitusStreetName + SitusStreetSuffix + SitusPostDirectional + SitusCity
			

			situsaddress = situsnumber = situspredir = situsname = situssuffix = situspostdir = situscity = situszip = situsunit = ""
			situsnumber = row['SitusStreetNumber']
			situspredir = row['SitusPreDirectional']
			situsname = row['SitusStreetName']
			situssuffix = row['SitusStreetSuffix']
			situszip = row['SitusZip']
			situspostdir = row['SitusPostDirectional']
			
			
			if situssuffix != ""
				situsname = situsname +" "+situssuffix
			end
			
			
			if situspostdir != ""
				situsname = situsname +" "+situspostdir
			end			
			situscity = row['SitusCity']
			
			if situspredir != ""
				situsname = situspredir+ " "+situsname
			end


			
			legal_description = row['LegalDesc'] 
			acreage = row['LegalAcres'] 
			taxingunitlist = row['TaxingUnitList'] 
			agricultural = row['CurrAgValue'] 
			land = row['CurrLandValue'] 
			improvement = row['CurrImprovmentValue'] 
			appraised = row['CurrMarketValue'] 
			assessed = row['CurrAssessedValue'] 
			situsstate = "Texas"
			
			situsaddress = situsnumber+" "+situsname
			
			qu1 = "Select * from owner where `Owner`.`QuickRefID` = '#{cad_propertyid}' "
			ownres = con.query(qu1)
			ownres.each do |ow|
				owner_name = ow['OwnerName']
				exelist = ow['ExemptionList']
				exemptions = ""
				
				if exelist != nil
					ecount = 1
					exelist.split(",").each do |e|					
						
						if ecount == 1
							exemptions = code_meaning[e.strip]
						else
							exemptions = exemptions+", "+code_meaning[e.strip]
						end
						ecount += 1
						
					end
				end
				mail_address1 = mail_add3 = mail_address2 = ""
				ownership = ow['OwnershipPercent']
				mail_address1 = ow['Address1']
				mail_address2 = ow['Address2']
				mail_add3 = ow['Address3']
				if mail_add3 != nil and mail_address2 == ""
					mail_address2 = mail_add3.strip
				elsif mail_add3 != nil and mail_address2 != nil
					mail_address2 = mail_address2.strip+", "+mail_add3.strip
				end
				
				mail_city = ow['City'] 
				mail_state = ow['State']
				mail_zip = ow['Zip']
				
				caploss = ow['CurrHSCapAdj']
				# select * from property_sales where QuickRefID = 


				
 
				csv << [id, batch_id, state, county, rundate, cad_propertyid, cad_geoid, taxyear, situsaddress, "", "", "", "", "", situsunit, situscity, situsstate, situszip, legal_description, taxingunitlist, appraised, assessed, land, improvement, agricultural, lastagvalue, subdivision, acreage, deedreference, caploss, ownership, exemptions, parcel_type, tax_assessor_id, cross_tax_id, owner_name, owner_dba_name, mail_address1, mail_address2, mail_city, mail_state, mail_zip, ag_cleared, parcel_comments, tax_suit_indicator, tax_suit_fees_comments, future_review, linked_parcels, parcel_id_build_history, ownership_code, created_at, updated_at]
				break
			end
						
				puts cn
				cn += 1
		
		end
		
	end




end


# ar.each do |a|

	# puts a
# end