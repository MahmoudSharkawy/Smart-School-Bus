const { io } = require('socket.io-client');
const socket = io('http://localhost:6002');
// مصفوفة بأسماء السائقين والمشرفات لإنشاء باصات عشوائية ومكثفة
const drivers = ['Captain Ahmed', 'Captain Shaban', 'Captain Metwally', 'Captain Youssef', 'Captain Mostafa', 'Captain Ramadan', 'Captain Kareem'];
const attendants = ['Miss Mona', 'Miss Sarah', 'Miss Fatma', 'Miss Aya', 'Miss Rania', 'Miss Heba', 'Miss Zainab'];

const simulatedBuses = [];

// توليد 28 باصاً جغرافياً في نقاط مختلفة حول القاهرة الكبرى
for (let i = 1; i <= 28; i++) {
    simulatedBuses.push({
        bus_id: i,
        bus_number: `${String.fromCharCode(1600 + (i % 10))}${(i * 3) % 9} ${i + 400} مصر`,
        driver_name: drivers[i % drivers.length],
        attendant_name: attendants[i % attendants.length],
        students_count: Math.floor(Math.random() * 15) + 10, // من 10 لـ 25 طالب
        // توزيع الباصات في محيط القاهرة (مصر الجديدة، التجمع، المعادي، المهندسين)
        latitude: 30.0444 + (Math.random() - 0.5) * 0.09,
        longitude: 31.2357 + (Math.random() - 0.5) * 0.09,
        speed: Math.floor(Math.random() * 40) + 20 // سرعة بين 20 و 60 كم/س
    });
}

socket.on('connect', () => {
    console.log(`🚀 Matrix Cluster Connected: Simulating ${simulatedBuses.length} Active School Buses!`);

    setInterval(() => {
        simulatedBuses.forEach(bus => {
            // محاكاة تحرك حقيقي في الشوارع وسرعات متغيرة
            bus.latitude += (Math.random() - 0.5) * 0.0006;
            bus.longitude += (Math.random() - 0.5) * 0.0006;
            bus.speed = Math.max(0, Math.min(100, bus.speed + Math.floor((Math.random() - 0.5) * 10)));

            // بث حزمة البيانات المتكاملة عبر الـ WebSockets
            socket.emit('ping_bus_gps', bus);
        });
    }, 4000); // تحديث عالي التردد كل 4 ثوانٍ
});