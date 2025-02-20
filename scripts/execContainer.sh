__execContainers() {
    docker compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec hexagonal-make-bundle sudo -u www-data /bin/bash
}

__execContainers