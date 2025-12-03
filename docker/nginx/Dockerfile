FROM nginx:alpine

# Install envsubst (ada di paket gettext)
RUN apk add --no-cache gettext

# Copy template config
COPY ./default.conf /etc/nginx/templates/default.conf.template
