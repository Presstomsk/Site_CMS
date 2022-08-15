DROP TABLE IF EXISTS articles;
CREATE TABLE articles
(
  id              smallint unsigned NOT NULL auto_increment,
  publicationDate date NOT NULL,                              # Когда статья опудликована
  title           varchar(255) NOT NULL,                      # Полный заголовок статьи
  summary         text NOT NULL,                              # Резюме статьи
  content         mediumtext NOT NULL,                        # HTML содержание статьи

  PRIMARY KEY     (id)
);

CREATE TABLE users
(
  id              smallint unsigned NOT NULL auto_increment,  
  username        varchar(255) NOT NULL,                      # Логин
  pass            varchar(255) NOT NULL,                      # Пароль

  PRIMARY KEY     (id)
);
