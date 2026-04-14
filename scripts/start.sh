#!/bin/bash
set -e

echo "🚀 Iniciando Chat Interno - Dockerizado"

# Verificar .env
if [ ! -f .env ]; then
    echo "❌ Arquivo .env não encontrado!"
    echo "📝 Copie .env.docker.example para .env e configure as variáveis"
    exit 1
fi

# Build (apenas primeira vez ou quando houver mudanças)
echo "🔨 Building images..."
docker-compose build

# Subir containers
echo "📦 Starting containers..."
docker-compose up -d

# Aguardar health checks
echo "⏳ Aguardando serviços ficarem saudáveis..."
sleep 10

# Status
docker-compose ps

echo ""
echo "✅ Sistema disponível em:"
echo "   HTTP: http://localhost"
echo "   WebSocket: ws://localhost:8080"
echo ""
echo "📊 Ver logs em tempo real:"
echo "   docker-compose logs -f"