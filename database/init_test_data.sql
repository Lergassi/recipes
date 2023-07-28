insert into qualities (name, alias, sort) values ('Common', 'common', 500);         # белый
insert into qualities (name, alias, sort) values ('Uncommon', 'uncommon', 510);     # зеленый
insert into qualities (name, alias, sort) values ('Rare', 'rare', 520);             # синий
insert into qualities (name, alias, sort) values ('Epic', 'epic', 530);             # фиолетовый
insert into qualities (name, alias, sort) values ('Legendary', 'legendary', 540);   # оранжевый

set @common_id = (select id from qualities a where a.alias = 'common');
set @uncommon_id = (select id from qualities a where a.alias = 'uncommon');
set @rare_id = (select id from qualities a where a.alias = 'rare');
set @epic_id = (select id from qualities a where a.alias = 'epic');
set @legendary_id = (select id from qualities a where a.alias = 'legendary');

insert into reference_products (name, alias, sort) values ('Рис', 'rice', 300);
insert into reference_products (name, alias, sort) values ('Вода', 'water', 100);
insert into reference_products (name, alias, sort) values ('Курица', 'chicken', 300);
insert into reference_products (name, alias, sort) values ('Свинина', 'pork', 300);
insert into reference_products (name, alias, sort) values ('Говядина', 'beef', 300);
insert into reference_products (name, alias, sort) values ('Морковь', 'carrot', 300);
insert into reference_products (name, alias, sort) values ('Лук репчатый', 'onion', 300);
insert into reference_products (name, alias, sort) values ('Соль', 'salt', 400);
insert into reference_products (name, alias, sort) values ('Черный перец', 'black_pepper', 400);
insert into reference_products (name, alias, sort) values ('Чеснок', 'garlic', 500);
insert into reference_products (name, alias, sort) values ('Сушёный чеснок', 'garlic_powder', 1000);
insert into reference_products (name, alias, sort) values ('Зира', 'cumin', 1000);
insert into reference_products (name, alias, sort) values ('Кориандр', 'coriander', 1000);

insert into dishes (name, alias, quality_id) values ('Плов', 'pilaf', @rare_id);
insert into dishes (name, alias, quality_id) values ('Котлеты', 'russian_cutlets', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Харчо', 'kharcho', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Щи', 'shchi', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Лазанья', 'lasagna', @rare_id);

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