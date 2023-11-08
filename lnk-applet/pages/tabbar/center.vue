<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box"
			:style="{paddingTop: statusBarHeight+'px',background: 'rgba(255, 255, 255,'+navbarTrans+')'}">
			<view class="nav-item df">
				<image v-if="navbarTrans>=1" :src="user_info.avatar"></image>
				<view v-if="navbarTrans>=1" class="name">{{ user_info.name }}</view>
			</view>
		</view>
		<!-- User Info -->
		<view class="user-info" :style="{paddingTop: statusBarHeight+54+'px'}">
			<view class="user-img">
				<lazy-load :src="user_info.avatar"></lazy-load>
			</view>
			<view class="user-bg"></view>
			<view class="df" style="width: 100%;">
				<view class="avatar-box" data-url="center/means" @click="navigateToFun">
					<lazy-load :src="user_info.avatar" border-radius="90rpx"></lazy-load>
				</view>
				<view class="btn-box df">
					<view class="bg1 df" data-url="center/means" @click="navigateToFun">
						<image src="/static/img/bj.png"></image>
						<text>编辑</text>
					</view>
					<view class="bg2 df" data-url="setting/index" @click="navigateToFun">
						<text>设置</text>
					</view>
				</view>
			</view>
			<view class="info-box">
				<view class="name ohto" data-url="center/means" @click="navigateToFun">{{ user_info.name }}</view>
				<view v-if="user_info.career" class="tips ohto" data-url="center/means" @click="navigateToFun">
					@{{ user_info.career }}
				</view>
				<view class="tag-box df" data-url="center/means" @click="navigateToFun">
					<view class="df">
						<image v-if="user_info.gender == 1" src="/static/img/nan.png"></image>
						<image v-else src="/static/img/nv.png"></image>
					</view>
					<view class="df" v-if="user_info.age">{{ user_info.age }}</view>
					<view class="df">IP：{{ user_info.province }}</view>
				</view>
				<view class="num-box df">
					<view class="num-item df" data-url="center/follow?type=2" @click="navigateToFun">
						<text class="t1">{{ user_info.fans }}</text>
						<text>粉丝</text>
					</view>
					<view class="num-item df" data-url="center/follow?type=1" @click="navigateToFun">
						<text class="t1">{{ user_info.follow }}</text>
						<text>关注</text>
					</view>
				</view>
			</view>
		</view>
		<!-- Circle Box -->
		<view class="circle">
			<view class="circle-title">加入的圈子</view>
			<view class="circle-box df">
				<view v-if="!circle_list.length" class="circle-item df" @click="toCircle">
					<view class="circle-add">＋</view>
				</view>
				<view v-else :data-url="'circle/details?id='+item.circle_id" @click="navigateToFun"
					class="circle-item df" v-for="(item,idx) in circle_list" :key="idx">
					<lazy-load :src="item.circle.avatar" width="100rpx" height="100rpx"
						border-radius="50rpx"></lazy-load>
					<view v-if="item.circle.is_new" class="new"></view>
					<view class="name ohto">{{ item.circle.name }}</view>
				</view>
			</view>
		</view>
		<!-- Bar Box -->
		<view class="bar-box df" :style="{top:statusBarHeight+43+'px'}">
			<block v-for="(item,idx) in bararr" :key="idx">
				<view v-if="idx>0" class="bar-line"></view>
				<view @click="barClick" :data-i="idx" class="bar-item" :class="{'bar-active':idx==baridx}">
					{{item}}
					<text v-if="idx==0 && user_info.dynamic_count">{{user_info.dynamic_count}}</text>
					<text v-if="idx==1 && user_info.like_dynamic_count">{{user_info.like_dynamic_count}}</text>
				</view>
			</block>
		</view>
		<!-- Content Box -->
		<view class="content">
			<view class="empty-box" v-if="isEmpty">
				<image v-if="baridx==0" :src="insetUrl+'9.png'" mode="aspectFill" />
				<image v-else :src="insetUrl+'8.png'" mode="aspectFill" />
				<view v-if="baridx==0" class="text df">
					<view>没有动态，</view>
					<view data-url="dynamic/add" @click="navigateToFun" style="color: #4cd964;">去发一个</view>
				</view>
				<view v-else class="text">这里空空的..</view>
			</view>
			<view class="content-box" v-if="!isEmpty && baridx!=2">
				<block v-for="(item,idx) in list" :key="item.id">
					<dynamicMy :item="item" :idx="idx" :showView="baridx==0?true:false"></dynamicMy>
				</block>
			</view>
		</view>
		<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
		<view v-else class="no-more">已经到底了</view>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	import dynamicMy from '@/components/dynamic/my.vue';
	export default {
		components: {
			dynamicMy
		},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				insetUrl: api.insetUrl,
				scrollTop: 0,
				navbarTrans: 0,
				notice: 0,
				user_info: {
					name: '...',
					fans: 0,
					follow: 0,
					gender: 0,
					province: '...',
				},
				circle_list: [],
				bararr: ['动态', '赞'],
				baridx: 0,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
				isdt: true,
			}
		},
		async onLoad() {
			uni.showShareMenu();
			await this.$onLaunched;
			if (this.isUserInfo) this.user_info = uni.getStorageSync('user_info');
			if (!this.isUserInfo && this.isdt) {
				this.userCircle();
				this.userPublishContent();
			}
		},
		onShow() {
			if (uni.getStorageSync('user_info')) {
				this.isUserInfo = false;
				this.user_info = uni.getStorageSync('user_info');
				this.getMessageCount();
				if (app.globalData.dw == 1) {
					app.globalData.dw = 0;
					this.isdt = false;
					this.baridx = 0;
					this.list = [];
					this.page = 1;
					this.userPublishContent();
				}
			}
		},
		onPullDownRefresh() {
			this.userRefreshInfo();
			this.userCircle();
			this.list = [];
			this.page = 1;
			this.userPublishContent();
			uni.stopPullDownRefresh();
		},
		methods: {
			userRefreshInfo() {
				let that = this
				util.request(api.userRefreshInfoUrl).then(function(res) {
					that.user_info = res.data.info;
					uni.setStorageSync('user_info', res.data.info);
					uni.setStorageSync('user_token', res.data.token);
				});
			},
			userCircle() {
				let that = this
				util.request(api.userCircleUrl).then(function(res) {
					that.circle_list = res.data;
				});
			},
			userPublishContent() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.userPublishContentUrl, {
					type: that.baridx,
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
			barClick(e) {
				if (this.isFun) {
					this.baridx = e.currentTarget.dataset.i;
					this.isFun = false;
					this.list = [];
					this.page = 1;
					this.userPublishContent();
				}
			},
			toCircle(e) {
				uni.switchTab({
					url: '/pages/tabbar/circle'
				})
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			getMessageCount() {
				let that = this;
				util.request(api.messageCountUrl).then(function(res) {
					that.notice = res.data;
					if (res.data) {
						uni.setTabBarBadge({
							index: 2,
							text: res.data.toString()
						})
					} else {
						uni.removeTabBarBadge({
							index: 2
						})
					}
				})
			}
		},
		onReachBottom() {
			if (this.list.length) {
				this.page = this.page + 1;
				this.userPublishContent();
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
			let frontColor = "#ffffff";
			var trans = (e.scrollTop > 200 ? 200 : e.scrollTop) / 200;
			if (trans >= 1) {
				frontColor = "#000000";
			}
			this.navbarTrans = trans;
			uni.setNavigationBarColor({
				frontColor: frontColor,
				backgroundColor: "#ffffff",
				animation: {
					duration: 400,
					timingFunc: 'easeIn'
				}
			})
		},
		onShareAppMessage: function() {},
		onShareTimeline(res) {
			return {
				title: app.globalData.shareTitle
			}
		},
	}
</script>

<style>
	@import url("/static/css/tabbar/center.css");
</style>