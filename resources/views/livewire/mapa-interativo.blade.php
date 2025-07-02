
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900 dark:to-primary-800">
        <div class="flex items-center">
            <x-heroicon-o-map class="w-5 h-5 text-primary-600 dark:text-primary-400 mr-2" />
            <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-100">
                Mapa Interativo ({{ count($pontos) }} pontos)
            </h3>
        </div>
    </div>
    
    <!-- wire:ignore impede que o Livewire toque neste elemento -->
    <div wire:ignore>
        <div id="{{ $mapId }}" class="w-full" style="height: 600px;"></div>
    </div>
</div>

@once
@push('scripts')
<script>
    // ETAPA 1: Definir estado e funções em um escopo acessível
    // Manter um objeto global para o mapa evita múltiplas instâncias
    if (!window.mapaGlobal) {
        window.mapaGlobal = {
            map: null,
            markers: [],
            infoWindow: null,
            isReady: false,
            currentMapId: null
        };
    }

    // Cores por categoria
    const coresCategorias = {
        'educacao': '#3B82F6', 'saude': '#EF4444', 'lazer_esporte': '#10B981',
        'servicos_publicos': '#F59E0B', 'legislativo': '#8B5CF6', 'turismo': '#06B6D4',
        'religioso': '#A855F7', 'comercio_servicos': '#059669'
    };

    function updateGlobalMarkers(pontos) {
        if (!window.mapaGlobal.isReady || !window.mapaGlobal.map) return;
        window.mapaGlobal.markers.forEach(marker => marker.setMap(null));
        window.mapaGlobal.markers = [];
        if (!pontos || pontos.length === 0) return;

        const bounds = new google.maps.LatLngBounds();
        let validMarkers = 0;

        pontos.forEach((ponto, index) => {
            if (ponto.latitude && ponto.longitude) {
                const lat = parseFloat(ponto.latitude);
                const lng = parseFloat(ponto.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                const position = { lat, lng };
                const marker = new google.maps.Marker({
                    position,
                    map: window.mapaGlobal.map,
                    title: ponto.nome,
                    label: { text: (index + 1).toString(), color: 'white', fontWeight: 'bold', fontSize: '12px' },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE, scale: 20,
                        fillColor: coresCategorias[ponto.categoria] || '#6B7280',
                        fillOpacity: 1, strokeColor: 'white', strokeWeight: 3
                    }
                });

                const telefone = ponto.telefone ? `<br><strong>Telefone:</strong> ${ponto.telefone}` : '';
                const horario = ponto.funciona_24h ? '<br><strong>Funcionamento:</strong> 24 horas' : '';
                const infoContent = `<div style="padding: 15px; max-width: 300px; font-family: system-ui;"><h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px; font-weight: bold;">${ponto.nome}</h3><p style="margin: 0 0 8px 0; color: #3b82f6; font-size: 14px; font-weight: 600;">${ponto.categoria_label || 'Sem categoria'}</p><p style="margin: 0 0 5px 0; color: #4b5563; font-size: 13px;">${ponto.endereco_completo || 'Endereço não informado'}</p><p style="margin: 0 0 8px 0; color: #6b7280; font-size: 12px;">${ponto.bairro || 'Bairro não informado'}</p>${telefone}${horario}</div>`;

                marker.addListener('click', () => {
                    window.mapaGlobal.infoWindow.setContent(infoContent);
                    window.mapaGlobal.infoWindow.open(window.mapaGlobal.map, marker);
                });

                window.mapaGlobal.markers.push(marker);
                bounds.extend(position);
                validMarkers++;
            }
        });

        if (validMarkers > 0) {
            if (validMarkers === 1) {
                window.mapaGlobal.map.setCenter(window.mapaGlobal.markers[0].getPosition());
                window.mapaGlobal.map.setZoom(16);
            } else {
                window.mapaGlobal.map.fitBounds(bounds);
            }
        }
    }

    window.focarPontoGlobal = function(lat, lng, nome, index) {
        if (!window.mapaGlobal.isReady || !window.mapaGlobal.map) return;
        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
        window.mapaGlobal.map.setCenter(position);
        window.mapaGlobal.map.setZoom(17);
        if (window.mapaGlobal.markers[index]) {
            google.maps.event.trigger(window.mapaGlobal.markers[index], 'click');
        }
    };

    function initializeGlobalMap() {
        const mapElement = document.querySelector('[id^="mapa"]');
        if (!mapElement) return;

        // Evita reinicializar o mapa se ele já existe no elemento correto
        if (window.mapaGlobal.map && window.mapaGlobal.map.getDiv().id === mapElement.id) {
            return;
        }

        console.log('Inicializando o mapa no elemento:', mapElement.id);
        const mapOptions = {
            zoom: 13, center: { lat: -17.7972, lng: -50.9289 },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        window.mapaGlobal.map = new google.maps.Map(mapElement, mapOptions);
        window.mapaGlobal.infoWindow = new google.maps.InfoWindow();
        window.mapaGlobal.isReady = true;
        window.mapaGlobal.currentMapId = mapElement.id;
        
        // Dispara uma atualização para carregar os pontos iniciais
        const component = Livewire.find(mapElement.closest('[wire\\:id]').getAttribute('wire:id'));
        if (component) {
            updateGlobalMarkers(component.get('pontos'));
        }
    }

    function loadGoogleMapsAPI() {
        if (typeof google !== 'undefined' && google.maps) {
            initializeGlobalMap();
            return;
        }
        if (!window.googleMapsLoading) {
            window.googleMapsLoading = true;
            window.initMapCallback = function() {
                window.googleMapsLoaded = true;
                initializeGlobalMap();
            };
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapCallback&loading=async`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        } else if (window.googleMapsLoaded) {
            initializeGlobalMap();
        }
    }

    // ETAPA 2: Criar uma função para configurar a página do mapa
    const setupMapPage = () => {
        // Verifica se o elemento do mapa existe na página atual
        const mapElement = document.querySelector('[id^="mapa"]');
        if (mapElement) {
            // Se existir, carrega a API e inicializa o mapa
            loadGoogleMapsAPI();
        }
    };

    // ETAPA 3: Adicionar os listeners de ciclo de vida
    document.addEventListener('livewire:init', () => {
        // Listener para atualizações de pontos (quando você filtra)
        Livewire.on('pontosAtualizados', (event) => {
            updateGlobalMarkers(event.pontos || event[0].pontos || event[0]);
        });

        // Configura o mapa na carga inicial do Livewire
        setupMapPage();
    });

    // Listener para navegação SPA (quando você volta para a página do mapa)
    document.addEventListener('livewire:navigated', () => {
        // Reconfigura o mapa após a navegação
        setupMapPage();
    });

</script>
@endpush
@endonce