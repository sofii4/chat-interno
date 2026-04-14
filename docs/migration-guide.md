# Guia de Migração - VM Bare Metal → Docker

## Pré-requisitos

### No servidor atual (VM Linux)
```bash
# 1. Instalar Docker e Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker

# 2. Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

## Passo 1: Backup do Banco Atual

```bash
# Exportar banco completo
mysqldump -u chat_user -p --single-transaction --routines --triggers --events --no-tablespaces chat_db > /tmp/chat_db_backup.sql

# Copiar para pasta do projeto
cp /tmp/chat_db_backup.sql ~/projeto-chat/config/migration_data.sql
```

## Passo 2: Backup dos Uploads

```bash
# Criar arquivo tar dos uploads
cd ~/projeto-chat
tar -czf uploads_backup.tar.gz public/uploads/

# Verificar tamanho
du -sh uploads_backup.tar.gz
```

## Passo 3: Preparar Configuração Docker

```bash
cd ~/projeto-chat

# Criar .env a partir do exemplo
cp .env.docker.example .env

# IMPORTANTE: Editar senhas fortes
nano .env
```

**Gerar secrets seguros:**
```bash
# APP_SECRET
openssl rand -hex 32

# DB_PASS e DB_ROOT_PASS
openssl rand -base64 24
```

## Passo 4: Build e Subida dos Containers

```bash
# Build de todas as imagens
docker-compose build --no-cache

# Subir apenas o MySQL primeiro
docker-compose up -d mysql

# Aguardar MySQL inicializar (health check)
docker-compose ps
# Aguardar até status "healthy"

# Subir restante dos serviços
docker-compose up -d
```

## Passo 5: Importar Dados Migrados

```bash
# Importar dump SQL no MySQL do container
docker exec -i chat_mysql mysql -u root -p${DB_ROOT_PASS} chat_db < config/migration_data.sql

# Verificar importação
docker exec chat_mysql mysql -u root -p${DB_ROOT_PASS} -e "USE chat_db; SHOW TABLES;"
```

## Passo 6: Restaurar Uploads

```bash
# Extrair uploads no volume Docker
tar -xzf uploads_backup.tar.gz -C public/

# Ajustar permissões
docker exec chat_php chown -R www-data:www-data /var/www/html/public/uploads
docker exec chat_php chmod -R 775 /var/www/html/public/uploads
```

## Passo 7: Validação Funcional

### 7.1 Testar HTTP
```bash
curl -I http://localhost:8188/login
# Esperar: HTTP/1.1 200 OK
```

### 7.2 Testar WebSocket
```bash
# Instalar wscat se necessário
npm install -g wscat

# Testar conexão WebSocket
wscat -c ws://localhost:8080
# Enviar: {"type":"ping"}
# Esperar: {"type":"pong"}
```

### 7.3 Testar Upload
- Acessar http://localhost/chat
- Enviar mensagem com anexo
- Verificar que arquivo foi salvo em `public/uploads`

### 7.4 Logs
```bash
# Ver logs em tempo real
docker-compose logs -f

# Logs específicos do WebSocket
docker-compose logs -f websocket

# Logs do PHP
docker exec chat_php tail -f /var/www/html/var/logs/php_errors.log
```

## Passo 8: Configurar Redirecionamento NAT (Windows Host)

### No VirtualBox (se aplicável)
1. Abrir configurações da VM
2. Rede → Adaptador 1 → Avançado → Redirecionamento de Portas
3. Adicionar regras:

| Nome | Protocolo | IP Host | Porta Host | IP Convidado | Porta Convidado |
|------|-----------|---------|------------|--------------|-----------------|
| HTTP | TCP | 127.0.0.1 | 8188 | 10.0.2.15 | 8188 |
| WebSocket | TCP | 127.0.0.1 | 8080 | 10.0.2.15 | 8080 |

### No Hyper-V
```powershell
# PowerShell como Admin
netsh interface portproxy add v4tov4 listenport=8188 listenaddress=0.0.0.0 connectport=8188 connectaddress=<VM_IP>
netsh interface portproxy add v4tov4 listenport=8080 listenaddress=0.0.0.0 connectport=8080 connectaddress=<VM_IP>
```

## Passo 9: Ajustar Frontend (se necessário)

**No código JavaScript que conecta ao WebSocket:**

```javascript
// ANTES (ambiente desenvolvimento local)
const wsUrl = 'ws://localhost:8080';

// DEPOIS (produção via IP da VM)
const wsUrl = `ws://${window.location.hostname}:8080`;
```

## Passo 10: Parar Serviços Antigos

```bash
# Parar Apache e MySQL nativos
sudo systemctl stop apache2
sudo systemctl stop mysql
sudo systemctl disable apache2
sudo systemctl disable mysql

# Liberar portas
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :8080
```

## Troubleshooting

### MySQL não aceita conexões
```bash
docker exec chat_mysql mysql -u root -p${DB_ROOT_PASS} -e "SELECT 1;"
# Se falhar, verificar logs:
docker-compose logs mysql
```

### WebSocket não conecta
```bash
# Verificar se processo está rodando
docker exec chat_websocket ps aux | grep chat-server

# Testar porta internamente
docker exec chat_websocket nc -zv localhost 8080

# Verificar logs do Supervisor
docker exec chat_websocket tail -f /var/log/supervisor/ratchet.out.log
```

### Erro de permissão em uploads
```bash
# Reconfigurar permissões
docker exec chat_php sh -c 'chown -R www-data:www-data /var/www/html/public/uploads && chmod -R 775 /var/www/html/public/uploads'
```

### Container crashando
```bash
# Ver motivo da falha
docker-compose ps
docker-compose logs <service_name>

# Restart forçado
docker-compose restart <service_name>
```

## Rollback (Se necessário)

```bash
# Parar containers
docker-compose down

# Reativar serviços nativos
sudo systemctl start mysql
sudo systemctl start apache2

# Restaurar banco do backup
mysql -u root -p chat_db < /tmp/chat_db_backup.sql
```

## Manutenção em Produção

### Backup automatizado
```bash
# Criar cron job (exemplo diário às 2h)
0 2 * * * docker exec chat_mysql mysqldump -u root -p${DB_ROOT_PASS} chat_db > /backup/chat_$(date +\%Y\%m\%d).sql
```

### Atualização de código
```bash
git pull origin main
docker-compose build php websocket
docker-compose up -d --no-deps php websocket nginx
```

### Limpeza de logs
```bash
# Rotacionar logs do Docker
docker-compose logs --no-log-prefix > /backup/docker_logs_$(date +%Y%m%d).log
docker-compose down && docker-compose up -d
```