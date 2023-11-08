<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box"
			:style="{paddingTop: statusBarHeight+'px',background: 'rgba(255, 255, 255, '+navbarTrans+')'}">
			<view class="nav-item df">
				<view class="nav-back df" @click="navBack">
					<image :src="'/static/img/'+(navbarTrans==1?'back.png':'backw.png')"></image>
				</view>
				<view v-if="scrollTop>350" class="nav-name">{{ user_info.name }}</view>
			</view>
		</view>
		<!-- User Info -->
		<view class="user-info" :style="{paddingTop: statusBarHeight+54+'px'}">
			<view class="user-img">
				<lazy-load :src="user_info.avatar"></lazy-load>
			</view>
			<view class="user-bg"></view>
			<view class="df" style="width: 100%;">
				<view class="avatar-box">
					<lazy-load :src="user_info.avatar" border-radius="90rpx"></lazy-load>
				</view>
				<view class="btn-box df" @click="followUser">
					<view class="bg1" :class="{'bg2':user_info.is_follow}">{{user_info.is_follow?'已关注':'＋关注'}}</view>
				</view>
			</view>
			<view class="info-box">
				<view class="name ohto">{{ user_info.name }}</view>
				<view class="tips ohto" v-if="user_info.career">@{{ user_info.career }}</view>
				<view class="tag-box df">
					<view class="df">
						<image v-if="user_info.gender == 1" src="/static/img/nan.png"></image>
						<image v-else src="/static/img/nv.png"></image>
					</view>
					<view class="df" v-if="user_info.age">{{ user_info.age }}</view>
					<view class="df">IP：{{ user_info.province }}</view>
				</view>
				<view class="num-box df">
					<view class="num-item df">
						<text class="t1">{{ user_info.fans }}</text>
						<text>粉丝</text>
					</view>
					<view class="num-item df">
						<text class="t1">{{ user_info.follow }}</text>
						<text>关注</text>
					</view>
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
				<image :src="insetUrl+'9.png'" mode="aspectFill" />
				<view class="text">空..</view>
			</view>
			<view class="content-box" v-if="!isEmpty && baridx!=2">
				<block v-for="(item,idx) in list" :key="item.id">
					<dynamicBox :item="item" :idx="idx" @likeback="likeclick">
					</dynamicBox>
				</block>
			</view>
		</view>
		<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
		<view v-else class="no-more">已经到底了</view>
		<!-- Tips Box -->
		<uni-popup ref="tipsPopup" type="top" mask-background-color="rgba(0, 0, 0, 0)">
			<view class="tips-box df" :style="{marginTop: statusBarHeight+44+'px'}">
				<view class="tips-item">{{tips_title}}</view>
			</view>
		</uni-popup>
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
				scrollTop: 0,
				navbarTrans: 0,
				user_info: {
					name: '...',
					fans: 0,
					follow: 0,
					gender: 0,
					province: '...',
				},
				bararr: ['动态', '赞'],
				baridx: 0,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
				tips_title: '',
			}
		},
		async onLoad(option) {
			uni.showShareMenu();
			await this.$onLaunched;
			let that = this;
			if (option.id && option.id > 0) {
				that.id = option.id;
				that.userDetails();
				that.userPublishContent();
			} else {
				that.tips_title = '未找到用户或用户异常！';
				that.$refs.tipsPopup.open();
				setTimeout(function() {
					that.$refs.tipsPopup.close();
					that.navBack();
				}, 2000);
			}
		},
		methods: {
			userDetails() {
				let that = this;
				util.request(api.userDetailsUrl, {
					user_id: that.id
				}).then(function(res) {
					if (res.code == 200) {
						that.user_info = res.data;
					} else {
						that.tips_title = res.msg;
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
						}, 2000);
					}
				});
			},
			userPublishContent() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.userPublishContentUrl, {
					type: that.baridx,
					user_id: that.id,
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
			followUser() {
				let that = this;
				util.request(api.followUserUrl, {
					uname: uni.getStorageSync('user_info').name,
					user_id: that.user_info.id,
					is_follow: that.user_info.is_follow
				}, 'POST').then(function(res) {
					if (res.code == 200) {
						that.user_info.is_follow = !that.user_info.is_follow;
					} else {
						that.tips_title = res.msg;
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
						}, 1500);
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
						url: '/pages/tabbar/index'
					})
				}
			},
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
	@import url("/static/css/user/details.css");
</style>