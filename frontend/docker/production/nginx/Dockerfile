FROM node:17-alpine as builder

WORKDIR /app

COPY ./package.json ./package-lock.json ./
RUN npm install --cache /tmp/empty-cache

COPY ./ ./
RUN npm run build

FROM nginx:1.21-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY --from=builder ./app/build ./public
