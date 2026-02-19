create table covoiturage_participant(
    covoiturage_id int not null,
    utilisateur_id int not null,
    confirme TINYINT(1) NOT NULL DEFAULT 0,

    primary key (covoiturage_id,utilisateur_id),

    constraint fk_covoiturage_copa foreign key (covoiturage_id) references covoiturage(id) on delete cascade,
    constraint fk_utilisateur_copa foreign key (utilisateur_id) references utilisateur(id) on delete cascade
)