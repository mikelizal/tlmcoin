# Moneda digital @nombre #

Es una moneda digital centralizada para uso educativo en asignaturas de servicios web.

Es una moneda que requiere un servidor central para verificar el valor y realizar pagos y transferencias. Esta es la pégina de ese servidor central. La idea es que con esta moneda se puedan construir sistemas de pago en páginas web, bancos, mercados, juegos sin utilizar dinero de verdad. Esta moneda no debería ser aceptada en el mundo real.

El servidor central garantiza que no se puede crear dinero nuevo solo pasar de unas monedas a otras, y permite verificar si una moneda que te han pagado tiene o no valor y cuanto.

## Las monedas o @coins ##

Un @coin sería como una moneda es un token pequeño que puedes tener y tiene un valor. Se escribirá como un número (que llamaremos id o coinid) y una cadena de números y letras (que llamaremos auth o secreto) separados por un guión. 
También se acepta escribirlo como el número seguido de XXX para no mostrar el secreto. 
Y tambien se acepta añadir otro guión seguido del valor del @coin. Pero este valor es simplemente una anotación, el valor real es el que este almacenado en el servidor.

	57-e1ff0a202a3a8082d240
	57-e1ff0a202a3a8082d240-7.0
	57-XXX
	57-XXX-7.0

Las primeras dos son el @coin completo. Si tienes esa información puedes gastar el @coin, transferirlo o usarlo. Las dos ultimas son la forma pública del @coin permiten comprobar que esa moneda existe y que valor tiene pero no te permite usarla.

El tener un @coin significa 

* que sabes el nombre completo por lo que la puedes gastar.
* que eres el único que sabe el nombre completo por lo que no puede gastarlo otro sin que te des cuenta.

Conocer un coin significa

* que sabes el nombre público con lo que puedes comprobar si existe y que valor tiene, o puedes también transferir valor desde otros @coins

El servidor mantiene una lista de todas los @coins que existen. Los coins pueden crearse y destruirse pero el valor total se mantiene. Puede pasarse valor de unos @coins a otros.

## Operaciones ##

Se pueden hacer tres operaciones basicas con los @coins (aunque más bien son dos)

### Verificar ###

Esta lo puede hacer todo el mundo que conozca el nombre público de un @coin. Con el nombre publico el servidor indicará si existe y que valor hay en ese @coin. 

	Verificar( 57-e1ff0a202a3a8082d240 )
		-> si existe y vale 7.0
	
	Verificar( 57-XXX )
		-> si existe y vale 7.0
	
	Verificar( 38-XXX )
		-> no existe ese @coin

Verificar un coin es una operacion efímera. Te dice que en el momento de la verificación tenía ese valor. Pero salvo que sepas que eres el unico conocedor del secreto no estás seguro de que un segundo despues alguien lo transfirió y le quito una parte del valor o incluso le quitó todo el valor y dejó de existir.

### Transferir ###

Conociendo el nombre completo de un @coin (que llamaremos origen) se puede transferir parte del valor almacenado a un @coin ya existente (que llamaremos destino). Hay que indicar el valor a transferir. El servidor quitara el valor indicado del @coin origen y lo pasara al @coin destino.

	Verificar( 57-XXX )
		-> si existe y vale 7.0
	Verificar( 12-XXX )
		-> si existe y vale 3.0
	 
	Transferir( 57-e1ff0a202a3a8082d240 , 12-XXX , 5.0 )
		-> ok transferidos

	Verificar( 57-XXX )
		-> si existe y vale 2.0
	Verificar( 12-XXX )
		-> si existe y vale 8.0

Para que la transferencia funcione el secreto del origen tiene que ser el correcto y no hace falta el secreto del destino.

Solo se puede transferir el valor solicitado o nada. Si en el origen no hay suficiente valor no se aceptará la transferencia. Si el destino no existe no se aceptara la transferencia.

Si se transfiere todo el valor de un origen a un destino el origen dejara de existir y se destruye el coin. Se puede pedir la transferen de todo el valor indicando valor=all

### Crear ###

Se puede crear un nuevo @coin, pero no se acepta la existencia de @coins con valor 0 por lo que para crear hace falta indicar un @coin origen del que transferir algo de valor. El @coin origen debe indicarse con el nombre completo para permitir extraer valor.

	Verificar( 57-XXX )
		-> si existe y vale 2.0	
	
	Crear( 57-e1ff0a202a3a8082d240 , 0.5 )
		-> nuevo @coin 103-08a5c311c5da29a93df4

	Verificar( 103-XXX )
		-> si existe y vale 0.5	
	Verificar( 57-XXX )
		-> si existe y vale 1.5	

	Crear( 57-e1ff0a202a3a8082d240 , all )
		-> nuevo coin 107-f034588ff34c90efed87
	
	Verificar( 107-XXX )
		-> si existe y vale 1.5	
	Verificar( 57-XXX )
		-> no existe ese @coin

Igual que en la transferencia si el @coin origen se queda sin valor es destruido.
En realidad la creacion es practicamente igual que la transferencia solo que se le permite hacer nuevos @coins


## Servidor de @nombre ##

El servidor central esta en la dirección 

https://@servidor

Asegurese de utilizar la pagina segura https.
En la pagina web un usuario puede hacer las operaciones básicas con los @coins. 

* Puede verificar @coins que conozca.
* Puede transferir valor de unos @coins a otros.
* Puede crear nuevos @coins. Por ejemplo si no esta seguro de ser el único que conoce el nombre se puede usar un @coin para crear otro nuevo con todo el valor destruyendo en el primero. A eso lo llamaremos cobrar el @coin.

Pero recuerde **MUY IMPORTANTE** el sistema guarda la existencia del @coin y su valor pero no guarda a quién pertence. La pertenencia es conocer el nombre. Asi que cada usuario es responsable de recordar el nombre del @coin. Puede anotarlo en su ordenador bien guarado, cifrarlo o lo que sea, pero no pierda el nombre. Perder el nombre seria equivalente a destruir un billete real. El billete existe, tiene un numero de identificacion, pero nadie puede volver a usarlo.

El seridor proporciona tambien un API web en la direccion

https://@servidor/api/

Para que un programa pueda pedir la realización de las operaciones básicas. Con este API se tratará de construir aplicaciones que puedan aceptar pagos, almacenar @coins, hacer bancos que almacenen o presten @coins ...
