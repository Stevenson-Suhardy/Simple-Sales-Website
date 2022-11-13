/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

-- Drop existing tables
DROP TABLE IF EXISTS users;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP SEQUENCE IF EXISTS users_id_seq;
CREATE SEQUENCE users_id_seq START 1000;


-- Create table
CREATE TABLE users(
    Id INT PRIMARY KEY DEFAULT nextval('users_id_seq'),
	EmailAddress VARCHAR(255) UNIQUE,
	Password VARCHAR(255) NOT NULL,
	FirstName VARCHAR(128),
	LastName VARCHAR(128),
	PhoneExtension INT,
	LastAccess TIMESTAMP,
	EnrolDate TIMESTAMP,
	Enable BOOLEAN,
	Type VARCHAR(2)
);

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enable, Type) VALUES(
	'jdoe@durhamcollege.ca',
	crypt('testpass', gen_salt('bf')),
	'John',
	'Doe',
	'2022-1-1 20:15:30',
	'2019-3-20 09:15:30',
    true,
    's'
);

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enable, Type) VALUES(
	'stevenson.suhardy@dcmail.ca',
	crypt('pass123', gen_salt('bf')),
	'Stevenson',
	'Suhardy',
	'2022-3-23 05:30:27',
	'2021-4-7 15:23:00',
    true,
    'a'
);

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enable, Type) VALUES(
	'whoknows@durhamcollege.ca',
	crypt('randompass', gen_salt('bf')),
	'Random',
	'Person',
	'2022-7-12 13:45:14',
	'2021-10-9 16:43:29',
    true,
    's'
);