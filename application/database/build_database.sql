drop database if exists yumbox_dev;
create database yumbox_dev;
use yumbox_dev;

drop table if exists user;
create table user
(
	id int unsigned not null auto_increment,
    user_type tinyint unsigned not null, 	# 0=consumer, 1=vendor
    status tinyint unsigned not null,	# 0=inactive, 1=active, 2=licensed
    date_joined datetime not null,
    name varchar(255) not null,
    email varchar(255) not null,
    
	fb_id varchar(22),

    phone varchar(25),
    driver_lic varchar(30),
    passport_num varchar(30),
    return_date datetime,					# date of return from break
    
    primary key (id),
    index fb_id_index (fb_id),
    index user_type_index (user_type),
    index status_user_index (status),
    index return_date_user_index (return_date)
) engine = InnoDB;

drop table if exists user_picture;
create table user_picture
(
	id int unsigned not null auto_increment,
	user_id int unsigned not null,
	path varchar(255) not null,
	
	primary key (id),
	index user_id_picture_index (user_id),
	
	constraint user_id_picture_constraint
		foreign key (user_id)
		references user (id)
		on delete cascade
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

drop table if exists food_picture;
create table food_picture
(
	id int unsigned not null auto_increment,
	food_id int unsigned not null,
	path varchar(255) not null,
	
	primary key (id),
	index food_id_picture_index (food_id),
	
	constraint food_id_picture_constraint
		foreign key (food_id)
		references food (id)
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
	settled tinyint(1) unsigned not null default 0,
    
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

drop table if exists ci_sessions;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(40) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
) engine = InnoDB;


drop procedure if exists add_user;
delimiter //
create procedure add_user(in user_type tinyint, in name varchar(255), in email varchar(255),
	in fb_id varchar(25))
begin
	set @id = null;
	
    if (fb_id is not null) then
		select u.id into @id
        from user u
        where
			u.fb_id = fb_id;
            
		if (@id is null) then
			insert into user (user_type, status, date_joined, name, email, fb_id)
            values (user_type, 1, now(), name, email, fb_id);
		end if;
    end if;
    
end//
delimiter ;