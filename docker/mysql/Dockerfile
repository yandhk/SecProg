FROM mysql:8.0

ENV MYSQL_ROOT_PASSWORD=rootsecret
ENV MYSQL_DATABASE=acadeasy
ENV MYSQL_USER=acadeasy
ENV MYSQL_PASSWORD=secret
ENV TZ=Asia/Jakarta

COPY ./my.cnf /etc/mysql/conf.d/custom.cnf

CMD ["--default-authentication-plugin=mysql_native_password", "--character-set-server=utf8mb4", "--collation-server=utf8mb4_unicode_ci"]
