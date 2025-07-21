#!/bin/bash

# ========================================
# SCRIPT DE DEPLOY AUTOMATIZADO
# deploy.sh
# ========================================

echo "🚀 Iniciando deploy..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log colorido
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# ========================================
# 1. ATUALIZAR CÓDIGO (SEM FORÇAR)
# ========================================
log_info "Fazendo pull do repositório..."
git pull origin main

# Verificar se deu certo
if [ $? -ne 0 ]; then
    log_error "Erro no git pull. Verifique conflitos manualmente."
    exit 1
fi

# ========================================
# 2. DEPENDÊNCIAS (APENAS SE NECESSÁRIO)
# ========================================
log_info "Verificando se precisa atualizar dependências..."

# Só roda composer se composer.json foi alterado
if git diff --name-only HEAD~1 HEAD | grep -q "composer.json\|composer.lock"; then
    log_info "composer.json alterado, atualizando dependências PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    log_info "composer.json não alterado, pulando dependências PHP"
fi

# Só roda npm se package.json foi alterado
if git diff --name-only HEAD~1 HEAD | grep -q "package.json\|package-lock.json"; then
    log_info "package.json alterado, atualizando dependências Node.js..."
    npm install
    npm run build
else
    log_info "package.json não alterado, pulando dependências Node.js"
fi

# ========================================
# 3. CORRIGIR PERMISSÕES (CRÍTICO)
# ========================================
log_info "Corrigindo permissões..."

# Dono dos arquivos
sudo chown -R webmaster:www-data .

# Permissões gerais
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# Permissões especiais para Laravel
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
sudo chmod -R 775 public/storage/ 2>/dev/null || true

# ========================================
# 4. LARAVEL/FILAMENT
# ========================================
log_info "Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan filament:clear-cached-components

log_info "Executando migrations..."
php artisan migrate --force

log_info "Aplicando caches de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan filament:cache-components

# ========================================
# 5. REINICIAR SERVIÇOS
# ========================================
log_info "Reiniciando serviços..."
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx

# ========================================
# 6. VERIFICAÇÕES
# ========================================
log_info "Verificando deploy..."

# Verificar se serviços estão rodando
if systemctl is-active --quiet php8.4-fpm; then
    log_info "✅ PHP-FPM rodando"
else
    log_error "❌ PHP-FPM com problema"
fi

if systemctl is-active --quiet nginx; then
    log_info "✅ Nginx rodando"
else
    log_error "❌ Nginx com problema"
fi

# Verificar permissões críticas
if [ -w "storage/logs/" ]; then
    log_info "✅ Permissões do storage OK"
else
    log_error "❌ Problema nas permissões do storage"
fi

log_info "🎉 Deploy concluído!"
echo ""
echo "Teste o site agora:"
echo "- Painel: https://rio-verde.camaramunicipal.app/admin"
echo "- API: https://rio-verde.camaramunicipal.app/api/"