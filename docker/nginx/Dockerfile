FROM nginx:stable

ADD nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled/* /etc/nginx/sites-enabled/

RUN rm /etc/nginx/conf.d/default.conf
RUN rm -rf /var/lib/apt/lists/*

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log
