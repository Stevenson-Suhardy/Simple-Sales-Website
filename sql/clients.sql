/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

DROP TABLE IF EXISTS clients;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP SEQUENCE IF EXISTS client_id_seq;
CREATE SEQUENCE client_id_seq START 1000;

CREATE TABLE clients (
    ClientId INT PRIMARY KEY DEFAULT nextval('client_id_seq'),
    SalesPersonId INT NOT NULL,
    FirstName VARCHAR(128) NOT NULL,
    LastName VARCHAR(128) NOT NULL,
    EmailAddress VARCHAR(255) UNIQUE,
    PhoneNumber VARCHAR(20) NOT NULL,
    Extension INT,
    LogoPath VARCHAR(255),
    CONSTRAINT fk_sales_person FOREIGN KEY(SalesPersonId) REFERENCES users(Id)
);

INSERT INTO clients(SalesPersonId, FirstName, LastName, EmailAddress, PhoneNumber, Extension, LogoPath) VALUES(
    1000,
    'Jane',
    'Doe',
    'jane.doe@dcmail.ca',
    '9051112222',
    NULL,
    './uploads/client_logo1.jpeg'
);

INSERT INTO clients(SalesPersonId, FirstName, LastName, EmailAddress, PhoneNumber, Extension) VALUES(
    1002,
    'Dream',
    'Client',
    'dream.client@dcmail.ca',
    '9052221111',
    NULL
);