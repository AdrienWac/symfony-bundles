__runAllContainers() {
    docker compose -f docker-compose.dev.yml --env-file .env --env-file .env.local up
}

__runAllContainers