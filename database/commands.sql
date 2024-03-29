# wipe
set foreign_key_checks = 0;
truncate table heads;
truncate table recipe_commit_positions;
truncate table recipe_commits;
truncate table recipe_positions;
truncate table recipes;
truncate table dish_versions;
truncate table dishes;
truncate table reference_products;
truncate table qualities;
set foreign_key_checks = 1;

# user data
set foreign_key_checks = 0;
truncate table heads;
truncate table recipe_commit_positions;
truncate table recipe_commits;
truncate table recipe_positions;
truncate table recipes;
truncate table dish_versions;
truncate table dishes;
set foreign_key_checks = 1;

# drop schema
set foreign_key_checks = 0;
drop table heads;
drop table recipe_commit_positions;
drop table recipe_commits;
drop table recipe_positions;
drop table recipes;
drop table dish_versions;
drop table dishes;
drop table reference_products;
drop table qualities;
drop table users_has_groups;
drop table user_groups;
drop table users;
set foreign_key_checks = 1;