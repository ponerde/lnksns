<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box" :style="{paddingTop: statusBarHeight+'px',background:navbarTrans==1?'#fff':'#101010'}">
			<view class="nav-item df">
				<view data-url="circle/search" @click="navigateToFun" class="search df"
					:style="{background:navbarTrans==1?'#f5f5f5':'#212121'}">
					<image src="/static/img/ss.png"></image>
					<text>搜索</text>
				</view>
			</view>
		</view>
		<!-- Put Box -->
		<view class="put-box" :style="{paddingTop: statusBarHeight+54+'px'}">
			<block v-for="(item,idx) in putarr" :key="idx">
				<view class="put-item df">
					<block v-for="(v,i) in item" :key="i">
						<view class="put-view df" :data-url="'circle/details?id='+v.id" @click="navigateToFun"
							:style="{background:v.highlight?'#4cd964':'#212121'}">
							<view class="cu-img-group" v-if="v.user_count">
								<view v-for="(img,k) in v.user" :key="k" class="cu-img">
									<image :src="img" mode="aspectFill"></image>
								</view>
								<view class="cu-img cu-item" :class="{'cu-active':v.highlight}">{{v.user_count}}人</view>
							</view>
							<view class="cu-name" :style="{color:v.highlight?'#000':'#ccc'}">{{v.name}}</view>
						</view>
					</block>
					<view style="flex-shrink: 0;width: 10px;color: #101010;">LNK</view>
				</view>
			</block>
		</view>
		<!-- Content Box -->
		<view class="content">
			<block v-for="(item,idx) in list" :key="idx">
				<view class="circle-card">
					<view class="circle-top df" :data-url="'circle/details?id='+item.id" @click="navigateToFun">
						<view>
							<view class="top-name df">
								<image src="/static/img/qz.png"></image>
								<text>{{ item.name }}</text>
							</view>
							<view class="circle-num">
								<text>{{ item.dynamic_count }} 篇内容</text>
								<text>{{ item.user_count }} 人在讨论</text>
							</view>
						</view>
						<lazy-load :src="item.avatar" width="120rpx" height="120rpx" border-radius="60rpx"></lazy-load>
					</view>
					<view class="circle-center" :data-url="'dynamic/details?id='+v.id" @click="navigateToFun"
						v-for="(v,i) in item.dynamic" :key="i">
						<view class="circle-left" :style="{width: v.dynamic_img?'calc(100% - 180rpx)':'100%'}">
							<view class="circle-user df" :data-url="'user/details?id='+v.user_id"
								@click.stop="navigateToFun">
								<lazy-load :src="v.user.avatar" width="48rpx" height="48rpx"
									border-radius="48rpx"></lazy-load>
								<view class="name ohto">{{ v.user.name }}</view>
								<text v-if="v.user.career">@{{ v.user.career }}</text>
							</view>
							<view class="circle-content ohto2">{{ v.content }}</view>
							<view class="circle-num">
								<text>评论 {{ v.dynamic_comment }}</text>
								<text>赞 {{ v.dynamic_like }}</text>
							</view>
						</view>
						<block v-if="v.dynamic_img">
							<lazy-load :src="v.img" width="160rpx" height="160rpx" border-radius="8rpx"></lazy-load>
							<view class="img-count df">
								<image src="/static/img/i.png"></image>
								<text>{{ v.dynamic_img }}</text>
							</view>
						</block>
					</view>
				</view>
			</block>
			<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
			<view v-else class="no-more">没有更多了️</view>
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
				statusBarHeight: app.globalData.statusBarHeight,
				scrollTop: 0,
				navbarTrans: 0,
				putarr: [],
				list: [],
				page: 1,
				isEmpty: false,
				loadStatus: 'more',
			}
		},
		onPullDownRefresh() {
			this.list = [];
			this.page = 1;
			this.getTopCircle();
			this.getCircleList();
			uni.stopPullDownRefresh();
		},
		async onLoad() {
			uni.showShareMenu();
			await this.$onLaunched;
			this.getTopCircle();
			this.getCircleList();
		},
		onShow() {
			this.getMessageCount();
		},
		methods: {
			getCircleList() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.getCircleListUrl, {
					page: that.page
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
			},
			getTopCircle() {
				let that = this;
				util.request(api.getTopCircleUrl).then(function(res) {
					that.putarr = res.data;
				});
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			getMessageCount() {
				util.request(api.messageCountUrl).then(function(res) {
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
				this.getCircleList();
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
	@import url("/static/css/tabbar/circle.css");
</style>