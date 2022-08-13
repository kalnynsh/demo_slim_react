init: init-ci frontend-ready

init-ci: docker-down-clear \
	api-clear frontend-clear cucumber-clear \
	docker-pull docker-build docker-up \
	api-init frontend-init cucumber-init

up: docker-up
down: docker-down
restart: docker-down docker-up
check: lint analyze api-validate-schema test test-e2e

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
test-e2e: api-fixtures cucumber-clear cucumber-e2e

frontend-init: frontend-npm-install

deps-update: api-composer-update cucumber-npm-update frontend-npm-update restart

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

api-backup:
	docker compose run --rm api-postgres-backup

api-check: api-validate-schema api-lint api-analyze api-test

api-validate-schema:
	docker compose run --rm api-php-cli php bin/app.php orm:validate-schema

api-gen-token:
	docker compose run --rm api-php-cli php bin/app.php oauth:e2e-token

api-lint:
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

api-cs-fix:
	docker compose run --rm api-php-cli composer php-cs-fixer fix

api-analyze:
	docker compose run --rm api-php-cli composer psalm -- --no-diff

api-analyze-diff:
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

api-composer-outdated:
	docker compose run --rm api-php-cli composer outdated --direct

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-npm-install:
	docker compose run --rm frontend-node-cli npm install

frontend-npm-update:
	docker compose run --rm frontend-node-cli npm update --save

frontend-npm-outdated:
	docker compose run --rm frontend-node-cli npm outdated

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-check: frontend-lint frontend-test

frontend-lint:
	docker compose run --rm frontend-node-cli npm run eslint
	docker compose run --rm frontend-node-cli npm run stylelint

frontend-start:
	docker compose run --rm frontend-node-cli npm run start

frontend-test:
	docker compose run --rm frontend-node-cli npm run test

frontend-test-watch:
	docker compose run --rm frontend-node-cli npm run test_with_watch

frontend-eslint-fix:
	docker compose run --rm frontend-node-cli npm run eslint-fix

frontend-pretty:
	docker compose run --rm frontend-node-cli npm run prettier

cucumber-init: cucumber-npm-install

cucumber-npm-install:
	docker compose run --rm cucumber-node-cli npm install

cucumber-npm-update:
	docker compose run --rm cucumber-node-cli npm update --save

cucumber-npm-outdated:
	docker compose run --rm cucumber-node-cli npm outdated

cucumber-e2e:
	docker compose run --rm cucumber-node-cli npm run e2e

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
	docker --log-level=debug build --pull --file=api/docker/common/postgres-backup/Dockerfile --tag=${REGISTRY}/auction-api-postgres-backup:${IMAGE_TAG} api/docker/common

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
	docker push ${REGISTRY}/auction-api-postgres-backup:${IMAGE_TAG}

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

push-testing: push-testing-gateway push-testing-api-php-cli push-testing-cucumber

push-testing-gateway:
	docker push ${REGISTRY}/auction-testing-gateway:${IMAGE_TAG}

push-testing-api-php-cli:
	docker push ${REGISTRY}/auction-testing-api-php-cli:${IMAGE_TAG}

push-testing-cucumber:
	docker push ${REGISTRY}/auction-cucumber-node-cli:${IMAGE_TAG}

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
	curl --user ${J_USER} -X POST -F "jenkinsfile=<Jenkinsfile" ${J_HOST}/pipeline-model-converter/validate

testing-smoke:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm cucumber-node-cli npm run smoke-ci

testing-e2e:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker-compose-testing.yml run --rm cucumber-node-cli npm run e2e-ci

try-testing: try-build try-testing-build try-testing-init try-testing-smoke try-testing-e2e try-testing-down-clear

deploy:
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'

	envsubst < docker-compose-production.yml > docker-compose-production-env.yml
	scp -o StrictHostKeyChecking=no -P ${PORT} docker-compose-production-env.yml ${D_USER}@${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	rm -f docker-compose-production-env.yml

	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}/secrets'
	scp -o StrictHostKeyChecking=no -p ${PORT} ${API_DB_PASSWORD_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/api_db_password
	scp -o StrictHostKeyChecking=no -p ${PORT} ${API_MAILER_PASSWORD_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/api_mailer_password
	scp -o StrictHostKeyChecking=no -p ${PORT} ${SENTRY_DSN_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/sentry_dsn
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_ENCRYPTION_KEY_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_encryption_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PUBLIC_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_public_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PRIVATE_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_private_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${BACKUP_AWS_SECRET_ACCESS_KEY_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/backup_aws_secret_access_key

	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml auction --with-registry-auth --prune'

deploy-clean:
	rm -f docker-compose-production-env.yml

rollback:
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml auction --with-registry-auth --prune'

deploy-vm:
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'

	envsubst < docker-compose-vm.yml > docker-compose-vm-env.yml
	scp -o StrictHostKeyChecking=no -P ${PORT} docker-compose-production-env.yml ${D_USER}@${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	scp -o StrictHostKeyChecking=no -p ${PORT} ${API_DB_PASSWORD_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/api_db_password
	scp -o StrictHostKeyChecking=no -p ${PORT} ${API_MAILER_PASSWORD_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/api_mailer_password
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_ENCRYPTION_KEY_FILE} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_encryption_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PUBLIC_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_public_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PRIVATE_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_private_key
	rm -f docker-compose-vm-env.yml

	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}/secrets'
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PUBLIC_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_public_key
	scp -o StrictHostKeyChecking=no -p ${PORT} ${JWT_PRIVATE_KEY} ${D_USER}@${HOST}site_${BUILD_NUMBER}/secrets/jwt_private_key

	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml auction --with-registry-auth --prune'

deploy-vm-clean:
	rm -f docker-compose-vm-env.yml

rollback-vm:
	ssh -o StrictHostKeyChecking=no ${D_USER}@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml auction --with-registry-auth --prune'
