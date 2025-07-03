<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\FileUpload;
use App\Traits\OptimizesImages;
use Filament\Support\Enums\Width;
use Closure;

class OptimizedImageUpload extends FileUpload
{
    use OptimizesImages;

    protected int $imageQuality = 60;
    protected Width|string|Closure|null $maxWidth = null;
    protected ?int $maxHeight = null;
    protected bool $showCompressionStats = false;


    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
            ->maxSize(20480)
            ->saveUploadedFileUsing(function ($file) {
                return $this->optimizeImage(
                    file: $file,
                    directory: $this->getDirectory() ?? 'images',
                    quality: $this->imageQuality,
                    maxWidth: $this->maxWidth,
                    maxHeight: $this->maxHeight
                );
            });
    }


    public function quality(int $quality): static
    {
        $this->imageQuality = max(1, min(100, $quality));
        return $this;
    }

    public function maxDimensions(?int $width = null, ?int $height = null): static
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
        return $this;
    }

    public function showCompressionStats(bool $show = true): static
    {
        $this->showCompressionStats = $show;

        if ($show) {
            $this->helperText('✨ Imagens são automaticamente convertidas para WebP e otimizadas para melhor performance.');
        }

        return $this;
    }

    public function highQuality(): static
    {
        return $this->quality(85);
    }

    public function standardQuality(): static
    {
        return $this->quality(60);
    }

    public function lowQuality(): static
    {
        return $this->quality(40);
    }
}
