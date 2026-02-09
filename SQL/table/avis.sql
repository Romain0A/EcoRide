create table avis(
    id int auto_increment primary key,
    commentaire varchar(255),
    note smallint not null,
    statut varchar(15) not null,
    poster_id int not null,
    receveur_id int not null,
    covoiturage_id int not null,

    constraint fk_poster foreign key (poster_id) references utilisateur(id),
    constraint fk_receveur foreign key (receveur_id) references utilisateur(id),
    constraint fk_covoiturage foreign key (covoiturage_id) references covoiturage(id)
)