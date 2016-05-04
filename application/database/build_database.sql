drop database if exists yumbox_dev;
create database yumbox_dev default character set utf8 default collate utf8_general_ci;
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
    start_time time not null default '09:00:00',
    end_time time not null default '20:00:00',
    max_unfilled_orders mediumint unsigned not null default 10,
    
	fb_id varchar(25),
    google_id varchar(25),

	alternate_name varchar(255),
    descr text,
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
	id bigint unsigned not null auto_increment,
	user_id int unsigned not null,
	path varchar(255) not null,
	
	primary key (id),
	index user_id_picture_index (user_id),
	
	constraint user_id_picture_constraint
		foreign key (user_id)
		references user (id)
		on delete cascade
) engine = InnoDB;

drop table if exists user_follow_assoc;
create table user_follow_assoc
(
	id bigint unsigned not null auto_increment,
    user_id int unsigned not null,
	vendor_id int unsigned not null,
    
    primary key (id),
    index user_id_user_follow_index (user_id),
    index vendor_id_user_follow_index (vendor_id),
    
    constraint user_id_user_follow_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade,
	constraint vendor_id_user_follow_constraint
		foreign key (vendor_id)
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
    main tinyint(1) not null default 0,
    
    primary key (id),
    index name_food_category_index (name),
    index main_food_category_index (main)
) engine = InnoDB;

drop table if exists food;
create table food
(
	id bigint unsigned not null auto_increment,
    status tinyint unsigned not null,		# 0=inactive, 1=active
    user_id int unsigned not null,
    name varchar(255) not null,
    price decimal(5,2) unsigned not null,
    
    cutoff_time time default '00:00:00',		# not used at the moment, replaced by user.start_time and end_time
	alternate_name varchar(255),
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
	id bigint unsigned not null auto_increment,
    food_id bigint unsigned not null,
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
	id bigint unsigned not null auto_increment,
	food_id bigint unsigned not null,
	path varchar(255) not null,
	
	primary key (id),
	index food_id_picture_index (food_id),
	
	constraint food_id_picture_constraint
		foreign key (food_id)
		references food (id)
		on delete cascade
) engine = InnoDB;

drop table if exists food_follow_assoc;
create table food_follow_assoc
(
	id bigint unsigned not null auto_increment,
    user_id int unsigned not null,
	food_id bigint unsigned not null,
    
    primary key (id),
    index user_id_food_follow_index (user_id),
    index food_id_food_follow_index (food_id),
    
    constraint user_id_food_follow_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade,
	constraint food_id_food_follow_constraint
		foreign key (food_id)
        references food (id)
        on delete cascade
) engine = InnoDB;

drop table if exists food_review;
create table food_review
(
	id bigint unsigned not null auto_increment,
    food_id bigint unsigned not null,
    user_id int unsigned not null,
    rating tinyint unsigned not null default 3,		# scale 0 to 5
    
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

drop table if exists credit_card;
create table if not exists credit_card(
	id bigint unsigned not null auto_increment,
    exp_month tinyint unsigned not null,
    exp_year smallint unsigned not null,
    last4 smallint unsigned not null,
    type varchar(10) not null,		# Visa, Master
    
    stripe_cust_id varchar(50),
    stripe_card_id varchar(50),
    
    primary key (id),
    index stripe_cust_id_index (stripe_cust_id),
    index stripe_card_id_index (stripe_card_id)
) engine = InnoDB;

drop table if exists payment;
create table if not exists payment (
	id bigint unsigned not null auto_increment,
    amount decimal(8,2) not null,
    payment_date datetime not null,
    
    stripe_charge_id varchar(50),
    
    primary key (id),
    index amount_payment_index (amount),
    index payment_date_index (payment_date),
    index stripe_charge_id_index (stripe_charge_id)
) engine = InnoDB;

drop table if exists order_basket;
create table order_basket
(
	id bigint unsigned not null auto_increment,
    order_date datetime not null,
    user_id int unsigned not null,
    is_filled tinyint(1) unsigned not null default 0,
    
    delivery_address int unsigned,
    payment_id bigint unsigned,
    
    primary key (id),
    index order_date_index (order_date),
    index user_id_order_index (user_id),
    index payment_id_order_index (payment_id),
    
    constraint user_id_order_constraint
		foreign key (user_id)
        references user (id)
        on delete cascade,
	constraint address_order_constraint
		foreign key (delivery_address)
        references address (id)
        on delete set null,
	constraint payment_id_order_constraint
		foreign key (payment_id)
        references payment (id)
        on delete set null
) engine = InnoDB;

drop table if exists order_item;
create table order_item
(
	id bigint unsigned not null auto_increment,
    food_id bigint unsigned not null,
    quantity smallint unsigned not null,
    order_basket_id bigint unsigned not null,
    
    primary key (id),
    index food_id_order_index (food_id),
    index order_item_basket_index (order_basket_id),
    
    constraint food_id_order_constraint
		foreign key (food_id)
        references food (id)
        on delete cascade,
	constraint order_item_basket_constraint
		foreign key (order_basket_id)
        references order_basket (id)
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
	in fb_id varchar(25), in google_id varchar(25))
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
    elseif (google_id is not null) then
		select u.id into @id
        from user u
        where
			u.google_id = google_id;
            
		if (@id is null) then
			insert into user (user_type, status, date_joined, name, email, google_id)
            values (user_type, 1, now(), name, email, google_id);
		end if;
    end if;
    
end//
delimiter ;


drop procedure if exists add_user_follower;
delimiter //
create procedure add_user_follower(in user_id int, in vendor_id int)
begin
	set @exist = null;
    
    select a.id into @exist
    from user_follow_assoc a
    where
		a.user_id = user_id
        and a.vendor_id = vendor_id;
        
	if (@exist is null) then
		insert into user_follow_assoc (user_id, vendor_id)
        values (user_id, vendor_id);
    end if;
end//
delimiter ;


/** essential data **/
insert into food_category (name, main) values ('dessert', 1); #1
insert into food_category (name, main) values ('indian', 1); #2
insert into food_category (name, main) values ('chinese', 1); #3
insert into food_category (name, main) values ('french', 1); #4
insert into food_category (name, main) values ('fruit', 0); #5
insert into food_category (name, main) values ('thai', 1); #6
insert into food_category (name, main) values ('vietnam', 1); #7
insert into food_category (name, main) values ('burger', 0); #8
insert into food_category (name, main) values ('sandwich', 0); #9
insert into food_category (name, main) values ('soup', 0); #10
insert into food_category (name, main) values ('breakfast', 0); #11
insert into food_category (name, main) values ('vegetarian', 1); #12 
insert into food_category (name, main) values ('sea food', 1); #13
insert into food_category (name, main) values ('drink', 1); #14
insert into food_category (name, main) values ('italian', 1); #15
insert into food_category (name, main) values ('mexican', 1); #16 
insert into food_category (name, main) values ('persian', 1); #17 