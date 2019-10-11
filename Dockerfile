FROM alpine:3.10

COPY ./ /var/www/html

WORKDIR /var/www/html

VOLUME /var/www/html

RUN addgroup -g 10000 -S web-user \
    && adduser -u 10000 -D -S -G web-user web-user

CMD ["/bin/sh", "-c", "chown -R web-user:web-user ./ \
    && chmod -R 775 bootstrap/cache storage \
    && while true; do sleep 1; done"]
