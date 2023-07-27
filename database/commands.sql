# wipe
set foreign_key_checks = 0;
truncate table dish_versions;
truncate table dishes;
truncate table reference_products;
truncate table qualities;
set foreign_key_checks = 1;