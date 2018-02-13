FROM nginx:stable

ADD nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled/* /etc/nginx/sites-enabled/

RUN if [ -f "certs/default.crt" && -f "certs/default.key" && -f "certs/dhparam.pem"];\
    then \
        cp certs/default.crt /etc/nginx/certs/default.crt \
        cp certs/default.key /etc/nginx/certs/default.key \
        cp certs/dhparam.pem /etc/nginx/certs/dhparam.pem \
        cp sites-enabled\erentpay-ssl.conf /etc/nginx/sites-enabled/default.conf \
    else \
        cp sites-enabled\erentpay.conf /etc/nginx/sites-enabled/default.conf \
  fi

RUN rm /etc/nginx/conf.d/default.conf
RUN rm -rf /var/lib/apt/lists/*

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log
