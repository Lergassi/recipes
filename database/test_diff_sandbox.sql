create table test_users
(
    id int,
    primary key (id)
);

create table test_products
(
    id int,
    name text,
    primary key (id)
);

create table test_carts
(
    id int,
    user_id int not null,
    primary key (id),
    foreign key (user_id) references test_users (id)
);

create table test_cart_products
(
    id int,
    value int not null,
    cart_id int not null,
    product_id int not null,
    primary key (id),
    foreign key (cart_id) references test_carts (id),
    foreign key (product_id) references test_products (id)
);

create table test_features
(
    id int,
    product_id int,
    user_id int,
    primary key (id),
    foreign key (product_id) references test_products (id),
    foreign key (user_id) references test_users (id)
);

insert into test_users (id) values (1);
insert into test_users (id) values (2);

insert into test_products (id, name) values (1, 'вода');
insert into test_products (id, name) values (2, 'соль');
insert into test_products (id, name) values (3, 'рис');
insert into test_products (id, name) values (4, 'курица');
insert into test_products (id, name) values (5, 'зира');
insert into test_products (id, name) values (6, 'перец');

insert into test_carts (id, user_id) values (1, 1);
insert into test_carts (id, user_id) values (2, 2);

insert into test_cart_products (id, value, cart_id, product_id) values (101, 100, 1, 1);
insert into test_cart_products (id, value, cart_id, product_id) values (102, 200, 1, 2);
insert into test_cart_products (id, value, cart_id, product_id) values (103, 300, 1, 3);

insert into test_cart_products (id, value, cart_id, product_id) values (104, 100, 2, 4);

insert into test_features (id, product_id, user_id) values (1, 1, 1);
insert into test_features (id, product_id, user_id) values (2, 4, 1);

insert into test_features (id, product_id, user_id) values (3, 3, 2);
insert into test_features (id, product_id, user_id) values (4, 5, 2);

select * from test_carts;
select * from test_cart_products;
select * from test_features;
select f.*, tp.name, tp.id from test_features f left join test_products tp on tp.id = f.product_id;

select
    cp.*
     ,p.name
from test_cart_products cp
    left join test_products p on p.id = cp.product_id
#     right join test_products p on p.id = cp.product_id
where
    cp.cart_id = 1
;

select
    f.*
     ,p.name
from test_features f
    left join test_products p on p.id = f.product_id
#     right join test_products p on p.id = f.product_id
where
    f.user_id = 1
;

# --------
# carts:
# 1(1): вода 1, соль 2, рис 3   -   нету курица 4
# 2(2): курица 4
# features:
# 1:    вода 1, курица 4        -   нету соль 2
# 2:    рис 3, риза 5
select
    cp.*
#     ,p.name
#     ,f.product_id
#     ,f.product_id
    ,p.id
    ,f.product_id
    ,p.name
from test_cart_products cp
#     left join test_features f on f.product_id = cp.product_id
#     left join test_products p on p.id = cp.product_id
# ----
#     left join test_features f on f.product_id <> cp.product_id

    right join test_features f on f.product_id = cp.product_id
    left join test_products p on p.id = f.product_id

#     left join test_features f on f.product_id = cp.product_id
#     left join test_products p on p.id = cp.product_id
#     right join test_features r_f on r_f.product_id = cp.product_id
#     left join test_products p on p.id = f.product_id
# where
#     cp.cart_id = 1
;

select
    cp.*
     ,p.name
     ,p.id
from test_cart_products cp
    left join test_products p on cp.product_id = p.id
#     left join test_products p on cp.product_id <> p.id
where
    cp.cart_id = 1
;

# предварительный результат без условия по users
select
    p.id
     ,p.name
     ,cp.product_id
     ,cp.cart_id
     ,u.id
     ,c.id
     ,c.user_id
     ,f.product_id
     ,f.user_id
from test_products p
    left join test_cart_products cp on p.id = cp.product_id
    left join test_features f on p.id = f.product_id

    left join test_carts c on cp.cart_id = c.id
    left join test_users u on f.user_id = u.id  # тут u считается на основе f и поэтому соли и перца нету в выборке по u.id. Следовательно u.id = 1 и c.user_id = 1 сделать не получиться
where
    (
        cp.product_id is null
        or f.product_id is null
    )
    and (cp.product_id is not null or f.product_id is not null)
#     and u.id = 1
#     u.id = 1
;

select
    p.id
     ,p.name
     ,cp.product_id
     ,cp.cart_id
#      ,u.id
     ,c.id
     ,c.user_id
#      ,f.product_id
#      ,f.user_id
from test_products p
    left join test_cart_products cp on p.id = cp.product_id
#     left join test_features f on p.id = f.product_id
#
    left join test_carts c on cp.cart_id = c.id
#     left join test_features f on p.id = f.product_id
#     left join test_features f on c.user_id = f.user_id and p.id = f.product_id
    left join test_features f on c.user_id = f.user_id
    right join test_features r_f on c.user_id = r_f.user_id
#     left join test_users u on f.user_id = u.id
# where
#     (
#         cp.product_id is null
#         or f.product_id is null
#     )
#     and (cp.product_id is not null or f.product_id is not null)
#     and u.id = 1
#     cp.cart_id = 1 or
;

select
#     cp.*
    p.id
     ,p.name
     ,cp.product_id
     ,cp.cart_id
#      ,u.id
#      ,c.id
#      ,c.user_id
     ,f.product_id
     ,f.user_id
from test_users u
    left join test_carts c on u.id = c.user_id
    left join test_cart_products cp on c.id = cp.cart_id

    left join test_features f on u.id = f.user_id and f.product_id = cp.product_id
#     right join test_features r_f on u.id = r_f.user_id and r_f.product_id = cp.product_id
#     right join test_features r_f on u.id = r_f.user_id and r_f.product_id = cp.product_id
#     left join test_features f on u.id = f.user_id
#     right join test_features f on cp.product_id = f.product_id
#     right join cp on cp.product_id = f.product_id

    left join test_products p on cp.product_id = p.id
where
    u.id = 1
#     (
#         cp.product_id is null
#         or f.product_id is null
#     )
#     and (cp.product_id is not null or f.product_id is not null)
#     and u.id = 1
#     u.id = 1
#     c.user_id = 1 or f.user_id = 1
#     cp.cart_id = 1
;

# main начало с products
select
#     cp.*
    p.id
     ,p.name
     ,cp.product_id as cp_product_id
     ,cp.cart_id as cp_cart_id
# #      ,u.id
# #      ,c.id
# #      ,c.user_id
#      ,f.product_id
#      ,f.user_id
# #      ,r_f.product_id
# #      ,r_f.user_id
#      ,l_f.product_id
#      ,l_f.user_id
#     ,cp2.product_id
from test_products p
    left join test_cart_products cp on p.id = cp.product_id
    left join test_carts c on cp.cart_id = c.id

#     left join test_features f on p.id = f.product_id and f.user_id = c.user_id #!!!
#     left join test_features f on p.id = f.product_id
#     left join test_cart_products cp2 on f.product_id = cp2.product_id
# where
#     (c.user_id = 1 and f.user_id = 1) or r_f.user_id = 1
#     (c.user_id = 1 or f.user_id = 1)
#     (c.user_id = f.user_id)
#     c.user_id = 1
#     f.user_id = 1
#     (
#         cp.product_id is null
#         or f.product_id is null
#     )
#     and (cp.product_id is not null or f.product_id is not null)
#     and u.id = 1
#     u.id = 1
#     c.user_id = 1 or f.user_id = 1
#     cp.cart_id = 1
;

# начало со связей через right
select
    p.id
    ,p.name
    ,c.user_id
    ,cp.cart_id
    ,cp.product_id
    ,f.product_id
from test_cart_products cp
    right join test_products p on cp.product_id = p.id
    left join test_carts c on cp.cart_id = c.id
    left join test_features f on p.id = f.product_id and f.user_id = c.user_id
#     left join test_products p on cp.product_id = p.id

# начало с carts и users
select
    c.*
    ,cp.product_id
    ,p.name
    ,f.product_id
    ,p2.name
from test_users u
    left join test_carts c on u.id = c.user_id

    right join test_cart_products cp on c.id = cp.cart_id
    left join test_products p on p.id = cp.product_id
    right join test_features f on u.id = f.user_id

    left join test_products p2 on p2.id = f.product_id
where
    u.id = 1
;

# РАБОТАЕТ!!!
# Запрос разницы между двумя таблицами через union для тестовых данных.
# v.0.0.1
select
    p.*
#     ,cp.product_id
#     ,f.product_id
#     ,f.user_id
from test_cart_products cp
    left join test_carts c on cp.cart_id = c.id
    left join test_products p on cp.product_id = p.id
    left join test_features f on p.id = f.product_id and f.user_id = c.user_id
where
    c.user_id = 1
    and f.product_id is null
union
select
    p.*
#      ,cp.product_id
#      ,f.product_id
#      ,f.user_id
from test_features f
    left join test_products p on f.product_id = p.id
    left join test_users u on f.user_id = u.id
    left join test_carts c on u.id = c.user_id
    left join test_cart_products cp on p.id = cp.product_id and cp.cart_id = c.id
where
    f.user_id = 1
    and cp.product_id is null
;