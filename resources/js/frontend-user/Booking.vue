<!--
  乘客叫車頁面 - Vue Option API
  模擬手機尺寸 (max-width: 480px)
  起點、終點輸入，由地址經緯度自動計算距離與預估金額
  確認叫車後 POST /api/orders，成功顯示「等待司機接單中...」動畫
-->
<template>
  <div class="booking-page">
    <div class="booking-card">
      <div class="card-header">
        <span class="icon-taxi">🚕</span>
        <h1 class="booking-title">乘客叫車</h1>
        <p class="booking-subtitle">輸入起點與終點，系統自動計算距離與車資</p>
      </div>

      <!-- 表單區塊：未叫車成功時顯示 -->
      <form v-if="!orderSuccess" @submit.prevent="submitOrder" class="booking-form">
        <!-- 起點輸入 -->
        <div class="form-group">
          <label for="start">
            <span class="label-icon">📍</span> 起點
          </label>
          <input
            id="start"
            v-model="form.start_location"
            type="text"
            placeholder="例：台北車站"
            class="form-input"
          />
        </div>

        <!-- 終點輸入 -->
        <div class="form-group">
          <label for="end">
            <span class="label-icon">🎯</span> 終點
          </label>
          <input
            id="end"
            v-model="form.end_location"
            type="text"
            placeholder="例：松山機場"
            class="form-input"
          />
        </div>

        <!-- 預估顯示：計算後才顯示 -->
        <div v-if="estimatedDistance > 0" class="estimate-block">
          <div class="estimate-row">
            <span>預估距離</span>
            <strong>{{ estimatedDistance.toFixed(1) }} 公里</strong>
          </div>
          <div class="estimate-row highlight">
            <span>預估車資</span>
            <strong>{{ estimatedPrice }} 元</strong>
          </div>
        </div>

        <!-- 預估按鈕：先計算距離與車資 -->
        <button
          v-if="canSubmit && !isSubmitting && estimatedDistance === 0"
          type="button"
          @click="calculateEstimate"
          class="estimate-btn"
        >
          預估車資
        </button>

        <!-- 錯誤訊息 -->
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

        <!-- 確認叫車按鈕 -->
        <button
          type="submit"
          :disabled="isSubmitting || !canSubmit"
          class="submit-btn"
        >
          <span v-if="isSubmitting" class="btn-loading"></span>
          {{ isSubmitting ? '正在計算距離...' : '確認叫車' }}
        </button>
      </form>

      <!-- 成功區塊：叫車成功後顯示 -->
      <div v-else class="success-block">
        <div class="success-icon">✓</div>
        <div class="loading-icon">
          <div class="spinner"></div>
        </div>
        <p class="success-text">等待司機接單中...</p>
        <p class="order-hint">訂單編號：{{ orderId }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

/**
 * 乘客叫車頁面 - Option API
 * 距離由起點→終點自動計算（Geocoding + Haversine）
 * 車資：每公里 50 元
 */
export default {
  name: 'Booking',

  data() {
    return {
      form: {
        start_location: '',
        end_location: '',
      },
      isSubmitting: false,
      errorMessage: '',
      orderSuccess: false,
      orderId: null,
      estimatedDistance: 0,
      estimatedPrice: 0,
      cachedCoords: null,
    };
  },

  computed: {
    canSubmit() {
      return (
        this.form.start_location.trim() !== '' &&
        this.form.end_location.trim() !== '' &&
        !this.isSubmitting
      );
    },
  },

  methods: {
    /**
     * 使用 Nominatim (OpenStreetMap) 取得地址經緯度
     */
    async geocodeAddress(address) {
      const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`;
      const res = await axios.get(url, {
        headers: { 'User-Agent': 'TaxiDev-Passenger/1.0' },
      });
      if (res.data && res.data[0]) {
        return { lat: parseFloat(res.data[0].lat), lng: parseFloat(res.data[0].lon) };
      }
      return null;
    },

    /**
     * Haversine 公式：計算兩點距離（公里）
     */
    haversine(lat1, lng1, lat2, lng2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLng = (lng2 - lng1) * Math.PI / 180;
      const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    },

    async fetchCoords() {
      const startAddr = this.form.start_location.trim();
      const endAddr = this.form.end_location.trim();
      const [startCoords, endCoords] = await Promise.all([
        this.geocodeAddress(startAddr),
        this.geocodeAddress(endAddr),
      ]);
      if (!startCoords || !endCoords) return null;
      const distance = this.haversine(
        startCoords.lat, startCoords.lng,
        endCoords.lat, endCoords.lng
      );
      return { startCoords, endCoords, distance, totalPrice: Math.ceil(distance * 50) };
    },

    async calculateEstimate() {
      if (!this.canSubmit || this.isSubmitting) return;
      this.errorMessage = '';
      this.isSubmitting = true;
      try {
        const result = await this.fetchCoords();
        if (!result) {
          this.errorMessage = '無法辨識地址，請輸入更完整的起點與終點。';
          return;
        }
        this.estimatedDistance = result.distance;
        this.estimatedPrice = result.totalPrice;
        this.cachedCoords = result;
      } catch {
        this.errorMessage = '計算預估時發生錯誤，請稍後再試。';
      } finally {
        this.isSubmitting = false;
      }
    },

    async submitOrder() {
      if (!this.canSubmit || this.isSubmitting) return;

      this.errorMessage = '';
      this.isSubmitting = true;

      try {
        const startAddr = this.form.start_location.trim();
        const endAddr = this.form.end_location.trim();
        let startCoords, endCoords, distance, totalPrice;

        if (this.cachedCoords && this.cachedCoords.distance > 0) {
          ({ startCoords, endCoords, distance, totalPrice } = this.cachedCoords);
          this.estimatedDistance = distance;
          this.estimatedPrice = totalPrice;
        } else {
          const result = await this.fetchCoords();
          if (!result) {
            this.errorMessage = '無法辨識地址，請輸入更完整的起點與終點。';
            return;
          }
          ({ startCoords, endCoords, distance, totalPrice } = result);
          this.estimatedDistance = distance;
          this.estimatedPrice = totalPrice;
        }

        const payload = {
          start_location: startAddr,
          end_location: endAddr,
          distance: this.estimatedDistance,
          start_lat: startCoords.lat,
          start_lng: startCoords.lng,
          end_lat: endCoords.lat,
          end_lng: endCoords.lng,
        };

        const { data } = await axios.post('/api/orders', payload);

        if (data.success) {
          this.orderId = data.data?.order_id ?? null;
          this.orderSuccess = true;
        } else {
          this.errorMessage = data.message || '叫車失敗，請稍後再試。';
        }
      } catch (err) {
        const msg = err.response?.data?.message ?? err.message ?? '網路錯誤，請稍後再試。';
        this.errorMessage = msg;
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
/* 背景：漸層灰藍，非純白 */
.booking-page {
  min-height: 100vh;
  padding: 2rem 1.5rem;
  background: linear-gradient(160deg, #f0f4f8 0%, #e2e8f0 40%, #cbd5e1 100%);
  font-family: 'Instrument Sans', system-ui, sans-serif;
}

/* 卡片容器 */
.booking-card {
  max-width: 420px;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 1.25rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
  padding: 2rem;
  backdrop-filter: blur(8px);
}

.card-header {
  text-align: center;
  margin-bottom: 2rem;
}

.icon-taxi {
  display: block;
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.booking-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.booking-subtitle {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.label-icon {
  font-size: 1rem;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.75rem;
  font-size: 1rem;
  color: #1e293b;
  background: #f8fafc;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input::placeholder {
  color: #94a3b8;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* 預估區塊 */
.estimate-block {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  border-radius: 0.75rem;
  padding: 1rem 1.25rem;
  border: 1px solid #bfdbfe;
}

.estimate-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
  color: #475569;
  padding: 0.25rem 0;
}

.estimate-row.highlight {
  color: #1e40af;
  font-size: 1rem;
  margin-top: 0.25rem;
  padding-top: 0.5rem;
  border-top: 1px solid #bfdbfe;
}

.estimate-row strong {
  color: #1e40af;
}

.estimate-btn {
  padding: 0.75rem 1.25rem;
  background: #f1f5f9;
  color: #475569;
  border: 2px solid #e2e8f0;
  border-radius: 0.75rem;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.estimate-btn:hover {
  background: #e2e8f0;
  border-color: #cbd5e1;
  color: #334155;
}

.error-message {
  color: #dc2626;
  font-size: 0.875rem;
  margin: 0;
  padding: 0.5rem;
  background: #fef2f2;
  border-radius: 0.5rem;
}

/* 按鈕 */
.submit-btn {
  position: relative;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-loading {
  display: inline-block;
  width: 1em;
  height: 1em;
  border: 2px solid rgba(255,255,255,0.5);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  vertical-align: middle;
  margin-right: 0.35em;
}

/* 成功區塊 */
.success-block {
  text-align: center;
  padding: 2rem 1rem;
}

.success-icon {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0 auto 1rem;
}

.loading-icon {
  margin-bottom: 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #2563eb;
  border-radius: 50%;
  margin: 0 auto;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.success-text {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.25rem;
}

.order-hint {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}
</style>
