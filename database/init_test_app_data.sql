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