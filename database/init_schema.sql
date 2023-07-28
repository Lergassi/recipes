create table if not exists qualities
(
    id int unsigned auto_increment,
    name varchar(64) not null,
    alias varchar(100) not null,
    sort int not null,
    primary key (id),
    unique (alias)
);

create table if not exists reference_products
(
    id int unsigned auto_increment,
    name varchar(64) not null,
    alias varchar(100) not null,
    sort int unsigned not null,
    primary key (id),
    unique (alias)
);

create table if not exists dishes
(
    id int unsigned auto_increment,
    name varchar(128) not null,
    alias varchar(150) not null,
    quality_id int unsigned not null,
    primary key (id),
    foreign key (quality_id) references qualities (id),
    unique (alias)
);

create table if not exists dish_versions
(
    id int unsigned auto_increment,
    name varchar(128) not null,
    alias varchar(150) not null,
    dish_id int unsigned not null,
    quality_id int unsigned not null,
    primary key (id),
    foreign key (dish_id) references dishes (id),
    foreign key (quality_id) references qualities (id),
    unique (alias)
);

create table if not exists dish_version_branches
(
    id int unsigned auto_increment,
    name varchar(128) not null,
    description varchar(512) null,
    dish_version_id int unsigned not null,
    primary key (id),
    foreign key (dish_version_id) references dish_versions (id),
    unique (name)
);

create table if not exists recipes
(
    id int unsigned auto_increment,
    is_main bool not null, # main, committed
    dish_version_branch_id int unsigned not null,
    primary key (id),
    foreign key (dish_version_branch_id) references dish_version_branches (id),
    unique (is_main, dish_version_branch_id)
);

create table if not exists recipe_positions
(
    id int unsigned auto_increment,
    weight int unsigned not null,   # в граммах
#     sort int unsigned not null, # Брать из reference_product, позже сделать "перезагрузку" значения.
    reference_product_id int unsigned not null,
    recipe_id int unsigned not null,
    primary key (id),
    foreign key (reference_product_id) references reference_products (id),
    foreign key (recipe_id) references recipes (id),
    unique (recipe_id, reference_product_id)
);