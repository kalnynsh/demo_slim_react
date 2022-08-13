FROM alpine:3.16

RUN apk add --no-cache python3 py3-pip bash coreutils postgresql-client \
    && pip3 install --upgrade awscli

COPY ./wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY ./postgres-backup/backup.sh /usr/local/bin/backup
RUN chmod 555 /usr/local/bin/backup

RUN mkdir /app

WORKDIR /app

CMD ["backup"]
