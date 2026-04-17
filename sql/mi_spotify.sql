CREATE DATABASE IF NOT EXISTS mi_spotify;
USE mi_spotify;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE generos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE autores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(255)
);

CREATE TABLE albumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(255),
    anio INT,
    id_autor INT,
    FOREIGN KEY (id_autor) REFERENCES autores(id)
);

CREATE TABLE canciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_autor INT,
    id_genero INT,
    id_album INT,
    fecha_lanzamiento DATE,
    duracion VARCHAR(10),
    imagen VARCHAR(255),
    archivo_mp3 VARCHAR(255),
    FOREIGN KEY (id_autor) REFERENCES autores(id),
    FOREIGN KEY (id_genero) REFERENCES generos(id),
    FOREIGN KEY (id_album) REFERENCES albumes(id)
);

CREATE TABLE playlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    nombre VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE playlist_canciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_playlist INT,
    id_cancion INT,
    FOREIGN KEY (id_playlist) REFERENCES playlist(id),
    FOREIGN KEY (id_cancion) REFERENCES canciones(id)
);

INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('Administrador', 'admin@spotify.com', MD5('admin123'), 'admin');

INSERT INTO generos (nombre) VALUES
('Pop'), ('Rock'), ('Reggaeton'), ('Hip-Hop'), ('Electronica'),
('Balada'), ('Cumbia'), ('Jazz'), ('K-Pop'), ('Metal');