create trigger trigger_note_utilisateur
after insert on avis
for each row
begin
    update utilisateur 
    set note = round((select avg(avis.note) from avis where avis.receveur_id = new.receveur_id)) 
    where id = new.receveur_id;
end;