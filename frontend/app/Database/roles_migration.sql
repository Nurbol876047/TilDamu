-- TilDamu.kz: expand authentication roles to match the platform access model.
ALTER TABLE `users`
  MODIFY `role` enum('patient','parent','therapist','admin','researcher','developer') NOT NULL DEFAULT 'patient';

UPDATE `users` SET `role` = 'patient' WHERE `role` = 'parent';
