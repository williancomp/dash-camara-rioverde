<?php

// MANTENHA SEU COMPONENT EXATAMENTE COMO ESTÁ!
// app/Filament/Forms/Components/OptimizedImageUpload.php
// ✅ NÃO PRECISA MUDAR NADA NO SEU COMPONENT

// APENAS SUBSTITUA O CONTEÚDO DA SUA TRAIT POR ESTA VERSÃO CORRIGIDA:
// app/Traits/OptimizesImages.php

namespace App\Traits;

use Intervention\Image\Laravel\Facades\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

trait OptimizesImages
{
    public function optimizeImage(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 60,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): string {
        try {
            // Usar o mesmo disco que o Filament usa (igual ao FileUpload original)
            $disk = Storage::disk(config('filament.default_filesystem_disk', 'public'));

            // Gerar nome único para o arquivo WebP
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '_' . time() . '_' . uniqid() . '.webp';
            $relativePath = $directory . '/' . $fileName;

            // Carregar e processar a imagem
            $image = Image::read($file->getRealPath());

            // Redimensionar se necessário (mantendo proporção)
            if ($maxWidth || $maxHeight) {
                $image = $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Converter para WebP e obter conteúdo
            $webpContent = $image->toWebp($quality)->toString();

            // Salvar usando o sistema do Storage (igual ao Filament original)
            $disk->put($relativePath, $webpContent);

            // Otimizar o arquivo após salvar (se as ferramentas estiverem disponíveis)
            try {
                $fullPath = $disk->path($relativePath);
                if (file_exists($fullPath)) {
                    $optimizerChain = OptimizerChainFactory::create();
                    $optimizerChain->optimize($fullPath);
                }
            } catch (\Exception $e) {
                // Se a otimização falhar, continua sem erro (imagem já foi salva)
                Log::warning('Otimização adicional falhou, mas imagem foi salva', [
                    'file' => $fileName,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Imagem processada com sucesso', [
                'original' => $file->getClientOriginalName(),
                'optimized' => $fileName,
                'quality' => $quality,
                'path' => $relativePath
            ]);

            return $relativePath;
        } catch (\Exception $e) {
            Log::error('Erro ao processar imagem', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}

// SEU USO CONTINUA IGUAL:
/*
OptimizedImageUpload::make('thumbnail')
    ->label('Imagem Principal')
    ->directory('midias/thumbnails')
    ->standardQuality() // 60% qualidade (40% compressão)
    ->showCompressionStats()
    ->required()
*/

// RESUMO DO QUE VOCÊ PRECISA FAZER:

/*
1. ✅ MANTER seu component OptimizedImageUpload.php como está
2. ✅ SUBSTITUIR apenas o conteúdo de app/Traits/OptimizesImages.php pela versão acima
3. ✅ TESTAR o upload novamente

A diferença principal da nova trait:
- Usa Storage::disk() igual ao Filament original
- Não tenta criar diretórios manualmente
- Usa o mesmo sistema que seu FileUpload original que funcionava
*/

// SE AINDA DER ERRO, USE ESTA VERSÃO MAIS SIMPLES DA TRAIT:
namespace App\Traits;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait OptimizesImagesSimple
{
    public function optimizeImage(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 60,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): string {
        // Primeiro, salvar igual ao Filament original
        $disk = Storage::disk(config('filament.default_filesystem_disk', 'public'));
        $originalPath = $file->store($directory, $disk);

        try {
            // Carregar imagem
            $image = Image::read($file->getRealPath());

            // Redimensionar se necessário
            if ($maxWidth || $maxHeight) {
                $image = $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Criar novo arquivo WebP
            $fileName = pathinfo($originalPath, PATHINFO_FILENAME) . '.webp';
            $webpPath = $directory . '/' . $fileName;
            $webpContent = $image->toWebp($quality)->toString();

            // Salvar WebP
            $disk->put($webpPath, $webpContent);

            // Remover arquivo original
            $disk->delete($originalPath);

            return $webpPath;
        } catch (\Exception $e) {
            // Se falhar, retorna o arquivo original
            \Log::warning('Conversão para WebP falhou, usando original', [
                'error' => $e->getMessage()
            ]);
            return $originalPath;
        }
    }
}
