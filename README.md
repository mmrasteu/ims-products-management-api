# IMS (Inventory Management System) - IMS Products Management

IMS es un sistema de gestión de inventario construido con microservicios en Laravel.
Esta API llamada IMS Products Management contiene la lógica de la gestión de productos.
Junto al resto de APIs se creará el ecosistema de gestion de invetario IMS

## Uso

IMS proporciona una varias API RESTful para gestionar inventarios. Puedes acceder a las API a través de una API Gateway.

## Endpoints

Los endpoints disponibles en IMS Products Management son los siguientes:

### Productos

-   `GET /products`: Obtener todos los productos.
-   `GET /products/{id}`: Obtener un producto por su ID.
-   `POST /products`: Crear un nuevo producto.
-   `PUT /products/{id}`: Actualizar un producto existente.
-   `PATCH /products/{id}`: Actualizar parcialmente un producto.
-   `DELETE /products/{id}`: Eliminar un producto.

### Categorías

-   `GET /categories`: Obtener todas las categorías.
-   `GET /categories/tree`: Obtener todas las categorías con su estructura de árbol.
-   `GET /categories/{id}`: Obtener una categoría por su ID.
-   `GET /categories/tree/{id}`: Obtener una categoría con su estructura de árbol.
-   `POST /categories`: Crear una nueva categoría.
-   `PUT /categories/{id}`: Actualizar una categoría existente.
-   `PATCH /categories/{id}`: Actualizar parcialmente una categoría.
-   `DELETE /categories/{id}`: Eliminar una categoría.

### Proveedores

-   `GET /supplier`: Obtener todos los proveedores.
-   `GET /supplier/{id}`: Obtener un proveedor por su ID.
-   `POST /supplier`: Crear un nuevo proveedor.
-   `PUT /supplier/{id}`: Actualizar un proveedor existente.
-   `PATCH /supplier/{id}`: Actualizar parcialmente un proveedor.
-   `DELETE /supplier/{id}`: Eliminar un proveedor.

## Licencia

IMS es un proyecto de código abierto sin una licencia específica.

## Autoría

Miguel Ángel Magrañal Rasteu
