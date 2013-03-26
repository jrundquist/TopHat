Model

	has_many
	belongs_to

	validates_uniqueness_of [property]=>[scope]



	-----------------------------------

	new
	where
	find
	read_attribute
	write_attribute
	find_by_[...and...]
	find_or_create_by_[...and...]
	find_or_initialize_by_[...and...] <= does not save after
	columns
	attributes
	count_by_sql
	create([])
	find_by_sql <= returns parameters of the query as parameters in the object