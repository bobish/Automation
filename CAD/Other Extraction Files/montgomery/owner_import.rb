require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "montgomery")

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
CSV.foreach('PropertyDataExport2182745.txt', headers: true, col_sep: ",") do |row|
	
		puts cnt
		# puts row["RecordType"]
	  # Extract values from CSV row
record_type = row["RecordType"]
property_id = row["PropertyID"]
quick_ref_id = row["QuickRefID"]
property_number = row["PropertyNumber"]
owner_id = row["OwnerID"]
owner_quick_ref_id = row["OwnerQuickRefID"]
owner_property_number = row["OwnerPropertyNumber"]
owner_name = row["OwnerName"]
address1 = row["Address1"]
address2 = row["Address2"]
address3 = row["Address3"]
city = row["City"]
state = row["State"]
zip_code = row["Zip"]
ownership_percent = row["OwnershipPercent"]
confidential_owner = row["ConfidentialOwner"]
exemption_list = row["ExemptionList"]
hs_cap_adj = row["HSCapAdj"]
curr_hs_cap_adj = row["CurrHSCapAdj"]

sql = "INSERT INTO `owner`(`RecordType`, `PropertyID`, `QuickRefID`, `PropertyNumber`, `OwnerID`, `OwnerQuickRefID`, `OwnerPropertyNumber`, `OwnerName`, `Address1`, `Address2`, `Address3`, `City`, `State`, `Zip`, `OwnershipPercent`, `ConfidentialOwner`, `ExemptionList`, `HSCapAdj`, `CurrHSCapAdj`) VALUES ('#{con.escape(record_type)}', '#{con.escape(property_id)}', '#{con.escape(quick_ref_id)}', '#{con.escape(property_number)}', '#{con.escape(owner_id)}', '#{con.escape(owner_quick_ref_id)}', '#{con.escape(owner_property_number)}', '#{con.escape(owner_name)}', '#{con.escape(address1)}', '#{con.escape(address2)}', '#{con.escape(address3)}', '#{con.escape(city)}', '#{con.escape(state)}', '#{con.escape(zip_code)}', #{con.escape(ownership_percent)}, '#{con.escape(confidential_owner)}', '#{con.escape(exemption_list)}', '#{con.escape(hs_cap_adj)}', '#{con.escape(curr_hs_cap_adj)}')"

con.query(sql)
  
  cnt += 1
end

# Close the MySQL connection
con.close
