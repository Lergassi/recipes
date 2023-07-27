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