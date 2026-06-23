const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: { origin: "*" } // السماح لـ Laravel بالاستماع دون تعارض حماية
});

io.on('connection', (socket) => {
    console.log(`📡 Telemetry Link Established with Node: ${socket.id}`);

    // الاستماع لإحداثيات الـ GPS والبيانات الموجهة من أتوبيسات المدرسة
    socket.on('ping_bus_gps', (data) => {
        console.log(`🚌 Fleet Pipeline Log [Bus ${data.bus_number}]: Lat ${data.latitude} | Speed: ${data.speed} KM/H`);

        // إعادة بث الإحداثيات والبيانات كاملة فوراً إلى لوحة تحكم لارافيل
        io.emit('bus_location_updated', {
            bus_id: data.bus_id,
            bus_number: data.bus_number,
            driver: data.driver_name,
            attendant_name: data.attendant_name, // إضافة المشرفة
            students_count: data.students_count, // إضافة عدد الطلاب
            speed: data.speed,                   // إضافة السرعة الحية
            latitude: data.latitude,
            longitude: data.longitude
        });
    });

    socket.on('disconnect', () => {
        console.log('📡 Telemetry Link Severed.');
    });
});

server.listen(6002, () => {
    console.log('⚡ Nexus Transit Gateway Engine operational on core port 6002');
});