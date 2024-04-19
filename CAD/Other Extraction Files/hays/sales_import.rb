require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "hays")

ct = 1
# Open CSV file and iterate over each row
CSV.foreach('PropertyDataExport982297.txt', headers: true) do |row|
  # Insert row data into MySQL table
  puts ct
  ct += 1
  con.query("INSERT INTO property_sales (RecordType, PropertyID, QuickRefID, PropertyNumber, SaleDate, DeedDate, InstrumentNumber, Book, Page, PrevOwnerName, InstrumentType, OtherRef, DeedType) VALUES ('#{row["RecordType"]}', '#{row["PropertyID"]}', '#{row["QuickRefID"]}', '#{row["PropertyNumber"]}', '#{row["SaleDate"]}', '#{row["DeedDate"]}', '#{row["InstrumentNumber"]}', '#{row["Book"].gsub("'","")}', '#{row["Page"].gsub("'","")}', '#{con.escape(row["PrevOwnerName"])}', '#{con.escape(row["InstrumentType"])}', '#{con.escape(row["OtherRef"])}', '#{con.escape(row["DeedType"])}')")
end

# Close database connection
con.close
