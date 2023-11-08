<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box">
			<view class="nav-item df" @click="navClick" :data-i="i" v-for="(v,i) in navarr" :key="i">
				<image v-if="navidx==i" :src="v.icons"></image>
				<image v-else :src="v.icon"></image>
				<view v-if="v.count">{{v.count}}</view>
			</view>
		</view>
		<!-- Content Box -->
		<view class="content">
			<view class="empty-box" v-if="isEmpty">
				<image :src="insetUrl+'3.png'" />
				<view class="text">在信息爆炸的时代，这里格外宁静</view>
			</view>
			<view class="list" v-else>
				<block v-for="(item,idx) in list" :key="idx">
					<view class="card-box" @click="toNavigate" :data-url="item.content_url" :data-id="item.id"
						:data-idx="idx">
						<view class="card-item">
							<view class="badge" v-if="item.read == 0"></view>
							<view class="card-user df" @click.stop="toNavigate" :data-url="item.avatar_url"
								:data-id="item.id" :data-idx="idx">
								<image :src="item.user.avatar" mode="aspectFill"></image>
								<view class="name df">
									<view class="t1 ohto">{{ item.user.name }}</view>
									<view class="t2">{{ item.title }}</view>
								</view>
							</view>
							<view class="card-user-content">
								<view class="card-content" :style="{width: item.img?'calc(100% - 130rpx)':'100%'}">
									{{ item.content }}
								</view>
								<lazy-load v-if="item.img" :src="item.img" width="100rpx" height="100rpx"
									border-radius="8rpx"></lazy-load>
							</view>
							<view class="card-time">
								{{ item.create_time }}
							</view>
						</view>
					</view>
				</block>
				<uni-load-more :status="loadStatus"></uni-load-more>
			</view>
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
				insetUrl: api.insetUrl,
				navarr: [{
					count: 0,
					icon: '/static/img/t-1.png',
					icons: '/static/img/t-2.png',
				}, {
					count: 0,
					icon: '/static/img/x-1.png',
					icons: '/static/img/x-2.png',
				}, {
					count: 0,
					icon: '/static/img/p-1.png',
					icons: '/static/img/p-2.png',
				}],
				navidx: 0,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
			}
		},
		onPullDownRefresh() {
			this.list = [];
			this.page = 1;
			this.getMessageCount();
			this.getMessage();
			uni.stopPullDownRefresh();
		},
		async onLoad(option) {
			uni.showShareMenu();
			await this.$onLaunched;
		},
		onShow() {
			this.list = [];
			this.page = 1;
			this.getMessageCount();
			this.getMessage();
		},
		onHide() {
			util.request(api.readMessageUrl, {
				type: this.navidx
			}, "POST");
		},
		methods: {
			getMessageCount() {
				let that = this;
				util.request(api.getMessageCountUrl, {}).then(function(res) {
					that.navarr[0].count = res.data[0];
					that.navarr[1].count = res.data[1];
					that.navarr[2].count = res.data[2];
				});
			},
			getMessage() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.getMessageUrl, {
					type: that.navidx,
					page: that.page
				}).then(function(res) {
					that.isFun = true;
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
			navClick(e) {
				if (this.isFun) {
					this.navidx = e.currentTarget.dataset.i;
					this.isFun = false;
					this.list = [];
					this.page = 1;
					this.getMessage();
				}
			},
			toNavigate(e) {
				let url = e.currentTarget.dataset.url;
				let id = e.currentTarget.dataset.id;
				let idx = e.currentTarget.dataset.idx;
				if (this.list[idx].read == 0) {
					this.list[idx].read = 1;
					this.navarr[this.navidx].count--;
				}
				util.request(api.readMessageUrl, {
					id: id,
					type: this.navidx
				}, "POST");
				if (url) {
					uni.navigateTo({
						url: url
					})
				}
			},
		},
		onReachBottom() {
			this.page = this.page + 1;
			this.getMessage();
		},
	}
</script>

<style>
	@import url("/static/css/tabbar/notice.css");
</style>