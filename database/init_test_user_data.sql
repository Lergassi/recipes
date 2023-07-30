# Запустить init_test_app_data.sql

insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов обычный', 'plov_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Борщ', 'plov_borshch_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Лазерсон', 'plov_laserson_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Обломов', 'plov_oblomof_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Ивлев', 'plov_ivlev_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);

insert into dish_version_branches (name, description, dish_version_id) VALUES ('main', null, (select id from dish_versions where alias = 'plov_01'));
select dvb.* from dish_version_branches dvb left join dish_versions dv on dvb.dish_version_id = dv.id where dv.alias = 'plov_01';
select
    *
from recipes r
    left join dish_version_branches dvb on r.dish_version_branch_id = dvb.id
    left join dish_versions dv on dvb.dish_version_id = dv.id
where dv.alias = 'plov_01'
;
insert into recipes (is_main, dish_version_branch_id) VALUES (1, (select dvb.id from dish_version_branches dvb left join dish_versions dv on dvb.dish_version_id = dv.id where dv.alias = 'plov_01'));

set @recipe_id = (select
    r.id
from recipes r
         left join dish_version_branches dvb on r.dish_version_branch_id = dvb.id
         left join dish_versions dv on dvb.dish_version_id = dv.id
where dv.alias = 'plov_01');
select @recipe_id;

insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (1000, 1, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (2000, 2, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (1000, 3, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (1000, 6, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (200, 7, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (30, 8, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (1, 9, @recipe_id);
insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (30, 10, @recipe_id);