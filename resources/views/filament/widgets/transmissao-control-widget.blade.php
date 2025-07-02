<x-filament-widgets::widget>
    

    <x-filament::section>
        
        @php
            $transmissao = $this->getTransmissao();
        @endphp
        
        {{-- CONTAINER PRINCIPAL --}}
        <div class="fi-section-content-ctn space-y-6">
            @if (session()->has('message'))
                <x-filament::section class="fi-section-success animate-fade-in-up rounded-xl bg-green-50 ring-1 ring-inset ring-green-500/10 dark:bg-green-950/20 dark:ring-green-500/30">
                    <div class="flex items-center gap-x-3">
                        <x-filament::icon icon="heroicon-o-check-circle" class="fi-success-icon h-6 w-6 text-green-600 dark:text-green-400" />
                        <p class="text-sm font-medium text-green-950 dark:text-green-50">{{ session('message') }}</p>
                    </div>
                </x-filament::section>
            @endif

            <!-- Status Principal -->
            <div class="animate-fade-in-up">
                @if($transmissao->status === 'online')
                    <!-- AO VIVO -->
                    <div class="fi-section rounded-xl bg-red-50 p-6 dark:bg-red-950/10 live-top-glow border border-red-200 dark:border-red-800/30">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-x-3">
                                    <div class="relative flex h-3 w-3">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                                    </div>
                                    <x-filament::badge color="danger" size="lg">AO VIVO</x-filament::badge>
                                </div>
                                
                                {{-- Timer com data correta --}}
                                @if($transmissao->iniciada_em)
                                    <div 
                                        wire:ignore 
                                        x-data="{
                                            time: '00:00:00',
                                            interval: null,
                                            init() {
                                                const startTime = '{{ $transmissao->iniciada_em->toIso8601String() }}';
                                                if (!startTime) return;
                                                
                                                const start = new Date(startTime).getTime();
                                                if (isNaN(start)) {
                                                    console.error('Data de início inválida para o timer:', startTime);
                                                    return;
                                                }
                                                
                                                if (this.interval) clearInterval(this.interval);

                                                this.updateTime(start);
                                                
                                                this.interval = setInterval(() => {
                                                    this.updateTime(start);
                                                }, 1000);
                                            },
                                            updateTime(start) {
                                                const now = new Date().getTime();
                                                const distance = now - start;

                                                if (distance < 0) {
                                                    this.time = '00:00:00';
                                                    clearInterval(this.interval);
                                                    return;
                                                }

                                                const hours = Math.floor(distance / (1000 * 60 * 60));
                                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                this.time = String(hours).padStart(2, '0') + ':' + 
                                                              String(minutes).padStart(2, '0') + ':' + 
                                                              String(seconds).padStart(2, '0');
                                            }
                                        }" 
                                        x-init="init()" 
                                        class="flex items-center gap-x-2 rounded-lg bg-red-100/50 px-3 py-1 text-sm font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400"
                                    >
                                        <x-filament::icon icon="heroicon-m-signal" class="h-5 w-5" />
                                        <span x-text="time"></span>
                                    </div>
                                @endif
                            </div>
                            
                            <dl class="grid gap-4 sm:grid-cols-2">
                                @if($transmissao->titulo_transmissao)
                                    <div class="glass-effect rounded-lg p-4 bg-white/70 border border-white/20 dark:bg-gray-800/50 dark:border-gray-700/30">
                                        <dt class="flex items-center gap-x-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-o-bars-3-bottom-left" class="h-5 w-5" /> 
                                            Título
                                        </dt>
                                        <dd class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                                            {{ $transmissao->titulo_transmissao }}
                                        </dd>
                                    </div>
                                @endif
                                
                                @if($transmissao->descricao)
                                    <div class="glass-effect rounded-lg p-4 bg-white/70 border border-white/20 dark:bg-gray-800/50 dark:border-gray-700/30">
                                        <dt class="flex items-center gap-x-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-o-chat-bubble-bottom-center-text" class="h-5 w-5" /> 
                                            Descrição
                                        </dt>
                                        <dd class="mt-2 text-base text-gray-950 dark:text-white">
                                            {{ $transmissao->descricao }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                            
                            @if($transmissao->youtube_url)
                                <div class="glass-effect rounded-lg p-4 bg-white/70 border border-white/20 dark:bg-gray-800/50 dark:border-gray-700/30">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-x-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-950/20">
                                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">YouTube</dt>
                                                @if($transmissao->youtube_video_id)
                                                    <dd class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                                        ID: {{ $transmissao->youtube_video_id }}
                                                    </dd>
                                                @endif
                                            </div>
                                        </div>
                                        <x-filament::link 
                                            href="{{ $transmissao->youtube_url }}" 
                                            target="_blank" 
                                            icon="heroicon-m-arrow-top-right-on-square" 
                                            icon-position="after" 
                                            color="danger" 
                                            size="sm"
                                        >
                                            Abrir
                                        </x-filament::link>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($transmissao->status === 'aguarde')
                    <div class="fi-section rounded-xl bg-orange-50 p-6 ring-1 ring-inset ring-orange-200 dark:bg-orange-950/20 dark:ring-orange-950/20">
                        <div class="flex items-center gap-x-4">
                            <x-filament::icon icon="heroicon-o-pause-circle" class="h-10 w-10 animate-pulse text-orange-500" />
                            <div>
                                <h4 class="text-lg font-bold text-orange-800 dark:text-orange-300">Transmissão em Espera</h4>
                                <p class="text-sm text-orange-600 dark:text-orange-400">Aguardando início. Você pode editar os detalhes ou iniciar agora.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="fi-section rounded-xl bg-gray-50 p-6 ring-1 ring-inset ring-gray-200 dark:bg-gray-950/20 dark:ring-gray-950/20">
                        <div class="flex items-center gap-x-4">
                            <x-filament::icon icon="heroicon-o-x-circle" class="h-10 w-10 text-gray-400" />
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-300">Nenhuma Transmissão Ativa</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">O centro de transmissão está offline. Inicie uma nova transmissão quando estiver pronto.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Formulários -->
            @if($showIniciarForm || $showEditarForm)
                <div class="animate-fade-in-up">
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-x-3">
                                @if($showIniciarForm)
                                    <x-filament::icon icon="heroicon-o-play-circle" class="h-6 w-6 text-green-600 dark:text-green-400" />
                                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Iniciar Nova Transmissão</h3>
                                @else
                                    <x-filament::icon icon="heroicon-o-pencil-square" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Editar Transmissão</h3>
                                @endif
                            </div>
                        </x-slot>
                        
                        <form wire:submit="{{ $showIniciarForm ? 'iniciarTransmissao' : 'editarTransmissao' }}" class="fi-form space-y-6">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-wrp">
                                    <label class="fi-fo-field-wrp-label">
                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Título da Transmissão</span>
                                    </label>
                                    <div class="fi-input-wrp">
                                        <input 
                                            type="text" 
                                            wire:model="titulo_transmissao" 
                                            placeholder="Ex: Sessão Ordinária - Câmara Municipal" 
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-blue-500 focus:ring-1 focus:ring-inset focus:ring-blue-500 disabled:opacity-70 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500"
                                        />
                                    </div>
                                    @error('titulo_transmissao')
                                        <p class="fi-fo-field-wrp-error-message text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-wrp">
                                    <label class="fi-fo-field-wrp-label">
                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                            Descrição <sup class="text-xs text-gray-500 dark:text-gray-400">(opcional)</sup>
                                        </span>
                                    </label>
                                    <div class="fi-input-wrp">
                                        <input 
                                            type="text" 
                                            wire:model="descricao" 
                                            placeholder="Descrição da transmissão" 
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-blue-500 focus:ring-1 focus:ring-inset focus:ring-blue-500 disabled:opacity-70 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-wrp">
                                    <label class="fi-fo-field-wrp-label">
                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">URL do YouTube</span>
                                    </label>
                                    <div class="fi-input-wrp">
                                        <input 
                                            type="url" 
                                            wire:model="youtube_url" 
                                            placeholder="https://www.youtube.com/watch?v=VIDEO_ID" 
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-blue-500 focus:ring-1 focus:ring-inset focus:ring-blue-500 disabled:opacity-70 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500"
                                        />
                                    </div>
                                    @error('youtube_url')
                                        <p class="fi-fo-field-wrp-error-message text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            @if($showIniciarForm)
                                <div class="fi-fo-field">
                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                        <input 
                                            type="checkbox" 
                                            wire:model="notificar_usuarios" 
                                            class="fi-checkbox-input rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:checked:border-blue-500 dark:checked:bg-blue-500"
                                        />
                                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                            Enviar notificação push para usuários do app
                                        </span>
                                    </label>
                                </div>
                            @endif
                            
                            <div class="fi-form-actions flex items-center gap-x-3 pt-4">
                                @if($showIniciarForm)
                                    <x-filament::button type="submit" color="success" icon="heroicon-o-play">
                                        Iniciar Transmissão
                                    </x-filament::button>
                                @else
                                    <x-filament::button type="submit" icon="heroicon-o-check">
                                        Salvar Alterações
                                    </x-filament::button>
                                @endif
                                <x-filament::button type="button" wire:click="fecharForms" color="gray">
                                    Cancelar
                                </x-filament::button>
                            </div>
                        </form>
                    </x-filament::section>
                </div>
            @endif
            
            @if(!$showIniciarForm && !$showEditarForm)
                <div class="fi-ac animate-fade-in-up flex flex-wrap gap-3 justify-center pt-2">
                    @if($transmissao->status === 'offline')
                        <x-filament::button 
                            wire:click="abrirFormIniciar" 
                            color="success" 
                            size="lg" 
                            icon="heroicon-o-play-circle" 
                            class="flex-1 min-w-[200px] max-w-[300px] justify-center transition-transform hover:scale-105"
                        >
                            Iniciar Transmissão
                        </x-filament::button>
                    @endif
                    
                    @if($transmissao->status === 'aguarde')
                        <x-filament::button 
                            wire:click="tornarOnline" 
                            wire:confirm="Deseja iniciar a transmissão que está em modo de espera?" 
                            color="success" 
                            size="lg" 
                            icon="heroicon-o-play" 
                            class="flex-1 min-w-[200px] max-w-[300px] justify-center transition-transform hover:scale-105"
                        >
                            Ir para AO VIVO
                        </x-filament::button>
                    @endif
                    
                    @if($transmissao->status !== 'aguarde')
                        <x-filament::button 
                            wire:click="colocarAguarde" 
                            color="warning" 
                            size="lg" 
                            icon="heroicon-o-clock" 
                            class="flex-1 min-w-[200px] max-w-[300px] justify-center transition-transform hover:scale-105"
                        >
                            Colocar em Aguarde
                        </x-filament::button>
                    @endif
                    
                    @if(in_array($transmissao->status, ['online', 'aguarde']))
                        <x-filament::button 
                            wire:click="abrirFormEditar" 
                            color="gray" 
                            size="lg" 
                            icon="heroicon-o-pencil-square" 
                            class="flex-1 min-w-[200px] max-w-[300px] justify-center transition-transform hover:scale-105"
                        >
                            Editar Detalhes
                        </x-filament::button>
                    @endif
                    
                    @if(in_array($transmissao->status, ['online', 'aguarde']))
                        <x-filament::button 
                            wire:click="finalizarTransmissao" 
                            wire:confirm="Tem certeza que deseja finalizar a transmissão?" 
                            color="danger" 
                            size="lg" 
                            icon="heroicon-o-stop-circle" 
                            class="flex-1 min-w-[200px] max-w-[300px] justify-center transition-transform hover:scale-105"
                        >
                            Finalizar Transmissão
                        </x-filament::button>
                    @endif
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>