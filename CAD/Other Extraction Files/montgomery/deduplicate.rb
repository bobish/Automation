require "csv"

CSV.open("montExeRes.csv","a+") do |csv|
	
	CSV.foreach("montexe.csv") do |it|
		if it[0] != nil 
			it[0].split(",").each do |y|
			
				csv << [y.strip]

			end
		end
		
	end




end