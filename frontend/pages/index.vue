<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">加盟店一覧</h1>
    <div class="mb-4">
      <input v-model="search" placeholder="店舗名で検索" class="border p-2 rounded" />
      <select v-model="category" class="border p-2 rounded ml-2">
        <option value="">カテゴリー選択</option>
        <option value="gu">GU</option>
        <option value="yuniquro">ユニクロ</option>
        <!-- その他カテゴリー -->
      </select>
    </div>
    <ul>
      <li v-for="shop in shops" :key="shop.id" class="mb-4 p-4 border rounded">
        <h2 class="text-xl font-semibold">{{ shop.name }}</h2>
        <p>カテゴリー： {{ shop.category }}</p>
        <button @click="toggleFavorite(shop)" class="bg-blue-500 text-white px-2 py-1 rounded">
          {{ shop.isFavorite ? 'お気に入り解除' : 'お気に入り登録' }}
        </button>
        <div v-if="shop.coupons && shop.coupons.length > 0">
          <h3 class="font-semibold mt-2">利用可能クーポン</h3>
          <ul>
            <li v-for="coupon in shop.coupons" :key="coupon.id">
              {{ coupon.description }}
              <button @click="redeemCoupon(coupon)" class="bg-green-500 text-white px-2 py-1 rounded ml-2">
                取得
              </button>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useFetch } from '#app';

const search = ref('');
const category = ref('');

const { data: shops, refresh } = await useFetch('/api/shops', {
  query: () => ({
    search: search.value,
    category: category.value,
  }),
  lazy: true,
});

watch([search, category], () => {
  refresh();
});

const toggleFavorite = async (shop) => {
  try {
    await useFetch(`/api/shops/${shop.id}/favorite`, { method: 'POST' });
    shop.isFavorite = !shop.isFavorite;
  } catch (error) {
    console.error('お気に入りの切り替えに失敗しました:', error);
  }
};

const redeemCoupon = async (coupon) => {
  try {
    await useFetch(`/api/coupons/${coupon.id}/redeem`, { method: 'POST' });
    // クーポン取得後の処理、ユーザーに通知を表示
    alert('クーポンを取得しました！');
  } catch (error) {
    console.error('クーポンの取得に失敗しました:', error);
  }
};
</script>