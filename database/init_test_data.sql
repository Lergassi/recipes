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

insert into reference_products (name, alias) values ('Рис', 'rice');
insert into reference_products (name, alias) values ('Вода', 'water');
insert into reference_products (name, alias) values ('Курица', 'chicken');
insert into reference_products (name, alias) values ('Свинина', 'pork');
insert into reference_products (name, alias) values ('Говядина', 'beef');
insert into reference_products (name, alias) values ('Морковь', 'carrot');
insert into reference_products (name, alias) values ('Лук репчатый', 'onion');
insert into reference_products (name, alias) values ('Соль', 'salt');
insert into reference_products (name, alias) values ('Черный перец', 'black_pepper');
insert into reference_products (name, alias) values ('Чеснок', 'garlic');
insert into reference_products (name, alias) values ('Сушёный чеснок', 'garlic_powder');
insert into reference_products (name, alias) values ('Зира', 'cumin');
insert into reference_products (name, alias) values ('Кориандр', 'coriander');

insert into dishes (name, alias, quality_id) values ('Плов', 'pilaf', @rare_id);
insert into dishes (name, alias, quality_id) values ('Котлеты', 'russian_cutlets', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Харчо', 'kharcho', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Щи', 'shchi', @uncommon_id);
insert into dishes (name, alias, quality_id) values ('Лазанья', 'lasagna', @rare_id);

insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Борщ', 'plov_borshch_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Лазерсон', 'plov_laserson_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Обломов', 'plov_oblomof_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);
insert into dish_versions (name, alias, dish_id, quality_id) values ('Плов, Ивлев', 'plov_ivlev_01', (select id from dishes d where d.alias = 'pilaf'), @uncommon_id);