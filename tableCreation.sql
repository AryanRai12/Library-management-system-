drop database Library;
create database Library;
use Library;

create table reader(rollno char(8) primary key, email varchar(40) unique, firstname varchar(10), lastname varchar(10), address varchar(30), type varchar(10),
check (type in ("STAFF", "STUDENT")));

insert into reader values('19BCS027', 'murugappan.19cs@kct.ac.in', 'murugappan', 'm', 'madurai', 'STUDENT'),
('19BCS014', 'deekshith.19cs@kct.ac.in', 'deekshith', 'k', 'hosur', 'STUDENT'), ('19BCS058', 'hariprasad.19cs@kct.ac.in', 'hariprasad', 'b', 'trichy', 'STUDENT');

create table PhoneNumber(rollno char(8), phonenumber bigint unique,
check (length(phonenumber) = 10), foreign key (rollno) references reader(rollno));

insert into phonenumber values('19BCS027', 9095298712), ('19BCS014', 9080293608), ('19BCS027', 9095298713), ('19BCS058', 9080293604), ('19BCS058', 9095298726), ('19BCS014', 9080293638);

create table Category(title varchar(50) primary key, category varchar(30));

insert into category values('Charlie and chocolate factory', 'fiction'), ('Harry potter', 'fiction'), ('Goblet of fire', 'adventure'), 
('James and the giant peach', 'fiction'), ('engineering maths','Mathematics'), ('DBMS', 'CSE'), ('Discrete maths', 'Mathematics');

create table Edition(Title varchar(50), edition int, price decimal(30), primary key(title, edition));

insert into edition values('Charlie and chocolate factory', 3, 869.99), ('Harry potter', 4, 1369), ('Goblet of fire', 2, 1299),
('James and the giant peach', 1, 566), ('engineering maths', 3, 400), ('DBMS', 3, 1500), ('Discrete maths', 3, 799);

create table Book (ISBN bigint primary key, title varchar(50), edition int, authno mediumint, 
foreign key (title) references category(title), foreign key (title, edition) references edition(title, edition), 
check (length(ISBN) = 10 or length(ISBN) = 13), check(length(authno) = 6));

insert into Book values(1234567890, 'Charlie and chocolate factory', 3, 879654), (1234567891, 'Harry potter', 4, 879655),
(1234567892, 'Goblet of fire', 2, 879655), (1234567893, 'James and the giant peach', 1, 879654), (1234567894, 'engineering maths', 3, 879656),
(1234567895, 'DBMS', 3, 879657), (1234567896, 'Discrete maths', 3, 879656);

create table ReturnDate(ISBN bigint primary key, rollno char(8), borroweddate timestamp default now(), duedate timestamp default date_add(now(), interval 14 day),
 renewalstatus varchar(10),
check (renewalstatus in ("NOT", "RENEWED", "RETURNED")), foreign key (rollno) references reader(rollno));

insert into returndate values(1234567890, '19BCS027', now(), date_add(now(), interval 14 day), 'NOT'), (1234567896, '19BCS027', date_sub(now(), interval 16 day), date_sub(now(), interval 2 day), 'NOT'),
(1234567893, '19BCS027', date_sub(now(), interval 42 day), date_sub(now(), interval 14 day), 'RENEWED'), (1234567895, '19BCS014', now(), date_add(now(), interval 14 day), 'NOT');

create table Publisher(PublisherID varchar(10) primary key, name varchar(10), check (length(publisherid) = 6));

insert into publisher values('CEG232', 'Cengage'), ('PEN123', 'PENGUIN');

create table Publishes(ISBN bigint, publisherID varchar(10), 
foreign key (publisherid) references publisher(publisherid), foreign key (ISBN) references Book(ISBN));

insert into publishes values(1234567890, 'CEG232'), (1234567891, 'PEN123'), (1234567892, 'CEG232'), (1234567893, 'PEN123'), (1234567894, 'CEG232'), (1234567895, 'PEN123'), (1234567896, 'CEG232');

create table Authentication(loginid varchar(10) primary key, password varchar(100),
check (length(password) > 7));

#insert into authentication values('murugu21', 'murugu123');

create table Login(loginID varchar(10), rollno char(8), foreign key (rollno) references reader(rollno), foreign key (loginid) references authentication(loginid));

#insert into login values('murugu21', '19BCS027');