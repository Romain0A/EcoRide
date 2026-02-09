create table voiture (
    id int auto_increment primary key,
    modele varchar(50) not null,
    immatriculation varchar(50) not null,
    energie varchar(50) not null,
    couleur varchar(50) not null,
    date_premiere_immatriculation date not null,
    marque varchar(50) not null,
    utilisateur_id int not null,

    constraint fk_voiture_utilisateur foreign key (utilisateur_id) references utilisateur(id)
);