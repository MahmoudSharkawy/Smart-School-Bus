<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f5f5f7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart School-Bus Matrix — Apple Control Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f5f5f7;
        }
        .apple-blur {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        /* لتعديل شكل نافذة البيانات المنبثقة من الخريطة لتناسب طراز Apple iOS */
        .leaflet-popup-content-wrapper {
            border-radius: 14px !important;
            padding: 4px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
        }
    </style>
</head>
<body class="h-full antialiased text-[#1d1d1f] flex flex-col overflow-hidden">

    <header class="h-14 apple-blur border-b border-[#e8e8ed] fixed top-0 w-full z-[1050] flex items-center justify-between px-8">
        <div class="flex items-center space-x-4">
            <div class="bg-[#0071e3] text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-bus-school text-sm"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-semibold tracking-tight text-[#1d1d1f]">Smart School-Bus Matrix</span>
                <span class="text-[10px] font-medium text-[#86868b] uppercase tracking-wider">Enterprise Multi-Route Radar</span>
            </div>
        </div>
        <div class="flex items-center">
            <div class="flex items-center space-x-2 bg-[#e8e8ed]/60 px-3 py-1 rounded-full text-xs font-medium text-[#1d1d1f]">
                <span class="w-1.5 h-1.5 bg-[#34c759] rounded-full animate-pulse"></span>
                <span>All Matrix Clusters Live</span>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-[1600px] mx-auto w-full pt-20 px-8 pb-8 flex flex-col space-y-6 overflow-hidden">
        
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-[#1d1d1f]">Operations Dashboard</h2>
            <p class="text-sm text-[#86868b]">Real-time tracking, speed limits, and attendance log telemetry.</p>
        </div>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-[#e8e8ed] rounded-2xl p-6 shadow-sm flex flex-col justify-between h-28">
                <span class="text-xs font-semibold tracking-tight text-[#86868b] uppercase">Active Fleet Units</span>
                <h3 class="text-3xl font-bold tracking-tight text-[#1d1d1f]" id="active-buses-stat">0 Transit Buses</h3>
            </div>

            <div class="bg-white border border-[#e8e8ed] rounded-2xl p-6 shadow-sm flex flex-col justify-between h-28">
                <span class="text-xs font-semibold tracking-tight text-[#86868b] uppercase">Total On-Board Manifested</span>
                <h3 class="text-3xl font-bold tracking-tight text-[#0071e3]" id="total-students-stat">0 Students</h3>
            </div>

            <div class="bg-white border border-[#e8e8ed] rounded-2xl p-6 shadow-sm flex flex-col justify-between h-28">
                <span class="text-xs font-semibold tracking-tight text-[#86868b] uppercase">Fleet Status</span>
                <h3 class="text-3xl font-bold tracking-tight text-[#34c759]">Nominal</h3>
            </div>
        </section>

        <section class="flex-1 min-h-0 bg-white border border-[#e8e8ed] rounded-2xl p-2 shadow-sm flex">
            <div id="map" class="h-full w-full rounded-xl z-10"></div>
        </section>

    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <script>
        const map = L.map('map', { zoomControl: false }).setView([30.0444, 31.2357], 12);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);

        const busMarkers = {};
        const busDataStore = {}; // لتخزين بيانات كل باص وحساب إجمالي الطلاب لحظياً

        const socket = io('http://localhost:6002');

        socket.on('bus_location_updated', function(data) {
            const { bus_id, bus_number, driver, attendant_name, students_count, latitude, longitude, speed } = data;

            // حفظ وتحديث بيانات الباص في المخزن المحلي للمتصفح
            busDataStore[bus_id] = students_count;

            // 1. تحديث إجمالي عدد الأتوبيسات النشطة فوراً
            const currentActiveBuses = Object.keys(busDataStore).length;
            document.getElementById('active-buses-stat').innerText = `${currentActiveBuses} Transit Buses`;

            // 2. حساب إجمالي عدد الطلاب على متن كافة الحافلات النشطة حياً
            const globalStudentsSum = Object.values(busDataStore).reduce((a, b) => a + b, 0);
            document.getElementById('total-students-stat').innerText = `${globalStudentsSum} Students`;

            // تصميم علامة الأتوبيس الدائرية بنمط iOS مع نبضة حركية
            const appleMarkerHtml = `
                <div class="relative flex items-center justify-center w-7 h-7 bg-white border-2 border-[#0071e3] rounded-full shadow-md">
                    <i class="fa-solid fa-bus text-[11px] text-[#0071e3]"></i>
                    <span class="absolute inline-flex h-full w-full rounded-full bg-[#0071e3]/20 ${speed > 50 ? 'bg-red-500/30' : ''} animate-ping"></span>
                </div>`;

            const appleIcon = L.divIcon({
                className: 'custom-apple-icon',
                html: appleMarkerHtml,
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            // نافذة البيانات الكاملة المنسقة بنمط أبل الأنيق
            const popupContent = `
                <div class="font-sans text-xs text-[#1d1d1f] p-2 space-y-2" style="min-width: 180px;">
                    <div class="flex justify-between items-center border-b border-[#e8e8ed] pb-1.5">
                        <span class="font-bold text-sm text-[#0071e3]">${bus_number}</span>
                        <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] ${speed > 50 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'}">
                            ${speed} KM/H
                        </span>
                    </div>
                    <div class="grid grid-cols-1 gap-1 font-medium text-[#515154]">
                        <div><i class="fa-solid fa-user-steering text-gray-400 mr-1.5 w-3"></i><b>Driver:</b> ${driver}</div>
                        <div><i class="fa-solid fa-user-shield text-gray-400 mr-1.5 w-3"></i><b>Supervisor:</b> ${attendant_name}</div>
                        <div><i class="fa-solid fa-graduation-cap text-gray-400 mr-1.5 w-3"></i><b>Students:</b> <span class="text-[#0071e3] font-bold">${students_count} on board</span></div>
                    </div>
                </div>
            `;

            if (busMarkers[bus_id]) {
                busMarkers[bus_id].setLatLng([latitude, longitude]);
                // تحديث محتوى الكارت المفتوح بالسرعة والموقع الجديدين
                busMarkers[bus_id].getPopup().setContent(popupContent);
            } else {
                busMarkers[bus_id] = L.marker([latitude, longitude], { icon: appleIcon })
                    .addTo(map)
                    .bindPopup(popupContent);
            }
        });
    </script>
</body>
</html>