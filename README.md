# Social Web App

A social web application built with Symfony 6.4 and PHP 8.4.

## Tech Stack

- **Framework:** Symfony 6.4
- **Language:** PHP 8.4
- **Database:** MySQL 8.0
- **Web Server:** Nginx
- **Containerization:** Docker & Docker Compose

## Prerequisites

- [Docker](https://www.docker.com/get-started) and Docker Compose

## Getting Started

1. **Clone the repository:**
   ```bash
   git clone git@github.com:gabivicu/social-web-app.git
   cd social-web-app
   ```

2. **Copy the environment file:**
   ```bash
   cp .env.dev .env
   ```

3. **Start the containers:**
   ```bash
   docker compose up -d --build
   ```

4. **Access the application:**
   Open [http://localhost:8080](http://localhost:8080) in your browser.

## Docker Services

| Service | Description          | Port          |
|---------|----------------------|---------------|
| app     | PHP 8.4 FPM          | 9000 (internal) |
| nginx   | Nginx reverse proxy  | 8080          |
| db      | MySQL 8.0            | 3307          |

## Useful Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs -f

# Run Symfony console commands
docker compose exec app bin/console <command>

# Install dependencies
docker compose exec app composer install
```
