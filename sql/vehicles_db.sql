/********** DATABASE STRUCTURE **********/
CREATE DATABASE vehicles_db;

USE vehicles_db;

CREATE TABLE IF NOT EXISTS roles(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	external_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS users(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	role_id INT NOT NULL,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	email VARCHAR(60) NOT NULL,
	external_id INT NOT NULL,
	FOREIGN KEY(role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS permissions(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL
);

CREATE TABLE IF NOT EXISTS role_permissions(
	role_id INT NOT NULL,
	perm_id INT NOT NULL,
	PRIMARY KEY(role_id, perm_id),
	FOREIGN KEY(role_id) REFERENCES roles(id),
	FOREIGN KEY(perm_id) REFERENCES permissions(id)
);

CREATE TABLE IF NOT EXISTS annotations(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT NOT NULL,
	content TEXT NOT NULL,
	vehicle_id INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY(user_id) REFERENCES users(id)
);

/********** DEFAULT DATA **********/
INSERT INTO roles(id, name, external_id) VALUES
(1, 'Driver', 2),
(2, 'Analyst', 5),
(3, 'Director', 9);

INSERT INTO users(role_id, first_name, last_name, email, external_id) VALUES
(1, 'Peter', 'Svensson', 'petersvensson@mail.bg', 4),
(2, 'Anders', 'Eriksson', 'anderseriksson@mailinator.com', 6),
(3, 'Mattias', 'Berg', 'mattiasberg82@gmail.com', 1);

INSERT INTO permissions(id, name) VALUES (1, 'access_stock_data');
INSERT INTO permissions(id, name) VALUES (2, 'add_notes');

INSERT INTO role_permissions(role_id, perm_id) VALUES(3, 1);
INSERT INTO role_permissions(role_id, perm_id) VALUES(2, 2);