#!/bin/sh
set -e

# ============================================
# Entrypoint - Chat Interno PHP Container
# ============================================

echo "[ENTRYPOINT] Iniciando container PHP..."

# ------------------------------------------
# 1. Aguardar MySQL estar pronto
# ------------------------------------------
echo "[ENTRYPOINT] Aguardando MySQL ($DB_HOST:$DB_PORT)..."
timeout=60
while ! nc -z "$DB_HOST" "$DB_PORT" >/dev/null 2>&1; do
    timeout=$((timeout - 1))
    if [ $timeout -le 0 ]; then
        echo "[ERRO] Timeout ao conectar no MySQL!"
        exit 1
    fi
    sleep 1
done
echo "[ENTRYPOINT] MySQL disponível!"

# ------------------------------------------
# 2. Ajustar permissões de uploads
# ------------------------------------------
echo "[ENTRYPOINT] Configurando permissões de uploads..."
if [ -d "/var/www/html/public/uploads" ]; then
    chown -R www-data:www-data /var/www/html/public/uploads
    chmod -R 775 /var/www/html/public/uploads
fi

# ------------------------------------------
# 3. Criar diretórios de logs
# ------------------------------------------
mkdir -p /var/www/html/var/logs
chown -R www-data:www-data /var/www/html/var/logs

# ------------------------------------------
# 4. Validar .env
# ------------------------------------------
if [ ! -f "/var/www/html/.env" ]; then
    echo "[AVISO] Arquivo .env não encontrado! Use .env.docker.example"
fi

# ------------------------------------------
# 5. Executar comando principal
# ------------------------------------------
echo "[ENTRYPOINT] Iniciando PHP-FPM..."
exec "$@"