drop database if exists yumbox;
create database yumbox;

drop user 'yumbox'@'localhost';
create user 'yumbox'@'localhost' identified by 'yummy_tasty';
grant all on yumbox.* to 'yumbox'@'localhost';

use yumbox;

drop table if exists user;
create table user
(
	id int unsigned not null auto_increment,
    user_type tinyint unsigned not null, 	# 0=consumer, 1=vendor
    acct_status tinyint unsigned not null,	# 0=inactive, 1=active, 2=licensed
    date_joined datetime not null,
    
	fb_id varchar(22),
    
    name varchar(255),
    email varchar(255),
    phone varchar(25),
    driver_lic varchar(30),
    passport_num varchar(30),
    return_date datetime,					# date of return from break
    
    primary key (id),
    index fb_id_index (fb_id),
    index user_type_index (user_type),
    index acct_status_user_index (acct_status),
    index return_date_user_index (return_date)
) engine = InnoDB;

drop table if exists address;
create table address
(
	id int unsigned not null auto_increment,
    is_primary tinyint(1) unsigned not null,
    user_id int unsigned not null,
    
    address varchar(255),
    city varchar(10),
    province varchar(10),
    postal_code varchar(10),
    country varchar(10),
    
    primary key (id),
    index user_id_address_index (user_id),
    
    constraint user_id_address_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade
) engine = InnoDB;

drop table if exists food_category;
create table food_category
(
	id int unsigned not null auto_increment,
    name varchar(255) not null,
    
    primary key (id),
    index name_food_category_index (name)
) engine = InnoDB;

drop table if exists food;
create table food
(
	id int unsigned not null auto_increment,
    status tinyint unsigned not null,		# 0=inactive, 1=active
    user_id int unsigned not null,
    name varchar(255) not null,
    price decimal(5,2) unsigned not null,
    cutoff_time time not null default '00:00:00',
    
    descr text,
    ingredients text,
    health_benefits text,
    
    primary key (id),
    index user_id_food_index (user_id),
    
    constraint user_id_food_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade
) engine = InnoDB;

drop table if exists food_category_assoc;
create table food_category_assoc
(
	id int unsigned not null auto_increment,
    food_id int unsigned not null,
    food_category_id int unsigned not null,
    
    primary key (id),
    index food_id_assoc_index (food_id),
    index food_category_id_assoc_index (food_category_id),
    
    constraint food_id_assoc_constraint
		foreign key (food_id)
        references food (id)
        on delete cascade,
	constraint food_cat_id_assoc_constraint
		foreign key (food_category_id)
        references food_category (id)
        on delete cascade
) engine = InnoDB;

drop table if exists food_review;
create table food_review
(
	id int unsigned not null auto_increment,
    food_id int unsigned not null,
    user_id int unsigned not null,
    rating tinyint unsigned not null,
    
    review text,
    
    primary key (id),
    index food_id_review_index (food_id),
    index user_id_review_index (user_id),
    
    constraint food_id_review_constraint
		foreign key (food_id)
        references food (id)
        on delete cascade,
	constraint user_id_review_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade
) engine = InnoDB;

drop table if exists order_basket;
create table order_basket
(
	id int unsigned not null auto_increment,
    order_date datetime not null,
    user_id int unsigned not null,
    
    delivery_address int unsigned,
    
    primary key (id),
    index order_date_index (order_date),
    index user_id_order_index (user_id),
    
    constraint user_id_order_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade,
	constraint address_order_constraint
		foreign key (delivery_address)
        references address (id)
        on delete set null
) engine = InnoDB;

drop table if exists order_item;
create table order_item
(
	id int unsigned not null auto_increment,
    food_id int unsigned not null,
    quantity smallint unsigned not null,
    
    primary key (id),
    index food_id_order_index (food_id),
    
    constraint food_id_order_constraint
		foreign key (food_id)
        references food (id)
        on delete cascade
) engine = InnoDB;