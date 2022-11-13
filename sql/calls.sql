/*
Stevenson Suhardy
September 16, 2022
Webd3201
*/

DROP TABLE IF EXISTS calls;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP SEQUENCE IF EXISTS call_id_seq;
CREATE SEQUENCE call_id_seq START 1000;

CREATE TABLE calls (
    CallId INT PRIMARY KEY DEFAULT nextval('call_id_seq'),
    ClientId INT,
    TimeOfCall TIMESTAMP,
    CONSTRAINT fk_client FOREIGN KEY (ClientId) REFERENCES clients(ClientId)
);

INSERT INTO calls(ClientId, TimeOfCall) VALUES (
    1000,
    '2022-6-20 12:15:05'
);