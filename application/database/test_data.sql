insert into user (status, date_joined, name, email, descr, is_open)
	values (1, now(), 'James Kitchen', 'wildabyss@gmail.com', 
    'Luck is the last dying wish of those who wanna believe that winning can happen by accident, sweat on the other hand is for those who know it''s a choice, so decide now because destiny waits for no man. And when your time comes and a thousand different voices are trying to tell you you''re not ready for it, listen instead for that lone voice in decent the one that says you are ready, you are prepared, it’s all up to you now.',
    true);
insert into user (status, date_joined, name, email, is_open)
	values (1, now(), 'Chef Xu', 'mag@mag.com', true);
insert into user (status, date_joined, name, email, is_open)
	values (1, now(), 'Indian Bistro', 'indian@food.com', false);
insert into user (status, date_joined, name, email, is_open, descr)
	values (1, now(), 'Cocoral', 'ch@food.com', true,
    'Architect by education, chef by experience.');
    
insert into address (is_primary, user_id, address, city, province, postal_code, country, latitude, longitude)
	values (1, 1, '80 Western Battery Rd', 'Toronto', 'Ontario', 'M6K3S1', 'Canada', 43.6396, -79.4146);
insert into address (is_primary, user_id, address, city, province, postal_code, country, latitude, longitude)
	values (1, 2, '80 Western Battery Rd', 'Toronto', 'Ontario', 'M6K3S1', 'Canada', 43.6396, -79.4146);
insert into address (is_primary, user_id, address, city, province, postal_code, country, latitude, longitude)
	values (1, 3, '25 Mutual St', 'Toronto', 'Ontario', 'M5B2K1', 'Canada', 43.6543, -79.3743);
    
insert into food (status, user_id, name, price, descr, ingredients, health_benefits, pickup_method, quota)
	values (1, 1, 'Adrienne Burger', 12.99, 'Juicy burger with everything dressed', '', '', 1, 5);
insert into food (status, user_id, name, price, descr, ingredients, health_benefits, eating_instructions)
	values (1, 1, 'Kung Pao Chicken', 15.99, 'Spicy chicken with spicy nuts', 'Nuts, chicken', 'Good for your soul. Good for the environment. It''s even good for the chicken!',
	'Dump water into the food container. Heat for 50 min, add ample sugar and salt. Mix with terrazine, and you''re good to go!');
insert into food (status, user_id, name, price, prep_time_hours, descr, ingredients, health_benefits)
	values (1, 1, 'Apple', 1.50, 0.25, 'Delicious apple. Available in halves.', 'Apple', 'Good for your body');
insert into food (status, user_id, name, price, descr, ingredients, health_benefits)
	values (1, 2, 'Kung Pao Chicken', 8.49, 'Spicy chicken with spicy nuts', 'Nuts, chicken', 'Good for your soul');
insert into food (status, user_id, name, price, prep_time_hours, descr, ingredients)
	values (1, 3, 'Butter Chicken', 28.49, 1.5, 'Good chicken, good food', 'Chicken');
insert into food (status, user_id, name, price, descr, ingredients)
	values (1, 3, 'Vegetable soup', 3.99, 'Vegetables in a cup of soup', 'Carrots, brocolli');
insert into food (status, user_id, name, price, descr, ingredients)
	values (1, 2, 'Scrambled eggs', 5.99, 'Very scrambled eggs', 'Huevos');
insert into food (status, user_id, name, price, prep_time_hours, descr, quota)
	values (1, 1, 'Summer Delight', 5.99, 24, 'A delicious cake', 3);
insert into food (status, user_id, name, price, descr)
	values (1, 1, 'Porky Belly', 5.99, 'A rich meaty dish with the power to overwhelm your gastric senses. Get ready to dish out money.');
insert into food (status, user_id, name, alternate_name, prep_time_hours, price, descr)
	values (1, 4, 'Suave Milk', 'Suave 牛奶', 0.5, 5, 'Legen-dairy');
    
insert into food_category_assoc (food_id, food_category_id) values (1, 8);
insert into food_category_assoc (food_id, food_category_id) values (2, 3);
insert into food_category_assoc (food_id, food_category_id) values (3, 5);
insert into food_category_assoc (food_id, food_category_id) values (3, 1);
insert into food_category_assoc (food_id, food_category_id) values (3, 12);
insert into food_category_assoc (food_id, food_category_id) values (3, 11);
insert into food_category_assoc (food_id, food_category_id) values (4, 3);
insert into food_category_assoc (food_id, food_category_id) values (5, 2);
insert into food_category_assoc (food_id, food_category_id) values (6, 10);
insert into food_category_assoc (food_id, food_category_id) values (7, 11);
insert into food_category_assoc (food_id, food_category_id) values (8, 1);
insert into food_category_assoc (food_id, food_category_id) values (9, 3);
insert into food_category_assoc (food_id, food_category_id) values (10, 11);

insert into food_picture (food_id, path) values (2, '/food_pics/Easy-Kung-Pao-Chicken-Recipe-48.jpg');
insert into food_picture (food_id, path) values (4, '/food_pics/kung-pao-chicken-03.jpg');
insert into food_picture (food_id, path) values (1, '/food_pics/burger-chesseburger-fastfood.jpg');
insert into food_picture (food_id, path) values (3, '/food_pics/apple-fruit-free-wallpaper-for-pc.jpg');
insert into food_picture (food_id, path) values (3, '/food_pics/apple_red.jpg');
insert into food_picture (food_id, path) values (3, '/food_pics/apple_green.jpg');
insert into food_picture (food_id, path) values (5, '/food_pics/IMG_0209.jpg');
insert into food_picture (food_id, path) values (8, '/food_pics/photo-1456014673271-90b7fddf2eea.jpeg');
insert into food_picture (food_id, path) values (9, '/food_pics/yuan_f0d64856588e05e49b7cc348d15d327b.jpg');

insert into food_review (food_id, user_id, rating, review) values (1, 1, 5, 'An excellent source of protein and fat');
insert into food_review (food_id, user_id, rating, review) values (2, 1, 3, 'A classic Chinese dish that is both spicy and hot. Love the crazy about of nuts. So nutty!');
insert into food_review (food_id, user_id, rating) values (2, 2, 4);
insert into food_review (food_id, user_id, rating, review) values (4, 2, 2, 'Not as good as I though');
insert into food_review (food_id, user_id, rating, review) values (4, 1, 3, 'Edible');
insert into food_review (food_id, user_id, rating) values (10, 1, 4);
