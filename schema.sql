create table partner_types(
	id int auto_increment primary key,
    name varchar(100) not null
);
create table partners(
	id int auto_increment primary key,
    partner_type_id int not null references partner_types(id),
    name varchar(255) not null,
    address varchar(255) not null,
    location geometry default null,
    description varchar(500),
    image varchar(100) default null,
    color_style char(7) default null,
    courier_share_percent double default null,
    delivery_fee int default 0,
    email varchar(255) not null unique,
	password varchar(100) not null,
    foreign key (partner_type_id) references partner_types(id)
);

create table partner_open_times(
	id int auto_increment primary key,
    partner_id int not null references partners(id),
    day enum('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun') not null,
    open_from time not null,
    open_to time not null,
    foreign key (partner_id) references partners(id)
);

create table product_categories(
	id int auto_increment primary key,
    partner_id int not null references partners(id),
    name varchar(100) not null,
	foreign key (partner_id) references partners(id)
);

create table products(
	id int auto_increment primary key,
	partner_id int not null references partners(id),
    product_category_id int not null references product_categories(id),
    name varchar(255) not null,
    description varchar(500) default null,
    image varchar(100) default null,
    unit_price double not null,
    discount double default null,
	foreign key (partner_id) references partners(id),
	foreign key (product_category_id) references product_categories(id)
);

create table product_allergens(
	id int auto_increment primary key,
    name varchar(100) not null
);

create table product_has_allergen(
    product_id int not null,
    product_allergen_id int not null,
    foreign key (product_id) references products(id),
    foreign key (product_allergen_id) references product_allergens(id)
);

create table product_periods(
	id int auto_increment primary key,
    product_id int not null references products(id),
    available_from timestamp not null,
    available_to timestamp not null,
    foreign key (product_id) references products(id)
);

create table users(
	id int auto_increment primary key,
    email varchar(255) not null unique,
    address varchar(255) not null,
    location geometry default null,
    name varchar(255) not null,
    phone_number varchar(30) not null,
    password varchar(100) not null,
    registered_at datetime default current_timestamp
);

create table temp_users(
	id int auto_increment primary key,
    email varchar(255) not null unique,
    address varchar(255) default null,
    name varchar(255) not null,
    phone_number varchar(30) not null
);

create table orders(
	id int auto_increment primary key,
    partner_id int not null references partners(id),
    order_date datetime default current_timestamp,
    user_id int default null references users(id),
    temp_user_id int default null references temp_users(id),
    payment_type enum('cash', 'card') not null,
    description varchar(255) default null,
    status enum('pending', 'accepted', 'processing', 'delivering', 'delivered') default 'pending',
    needs_delivery tinyint default 1,
    courier_share_percent double default null,
    
    user_name varchar(255) not null,
    user_email varchar(255) not null,
    user_phone_number varchar(30) not null,
    user_address varchar(255) default null,
    foreign key (partner_id) references partners(id),
    foreign key (user_id) references users(id),
    foreign key (temp_user_id) references temp_users(id)
);

create table order_items(
	id int auto_increment primary key,
    order_id int not null references orders(id),
    product_id int not null references products(id),
    quantity smallint not null,
    unit_price double not null,
    foreign key (order_id) references orders(id),
    foreign key (product_id) references products(id)
);

create table couriers(
	id int auto_increment primary key,
	name varchar(255) not null,
    email varchar(255) unique not null,
    phone_number varchar(30) not null,
    login_id char(6),
    password varchar(100) not null
);

create table courier_periods(
	id int auto_increment primary key,
    courier_id int not null references couriers(id),
    day enum('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun') not null,
	available_from time not null,
    available_to time not null,
    foreign key (courier_id) references couriers(id)
);

create table courier_deliveries(
	id int auto_increment primary key,
    courier_id int not null references couriers(id),
    order_id int not null references orders(id),
    status enum('pending', 'accepted', 'declined'),
    foreign key (courier_id) references couriers(id),
    foreign key (order_id) references orders(id)
);