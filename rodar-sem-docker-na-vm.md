# Rodar Direto na VM sem Docker

Este guia descreve a execução do projeto diretamente na VM Linux, sem containerização.

## Pré-requisitos

Instale os pacotes necessários:

```bash
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mysql php8.3-zip php8.3-sockets php8.3-curl php8.3-mbstring php8.3-xml php8.3-gd apache2 libapache2-mod-php8.3 mysql-server unzip git composer
```

## Preparar o Banco

1. Inicie o MySQL nativo da VM.

```bash
sudo systemctl enable mysql
sudo systemctl start mysql
```

2. Crie o banco e o usuário da aplicação.

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS chat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'chat_user'@'localhost' IDENTIFIED BY 'SuaSenhaForteAqui';"
sudo mysql -e "GRANT ALL PRIVILEGES ON chat_db.* TO 'chat_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

3. Importe o schema.

```bash
mysql -u chat_user -p chat_db < config/schema.sql
```

Se você tiver um dump antigo, importe o backup no lugar do schema.

## Configurar a Aplicação

1. Copie o arquivo de ambiente.

```bash
cp .env.example .env
nano .env
```

2. Ajuste as variáveis de banco e ambiente para apontar para a VM local.

Exemplo:

```dotenv
APP_ENV=production
APP_SECRET=troque-por-uma-chave-segura
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=chat_db
DB_USER=chat_user
DB_PASS=SuaSenhaForteAqui
UPLOAD_MAX_SIZE=10485760
UPLOAD_ALLOWED=jpg,jpeg,png,gif,pdf,doc,docx,zip
TZ=America/Sao_Paulo
```

## Instalar Dependências PHP

```bash
composer install --no-dev --optimize-autoloader
```

## Configurar Apache

1. Habilite rewrite.

```bash
sudo a2enmod rewrite
```

2. Aponte o DocumentRoot para a pasta `public` do projeto.

Se necessário, crie um VirtualHost semelhante a este:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /home/sofia/projeto-chat/public

    <Directory /home/sofia/projeto-chat/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/chat_error.log
    CustomLog ${APACHE_LOG_DIR}/chat_access.log combined
</VirtualHost>
```

3. Reinicie o Apache.

```bash
sudo systemctl restart apache2
```

## Permissões

```bash
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 775 public/uploads
sudo mkdir -p var/logs
sudo chown -R www-data:www-data var/logs
```

## Subir o WebSocket

Em uma sessão separada da VM:

```bash
php bin/chat-server.php
```

Se preferir manter o processo em background, use `screen`, `tmux` ou um serviço `systemd`.

## Acessar a Aplicação

- HTTP: `http://localhost/login`
- Chat: `http://localhost/chat`
- Dashboard TI: `http://localhost/dashboard-ti`
- Relatório: `http://localhost/dashboard-ti/relatorio`
- Meus chamados: `http://localhost/meus-chamados`
- Admin: `http://localhost/admin`

## Observações para Acesso via Windows

Se a VM estiver atrás de NAT no VirtualBox, você pode manter redirecionamento de portas para a aplicação direta na VM.

Sugestão:

- Host 80 -> Guest 80
- Host 8080 -> Guest 8080

Se a porta 80 estiver ocupada no Windows, use uma porta alternativa no host e redirecione para a porta 80 da VM.

## Troubleshooting

### PHP não sobe

```bash
php -v
php -m
```

### Banco não conecta

```bash
sudo systemctl status mysql
mysql -u chat_user -p -e "SHOW DATABASES;"
```

### Apache não responde

```bash
sudo systemctl status apache2
sudo tail -f /var/log/apache2/error.log
```

### WebSocket não responde

```bash
php bin/chat-server.php
```

Se houver erro de porta ocupada, verifique o processo que está usando a 8080:

```bash
sudo ss -ltnp | grep :8080
```
