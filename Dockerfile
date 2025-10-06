FROM php:8.2-apache

# System-Tools und Abhängigkeiten
RUN apt-get update && apt-get install -y \
    gnupg2 \
    unixodbc \
    unixodbc-dev \
    curl \
    apt-transport-https \
    libgssapi-krb5-2 \
    build-essential \
    locales \
    && rm -rf /var/lib/apt/lists/*

# Microsoft GPG Key & Repo (ohne apt-key)
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc \
      -o /etc/apt/trusted.gpg.d/microsoft.asc && \
    curl -sSL https://packages.microsoft.com/config/debian/11/prod.list \
      -o /etc/apt/sources.list.d/mssql-release.list

# MS ODBC & Tools installieren
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
    msodbcsql18 \
    mssql-tools18 \
    && rm -rf /var/lib/apt/lists/*

# SQLSRV & PDO_SQLSRV Extensions installieren
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# App-Code in Container
COPY ./src/ /var/www/html/

EXPOSE 80
