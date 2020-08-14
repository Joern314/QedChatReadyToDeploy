# THIS IS A TESTING ONLY SETUP!!!

# Copyright (C) 2004-2018 Quod Erat Demonstrandum e.V. <webmaster@qed-verein.de>
#
# This file is part of QED-Chat.
#
# QED-Chat is free software: you can redistribute it and/or modify it
# under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# QED-Chat is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public
# License along with QED-Chat.  If not, see
# <http://www.gnu.org/licenses/>.

FROM debian:buster

ENV DEBIAN_FRONTEND noninteractive 
RUN apt-get update && apt-get install -y \ 
    build-essential \
    libmariadbclient-dev \
    libpq-dev \
    libgdbm-dev \
    libgdbm-compat-dev \
    ruby \ 
    ruby-dev \ 
    rubygems \
    apache2 \
    ruby-eventmachine \
    ruby-sequel \
    ruby-json \
    ruby-mysql2 \
    ruby-jwt && \
    gem update && \
    gem install websocket && \
    a2enmod proxy && \
    a2enmod proxy_wstunnel && \
    a2enmod proxy_scgi && \
    a2enmod rewrite

ADD . /code
WORKDIR /code

COPY .docker/rubychat-config.AKA20.rb /etc/chat/rubychat-config.rb
COPY .docker/000-default.conf /etc/apache2/sites-enabled/
#COPY .docker/default-ssl.conf /etc/apache2/sites-enabled/
#COPY .docker/ssl-params.conf /etc/apache2/conf-enabled/

COPY .docker/docker_start.sh .
RUN chmod u+x docker_start.sh

COPY public_html/ /www
RUN chown www-data.www-data -R /www

COPY cert/selfsigned.key /etc/ssl/private/selfsigned.key
COPY cert/selfsigned.crt /etc/ssl/certs/selfsigned.crt

EXPOSE 80
EXPOSE 443

RUN a2enmod ssl

CMD [ "./docker_start.sh" ]