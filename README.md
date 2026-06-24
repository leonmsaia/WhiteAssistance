# WhiteAssistance

Plataforma de telemedicina dockerizable para gestión de citas médicas, consultas remotas por videollamada, sala de espera, triage, validación de pagos, notas médicas y administración.

**Development is supported only through Docker.**

El archivo `.env` en la raíz del repositorio es la única fuente de verdad de configuración. El archivo `01-app/.env` es un stub local; en contenedor se monta el `.env` raíz.

## Objetivo del proyecto

Construir un **monolito modular** en Laravel, preparado para una futura extracción a microservicios, que cubra el flujo completo de teleconsulta:

- Autenticación y roles
- Pacientes, especialistas y especialidades
- Citas, triage y sala de espera
- Pagos y validación
- Teleconsulta y salas de video (Jitsi)
- Historia clínica y notas médicas
- Notificaciones y panel de administración

## Estructura de carpetas

```
whiteassistance/
├── 01-app/              # Aplicación Laravel + Dockerfile
│   └── .docker/         # Configuración PHP y nginx para Docker
├── 02-db/
│   ├── init/            # Scripts de inicialización de base de datos
│   └── backups/         # Respaldos de base de datos
├── 03-jitsi/            # Configuración y despliegue de Jitsi (futuro)
├── docker-compose.yml   # Orquestación Docker
├── .env.example         # Variables de referencia
└── README.md            # Este archivo
```

### Convenciones

| Carpeta    | Contenido |
|------------|-----------|
| `01-app/`  | Toda la aplicación Laravel. No hay código Laravel en la raíz. |
| `02-db/`   | Scripts SQL, respaldos y documentación de base de datos. |
| `03-jitsi/`| Archivos de configuración y despliegue de Jitsi, separados de la app. |

## Servicios Docker

| Servicio  | Descripción                           | Puerto (host)   |
|-----------|---------------------------------------|-----------------|
| `app`     | PHP 8.3-FPM + Laravel                 | —               |
| `nginx`   | Reverse proxy → `01-app/public`       | 8080            |
| `mysql`   | Base de datos MySQL 8                 | 3306            |
| `redis`   | Cache, sesiones y colas               | 6379            |
| `mailpit` | SMTP local + UI web de correo         | 1025 / **8025** |

Volúmenes nombrados: `mysql_data`, `redis_data`.

### Paquetes instalados sin uso activo en el MVP

- **Laravel Sanctum** — Reserved for future mobile API support
- **Redis** — Reserved for queues and async notifications

## Puesta en marcha (Docker)

### 1. Preparar entorno

```powershell
cd e:\docker\whiteassistance
Copy-Item .env.example .env
```

Editar `.env` raíz según sea necesario. No copiar ni completar `01-app/.env` para desarrollo.

### 2. Construir y levantar

```powershell
docker compose build
docker compose up -d
```

Al iniciar, el servicio `app` ejecuta `php artisan storage:link` solo si `public/storage` no existe.

### 3. Inicializar aplicación

```powershell
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### 4. Verificar

| Recurso    | URL                        |
|------------|----------------------------|
| Aplicación | http://localhost:8080      |
| Mailpit UI | http://localhost:8025      |

```powershell
docker compose ps
docker compose logs app
docker compose exec app php artisan test
```

## Current MVP Features

- Registration
- Appointments
- Teleconsultation
- Jitsi
- Consultation notes
- Transactional emails

## Deferred Modules

- Payments
- WaitingRoom
- Triage
- ClinicalRecords

## Módulos previstos (Laravel)

La aplicación se organizará como monolito modular bajo `01-app/app/Modules/`:

- Authentication
- Users
- Patients
- Specialists
- Specialties
- Appointments
- Payments
- Triage
- WaitingRoom
- Teleconsultation
- VideoRooms
- MedicalRecords
- MedicalNotes
- Administration
- Notifications

## Licencia

Por definir.
