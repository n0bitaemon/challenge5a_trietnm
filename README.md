---MYSQL---
create table account(
	id int not null auto_increment,
    username varchar(255) not null unique,
    password varchar(255) not null,
    fullname varchar(255) not null,
    avatar varchar(255),
    email varchar(255),
    phone varchar(10),
    is_teacher boolean not null default 0,
    primary key(id)
);

create table exercise(
    id int not null auto_increment,
    title varchar(255) not null,
    description varchar(255),
    file varchar(255) not null,
    creator int not null,
    create_date date not null default curdate(),
    end_date datetime,
    published boolean not null default 0,
    primary key(id),
    foreign key(creator) references account(id)
);

create table exercise_ans(
    id int not null auto_increment,
    exercise_id int not null,
    user_id int not null,
    ans_file varchar(255) not null,
    is_done boolean not null default 0,
    submit_date datetime not null default current_timestamp(),
    primary key(id),
    foreign key(exercise_id) references exercise(id),
    foreign key(user_id) references account(id)
);

create table quiz(
    id int not null auto_increment,
    title varchar(255) not null,
    description varchar(255),
    hint varchar(255) not null,
    file varchar(255) not null,
    creator id int not null,
    create_date date not null default curdate(),
    end_date datetime not null,
    published boolean not null default 0,
    primary key(id),
    foreign key(creator) references account(id)
);

create table quiz_ans(
    id int not null auto_increment,
    quiz_id int not null,
    user_id int not null,
    answer varchar(255) not null,
    primary key(id),
    foreign key(quiz_id) references quiz(id),
    foreign key(user_id) references account(id)
);

create table message(
    id int not null auto_increment,
    from_id int not null,
    to_id int not null,
    content text not null,
    is_seen boolean not null default 0,
    create_date datetime not null default current_timestamp(),
    primary key(id),
    foreign key(from_id) references account(id),
    foreign key(to_id) references account(id)
);
