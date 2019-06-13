
ALTER TABLE llx_user_project ADD PRIMARY KEY pk_user_project (fk_user, fk_project);
ALTER TABLE llx_user_project ADD INDEX idx_user_project_fk_user (fk_user);
ALTER TABLE llx_user_project ADD INDEX idx_user_project_fk_project (fk_project);

ALTER TABLE llx_user_project ADD CONSTRAINT fk_user_project_user_rowid FOREIGN KEY (fk_user) REFERENCES llx_user (rowid);
ALTER TABLE llx_user_project ADD CONSTRAINT fk_user_project_project_rowid   FOREIGN KEY (fk_project) REFERENCES llx_entrepot (rowid);
