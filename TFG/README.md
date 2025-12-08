# 🌄 Gestión de Ofertas de Actividades (PHP + MySQL)

Aplicación web desarrollada con **PHP**, **MySQL** y **Bootstrap**, que permite la gestión de ofertas de actividades como excursiones, expediciones, talleres, etc., mediante distintos roles de usuarios y funcionalidades adaptadas a cada perfil.

Enlace a la aplicación: https://proyectointegralmanuelroldan.rf.gd/index.php

---

## 📌 Funcionalidades principales

### 🏠 Landing Page
- Muestra pública de todas las ofertas **visadas**.
- Filtros disponibles por:
  - Categoría
  - Fecha
  - Aforo

### 🔐 Sistema de autenticación por roles
- Acceso diferenciado según el tipo de usuario:
  - **Demandantes**
  - **Ofertantes**
  - **Gestores**
  - **Administradores**

---

## 👥 Roles de Usuario

### 🔹 Demandantes
- Pueden inscribirse en ofertas disponibles (siempre que no estén llenas).
- Consultan su historial de inscripciones.
- Filtran ofertas por criterios como aforo, fecha y categoría.

### 🔹 Ofertantes
- Crean nuevas ofertas mediante un formulario con los siguientes campos:
  - Nombre de la actividad
  - Aforo máximo
  - Fecha de realización (mínimo 15 días desde su publicación)
- Las ofertas creadas quedan **ocultas** hasta ser validadas por un gestor.

### 🔹 Gestores
- Revisan ofertas pendientes creadas por ofertantes.
- Pueden **visar o desvisar** ofertas según proceda.
- Solo las ofertas visadas se hacen públicas en la web.

### 🔹 Administradores
- Tienen acceso total al sistema.
- Pueden operar en "modo dios", asumiendo temporalmente el rol de cualquier otro usuario.
- Gestionan directamente la base de datos de usuarios:
  - Alta, baja o modificación de administradores, gestores, ofertantes y demandantes.

---

## 🧪 Credenciales de prueba

| Rol            | Email                    | Contraseña |
|----------------|--------------------------|------------|
| Demandante     | demandante1@email.com    | 12345      |
| Ofertante      | ofertante1@email.com     | 12345      |
| Gestor         | gestor@email.com         | 12345      |
| Administrador  | admin@email.com          | 12345      |

---

## 🛠️ Tecnologías utilizadas

- 🐘 **PHP** — Lógica del servidor y gestión de sesiones
- 🐬 **MySQL** — Almacenamiento y relaciones entre usuarios, roles y ofertas
- 🎨 **HTML5 + CSS3 + Bootstrap** — Estructura y diseño responsivo
- 📊 **SQL** — Consultas avanzadas y filtros personalizados
- ⚙️ **Git + GitHub** — Control de versiones y despliegue del código fuente
- ☁️ **FileZilla + InfinityFree** — Hosting gratuito para subir el proyecto a Internet
- 🖥️ **VirtualBox + Linux Mint (ISO ligera)** — Servidor de pruebas virtualizado
  - Configurado con **Apache**, **PHP**, **MySQL** y **phpMyAdmin**
  - Acceso desde Windows al servidor virtual vía IP local

---

## 💻 Entorno de pruebas virtualizado

Para simular un entorno real cliente-servidor se ha utilizado:

- **VirtualBox** con una ISO ligera de **Linux Mint**
- Configuración del entorno servidor completo en la máquina virtual:
  - 🧩 Apache + PHP
  - 🐬 MySQL
  - 🛠️ phpMyAdmin
- Acceso al servidor desde el navegador de Windows mediante la IP local
- Permite ejecutar el proyecto en un entorno **aislado** para mayor realismo y seguridad

---

## 🌍 Despliegue en Internet

El proyecto ha sido desplegado en un servidor gratuito:

- 🆓 Hosting: [InfinityFree](https://www.infinityfree.net/)
- 📁 Cliente FTP: [FileZilla](https://filezilla-project.org/) para subir los archivos
- ✅ Permite ver y probar el proyecto sin necesidad de configuración local
