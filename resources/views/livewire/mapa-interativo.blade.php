
<div wire:init="carregarDadosIniciais" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
    // SCRIPT COMPLETO E CORRIGIDO
    if (!window.mapaGlobal) {
        window.mapaGlobal = {
            map: null,
            markers: [],
            infoWindow: null,
            isReady: false,
            pendingPoints: null // Para o "handshake"
        };
    }

    const coresCategorias = { 'educacao': '#3B82F6', 'saude': '#EF4444', 'lazer_esporte': '#10B981', 'servicos_publicos': '#F59E0B', 'legislativo': '#8B5CF6', 'turismo': '#06B6D4', 'religioso': '#A855F7', 'comercio_servicos': '#059669' };

    function updateGlobalMarkers(pontos) {
        if (!window.mapaGlobal.isReady || !window.mapaGlobal.map) return;
        window.mapaGlobal.markers.forEach(marker => marker.setMap(null));
        window.mapaGlobal.markers = [];
        if (!pontos || pontos.length === 0) return;
        const bounds = new google.maps.LatLngBounds();
        let validMarkers = 0;
        pontos.forEach((ponto, index) => {
            if (!ponto.latitude || !ponto.longitude) return;
            const lat = parseFloat(ponto.latitude);
            const lng = parseFloat(ponto.longitude);
            if (isNaN(lat) || isNaN(lng)) return;
            const position = { lat, lng };
            const marker = new google.maps.Marker({
                position,
                map: window.mapaGlobal.map,
                title: ponto.nome,
                label: { text: (index + 1).toString(), color: 'white', fontWeight: 'bold', fontSize: '12px' },
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 20, fillColor: coresCategorias[ponto.categoria] || '#6B7280', fillOpacity: 1, strokeColor: 'white', strokeWeight: 3 }
            });
            const telefone = ponto.telefone ? `<br><strong>Telefone:</strong> ${ponto.telefone}` : '';
            const horario = ponto.funciona_24h ? `<br><strong>Funcionamento:</strong> 24 horas'` : '';
            const infoContent = `<div style="padding: 15px; max-width: 300px; font-family: system-ui;"><h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px; font-weight: bold;">${ponto.nome}</h3><p style="margin: 0 0 8px 0; color: #3b82f6; font-size: 14px; font-weight: 600;">${ponto.categoria_label || 'Sem categoria'}</p><p style="margin: 0 0 5px 0; color: #4b5563; font-size: 13px;">${ponto.endereco_completo || 'Endereço não informado'}</p><p style="margin: 0 0 8px 0; color: #6b7280; font-size: 12px;">${ponto.bairro || 'Bairro não informado'}</p>${telefone}${horario}</div>`;
            marker.addListener('click', () => {
                window.mapaGlobal.infoWindow.setContent(infoContent);
                window.mapaGlobal.infoWindow.open(window.mapaGlobal.map, marker);
            });
            window.mapaGlobal.markers.push(marker);
            bounds.extend(position);
            validMarkers++;
        });
        if (validMarkers > 1) { window.mapaGlobal.map.fitBounds(bounds); } 
        else if (validMarkers === 1) {
            window.mapaGlobal.map.setCenter(window.mapaGlobal.markers[0].getPosition());
            window.mapaGlobal.map.setZoom(16);
        }
    }

    // SUBSTITUA QUALQUER VERSÃO ANTIGA DESTA FUNÇÃO POR ESTA:
    window.focarPontoGlobal = function(lat, lng, nome, index) {
        // 1. Verifica se o mapa está pronto
        if (!window.mapaGlobal.isReady || !window.mapaGlobal.map) {
            console.error('Mapa não está pronto para focar.');
            return;
        }

        // 2. Cria o objeto de posição
        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };

        // 3. Centraliza o mapa e aplica o zoom
        window.mapaGlobal.map.setCenter(position);
        window.mapaGlobal.map.setZoom(17);

        // 4. Dispara o evento de clique no marcador correspondente para abrir a info-window
        if (window.mapaGlobal.markers && window.mapaGlobal.markers[index]) {
            google.maps.event.trigger(window.mapaGlobal.markers[index], 'click');
        } else {
            console.warn(`Marcador com índice ${index} não encontrado.`);
        }
    };

    function initializeGlobalMap() {
        const mapElement = document.querySelector('[id^="mapa"]');
        if (!mapElement || (window.mapaGlobal.map && window.mapaGlobal.map.getDiv().id === mapElement.id)) return;
        const mapOptions = { zoom: 13, center: { lat: -17.7972, lng: -50.9289 }, mapTypeId: google.maps.MapTypeId.ROADMAP };
        window.mapaGlobal.map = new google.maps.Map(mapElement, mapOptions);
        window.mapaGlobal.infoWindow = new google.maps.InfoWindow();
        window.mapaGlobal.isReady = true;

        // O "HANDSHAKE": Se pontos chegaram antes do mapa, desenhe-os agora.
        if (window.mapaGlobal.pendingPoints) {
            console.log('Mapa pronto. Desenhando pontos pendentes.');
            updateGlobalMarkers(window.mapaGlobal.pendingPoints);
            window.mapaGlobal.pendingPoints = null;
        }
    }

    function loadGoogleMapsAPI() {
        if (typeof google !== 'undefined' && google.maps) { initializeGlobalMap(); return; }
        if (!window.googleMapsLoading) {
            window.googleMapsLoading = true;
            window.initMapCallback = function() { window.googleMapsLoaded = true; initializeGlobalMap(); };
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google-maps-key') }}&callback=initMapCallback&loading=async`;
            script.async = true; script.defer = true;
            document.head.appendChild(script);
        } else if (window.googleMapsLoaded) { initializeGlobalMap(); }
    }

    const setupMapPage = () => { if (document.querySelector('[id^="mapa"]')) { loadGoogleMapsAPI(); } };

    document.addEventListener('livewire:init', () => {
        Livewire.on('pontosAtualizados', (event) => {
            const pontos = event.pontos || (event[0] && event[0].pontos) || event[0];
            if (!pontos) return;

            // O "HANDSHAKE": Se o mapa não está pronto, armazene os pontos. Se está, desenhe.
            if (window.mapaGlobal.isReady) {
                updateGlobalMarkers(pontos);
            } else {
                console.log('Mapa não está pronto. Armazenando pontos para mais tarde.');
                window.mapaGlobal.pendingPoints = pontos;
            }
        });
        setupMapPage();
    });

    document.addEventListener('livewire:navigated', () => { setupMapPage(); });
</script>
@endpush
@endonce