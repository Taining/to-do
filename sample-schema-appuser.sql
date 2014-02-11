DROP TABLE appuser CASCADE;

CREATE TABLE appuser (
	uid SERIAL,
	email VARCHAR(100),
	fname VARCHAR(20),
	lname VARCHAR(20),
	password VARCHAR(100),
	PRIMARY KEY (uid)
);

-- Adding a sample row to appuser TABLE
-- Password is 12345
INSERT INTO appuser (email, fname, lname, password) VALUES ('test@localhost', 'test', 'todo', '827ccb0eea8a706c4c34a16891f84e7b');
