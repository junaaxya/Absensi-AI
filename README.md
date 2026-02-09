# Absensi Face Recognition

Attendance system using face recognition technology with Laravel and Python-based AI microservice.

## Features

- 🔐 User authentication and role-based access
- 📸 Face recognition for automatic attendance
- ⏰ Check-in/check-out tracking
- 📝 Leave request management
- 👥 Employee management (admin)
- 🎯 Real-time face detection with YOLOv8
- 🧠 Face embeddings with InsightFace

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **AI Service**: Flask + YOLOv8 + InsightFace
- **Database**: MySQL 8.0
- **Frontend**: Blade Templates + Tailwind CSS
- **Deployment**: Docker Compose

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Git

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd absensi
   ```

2. **Configure environment**
   ```bash
   cd laravel-app
   cp .env.example .env
   # Edit .env and set database credentials
   ```

3. **Start services**
   ```bash
   docker compose up -d
   ```

4. **Run migrations**
   ```bash
   docker compose exec laravel php artisan migrate
   ```

5. **Access the application**
   - Laravel App: http://localhost:8000
   - Face Service: http://localhost:5000

## Project Structure

```
absensi/
├── laravel-app/      # Main web application (Laravel 12)
├── face-service/     # Face recognition microservice (Flask)
└── docker-compose.yml
```

## Development

### Running Locally

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f

# Stop services
docker compose down
```

### Laravel Commands

```bash
# Inside laravel container
docker compose exec laravel php artisan migrate
docker compose exec laravel php artisan tinker
```

## Documentation

- **[AGENTS.md](AGENTS.md)** - Detailed architecture and service documentation
- **[DOCKER_GUIDE.md](DOCKER_GUIDE.md)** - Docker setup guide

## Environment Variables

### Laravel (.env)

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=absensi
DB_USERNAME=root
DB_PASSWORD=secret

FLASK_SERVICE_URL=http://face-service:5000
```

## License

[Your License Here]

## Contributing

[Contributing guidelines if applicable]
