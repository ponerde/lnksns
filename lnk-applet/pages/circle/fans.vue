<template>
	<view class="container">
		<view class="box">
			<view class="box-title">{{ name }}的圈友 🎉</view>
			<view class="box-doc">共有 {{user_count}} 人加入</view>
			<view class="data-box">
				<view class="data-item df" v-for="(v,i) in list" :key="i" :data-id="v.user_id" @click="toUser">
					<image class="img" :src="v.user.avatar"></image>
					<view class="data-txt df">
						<view class="ohto">{{v.user.name}}</view>
					</view>
				</view>
			</view>
			<block v-if="!isEmpty">
				<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
				<view v-else class="no-more">没有更多了️</view>
			</block>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		components: {},
		data() {
			return {
				id: 0,
				name: '...',
				user_count: 0,
				list: [],
				page: 1,
				isEmpty: false,
				loadStatus: 'more',
			}
		},
		onLoad(option) {
			this.id = option.id;
			this.name = option.name;
			this.user_count = option.user_count;
			this.circleFans();
		},
		methods: {
			circleFans() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.circleFansUrl, {
					id: that.id,
					page: that.page
				}).then(function(res) {
					if (res.data && res.data.data.length) {
						that.list = that.list.concat(res.data.data);
						that.page = res.data.current_page;
						that.loadStatus = 'more';
					} else if (that.page == 1) {
						that.isEmpty = true;
						that.loadStatus = 'more';
					} else {
						that.loadStatus = 'no-more';
					}
				});
			},
			toUser(e) {
				uni.navigateTo({
					url: '/pages/user/details?id=' + e.currentTarget.dataset.id
				})
			},
		},
		onReachBottom() {
			if (this.list.length) {
				this.page = this.page + 1;
				this.circleFans();
			}
		},
	}
</script>

<style>
	@import url("/static/css/circle/fans.css");
</style>