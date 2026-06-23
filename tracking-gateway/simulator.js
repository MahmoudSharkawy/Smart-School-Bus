const { io } = require('socket.io-client');
const socket = io('http://localhost:6001');

const simulatedBuses = [
    { bus_id: 1, bus_number: 'أ ر ج ٩٨٣', driver_name: 'Captain Metwally', latitude: 30.0444, longitude: 31.2357 },
    { bus_id: 2, bus_number: 'ق ط و ٥١٢', driver_name: 'Captain Shaban', latitude: 30.0512, longitude: 31.2410 }
];

socket.on('connect', () => {
    console.log('🚀 Egypt School-Bus Fleet Simulation Cluster Activated!');

    setInterval(() => {
        simulatedBuses.forEach(bus => {
            // إضافة حركة عشوائية طفيفة لمحاكاة السير الحقيقي في شوارع القاهرة
            bus.latitude += (Math.random() - 0.5) * 0.0012;
            bus.longitude += (Math.random() - 0.5) * 0.0012;

            // إرسال البيانات اللحظية لخادم الـ Node.js
            socket.emit('ping_bus_gps', bus);
        });
    }, 4000); // إرسال نبضة إحداثيات جديدة كل 4 ثوانٍ دقة عالية
});