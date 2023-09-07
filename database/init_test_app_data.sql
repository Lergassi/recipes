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

insert into reference_products (name, alias, sort) values ('Вода', 'water', 100);
insert into reference_products (name, alias, sort) values ('Рис', 'rice', 300);
insert into reference_products (name, alias, sort) values ('Макароны', 'pasta', 300);
insert into reference_products (name, alias, sort) values ('Гречка', 'buckwheat', 300);
insert into reference_products (name, alias, sort) values ('Булгур', 'bulgur', 300);
insert into reference_products (name, alias, sort) values ('Курица', 'chicken', 300);
insert into reference_products (name, alias, sort) values ('Свинина', 'pork', 300);
insert into reference_products (name, alias, sort) values ('Говядина', 'beef', 300);
insert into reference_products (name, alias, sort) values ('Баранина', 'mutton', 300);
insert into reference_products (name, alias, sort) values ('Морковь', 'carrot', 300);
insert into reference_products (name, alias, sort) values ('Лук репчатый', 'onion', 300);
insert into reference_products (name, alias, sort) values ('Чеснок', 'garlic', 500);
insert into reference_products (name, alias, sort) values ('Мука', 'flour', 500);
insert into reference_products (name, alias, sort) values ('Яйцо', 'egg', 500);
insert into reference_products (name, alias, sort) values ('Масло растительное', 'oil', 500);
insert into reference_products (name, alias, sort) values ('Оливковое масло', 'olive_oil', 500);
insert into reference_products (name, alias, sort) values ('Сахар', 'sugar', 500);
insert into reference_products (name, alias, sort) values ('Хлеб', 'bread', 500);
insert into reference_products (name, alias, sort) values ('Лазанья', 'lasagna', 500);
insert into reference_products (name, alias, sort) values ('Сельдерей', 'celery', 500);
insert into reference_products (name, alias, sort) values ('Молоко', 'milk', 500);
insert into reference_products (name, alias, sort) values ('Томатная паста', 'tomato_paste', 500);
insert into reference_products (name, alias, sort) values ('Масло сливочное', 'butter', 500);
insert into reference_products (name, alias, sort) values ('Творог', 'tvorog', 500);
insert into reference_products (name, alias, sort) values ('Крупа манная', 'semolina', 500);
insert into reference_products (name, alias, sort) values ('Вино', 'wine', 500);
insert into reference_products (name, alias, sort) values ('Томаты СС', 'ss_tomatoes', 500);
insert into reference_products (name, alias, sort) values ('Пармезан', 'parmigiano_reggiano', 500);
insert into reference_products (name, alias, sort) values ('Сыр', 'cheese', 500);

insert into reference_products (name, alias, sort) values ('Соль', 'salt', 900);
insert into reference_products (name, alias, sort) values ('Укроп', 'dill', 950);
insert into reference_products (name, alias, sort) values ('Аджика', 'ajika', 950);
insert into reference_products (name, alias, sort) values ('Перец черный', 'black_pepper', 1000);
insert into reference_products (name, alias, sort) values ('Перец красный', 'chili_pepper', 1000);
insert into reference_products (name, alias, sort) values ('Чеснок сушёный', 'garlic_powder', 1000);
insert into reference_products (name, alias, sort) values ('Зира', 'cumin', 1000);
insert into reference_products (name, alias, sort) values ('Розмарин', 'rosemary', 1000);
insert into reference_products (name, alias, sort) values ('Тимьян', 'thyme', 1000);
insert into reference_products (name, alias, sort) values ('Базилик', 'basil', 1000);
insert into reference_products (name, alias, sort) values ('Хмели-сунели', 'khmeli_suneli', 1000);
insert into reference_products (name, alias, sort) values ('Уцхо-сунели', 'utskho_suneli', 1000);
insert into reference_products (name, alias, sort) values ('Кориандр', 'coriander', 1000);
insert into reference_products (name, alias, sort) values ('Мускатный орех', 'nutmeg', 1000);