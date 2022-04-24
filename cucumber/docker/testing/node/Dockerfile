FROM node:17-alpine

RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    freetype-dev \
    ttf-freefont \
    harfbuzz \
    ca-certificates

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

WORKDIR /app

COPY ./package.json ./package-lock.json ./
RUN npm install --cache /tmp/empty-cache

COPY ./ ./
