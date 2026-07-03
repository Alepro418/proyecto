# Sistema de Gestión de Inventario y Ventas

> Trabajo de grado para optar al nivel académico de **Técnico Superior Universitario**

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![HTML](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)](https://developer.mozilla.org/es/docs/Web/HTML)
[![CSS](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)](https://developer.mozilla.org/es/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/es/docs/Web/JavaScript)

---

## 📋 Descripción

Este sistema web fue desarrollado como proyecto de grado para optar al título de **Técnico Superior Universitario**. Su propósito es facilitar la gestión integral de inventario, ventas y proveedores en un entorno comercial, proporcionando una interfaz intuitiva y funcionalidades clave para el control de stock, registro de transacciones y generación de reportes.

El proyecto está construido con tecnologías web estándar (HTML, CSS, JavaScript, PHP y MySQL) y sigue una arquitectura organizada que separa la lógica de negocio, la capa de presentación y el acceso a datos.

---

## ✨ Características

- **Autenticación de usuarios** – Inicio y cierre de sesión seguro.
- **Gestión de productos** – Alta, consulta y actualización de artículos.
- **Control de inventario** – Visualización de stock y alertas de productos con bajo inventario.
- **Registro de ventas** – Procesamiento de transacciones y generación de recibos.
- **Gestión de proveedores** – Administración de datos de proveedores y sus productos.
- **Reportes** – Generación de reportes de ventas, inventario y compras.
- **Respaldos** – Creación y restauración de copias de seguridad de la base de datos.
- **API interna** – Endpoints para consultas de productos, ventas, proveedores y más.

---

## 🛠️ Tecnologías utilizadas

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **PHP** | 7.4+ | Lógica del servidor y backend |
| **MySQL** | 5.7+ | Base de datos relacional |
| **HTML5** | - | Estructura de las vistas |
| **CSS3** | - | Estilos y diseño responsivo |
| **JavaScript** | ES6+ | Interactividad y peticiones asíncronas (AJAX) |
| **PlantUML** | - | Diagramas UML para documentación |

---

## 📁 Estructura del proyecto

proyecto/
├── api/ # Endpoints para consultas AJAX
│ ├── get_low_stock_products.php
│ ├── get_product_details.php
│ ├── get_products_data.php
│ ├── get_sale_data.php
│ ├── get_sales_details.php
│ ├── get_sale_reports.php
│ ├── get_shopping_data.php
│ ├── get_suppliers_products.php
│ ├── get_suppliers_data.php
│ ├── restock_products.php
│ └── php_errors.log
│
├── docs/ # Documentación del proyecto
│ ├── diagrams/ # Diagramas (casos de uso, secuencia, navegación)
│ ├── sources/ # Descripciones textuales de los módulos
│ └── uml/ # Diagramas UML en formato PlantUML
│
├── public/ # Frontend (carpeta pública)
│ ├── assets/ # Imágenes y recursos estáticos
│ ├── css/ # Hojas de estilo
│ ├── js/ # Scripts JavaScript
│ ├── about.php # Página "Acerca de"
│ ├── add_product.php # Alta de productos
│ ├── alarms.php # Alertas de stock bajo
│ ├── index.php # Página principal / dashboard
│ ├── inventory.php # Gestión de inventario
│ ├── reg_sale.php # Registro de ventas
│ ├── reports.php # Reportes
│ ├── sales.php # Historial de ventas
│ ├── shopping.php # Gestión de compras
│ ├── sign_in.html # Página de inicio de sesión
│ └── suppliers.php # Gestión de proveedores
│
├── src/ # Código fuente (lógica del negocio)
│ ├── auth/ # Autenticación
│ │ ├── login.php
│ │ └── logout.php
│ ├── core/ # Funcionalidades principales
│ │ ├── generate_receipt.php
│ │ ├── create_backup.php
│ │ ├── process_article.php
│ │ ├── process_sale.php
│ │ ├── process.php
│ │ └── restore_backup.php
│ └── db/ # Conexión a la base de datos
│ └── db_connect.php
│
└── template/ # Plantillas reutilizables
├── header.php
├── aside.php
└── footer.php


---

## 🚀 Instalación y configuración

Sigue estos pasos para poner el sistema en funcionamiento en tu entorno local:

### 1. Clonar el repositorio

bash

git clone https://github.com/Alepro418/proyecto.git
cd proyecto

2. Configurar el servidor web
Coloca la carpeta public/ como raíz del documento en tu servidor (Apache, Nginx o similar).
Asegúrate de que el módulo de PHP esté habilitado.

3. Configurar la base de datos
Crea una base de datos MySQL (por ejemplo, proyecto_db).

Importa el script SQL de creación de tablas (si no está incluido, deberás generarlo a partir del modelo de datos).

Configura la conexión en src/db/db_connect.php con tus credenciales:

$host = 'localhost';
$user = 'tu_usuario';
$password = 'tu_contraseña';
$database = 'proyecto_db';

4. Permisos de archivos
Asegúrate de que las carpetas logs/ y pdf/ tengan permisos de escritura para el servidor web.

5. Acceder al sistema
Abre tu navegador y visita http://localhost/ (o la URL que hayas configurado).
Inicia sesión con las credenciales de usuario registradas en la base de datos.

📖 Uso
Dashboard (index.php) – Resumen del estado del inventario y actividad reciente.

Inventario (inventory.php) – Consulta y gestión de productos.

Registrar venta (reg_sale.php) – Procesa una venta y genera recibo en PDF.

Proveedores (suppliers.php) – Administra la información de proveedores.

Reportes (reports.php) – Visualiza y filtra reportes de ventas, compras e inventario.

Alertas (alarms.php) – Muestra productos con stock por debajo del mínimo.

Contribuciones

Este proyecto fue desarrollado con fines académicos. Si deseas contribuir o mejorar el sistema, eres bienvenido a realizar un fork y enviar un pull request con tus propuestas.

Licencia

Este proyecto es de código abierto y se distribuye bajo la licencia MIT. Si utilizas este código, agradeceremos que menciones la fuente original.

 Autor
Alepro418 – GitHub

Proyecto realizado como trabajo de grado para optar al título de Técnico Superior Universitario.

Contacto
Si tienes preguntas o sugerencias, no dudes en abrir un issue en el repositorio o contactar al autor.

¡Gracias por visitar el proyecto!
