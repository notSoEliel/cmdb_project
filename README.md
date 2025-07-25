# 💻 CMDB System

## Sistema de Gestión de Activos y Configuración

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-Academic-blue?style=flat-square)](https://github.com)

> Este proyecto es la culminación del curso **Desarrollo VII (PHP)**, impartido por la **Profesora Irina Fong**, en la **Universidad Tecnológica de Panamá**.

---

## 🚀 Inicio Rápido del Proyecto

Sigue estos pasos para tener el CMDB System funcionando en tu entorno local.

### 📋 Prerrequisitos

Asegúrate de tener instalado lo siguiente:

- **Servidor Web**: Apache o Nginx
- **PHP**: Versión 8.1 o superior
- **Base de Datos**: MySQL o MariaDB
- **Composer**: Para la gestión de dependencias de PHP

---

### 1️⃣ Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/tu-repo.git
cd tu-repo-cmdb
```

> **Nota**: Reemplaza `https://github.com/tu-usuario/tu-repo.git` con la URL real de tu repositorio.

### 2️⃣ Instalar Dependencias de Composer

El proyecto utiliza Composer para gestionar sus dependencias (como PhpSpreadsheet y Endroid QR Code).

```bash
composer install
```

Esto creará la carpeta `vendor/` y el archivo `autoload.php` necesarios.

### 3️⃣ Configurar la Base de Datos

#### a. Crear la Base de Datos

Crea una base de datos MySQL/MariaDB con el nombre `cmdb_php_db2`.

#### b. Configurar Conexión

Abre el archivo `config/database.php` y ajusta las credenciales de tu base de datos:

```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cmdb_php_db2');
define('DB_USER', 'tu_usuario_db'); // Ej: root
define('DB_PASS', 'tu_contraseña_db'); // Ej: tu_contraseña_root
define('DB_CHARSET', 'utf8mb4');
```

#### c. Importar Esquema y Datos Iniciales

Utiliza el script SQL completo para crear las tablas y poblar la base de datos con datos de prueba.

1. Abre tu herramienta de base de datos (phpMyAdmin, MySQL Workbench, etc.)
2. Selecciona la base de datos `cmdb_php_db2`
3. Ejecuta el contenido del archivo `public/dumps/cmdb_php_db2.sql`

> **💡 Tip**: Este script ya incluye toda la estructura de tablas, categorías, inventario, asignaciones, necesidades, usuarios administradores, colaboradores e imágenes de ejemplo. Es un dump completo listo para usar.

### 4️⃣ Configurar URL Base de la Aplicación

Abre el archivo `config/app.php` y configura la `BASE_URL`:

```php
// config/app.php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', "{$protocol}://{$host}{$script_path}/");

// O si prefieres una IP fija para pruebas en red local (descomenta y ajusta):
// define('TU_IP_LOCAL', '192.168.1.15'); // Reemplaza con tu IP local
// define('BASE_URL', 'http://'.TU_IP_LOCAL.'/nombre_carpeta_proyecto_en_htdocs/public/');
```

### 5️⃣ Configurar Permisos de Archivos

Asegúrate de que la carpeta `public/uploads/` y sus subcarpetas (`inventario/`, `colaboradores/`) tengan permisos de escritura para el usuario de tu servidor web (ej. `www-data` en Linux, `daemon` en macOS).

```bash
sudo chown -R tu_usuario_servidor_web:tu_grupo_servidor_web public/uploads
sudo find public/uploads -type d -exec chmod 775 {} +
sudo find public/uploads -type f -exec chmod 664 {} +
```

> **Nota**: Reemplaza `tu_usuario_servidor_web` y `tu_grupo_servidor_web` con los valores correctos de tu sistema, ej. `daemon:daemon` para macOS.

### 6️⃣ Acceder a la Aplicación

Una vez configurado, accede a la aplicación desde tu navegador:

**URL**: `http://localhost/tu_ruta_proyecto/public/`

**Credenciales por defecto**:

- **Administrador**: `admin@cmdb.com` / `admin123`
- **Colaborador**: `juan.perez@example.com` / `colaborador123` (y otros emails generados)

---

## 💡 Sobre el Proyecto

El **CMDB System** es un sistema de gestión de activos y configuración desarrollado como proyecto final del curso de **Desarrollo VII (PHP)**. Su objetivo principal es centralizar y optimizar la administración del inventario de equipos (hardware y software) y la gestión de solicitudes dentro de una organización.

### ⭐ Características Principales

#### 🏢 Gestión de Inventario Completa

- **Registro detallado de equipos**: nombre, marca, modelo, serie, costo, fecha de ingreso, depreciación
- **Clasificación por categorías** para mejor organización
- **Manejo de estados del equipo**: En Stock, Asignado, En Reparación, Dañado, En Descarte, Donado
- **Creación Unificada**: Formulario único para añadir equipos individualmente o por lotes
- **Control de Series**: Validación de unicidad de números de serie con prefijos y números incrementales
- **Gestión de Imágenes**: Asociación de múltiples imágenes por equipo con selección de miniatura principal
- **Códigos QR**: Generación automática para acceso rápido a detalles públicos del equipo
- **Trazabilidad**: Registro completo de asignaciones y devoluciones
- **Vistas Especializadas**: Listados dedicados para equipos Donados y Descartados
- **Control de Vida Útil**: Visualización de equipos Expirados y Por Expirar por depreciación

#### 👥 Gestión de Colaboradores

- **Registro de información detallada** de los colaboradores
- **Asignación y desasignación** de equipos de forma eficiente
- **Portal de Colaborador** para autogestión de información personal

#### 📋 Gestión de Solicitudes

- **Colaboradores pueden crear y gestionar** sus solicitudes de equipos/software
- **Administradores pueden revisar** y cambiar el estado de las solicitudes
- **Estados disponibles**: Solicitado, Aprobado, Rechazado, Completado

#### 🔐 Roles de Usuario (Autenticación y Autorización)

- **👨‍💼 Administrador**: Acceso completo a la gestión de inventario, colaboradores, categorías, usuarios admin, solicitudes y reportes
- **👤 Colaborador**: Acceso a su portal personal, equipos asignados, gestión de solicitudes y perfil
- **Sistema de autenticación robusto** con manejo de sesiones y restablecimiento de contraseña

#### 📊 Reportes y Análisis

- **Dashboard interactivo** para administradores con KPIs clave
- **Gráficos de distribución** de inventario y estado de solicitudes
- **Reportes exportables a Excel**: resumen por categoría, asignaciones activas, detalle por categoría

#### 🎨 Interfaz de Usuario

- **Diseño responsivo** con Bootstrap 5
- **Validación de formularios** del lado del cliente con jQuery Validate
- **Notificaciones interactivas** con SweetAlert2
- **Experiencia de usuario optimizada** para todas las pantallas

---

## 🏗️ Estructura del Proyecto

El proyecto sigue una arquitectura **MVC (Modelo-Vista-Controlador)** simplificada para PHP, organizada de la siguiente manera:

```text
.
├── 📁 config/                  # Archivos de configuración de la aplicación y DB
│   ├── app.php              # Configuración de URL base
│   ├── database.php         # Credenciales de la base de datos
│   └── validation_rules_jquery.php # Reglas de validación para jQuery Validate
├── 📁 public/                  # Archivos accesibles públicamente (punto de entrada)
│   ├── 📁 assets/              # Imágenes por defecto, etc.
│   │   ├── default-avatar.png
│   │   └── placeholder.png
│   ├── 📁 css/                 # Hojas de estilo CSS
│   │   └── style.css
│   ├── 📁 dumps/               # Scripts SQL para inicializar la base de datos
│   │   └── cmdb_php_db2.sql # Script completo: estructura y datos
│   ├── 📁 js/                  # Archivos JavaScript
│   │   └── app.js           # Lógica JS principal y validaciones
│   ├── 📁 uploads/             # Directorio para archivos subidos
│   │   ├── colaboradores/
│   │   └── inventario/
│   └── index.php            # Punto de entrada de la aplicación (router)
├── 📁 src/                     # Código fuente de la aplicación
│   ├── 📁 Controllers/         # Lógica de negocio y manejo de solicitudes
│   │   ├── AdminProfileController.php
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── CategoriaController.php
│   │   ├── ColaboradorController.php
│   │   ├── DashboardController.php
│   │   ├── InventarioController.php
│   │   ├── NecesidadController.php
│   │   ├── PortalController.php
│   │   ├── PublicController.php
│   │   ├── ReporteController.php
│   │   └── UsuarioController.php
│   ├── 📁 Core/                # Componentes centrales del framework
│   │   ├── AuthService.php  # Manejo de autenticación y autorización
│   │   ├── Database.php     # Conexión a la base de datos (Singleton)
│   │   ├── Helpers.php      # Funciones de ayuda globales
│   │   └── ValidationService.php # Servicio para generar scripts de validación
│   ├── 📁 Models/              # Lógica de interacción con la base de datos
│   │   ├── Asignacion.php
│   │   ├── BaseModel.php    # Modelo base con CRUD genérico
│   │   ├── Categoria.php
│   │   ├── Colaborador.php
│   │   ├── HistorialLogin.php
│   │   ├── Inventario.php
│   │   ├── InventarioImagen.php
│   │   ├── Necesidad.php
│   │   ├── PasswordReset.php
│   │   └── Usuario.php
│   └── 📁 Views/               # Plantillas HTML/PHP para la interfaz de usuario
│       ├── 📁 admin/
│       │   ├── necesidades/
│       │   ├── profile/
│       │   ├── reportes/
│       │   └── usuarios/
│       ├── 📁 auth/
│       ├── 📁 categorias/
│       ├── 📁 colaboradores/
│       ├── 📁 dashboard/
│       ├── 📁 inventario/
│       │   ├── add_edit.php
│       │   ├── asignar.php
│       │   ├── donados.php
│       │   ├── descartados.php # Nueva vista
│       │   ├── imagenes.php
│       │   └── index.php
│       ├── 📁 portal/
│       ├── 📁 public/
│       ├── 📁 partials/          # Componentes de vista reutilizables
│       │   ├── dynamic_table.php
│       │   ├── notes_modal.php # Nuevo parcial para el modal de notas
│       │   └── sidebar.php
│       ├── error-403.php
│       ├── error-404.php
│       ├── error-duplicate.php
│       ├── error.php
│       └── layout.php         # Plantilla principal de la aplicación
├── 📁 vendor/                  # Dependencias de Composer
├── composer.json            # Definición de dependencias de Composer
├── composer.lock            # Bloqueo de versiones de dependencias
└── .gitignore               # Archivos y directorios a ignorar por Git
```

---

## 👥 Equipo de Desarrollo

Este proyecto ha sido desarrollado por un talentoso equipo de estudiantes de la **Universidad Tecnológica de Panamá**:

### 👨‍💻 Eliel García

- **Cédula**: 8-990-1192
- **Email**: [bgt.eliel@gmail.com](mailto:bgt.eliel@gmail.com)
- **Email UTP**: [eliel.garcia1@utp.ac.pa](mailto:eliel.garcia1@utp.ac.pa)

### 👩‍💻 Angélica Rodríguez

- **Cédula**: 2-751-41
- **Email**: [rodriguezq.angelicac@gmail.com](mailto:rodriguezq.angelicac@gmail.com)
- **Email UTP**: [angelica.rodriguez7@utp.ac.pa](mailto:angelica.rodriguez7@utp.ac.pa)

### 👩‍💻 Ericka Atencio

- **Cédula**: 8-1018-73
- **Email**: [atencio.ericka@hotmail.com](mailto:atencio.ericka@hotmail.com)
- **Email UTP**: [ericka.atencio@utp.ac.pa](mailto:ericka.atencio@utp.ac.pa)

---

**🎓 Universidad Tecnológica de Panamá**  
**📚 Desarrollo VII (PHP)**  
**👩‍🏫 Profesora: Irina Fong**

---

**Desarrollado con ❤️ por estudiantes de la UTP**
