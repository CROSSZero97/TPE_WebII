# TPE Web II
 
**Integrantes :** Facundo Agustin Figueroa Martin (facu.agus.figueroa@gmail.com)

**Tematica :** Paginas de Pizzerias

**Descripción :** Se plane que sea un sitio web en el cual puedas encontre entre las distintas pizzas que se ofrecen la pizzeria que tiene como especialidad esa pizza, tambien consultar el precio de esta, y la espera que hay en la pizzeria en razon de que tantas personas hay antes de ti en el pedido

**Cambios Pagina :** Los ultimos cambios fueron crear toda la pagina desde 0 y crear una nueva tabla la cual contenga usuarios, el funcionamiento de administrador es colocarle la variable de admin en 1, ya que viene seteada en 0, el admin puede modificar y eliminar o agragar pizzerias, y como usuario normal podes enlistarlas a gusto y por nombre, espera o precio de las pizzas especiales, se soluciono el problemas de los isset, de las carga y modificacion de las tablas, y el SQL se mantuvo al ya ser 1 a N, ya que una pizza especial Tabla A puede estas enlazada con varios Locales o Tabla B, osea que para pada fila de la tabla A puede haber muchas o cero filas de la Tabla B que la usea, entonces Tabla A (pizzas especiales) = 1 a Tabla B (locales ) = N

**API Restful :** la Api cumple varias peticiones la cual dejo anotado aca con su respectivo commando
GET .../api/locales : Enlista los locales
GET .../api/locales/{id} : Te muestra el local por id
POST .../auth/login : Te da el token para poder modificar y eliminar, la clave es la misma que la de la pagina web, usurio: webadmin contraseña: admin
PUT .../api/locales : Agrega local si estas autorizado y tenes que pasarlo en JSON en el body
DEL .../api/locales/{id} : Elimina el local dado por id
GET .../api/locales/order/lclespera o lclnombre/desc o asc : Te enlista los locales segun el orden que le hayas dado lclespera: es la espera, lclnombre: es el nombre del local / y desc: descendiente, asc: ascendente

**DER :** 
[DER](DER.png)

**SQL :** [SQL](webii_tpe.sql)



