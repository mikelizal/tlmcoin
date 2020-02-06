# tlmcoin
A centralized digital currency for educational purposses in my web programming course. This is tlmcoin version 1
# README #

Una moneda digital para usar en las asignaturas
La idea es que sea centralizada y basada  en web services REST

### Como instalar

* git clone repostiorio
* exportar www para que se vea en la web

* Lanzar servidor web de pruebas

	docker run -d --name db1 -e MYSQL_ROOT_PASSWORD=adsad -e MYSQL_DATABASE=coindb -e MYSQL_USER=coinuser -e MYSQL_PASSWORD=adaddsas mysql	

   docker run -p 80:80 -p 443:443 -v loquesea/www:/var/www/html -d --name a1 --link db1:dbserver eboraas/apache-php

* Crear el fichero www/conf/conf.php a partir de example_conf.php






