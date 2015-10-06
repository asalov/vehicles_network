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


INSERT INTO roles(name, external_id) VALUES
(1, 'Driver', 2),
(2, 'Analyst', 5),
(3, 'Director', 9);

INSERT INTO users(role_id, first_name, last_name, email, external_id) VALUES
(1, 'Peter', 'Svensson', 'petersvensson@mail.bg', 4),
(2, 'Anders', 'Eriksson', 'anderseriksson@maildrop.cc', 6),
(3, 'Mattias', 'Berg', 'mattiasberg82@gmail.com', 1);

INSERT INTO permissions(id, name) VALUES
(1, 'view_vehicle_usage')
(2, 'access_stock_data')
(3, 'view_videos');

INSERT INTO role_permissions(role_id, perm_id) VALUES();