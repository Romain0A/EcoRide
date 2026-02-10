CREATE TABLE utilisateur(
    id int auto_increment primary key,
    nom varchar(50) not null,
    prenom varchar(50) not null,
    email varchar(50) not null,
    password varchar(255) not null,
    telephone varchar(15) not null,
    adresse varchar(50) not null,
    date_naissance varchar(50) not null,
    photo varchar(255) not null,
    note smallint not null,
    roles json not null,
    pseudo varchar(180) not null,

    add constraint pseudo_unique unique (pseudo);
);