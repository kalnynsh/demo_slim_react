init: docker-down-clear \
	api-clear frontend-clear cucumber-clear \
	docker-pull docker-build docker-up \
	api-init frontend-init cucumber-init

up: docker-up
down: docker-down
restart: docker-down docker-up
check: lint analyze api-validate-schema test

lint: api-lint frontend-lint cucumber-lint
analyze: api-analyze

test: api-test api-fixtures frontend-test
test-unit: api-test-unit
test-functional: api-test-functional api-fixtures

test-e2e-full:
	make api-fixtures
	make cucumber-clear
	- make cucumber-e2e
	make cucumber-report

test-smoke: api-fixtures cucumber-clear cucumber-smoke
test-e2e: api-fixtures cucumber-clear cucumber-e2e-plane

frontend-init: frontend-npm-install frontend-ready
frontend-lint: frontend-eslint frontend-stylelint

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

api-init: api-permissions api-composer-install api-wait-db api-migrations-auto api-fixtures

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var/cache var/log var/test

api-wait-db:
	docker compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-migrations-auto:
	docker compose run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction

api-migrations:
	docker compose run --rm api-php-cli php bin/app.php migrations:migrate

api-migrations-diff:
	docker compose run --rm api-php-cli php bin/app.php migrations:diff

api-fixtures:
	docker compose run --rm api-php-cli php bin/app.php fixtures:load

api-validate-schema:
	docker compose run --rm api-php-cli php bin/app.php orm:validate-schema

api-lint:
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer cs-check

api-lint-fix:
	docker compose run --rm api-php-cli composer cs-fix

api-analyze:
	docker compose run --rm api-php-cli composer psalm

api-test:
	docker compose run --rm api-php-cli composer test

api-test-unit:
	docker compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

api-composer-update:
	docker compose run --rm api-php-cli composer update

api-composer-upgrade:
	docker compose run --rm api-php-cli composer upgrade

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-npm-install:
	docker compose run --rm frontend-node-cli npm install

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-start:
	docker compose run --rm frontend-node-cli npm run start

frontend-test:
	docker compose run --rm frontend-node-cli npm run test --watchAll=false

frontend-test-watch:
	docker compose run --rm frontend-node-cli npm run test

frontend-eslint:
	docker compose run --rm frontend-node-cli npm run eslint

frontend-stylelint:
	docker compose run --rm frontend-node-cli npm run stylelint

frontend-eslint-fix:
	docker compose run --rm frontend-node-cli npm run eslint-fix

frontend-pretty:
	docker compose run --rm frontend-node-cli npm run prettier

cucumber-init: cucumber-npm-install

cucumber-npm-install:
	docker compose run --rm cucumber-node-cli npm install

cucumber-e2e:
	docker compose run --rm cucumber-node-cli npm run e2e

cucumber-e2e-plane:
	docker compose run --rm cucumber-node-cli npm run e2e-plane

cucumber-lint:
	docker compose run --rm cucumber-node-cli npm run lint

cucumber-lint-fix:
	docker compose run --rm cucumber-node-cli npm run lint-fix

cucumber-clear:
	docker run --rm -v ${PWD}/cucumber:/app -w /app alpine sh -c 'rm -rf var/*'

cucumber-report:
	docker compose run --rm cucumber-node-cli npm run report

cucumber-smoke:
	docker compose run --rm cucumber-node-cli npm run smoke

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/auction-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/auction-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/auction-api:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-cli:${IMAGE_TAG}

try-testing-build:
	REGISTRY=localhost IMAGE_TAG=0 make testing-build

try-testing-init:
	REGISTRY=localhost IMAGE_TAG=0 make testing-init

try-testing-smoke:
	REGISTRY=localhost IMAGE_TAG=0 make testing-smoke

try-testing-e2e:
	REGISTRY=localhost IMAGE_TAG=0 make testing-e2e

try-testing-down-clear:
	REGISTRY=localhost IMAGE_TAG=0 make testing-down-clear

testing-build: testing-build-gateway testing-build-testing-api-php-cli testing-build-cucumber

testing-build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/testing/nginx/Dockerfile --tag=${REGISTRY}/auction-testing-gateway:${IMAGE_TAG} gateway/docker

testing-build-testing-api-php-cli:
	docker --log-level=debug build --pull --file=api/docker/testing/php-cli/Dockerfile --tag=${REGISTRY}/auction-testing-api-php-cli:${IMAGE_TAG} api

testing-build-cucumber:
	docker --log-level=debug build --pull --file=cucumber/docker/testing/node/Dockerfile --tag=${REGISTRY}/auction-cucumber-node-cli:${IMAGE_TAG} cucumber

testing-init:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml up -d
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm api-php-cli wait-for-it api-postgres:5432 -t 60
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm testing-api-php-cli php bin/app.php fixtures:load --no-interaction

testing-down-clear:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml down -v --remove-orphans

validate-jenkins:
	curl --user ${USER} -X POST -F "jenkinsfile=<Jenkinsfile" ${HOST}/pipline-model-converter/validate

testing-smoke:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm cucumber-node-cli npm run smoke

testing-e2e:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm cucumber-node-cli npm run e2e

try-testing: try-build try-testing-build try-testing-init try-testing-smoke try-testing-e2e try-testing-down-clear

deploy:
	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=auction" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_DB_PASSWORD=${API_DB_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_HOST=${API_MAILER_HOST}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_PORT=${API_MAILER_PORT}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_USER=${API_MAILER_USER}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_PASSWORD=${API_MAILER_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_FROM_EMAIL=${API_MAILER_FROM_EMAIL}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "SENTRY_DSN=${SENTRY_DSN}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build -d api-postgres api-php-cli'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

rollback:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
