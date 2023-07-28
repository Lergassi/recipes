create table test_categories
(
    id int unsigned,
    name text,
    primary key (id)
);

create table test_products
(
    id int unsigned,
    category_id int unsigned,
    primary key (id),
    foreign key (category_id) references test_categories (id)
);

insert into test_categories (id, name) values (1, 'this is 1');
insert into test_categories (id, name) values (2, 'this is 2');
insert into test_categories (id) values (3);

insert into test_products (id, category_id) values (10, 1);
insert into test_products (id, category_id) values (20, 1);
insert into test_products (id, category_id) values (30, 1);
insert into test_products (id, category_id) values (40, 2);
insert into test_products (id, category_id) values (50, 2);
insert into test_products (id, category_id) values (60, 2);

select tp.*, tc.name from test_products tp left join test_categories tc on tp.category_id = tc.id;

# delete from test_products left join

delete from recipe_positions d_rp where d_rp.reference_product_id = 2 and (select count(*) as count from recipe_positions rp left join recipes r on rp.recipe_id = r.id where r.is_main = 1) = 1;
delete d_rp from recipe_positions d_rp where d_rp.id = (select rp.id from recipe_positions rp left join recipes r on rp.recipe_id = r.id where r.is_main = 1);
delete d_rp from recipe_positions d_rp where d_rp.id = 7;
select rp.*, r.id from recipe_positions rp left join recipes r on rp.recipe_id = r.id where r.is_main = 0;
select rp.id from recipe_positions rp left join recipes r on rp.recipe_id = r.id where r.is_main = 0;
select count(*) as count from recipe_positions rp left join recipes r on rp.recipe_id = r.id where r.is_main = 1;