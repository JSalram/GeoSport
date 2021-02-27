# GeoSport

GeoSport es una aplicación web desarrollada como proyecto final del grado superior DAW, cuyo propósito es el de 
proporcionar una herramienta potencialmente útil a los amantes del skate y del surf principalmente.

**Contenidos**
- [Descripción](#descripción)
- [Objetivo](#objetivo)
- [Funciones](#funciones)
- [Tecnologías utilizadas](#tecnologías-utilizadas)
- [Desarrollo](#desarrollo)


## Descripción

El proyecto consiste en una aplicación para deportistas y aficionados a los deportes con tabla principalmente: skate y surf.
Cubre algo inexistente actualmente en internet, y que le resultará muy útil a éste tipo de público.

Se trata de una web que recogerá todas las zonas habilitadas y/o posibles en las que practicar estos deportes, 
así como su valoración correspondiente, tablón de opiniones, foros donde poder conversar y mucho más...

Además, serán los propios usuarios quienes aporten toda la información de la web, de forma que no quedará ningún rincón escondido donde
practicar su deporte favorito.


## Objetivo

El principal objetivo de la aplicación es el de registrar todas aquellas zonas donde poder patinar y surfear, para que aquellos
que practiquen este deporte puedan conocer sitios nuevos y dar a conocer otros más.

A parte de ayudar a conocer nuevos lugares, también permitirá conocer las opiniones de los usuarios al igual que su valoración, pudiendo
clasificar de esta manera las mejores zonas.

Mediante el foro que les proporcionará la web, los usuarios también podrán crear eventos para los aficionados, donde poder conocerse
y disfrutar juntos, así como crear competiciones, discusiones y mucho más.

La información contenida en la página será proporcionada por los propios usuarios, por lo que ellos mismos serán quienes hagan
crecer la web.


## Funciones

Las funciones que realizará la aplicación, en orden de relevancia, son las siguientes:
1. Registrar una ubicación y sus coordenadas.
2. Valorar cada zona *(por usuario)* y representar su media.
3. Buscar todos los puntos registrados en una zona y mostrar en orden de valoración.
4. Comentar *(opcional)* opiniones junto a su valoración correspondiente.
5. Representar todas las zonas registradas en un mismo mapa interactivo.
6. Crear temas en un foro relacionado y participar en ellos.

Todo esto teniendo siempre en cuenta la distinción entre ambas disciplinas, es decir, cada deporte individualmente soportará todas estas funciones.


## Tecnologías utilizadas

**Frontend:**
- HTML.
- CSS.
- JavaScript.

**Backend:**
- PHP.
- SQL (MariaDB).
- HeidiSQL (Cliente).

**Herramientas y Frameworks:**
- Bootstrap 4.
- Sass.
- Symfony.
- React.
- Leaflet (OpenStreetMap).

**Progressive Web AppO (PWA):**
- Node.js.
- Cordova / React Native / Ionic.


## Desarrollo

El desarrollo constará de cuatro partes fundamentales, las cuales se dividirán a su vez en varias fases.

#### Diseño
1. Idear la página web y sus funcionalidades detalladamente y documentarlas.
2. Diseñar los layouts / wireframes de las páginas necesarias.
3. Escoger los estilos y colores con los que personalizar la web.

**Frontend**
1. Maquetar las páginas a partir el diseño previamente realizado.
2. Implementar los estilos y colores necesarios.
3. Añadir lógica a la web: efectos y animaciones.

**Backend**
1. Idear y realizar modelo relacional de la base de datos.
2. Diseñar y crear la base de datos a partir de su modelo.
3. Crear páginas de registro e inicio de sesión.
4. Implementar registros de zonas y su lógica consecuente.
5. Establecer el foro de la web y su funcionalidad.

**RWD y PWA**
1. Retocar la web para que sea adaptable a todo tipo de dispositivos.
2. Desarrollar la aplicación progresiva para iOS y Android.

Para finalizar, se llevarán a cabo las pruebas necesarias en la aplicación para comprobar que todo funciona correctamente,
se tratarán los errores que se encuentren y se perfilarán aquellos aspectos que se crean correspondientes para presentar la
aplicación en su perfecto estado.

Por último, pero no menos importante, la web se podrá ubicar en un hosting para que todo el mundo tenga acceso a ella
esté donde esté, así como de manera opcional encontraremos las apps en su tiendas correspondientes (Play Store y Apple Store).
