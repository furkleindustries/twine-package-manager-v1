FROM postgres:9.6

RUN mkdir /etc/twinepm-server-heroku/

WORKDIR /etc/twinepm-server-heroku/

ENV POSTGRES_DB=twinepm

ENV POSTGRES_USER=root

COPY diskdb/00_twinepm_schema.sql /docker-entrypoint-initdb.d/

COPY diskdb/10_twinepm_contents.sql /docker-entrypoint-initdb.d/

COPY diskdb/ ./diskdb/