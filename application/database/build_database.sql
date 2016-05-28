drop database if exists yumbox;
create database yumbox default character set utf8 default collate utf8_general_ci;

grant all on yumbox.* to 'yumbox'@'localhost';

use yumbox;

drop table if exists user;
create table user
(
	id int unsigned not null auto_increment,
    user_type tinyint unsigned not null, 	# 0=consumer, 1=vendor
    status tinyint unsigned not null,	# 0=inactive, 1=active, 2=licensed
    date_joined datetime not null,
    name varchar(255) not null,
    email varchar(255) not null,
    max_unfilled_orders mediumint unsigned not null default 10,
    is_open tinyint(1) not null default 0,
    can_deliver tinyint(1) not null default 0,
    
    /* food pickup times */
    # set to 00:00:00 for not available
    pickup_mon time not null default '20:00:00',
    pickup_tue time not null default '20:00:00',
    pickup_wed time not null default '20:00:00',
    pickup_thu time not null default '20:00:00',
    pickup_fri time not null default '20:00:00',
    pickup_sat time not null default '20:00:00',
    pickup_sun time not null default '20:00:00',
    
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
    is_primary tinyint(1) unsigned not null default 1,
    user_id int unsigned not null,
    
    address varchar(255),
    city varchar(10),
    province varchar(10),
    postal_code varchar(10),
    country varchar(10),
    
    # geocoded location from above address
    latitude float,
    longitude float,
    
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
    
    /* food preparation */
    pickup_method tinyint(2) not null default 0,		# 0=any time the food is ready after prep_time_hours, # 1=limit pickup to times as specified in user table
    prep_time_hours float not null default 2.0,
    
	alternate_name varchar(255),
    descr text,
    ingredients text,
    health_benefits text,
    eating_instructions text,
    
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
    
    stripe_cust_id varchar(50),
    stripe_card_id varchar(50),
    
    exp_month tinyint unsigned,
    exp_year smallint unsigned,
    last4 smallint unsigned,
    type varchar(10),		# Visa, Master
    
    primary key (id),
    index stripe_cust_id_index (stripe_cust_id),
    index stripe_card_id_index (stripe_card_id)
) engine = InnoDB;

drop table if exists order_basket;
create table order_basket
(
	id bigint unsigned not null auto_increment,
    order_date datetime not null,
    user_id int unsigned not null,
    is_paid tinyint(1) unsigned not null default 0,
    
    delivery_address int unsigned,
    
    primary key (id),
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
	id bigint unsigned not null auto_increment,
    food_id bigint unsigned not null,
    quantity smallint unsigned not null,
    order_basket_id bigint unsigned not null,
    is_filled tinyint(1) not null default 0,	# 0 = unfilled, 1 = delivered
    
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

drop table if exists payment;
create table if not exists payment (
	id bigint unsigned not null auto_increment,
    amount decimal(8,2) not null,
    payment_date datetime not null,
    order_item_id bigint unsigned not null,
    tax_rate float not null default 0.13,
    take_rate float not null default 0.05,
    
    stripe_charge_id varchar(50),
    
    primary key (id),
    index stripe_charge_id_index (stripe_charge_id),
    index order_item_payment_index (order_item_id),
    
    constraint payment_order_constraint
		foreign key (order_item_id)
        references order_item (id)
        on delete restrict
) engine = InnoDB;

drop table if exists refund;
create table refund
(
	id bigint unsigned not null auto_increment,
    type tinyint unsigned not null,		# 0 = from customer, 1 = from chef
    amount decimal(8,2) not null,
    payment_date datetime not null,
    order_item_id bigint unsigned not null,
    
    stripe_refund_id varchar(50),
    explanation text,
    
    primary key (id),
    index refund_order_item_index (order_item_id),
    index stripe_refund_id_index (stripe_refund_id),

	constraint refund_order_item_constraint
		foreign key (order_item_id)
        references order_item (id)
        on delete restrict
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
create procedure add_user(in user_type tinyint unsigned, in name varchar(255), in email varchar(255),
	in fb_id varchar(25), in google_id varchar(25))
begin
	declare id int unsigned;
	
    if (fb_id is not null) then
		select u.id into id
        from user u
        where
			u.fb_id = fb_id;
            
		if (id is null) then
			insert into user (user_type, status, date_joined, name, email, fb_id)
            values (user_type, 1, now(), name, email, fb_id);
		end if;
    elseif (google_id is not null) then
		select u.id into id
        from user u
        where
			u.google_id = google_id;
            
		if (id is null) then
			insert into user (user_type, status, date_joined, name, email, google_id)
            values (user_type, 1, now(), name, email, google_id);
		end if;
    end if;
    
end//
delimiter ;


drop procedure if exists add_user_follower;
delimiter //
create procedure add_user_follower(in user_id int unsigned, in vendor_id int unsigned)
begin
	declare exist bigint unsigned;
    
    select a.id into exist
    from user_follow_assoc a
    where
		a.user_id = user_id
        and a.vendor_id = vendor_id;
        
	if (exist is null) then
		insert into user_follow_assoc (user_id, vendor_id)
        values (user_id, vendor_id);
    end if;
end//
delimiter ;


drop function if exists average_rating;
delimiter //
create function average_rating(food_id bigint unsigned) 
returns float not deterministic
begin
	declare average float;

    select avg(r.rating) into average
    from food_review r
    where
		r.food_id = food_id
	group by 
		r.food_id;
    
    if (average is null) then
		set average = 0;
	end if;
    
    return average;
end//
delimiter ;


drop function if exists total_orders;
delimiter //
create function total_orders(food_id bigint unsigned)
returns int not deterministic
begin
	declare total int;
    
    select sum(o.quantity) into total
    from order_item o
    where
		o.food_id = food_id
	group by
		o.food_id;
        
	if (total is null) then
		set total = 0;
	end if;
    
    return total;
end//
delimiter ;


drop procedure if exists add_order;
delimiter //
create procedure add_order(in order_basket_id bigint unsigned, in food_id bigint unsigned, in quantity smallint unsigned)
begin
	declare o_id bigint unsigned;
    
    select o.id into o_id
    from order_item o
    where
		o.food_id = food_id
        and o.order_basket_id = order_basket_id;
        
	if (o_id is null) then
		insert into order_item
			(food_id, quantity, order_basket_id)
		values
			(food_id, quantity, order_basket_id);
	else
		update order_item o
        set
			o.quantity = o.quantity + quantity
		where
			o.order_basket_id = order_basket_id
            and o.food_id = food_id;
	end if;
end//
delimiter ;


drop procedure if exists add_payment;
delimiter //
create procedure add_payment(in amount decimal(8,2), in take_rate float, in tax_rate float, in stripe_id varchar(50), in order_item_id bigint unsigned)
begin
	declare p_id bigint unsigned;
    
    select p.id into p_id
    from payment p
    where
		p.stripe_charge_id = stripe_id;
        
	if (p_id is null) then
		insert into payment
			(amount, take_rate, tax_rate, stripe_charge_id, payment_date, order_item_id)
		values
			(amount, take_rate, tax_rate, stripe_id, now(), order_item_id);
	else
		signal sqlstate '45000'
			set message_text = 'stripe payment exists';
	end if;
end//
delimiter ;


drop procedure if exists add_refund;
delimiter //
create procedure add_refund(in amount decimal(8,2), in stripe_id varchar(50), in order_item_id bigint unsigned, 
	in type tinyint unsigned, in explanation text)
begin
	declare r_id bigint unsigned;
    
    select r.id into r_id
    from refund r
    where
		r.stripe_refund_id = stripe_id;
        
	if (r_id is null) then
		insert into refund
			(amount, stripe_refund_id, payment_date, order_item_id, type, explanation)
		values
			(amount, stripe_id, now(), order_item_id, type, explanation);
	else
		signal sqlstate '45000'
			set message_text = 'stripe refund exists';
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


/** Email queue tables **/

CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_address` VARCHAR(255) NOT NULL,
  `from_name` VARCHAR(255) NOT NULL,
  `replyto` VARCHAR(255) NOT NULL,
  `replyto_name` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(1024) NOT NULL,
  `body` MEDIUMTEXT NOT NULL,
  `enqueue_date` DATETIME NOT NULL,
  `sent_date` DATETIME NOT NULL,
  `tries` SMALLINT NOT NULL DEFAULT 0,
  `try_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `mail_recipient` (
  `mail_id` BIGINT UNSIGNED NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `recipient_type` TINYINT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`mail_id`, `address`, `recipient_type`),
  CONSTRAINT `fk_mail_recipient_mail_queue`
    FOREIGN KEY (`mail_id`)
    REFERENCES `mail_queue` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
