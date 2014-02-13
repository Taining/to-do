DROP TABLE appuser CASCADE;
DROP TABLE tasks CASCADE;

CREATE TABLE appuser (
	uid SERIAL,
	email VARCHAR(100),
	fname VARCHAR(20),
	lname VARCHAR(20),
	password VARCHAR(100),
	sex integer,
	PRIMARY KEY (uid)
);

CREATE TABLE tasks(
	uid INTEGER REFERENCES appuser(uid),
	taskid SERIAL,
	title VARCHAR(40),
	dscrp VARCHAR(255),
	total INTEGER,
	progress INTEGER,
	PRIMARY KEY (taskid)
);

-- Adding a sample row to appuser TABLE
-- Password is 12345
INSERT INTO appuser (email, fname, lname, password, sex) VALUES ('test@localhost', 'test', 'todo', '827ccb0eea8a706c4c34a16891f84e7b', 1);
