# wipe
set foreign_key_checks = 0;
truncate table recipe_positions;
truncate table recipes;
truncate table dish_version_branches;
truncate table dish_versions;
truncate table dishes;
truncate table reference_products;
truncate table qualities;
set foreign_key_checks = 1;

# wipe schema
set foreign_key_checks = 0;
drop table recipe_positions;
drop table recipes;
drop table dish_version_branches;
drop table dish_versions;
drop table dishes;
drop table reference_products;
drop table qualities;
set foreign_key_checks = 1;