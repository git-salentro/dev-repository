FROM nginx:1.12

ADD nginx.conf /etc/nginx/nginx.conf

RUN mkdir /etc/nginx/sites-enabled
RUN rm /etc/nginx/conf.d/default.conf
RUN rm -rf /var/lib/apt/lists/*

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

COPY docker-entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]