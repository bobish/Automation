require 'csv'
require 'mysql2'



con = Mysql2::Client.new(:host => "localhost", :username => "root", :password => "kmsandkms", :database => "fortbend")

cnt = 1
CSV.foreach('2022_07_26_1356_ExemptionExport.txt', headers: true, col_sep: "\t") do |row|
	
	puts cnt if cnt % 10000 == 1
		recty = row["RecordType"]
		qrf = row["OwnerQuickRefID"]
		execode = row["ExemptionCode"]
		if execode != nil
			execode = con.escape(execode)
		end

sql = "INSERT INTO `exemptions`(`Id`, `RecordType`, `OwnerQuickRefID`, `ExemptionCode`) VALUES (0,'#{con.escape(recty)}','#{con.escape(qrf)}','#{execode}')"


con.query(sql)
  
  cnt += 1
end

# Close the MySQL connection
con.close
