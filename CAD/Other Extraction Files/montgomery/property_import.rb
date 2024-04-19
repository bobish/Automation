require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "montgomery")

cnt = 1
CSV.foreach('PropertyDataExport2182743.csv', headers: true, col_sep: ",") do |row|
	
	puts cnt
		record_type = row["RecordType"]
		property_id = row["PropertyID"]
		quick_ref_id = row["QuickRefID"]
		property_number = row["PropertyNumber"]
		legal_desc = row["LegalDesc"]
		legal_location_code = row["LegalLocationCode"]
		legal_location_desc = row["LegalLocationDesc"]
		legal_acres = row["LegalAcres"]
		abstract_block = row["AbstractBlock"]
		sub_block = row["SubBlock"]
		sub_lot = row["SubLot"]
		sub_lot_range = row["SubLotRange"]
		sub_section = row["SubSection"]
		sub_unit = row["SubUnit"]
		taxing_unit_list = row["TaxingUnitList"]
		lease_number = row["LeaseNumber"]
		map_number = row["MapNumber"]
		curr_market_value = row["CurrMarketValue"]
		curr_assessed_value = row["CurrAssessedValue"]
		curr_land_value = row["CurrLandValue"]
		curr_improvement_value = row["CurrImprovmentValue"]
		curr_ag_value = row["CurrAgValue"]
		market_value = row["MarketValue"]
		assessed_value = row["AssessedValue"]
		land_value = row["LandValue"]
		improvement_value = row["ImprovmentValue"]
		ag_value = row["AgValue"]
		square_footage = row["SquareFootage"]
		nbhd_code = row["NbhdCode"]
		nbhd_desc = row["NbhdDesc"]
		situs = row["Situs"]
		situs_pre_directional = row["SitusPreDirectional"]
		situs_street_number = row["SitusStreetNumber"]
		situs_street_name = row["SitusStreetName"]
		situs_street_suffix = row["SitusStreetSuffix"]
		situs_post_directional = row["SitusPostDirectional"]
		situs_city = row["SitusCity"]
		situs_state = row["SitusState"]
		situs_zip = row["SitusZip"]
		situs_location = row["SitusLocation"]


sql = "INSERT INTO `property`(`RecordType`, `PropertyID`, `QuickRefID`, `PropertyNumber`, `LegalDesc`, `LegalLocationCode`, `LegalLocationDesc`, `LegalAcres`, `AbstractBlock`, `SubBlock`, `SubLot`, `SubLotRange`, `SubSection`, `SubUnit`, `TaxingUnitList`, `LeaseNumber`, `MapNumber`, `CurrMarketValue`, `CurrAssessedValue`, `CurrLandValue`, `CurrImprovmentValue`, `CurrAgValue`, `MarketValue`, `AssessedValue`, `LandValue`, `ImprovmentValue`, `AgValue`, `SquareFootage`, `NbhdCode`, `NbhdDesc`, `Situs`, `SitusPreDirectional`, `SitusStreetNumber`, `SitusStreetName`, `SitusStreetSuffix`, `SitusPostDirectional`, `SitusCity`, `SitusState`, `SitusZip`, `SitusLocation`) VALUES ('#{con.escape(record_type)}', #{con.escape(property_id)}, '#{con.escape(quick_ref_id)}', '#{con.escape(property_number)}', '#{con.escape(legal_desc)}', '#{con.escape(legal_location_code)}', '#{con.escape(legal_location_desc)}', '#{con.escape(legal_acres)}', '#{con.escape(abstract_block)}', '#{con.escape(sub_block)}', '#{con.escape(sub_lot)}', '#{con.escape(sub_lot_range)}', '#{con.escape(sub_section)}', '#{con.escape(sub_unit)}', '#{con.escape(taxing_unit_list)}', '#{con.escape(lease_number)}', '#{con.escape(map_number)}', '#{con.escape(curr_market_value)}', '#{con.escape(curr_assessed_value)}', '#{con.escape(curr_land_value)}', '#{con.escape(curr_improvement_value)}', '#{con.escape(curr_ag_value)}', '#{con.escape(market_value)}', '#{con.escape(assessed_value)}', '#{con.escape(land_value)}', '#{con.escape(improvement_value)}', '#{con.escape(ag_value)}', '#{con.escape(square_footage)}', '#{con.escape(nbhd_code)}', '#{con.escape(nbhd_desc)}', '#{con.escape(situs)}', '#{con.escape(situs_pre_directional)}', '#{con.escape(situs_street_number)}', '#{con.escape(situs_street_name)}', '#{con.escape(situs_street_suffix)}', '#{con.escape(situs_post_directional)}', '#{con.escape(situs_city)}', '#{con.escape(situs_state)}', '#{con.escape(situs_zip)}', '#{con.escape(situs_location)}'
)"


con.query(sql)
  
  cnt += 1
end

# Close the MySQL connection
con.close
