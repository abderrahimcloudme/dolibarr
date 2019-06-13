create table llx_user_project
(
  fk_user  integer NOT NULL,
  fk_project    integer NOT NULL,
  import_key    varchar(14)
)ENGINE=innodb;
