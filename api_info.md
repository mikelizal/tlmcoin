# Documentacion del API #

Esto es la documentacion del API de la moneda electronica

Las operaciones son las siguientes

## Informacion general ##

Comando info.php

**Peticion**

    https://@servidor/api/info.php

**Resultado**

Un JSON con informacion sobre la moneda en general

	{
	"version":"1",
	"currency_name":"tlmDolares",
	"coin_name":"tlmCoin",
	"coins_name":"tlmCoins",
	"min_coin_value":0.001,
	"max_coin_value":1000000
	}

Quizas se añadan mas campos en el futuro

## Validar un coin ##

Comando status.php

**Peticion**

	https://@servidor/api/status.php ? coinid = <id>

Opcionalmente se puede indicar también el secreto del coin para verificar. En el caso de que se indique el secreto no se responderá la operación si el secreto no es válido

	https://@servidor/api/status.php ? coinid = <id> & auth= <secreto>

	Ejemplos:
	https://@servidor/api/status.php?coinid=2
	https://@servidor/api/status.php?coinid=2&auth=71238173689371


**Resultado**

Un JSON indicando el error o bien el valor del coin

Si existe el coin

	{
	"coinid":2,
	"value":1000
	}

Si no existe el coin

	{
	"error":"coin does not exist"
	}

	{
	"error":"coin does not exist or invalid secret"
	}


Si hay algun otro error el campo error indicara un texto con el error. La comprobacion puede ser si el campo error existe o no

## Transferir valor de un coin a otro ##

Comando transfer.php

**Peticion**

	https://@servidor/api/transfer.php? srcid= <id1> & dstid= <id2> & auth= <secreto> & value= <valor>
	<valor> puede ser un numero o all para indicar todo el valor
	
	Ejemplo:
	https://@servidor/api/transfer.php?srcid=17&dstid=43&auth=ahjsbdy1d189db&value=10
	https://@servidor/api/transfer.php?srcid=17&dstid=43&auth=ahjsbdy1d189db&value=all
	transfiere del coin 17 al 43 un valor de 10 o todo el valor


**Resultado**

Si hay un problema con los parametros devuelve un mensaje en el campo error. Si hay un fallo en la transferencia, por ejemplo porque el destino no existe o porque no hay suficiente dinero en el origen devuelve false en el campo ok

	{
	"error":"missing dstid"
	}
	
	{
	"ok":false,
	"msg":"transfer failed"
	}

Si la transferencia se realiza correctamente devuelve true en el campo ok y el valor transferido

	{
	"ok":true,
	"tvalue":3
	}

## Crear un nuevo coin ##

Comando new.php

**Peticion**

	https://@servidor/api/new.php? srcid= <id1> & auth= <secreto> & value= <valor>
	<valor> puede ser un numero o all para indicar todo el valor
	
	Ejemplo:
	https://@servidor/api/new.php?srcid=23&auth=ahjsbdy1d189db&value=10
	crea un nuevo coin con un valor de 10 extraido del coin 23
	https://@servidor/api/new.php?srcid=23&auth=ahjsbdy1d189db&value=all
	crea un nuevo coin con todo lo que hay en el coin 23 que quedara destruido


**Resultado**

Si hay un error con los parametros devuelve un mensaje de error. O si hay un problema al realizar la transferencia devuelve un codigo numerico en el campo problem y un mensaje de error en el campo msg. Se puede comprobar la presencia de los campos error o problem

	{
	"error":"missing value"
	}
	
	{
	"problem":1,
	"msg":"transfer failed, new coin deleted"
	}
	
Si la creacion es correcta devuelve la informacion del nuevo coin así como el valor que se le ha transferido.

	{
	"id":"38",
	"auth":"8a61144271e7e88a9137",
	"value":1
	}


