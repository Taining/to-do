DROP TABLE user CASCADE;

CREATE TABLE appuser (
	uid SERIAL,
	username VARCHAR(20),
	password VARCHAR(100),
	PRIMARY KEY (uid)
);

-- Adding a sample row to appuser TABLE
INSERT INTO appuser (username, password) VALUES ('test','12345');