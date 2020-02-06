# mCoin #

Moneda electronica centralizada para uso docente en SWS

La idea es que se pueda usar para hacer comercio electronico como webapi

Seria un webservice centralizado con un unico servidor

Los coins estan identificados por un identificador largo. Es dificil predecir un identificador largo de monedero aunque la seguridad no depende de no saber los monederos. Por ejemplo al pagar a alguien averiguas un id de su monedero

Hay una credencial de autentificacionde cada monedero. El tenerla permite hacer operaciones sobre el monedero dar dinero a otro. En principio recibir no requiere credencial cualqueira te puede enviar dinero sin saber de quien. Esto se podria complicar si interesa

Se pueden hacer diferentes credenciales para operar sobre un monedero por ejemplo una para consultar el saldo pero una diferente para dar dinero. Pero por simplicidad empezaremos por uno o dos

Un monedero sera el conjunto de un identificador, una credencial (clave simetrica) o varias para diferentes cosas, y el valor de dinero almacenado en el monedero

Un servidor centralizado tendra almacenada esa informacion para todo el mundo. Las operaciones seran cliente-servidor contra ese servidor

Para poder hacer pagos hace falta una forma de que un receptor pueda confirmar una transaccion antes de dar un servicio. No puedes darle el secreto de tu coin porque entonces te podria hacer transferencias no autorizadas. Hay varias posibilidades (de momento estaba suponiendo la primera)

* El pagador crea un nuevo coin con el dinero y le da al receptor el coin con su id y su secreto. El receptor no deberia darte el servicio hasta que haya transferido el coin. De lo contrario podia volverlo a gastar antes
	- Para esto hace falta un formato de transferencia de id+secreto. Pero es simple
	- Se podria hacer que se puedan emitir billetes que sean un qr o una cadena base64
* El que ordena una transferencia puede introducir una etiqueta en la transferencia y las transferencias quedan registradas al menos un tiempo. El receptor pide al emisor que etiquete la transferencia con un concepto que identifique a ese pago y luego comprueba que existe una transferencia correcta con ese tag (tiene que verificar que el es el destino y que el tag es el que pidio para evitar un pago para varias compras)
	- Para esto hace falta una tabla de transacciones separadas y api para buscar una por tag
* Se puede crear pagos pendientes. El receptor ordena un pago contra el id del monedero que le ha dado el pagador. El pagador tiene que confirmar el pago en otra pagina.
	- Para esto hace falta una tabla de pagos pendientes y que se puedan crear y cancelar.
	- Y hace falta que caduquen solos al tiempo o algo para que no se acumulen


La autentificacion del servidor se hara por ssl con clave publica. Por ejemplo asocindola al nombre de dominio con letsencrypt

## Operaciones ##

### Info ###

El webservice REST en

	https://coin.domain/api/index.php

info en html

	https://coin.domain/api/info.php

info json versiondel protocolo

	{ "title": nombre comun de la moneda , "name": nombre,  "version": version }
	

### Crear moneda ###

	https://coin.domani/api/new.php ? e=xxx
    
    e - entropia para la generacion de la clave
	
devuelve un monedero vacio con sus claves

	{ "coinid" :xxxx , "auth": aaaa }
	
Se puede hacer que no se soporta crear billetes de cero y para crear uno nuevo hay que ponerle algo de dinero de otro monedero

los parametros serian 
e 
srcid auth value
	

### Status monedero ###

	https://coin.domain/api/status.php?coinid=xxxx
	
devuelve el estado del monedero incluido el saldo en json, no modifica el estado

	{ "coinid": xxx, "value": 3.2 }

el contenido del monedero es publico o queremos autentificacion para consulta? o que lo pueda elegir el propietario???


### Pago a otro monedero ##

	https://coin.domain/api/transfer.php? srcid=xxx & auth=aaa & dstid=yyy & value=v
	value = all transfiere todo el monedero a uno nuevo destruyendo el anterior

transfiere el valor. Hay que decir la clave del source
si no hay dinero suficiente se cancela toda la operacion
no se requeiere auth del destino
podriamos hacer que sea con POST asi ademas exige usar post a los alumnos

El resultado puede implicar la destruccion del monedero source si se queda sin dinero

	{ "result": 1/0, "remaining": value, "destroyed": 1/0 } 

	{ "result": 1, "remaining": value, "destroyed": 1/0 }  se ha realizado el pago y el source ha sido destruido o no
	
	{ "result": 0, "errcode":  "errtext":  }
	
	code 1   text: auth error
	code 2   text: not enough money
	code 3   text: destino unexitent
no vamos a especificar
	solo parameter error o transfer failed

## Extension ##

Lo anterior es lo minimo basico. Se pueden añadir funcionalidades
Asociar identidad de alumnos y cuentas
Hacer operaciones complicadas de tipo comprueba y bloquea dinero

## Gestion de usuarios con monederos ##

Darles usuarios individuales que cada usuario tenga un monedero
Esto no sea un webapi sino una pagina web
Con sesiones








