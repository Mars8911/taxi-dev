/**
 * 乘客端 frontend-user 入口
 * 掛載 Booking.vue 到 #booking-app
 */
import { createApp } from 'vue';
import Booking from './Booking.vue';

createApp(Booking).mount('#booking-app');
