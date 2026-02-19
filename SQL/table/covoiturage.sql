create table covoiturage (
    id int auto_increment primary key,
    date_depart date not null,
    heure_depart time not null,
    lieu_depart varchar(50) not null,
    date_arrivee date not null,
    heure_arrivee time not null,
    lieu_arrivee varchar(50) not null,
    statut varchar(50) not null,
    nb_place int not null,
    prix_personne DOUBLE PRECISION not null,
    debut_trajet datetime,
    fin_trajet datetime,
    chauffeur_id int not null,
    voiture_id int not null,

    constraint fk_chauffeur foreign key (chauffeur_id) references utilisateur(id),
    constraint fk_voiture foreign key (voiture_id) references voiture(id)
);