<x-filament-panels::page>
    <div>
        <!-- Seção de Filtros -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-4">
                    <x-heroicon-o-funnel class="w-5 h-5 text-primary-600 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtros do Mapa</h3>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @foreach($this->getCategorias() as $key => $label)
                        <button 
                            type="button"
                            wire:click="filtrarPorCategoria('{{ $key }}')"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-sm
                                {{ $categoriaFiltro === $key 
                                    ? 'bg-primary-600 text-white shadow-lg ring-2 ring-primary-300' 
                                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600' 
                                }}"
                        >
                            @if($key === 'todos')
                                <x-heroicon-o-squares-2x2 class="w-4 h-4 mr-2" />
                            @elseif($key === 'educacao')
                                <x-heroicon-o-academic-cap class="w-4 h-4 mr-2" />
                            @elseif($key === 'saude')
                                <x-heroicon-o-heart class="w-4 h-4 mr-2" />
                            @elseif($key === 'lazer_esporte')
                                <x-heroicon-o-trophy class="w-4 h-4 mr-2" />
                            @elseif($key === 'servicos_publicos')
                                <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                            @elseif($key === 'legislativo')
                                <x-heroicon-o-scale class="w-4 h-4 mr-2" />
                            @else
                                <x-heroicon-o-map-pin class="w-4 h-4 mr-2" />
                            @endif
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Layout Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Coluna Esquerda: Lista de Pontos -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-full">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900 dark:to-primary-800 rounded-t-xl">
                        <div class="flex items-center">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-primary-600 dark:text-primary-400 mr-2" />
                            <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-100">
                                Pontos de Interesse ({{ count($pontosInteresse) }})
                            </h3>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto" style="max-height: 600px;">
                        @forelse($pontosInteresse as $index => $ponto)
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-primary-50 dark:hover:bg-gray-700 transition-all cursor-pointer group"
                                 onclick="focarPontoNoMapa({{ $ponto->latitude }}, {{ $ponto->longitude }}, '{{ addslashes($ponto->nome) }}', {{ $index }})">
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm group-hover:scale-110 transition-transform">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $ponto->nome }}
                                        </h4>
                                        <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-1">
                                            {{ $ponto->categoria_label }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                            {{ $ponto->endereco_completo }}
                                        </p>
                                        @if($ponto->bairro)
                                            <span class="inline-block bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded-full mt-2">
                                                {{ $ponto->bairro }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 text-primary-500" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <x-heroicon-o-map-pin class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" />
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Nenhum ponto encontrado</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Tente selecionar uma categoria diferente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Mapa -->
            <div class="lg:col-span-2">
                @livewire('mapa-interativo', ['pontos' => $pontosInteresse])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function focarPontoNoMapa(lat, lng, nome, index) {
            // Usar a função global unificada
            if (typeof window.focarPontoGlobal === 'function') {
                window.focarPontoGlobal(lat, lng, nome, index);
            } else {
                console.log('Função de foco global não disponível ainda');
            }
        }
    </script>
    @endpush
</x-filament-panels::page>