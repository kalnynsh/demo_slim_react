FROM node:18-alpine as builder

WORKDIR /app

COPY ./package.json ./package-lock.json ./
RUN npm install --cache /tmp/empty-cache

COPY ./ ./
RUN npm run build

FROM nginx:1.21-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY --from=builder ./app/build ./public

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s --retries=10 CMD curl --fail http://127.0.0.1/health || exit 1
