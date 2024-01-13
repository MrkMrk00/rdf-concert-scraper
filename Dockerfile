FROM php:8.2

WORKDIR /setup

RUN apt update && apt install -y git zip libzip-dev nodejs openjdk-17-jre
RUN docker-php-ext-install zip pdo pdo_mysql

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer

# install pnpm
RUN export SHELL=bash
RUN curl -fsSL https://get.pnpm.io/install.sh | bash -
RUN mv /root/.local/share/pnpm/pnpm /usr/bin/pnpm

WORKDIR /app
COPY . .
RUN composer install
RUN pnpm install
RUN pnpm build

# prepare ONTOP
RUN curl -#L https://github.com/ontop/ontop/releases/download/ontop-5.1.1/ontop-cli-5.1.1.zip -o ontop.zip
RUN unzip ontop.zip -d ontop
RUN curl -#L https://dlm.mariadb.com/3700566/Connectors/java/connector-java-3.3.2/mariadb-java-client-3.3.2.jar -o ontop/jdbc/mariadb-java-client-3.3.2.jar

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app/public"]
