<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Transit - School Operations Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="h-full font-sans antialiased text-slate-200 flex overflow-hidden">

    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex z-[1001]">
        <div>
            <div class="h-16 flex items-center px-6 border-b border-slate-800 space-x-3">
                <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-bus text-base animate-pulse"></i>
                </div>
                <span class="text-lg font-black tracking-wider text-white">NEXUS<span class="text-indigo-500">BUS</span></span>
            </div>
            <nav class="p-4 space-y-1">
                <a href="#" class="flex items-center space-x-3 bg-indigo-600/10 text-indigo-400 px-4 py-3 rounded-xl font-medium text-sm border border-indigo-500/20">
                    <i class="fa-solid fa-tower-radar text-sm"></i>
                    <span>Fleet Operations Radar</span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center space-x-3 p-2 bg-slate-950/40 rounded-xl border border-slate-800/60">
                <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></div>
                <span class="text-xs font-bold text-slate-400">Node Status: Live</span>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-6 z-[1000]">
            <h1 class="text-base font-bold text-white flex items-center tracking-tight">
                <i class="fa-solid fa-shield-halved text-indigo-500 mr-2"></i> Real-Time Student Transit Safety Control Room
            </h1>
        </header>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                    <span class="text-xs font-mono tracking-wider text-indigo-400 uppercase">Live Fleet</span>
                    <h3 class="text-2xl font-black text-white mt-1" id="active-buses-stat">{{ $activeBusesCount }} Tracking Now</h3>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                    <span class="text-xs font-mono tracking-wider text-amber-400 uppercase">Total Students Enrolled</span>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalStudents }} Manifested</h3>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                    <span class="text-xs font-mono tracking-wider text-rose-400 uppercase">SLA Delays</span>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $delayedBusesCount }} Breaches</h3>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 shadow-xl">
                    <div id="map" class="h-[550px] w-full rounded-xl border border-slate-800 z-10"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <script>
        // التمركز الافتراضي فوق خريطة القاهرة الجغرافية
        const map = L.map('map').setView([30.0444, 31.2357], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap © CARTO'
        }).addTo(map);

        const busMarkers = {};
        
        // الاتصال ببوابة خادم النقل والـ Node.js الذكي
        const socket = io('http://localhost:6001');

        socket.on('bus_location_updated', function(data) {
            const { bus_id, bus_number, driver, latitude, longitude } = data;

            // تحديث العداد بصورة حية عند استقبال الإرسال الأول لباص جديد
            document.getElementById('active-buses-stat').innerText = Object.keys(busMarkers).length + 1 + " Tracking Now";

            if (busMarkers[bus_id]) {
                busMarkers[bus_id].setLatLng([latitude, longitude]);
            } else {
                // أيقونة حافلة مخصصة مضيئة للـ Dark Mode
                busMarkers[bus_id] = L.marker([latitude, longitude])
                    .addTo(map)
                    .bindPopup(`<div class="p-1 font-sans text-slate-900"><b class="text-indigo-600">Bus Plate: ${bus_number}</b><br>Driver: ${driver}</div>`)
                    .openPopup();
            }
        });
    </script>
</body>
</html>