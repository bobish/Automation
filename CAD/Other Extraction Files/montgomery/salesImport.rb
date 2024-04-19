require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "montgomery")

cnt = 1
CSV.foreach('PropertyDataExport2182749.txt', headers: true, col_sep: ",") do |row|
	
	puts cnt
	record_type = row["RecordType"]
	property_id = row["PropertyID"]
	quick_ref_id = row["QuickRefID"]
	property_number = row["PropertyNumber"]
	sale_date = row["SaleDate"]
	deed_date = row["DeedDate"]
	instrument_number = row["InstrumentNumber"]
	book = row["Book"]
	page = row["Page"]
	prev_owner_name = row["PrevOwnerName"]
	instrument_type = row["InstrumentType"]
	other_ref = row["OtherRef"]
	deed_type = row["DeedType"]


sql = "INSERT INTO `property_sales`(`RecordType`, `PropertyID`, `QuickRefID`, `PropertyNumber`, `SaleDate`, `DeedDate`, `InstrumentNumber`, `Book`, `Page`, `PrevOwnerName`, `InstrumentType`, `OtherRef`, `DeedType`) VALUES ('#{con.escape(record_type)}', '#{con.escape(property_id)}', '#{con.escape(quick_ref_id)}', '#{con.escape(property_number)}', '#{con.escape(sale_date)}', '#{con.escape(deed_date)}', '#{con.escape(instrument_number)}', '#{con.escape(book)}', '#{con.escape(page)}', '#{con.escape(prev_owner_name)}', '#{con.escape(instrument_type)}', '#{con.escape(other_ref)}', '#{con.escape(deed_type)}'
)"

con.query(sql)
  
  cnt += 1
end

# Close the MySQL connection
con.close
