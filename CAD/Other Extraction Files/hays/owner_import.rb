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
CSV.foreach('PropertyDataExport982291.txt', headers: true, col_sep: ",") do |row|
	puts cnt
  # Extract values from CSV row
record_type = row["RecordType"],
property_id = row["PropertyID"],
quick_ref_id = row["QuickRefID"],
property_number = row["PropertyNumber"],
owner_id = row["OwnerID"],
owner_quick_ref_id = row["OwnerQuickRefID"],
owner_property_number = row["OwnerPropertyNumber"],
owner_name = row["OwnerName"],
address1 = row["Address1"],
address2 = row["Address2"],
address3 = row["Address3"],
city = row["City"],
state = row["State"],
zip_code = row["Zip"],
ownership_percent = row["OwnershipPercent"],
confidential_owner = row["ConfidentialOwner"],
exemption_list = row["ExemptionList"],
hs_cap_adj = row["HSCapAdj"],
curr_hs_cap_adj = row["CurrHSCapAdj"]
  
  # and so on for all the columns

  # Prepare the SQL statement to insert the row into the table
  statement = con.prepare("
    INSERT INTO owner (
	RecordType,
	PropertyID,
	QuickRefID,
	PropertyNumber,
	OwnerID,
	OwnerQuickRefID,
	OwnerPropertyNumber,
	OwnerName,
	Address1,
	Address2,
	Address3,
	City,
	State,
	Zip,
	OwnershipPercent,
	ConfidentialOwner,
	ExemptionList,
	HSCapAdj,
	CurrHSCapAdj
    ) VALUES (
      ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
    )
  ")

  # Execute the SQL statement with the row values
  statement.execute(
   record_type,
property_id,
quick_ref_id,
property_number,
owner_id,
owner_quick_ref_id,
owner_property_number,
owner_name,
address1,
address2,
address3,
city,
state,
zip_code,
ownership_percent,
confidential_owner,
exemption_list,
hs_cap_adj,
curr_hs_cap_adj
    # and so on for all the columns
  )
  cnt += 1
end

# Close the MySQL connection
con.close
