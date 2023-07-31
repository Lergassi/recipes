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

create table if not exists recipes
(
    id int unsigned auto_increment,
    name varchar(256) not null,
    dish_version_id int unsigned not null,
    primary key (id),
    foreign key (dish_version_id) references dish_versions (id)
);

create table if not exists recipe_positions
(
    id int unsigned auto_increment,
    weight int unsigned not null,   # в граммах
    reference_product_id int unsigned not null,
    recipe_id int unsigned not null,
    primary key (id),
    foreign key (reference_product_id) references reference_products (id),
    foreign key (recipe_id) references recipes (id),
    unique (recipe_id, reference_product_id)
);

create table if not exists recipe_commits
(
    id int unsigned auto_increment,
    recipe_id int unsigned not null,
    previous_commit_id int unsigned null,
    primary key (id),
    foreign key (recipe_id) references recipes (id),
    foreign key (previous_commit_id) references recipe_commits (id)
);

create table if not exists recipe_commit_positions
(
    id int unsigned auto_increment,
    weight int unsigned not null,   # в граммах
    reference_product_id int unsigned not null,
    recipe_commit_id int unsigned not null,
    primary key (id),
    foreign key (reference_product_id) references reference_products (id),
    foreign key (recipe_commit_id) references recipe_commits (id)
);

create table if not exists heads
(
    recipe_id int unsigned not null,
    recipe_commit_id int unsigned not null,
    foreign key (recipe_id) references recipes (id),
    foreign key (recipe_commit_id) references recipe_commits (id),
    unique (recipe_id)
);