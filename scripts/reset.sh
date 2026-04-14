#!/bin/bash
echo "⚠️  ATENÇÃO: Este script vai APAGAR TODOS OS DADOS!"
read -p "Confirmar reset completo? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "❌ Operação cancelada"
    exit 1
fi

docker-compose down -v
docker system prune -f
echo "🗑️  Reset completo executado"