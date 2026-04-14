FROM nginx:1.25-alpine

LABEL description="Nginx otimizado para Slim Framework"

# Healthcheck usa wget, então a ferramenta precisa existir na imagem
RUN apk add --no-cache wget

# Remover configuração padrão
RUN rm /etc/nginx/conf.d/default.conf

# Copiar configuração customizada
COPY docker/nginx/default.conf /etc/nginx/conf.d/

# Health check
HEALTHCHECK --interval=30s --timeout=3s --retries=3 \
    CMD wget --quiet --tries=1 --spider http://localhost/health || exit 1

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]