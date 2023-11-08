<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-bar" :style="{paddingTop: statusBarHeight+'px'}">
			<view class="nav-search df">
				<view class="nav-back df" @click="navBack">
					<image src="/static/img/back.png"></image>
				</view>
				<view class="search-box df">
					<input v-model="keyword" @confirm="searchConfirm" placeholder="输入搜索关键字" confirm-type="search"
						:focus="true" />
				</view>
			</view>
		</view>
		<!-- Content Box -->
		<view class="content" :style="{marginTop: statusBarHeight+44+'px'}">
			<view v-if="isEmpty" class="empty-box">
				<image :src="insetUrl+'6.png'" />
				<view class="text">空，换个词试试～</view>
			</view>
			<view v-else class="content-box">
				<block v-for="(item,idx) in list" :key="item.id">
					<dynamicBox :item="item" :idx="idx" @likeback="likeclick"></dynamicBox>
				</block>
				<block v-if="!isEmpty">
					<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
					<view v-else class="no-more">没有更多了</view>
				</block>
			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	import dynamicBox from '@/components/dynamic/box.vue';
	export default {
		components: {
			dynamicBox
		},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				insetUrl: api.insetUrl,
				keyword: '',
				list: [],
				page: 1,
				isEmpty: false,
				loadStatus: 'more',
			}
		},
		onLoad() {},
		methods: {
			searchConfirm() {
				let that = this;
				if (that.keyword) {
					that.loadStatus = 'loading';
					that.isEmpty = false;
					util.request(api.recommendDynamicUrl, {
						page: that.page,
						keyword: that.keyword,
					}).then(function(res) {
						if (res.data.data.length > 0) {
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
				}
			},
			likeclick(e) {
				this.list[e.idx].is_like = e.is_like;
				this.list[e.idx].like_count = e.like_count;
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/circle'
					})
				}
			}
		},
		onReachBottom() {
			if (this.list.length) {
				this.page = this.page + 1;
				this.searchConfirm();
			}
		},
	}
</script>

<style>
	@import url("/static/css/circle/search.css");
</style>