require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "hays")

# res = con.query("SELECT id, year, RecNum,Grantor, Grantee FROM `garfield` limit 3")


# Connect to MySQL database
# client = Mysql2::Client.new(
  # host: 'localhost',
  # username: 'your_username',
  # password: 'your_password',
  # database: 'PropertyRecords'
# )

# Open the CSV file and iterate over each row

cnt = 1
CSV.foreach('PropertyDataExport982290.txt', headers: true) do |row|
	puts cnt
  # Extract values from CSV row
  record_type = row['RecordType']
  property_id = row['PropertyID']
  quick_ref_id = row['QuickRefID']
  prop_num = row['PropertyNumber']
	legal_desc = row['LegalDesc']
	legal_location_code = row['LegalLocationCode']
	legal_location_desc = row['LegalLocationDesc']
	legal_acres = row['LegalAcres']
	abstract_block = row['AbstractBlock']
	sub_block = row['SubBlock']
	sub_lot = row['SubLot']
	sub_lot_range = row['SubLotRange']
	sub_section = row['SubSection']
	sub_unit = row['SubUnit']
	taxing_unit_list = row['TaxingUnitList']
	lease_number = row['LeaseNumber']
	map_number = row['MapNumber']
	curr_market_value = row['CurrMarketValue']
	curr_assessed_value = row['CurrAssessedValue']
	curr_land_value = row['CurrLandValue']
	curr_improvment_value = row['CurrImprovmentValue']
	curr_ag_value = row['CurrAgValue']
	market_value = row['MarketValue']
	assessed_value = row['AssessedValue']
	land_value = row['LandValue']
	improvment_value = row['ImprovmentValue']
	ag_value = row['AgValue']
	square_footage = row['SquareFootage']
	nbhd_code = row['NbhdCode']
	nbhd_desc = row['NbhdDesc']
	situs = row['Situs']
	situs_pre_directional = row['SitusPreDirectional']
	situs_street_number = row['SitusStreetNumber']
	situs_street_name = row['SitusStreetName']
	situs_street_suffix = row['SitusStreetSuffix']
	situs_post_directional = row['SitusPostDirectional']
	situs_city = row['SitusCity']
	situs_state = row['SitusState']
	situs_zip = row['SitusZip']
	situs_location = row['SitusLocation']
  
  # and so on for all the columns

  # Prepare the SQL statement to insert the row into the table
  statement = con.prepare("
    INSERT INTO Property (
      RecordType,
      PropertyID,
      QuickRefID,
      PropertyNumber,
      LegalDesc,
      LegalLocationCode,
      LegalLocationDesc,
      LegalAcres,
      AbstractBlock,
      SubBlock,
      SubLot,
      SubLotRange,
      SubSection,
      SubUnit,
      TaxingUnitList,
      LeaseNumber,
      MapNumber,
      CurrMarketValue,
      CurrAssessedValue,
      CurrLandValue,
      CurrImprovmentValue,
      CurrAgValue,
      MarketValue,
      AssessedValue,
      LandValue,
      ImprovmentValue,
      AgValue,
      SquareFootage,
      NbhdCode,
      NbhdDesc,
      Situs,
      SitusPreDirectional,
      SitusStreetNumber,
      SitusStreetName,
      SitusStreetSuffix,
      SitusPostDirectional,
      SitusCity,
      SitusState,
      SitusZip,
      SitusLocation
    ) VALUES (
      ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
    )
  ")

  # Execute the SQL statement with the row values
  statement.execute(
    record_type,
    property_id,
    quick_ref_id,
	prop_num,
	legal_desc,
	legal_location_code,
	legal_location_desc,
	legal_acres,
	abstract_block,
	sub_block,
	sub_lot,
	sub_lot_range,
	sub_section,
	sub_unit,
	taxing_unit_list,
	lease_number,
	map_number,
	curr_market_value,
	curr_assessed_value,
	curr_land_value,
	curr_improvment_value,
	curr_ag_value,
	market_value,
	assessed_value,
	land_value,
	improvment_value,
	ag_value,
	square_footage,
	nbhd_code,
	nbhd_desc,
	situs,
	situs_pre_directional,
	situs_street_number,
	situs_street_name,
	situs_street_suffix,
	situs_post_directional,
	situs_city,
	situs_state,
	situs_zip,
	situs_location	
    # and so on for all the columns
  )
  cnt += 1
end

# Close the MySQL connection
con.close
