#!/bin/bash -e
echo "===== Running entrypoint script ====="

# Define paths
PROJECT_ROOT=$(realpath ..)
APP_DIR=$PROJECT_ROOT/app
DOCKER_SCRIPTS_DIR=$PROJECT_ROOT/.docker/services/php/scripts/

# Switch to project directory
cd $PROJECT_ROOT

# Run service waiters
echo "Waiting for MariaDB to be up and running.."
wait-for-it --host=mariadb-testcase --port=3306 --timeout=30

# Run scripts after services are available
echo "Changing current working directory to application.."
cd $APP_DIR

echo "Installing dependencies.."
composer install

echo "Initializing Doctrine.."
bin/console doctrine:migrations:migrate --env=dev --no-interaction

echo "Setting user rights.."
chown -R php:php $APP_DIR

# Return to application directory
cd $APP_DIR
echo "Current working directory: $APP_DIR"
echo "===== Finished entrypoint script ====="

# Delegate current command
exec "$@"
