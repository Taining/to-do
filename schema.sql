DROP TABLE appuser CASCADE;
DROP TABLE tasks CASCADE;

/*
CREATE TABLE users(
	userid INTEGER PRIMARY KEY,
	password VARCHAR(20) NOT NULL
);
*/

CREATE TABLE appuser (
	uid SERIAL,
	email VARCHAR(100),
	fname VARCHAR(20),
	lname VARCHAR(20),
	password VARCHAR(100),
	PRIMARY KEY (uid)
);

CREATE TABLE tasks(
	uid INTEGER,
	taskid INTEGER PRIMARY KEY,
	dscrp VARCHAR(40),
	total INTEGER,
	progress INTEGER,
	FOREIGN KEY (uid) REFERENCES appuser(uid)
);
	
-- Adding a sample row to appuser TABLE
-- Password is 12345
INSERT INTO appuser (email, fname, lname, password) VALUES ('test@localhost', 'test', 'todo', '827ccb0eea8a706c4c34a16891f84e7b');